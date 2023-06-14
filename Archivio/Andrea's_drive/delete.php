<?php
@require 'config.php';
session_start();
$error=false;
if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    echo "Username non specificato.";
    exit();
}

if (isset($_GET['admin'])) {
    $adminId = $_GET['admin'];
} else {
    echo "ID admin non specificato.";
    exit();
}

if (isset($_GET['path'])) {
    $path = $_GET['path'];
} else {
    $path = "";
}

$_SESSION['username'] = $username;
$_SESSION['adminId'] = $adminId;


if (isset($_POST['delete'])) {
    try {
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $selectedFiles = $_POST['selectedFiles'];
        foreach ($selectedFiles as $fileName){
            if(!elimina($fileName, $username, $adminId,$path, $connessione)){
                $error=true;
            }
        }
        
    
    } catch (PDOException $e) {
        echo "Errore di connessione al database: " . $e->getMessage();
        return; // Esce dalla funzione in caso di errore di connessione
    }
}

function elimina($fileName, $username, $adminId, $path, $connessione) {
    $autore = trovaAutore($fileName, $adminId, $path, $connessione);
    if ($autore == $username || $username == $adminId || verificaAutorizzazione($connessione,$username)){
        if (is_dir($path . '/' . $fileName)) {
            // Elimina una directory
            if(!eliminaDirectory($path . '/' . $fileName,$connessione,$username,$adminId)) {
                return false;
            }
        }else {
            // Elimina un file
            if (!unlink($path . '/' . $fileName)) {
                return false;
            }
            // Aggiungi un nuovo record nella tabella "attivita"
            $azione = 'ha eliminato:';
            $data = date('Y-m-d H:i:s');

            $statement = $connessione->prepare("INSERT INTO attivita (sorgente, azione, destinazione, data, id_admin) VALUES (?,?,?,?,?)");
            $statement->execute([$username, $azione, $fileName,$data, getAdminIdByUserName($adminId)]);

            // Elimina i record dal database "file"
            $statement = $connessione->prepare("DELETE FROM file WHERE nome = ? AND id_admin = ? AND path = ?");
            $statement->execute([$fileName, getAdminIdByUserName($adminId), $path]);
        }
        return true;
    } else {
        return false;
    }
}

function verificaAutorizzazione($connessione, $username) {
    try {
        // Prepara e esegui la query per ottenere il campo privilegio corrispondente all'username
        $stmt = $connessione->prepare("SELECT privilegio FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result !== false && $result['privilegio'] == 1) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function eliminaDirectory($dirPath,$connessione,$username,$adminId) {
    if (!is_dir($dirPath)) {
        return false;
    }

    $files = scandir($dirPath);
    $files = array_diff($files, array('.', '..'));

    foreach ($files as $file) {
        $filePath = $dirPath . '/' . $file;
        if (is_dir($filePath)) {
            // Elimina la sottodirectory in modo ricorsivo
            eliminaDirectory($filePath,$connessione,$username,$adminId);
        } else {
            // Elimina il file
            unlink($filePath);

            $statement = $connessione->prepare("INSERT INTO attivita (sorgente, azione, destinazione, data, id_admin) VALUES (?,?,?,?,?)");
            $statement->execute([$username, "ha eliminato il file: ", basename($filePath),date('Y-m-d H:i:s'), getAdminIdByUserName($adminId)]);
            // Elimina i record dal database "file"
            $statement = $connessione->prepare("DELETE FROM file WHERE nome = ? AND id_admin = ? AND path = ?");
            $statement->execute([basename($filePath), getAdminIdByUserName($adminId),$dirPath]);
        }
    }

    $statement = $connessione->prepare("INSERT INTO attivita (sorgente, azione, destinazione, data, id_admin) VALUES (?,?,?,?,?)");
    $statement->execute([$username, "ha eliminato la cartella: ", basename($dirPath),date('Y-m-d H:i:s'), getAdminIdByUserName($adminId)]);
    // Elimina i record dal database "file"
    $statement = $connessione->prepare("DELETE FROM file WHERE nome = ? AND id_admin = ? AND path = ?");
    $statement->execute([basename($dirPath), getAdminIdByUserName($adminId), dirname($dirPath)]);
    rmdir($dirPath); // Elimina la directory vuota
    return true;
}


function trovaAutore($fileName, $adminId, $path,$connessione)
{       
        $statement = $connessione->prepare("SELECT COUNT(*) FROM file WHERE nome = :fileName AND id_admin = :adminId AND path = :path");
        $statement->execute([':fileName' => $fileName, ':adminId' => getAdminIdByUserName($adminId), ':path' => $path]);
        $result = $statement->fetchColumn();
        if ($result > 0) {
            $statement = $connessione->prepare("SELECT propietario FROM file WHERE nome = :fileName AND id_admin = :adminId AND path = :path");
            $statement->execute([':fileName' => $fileName, ':adminId' => getAdminIdByUserName($adminId), ':path' => $path]);
            $propietario = $statement->fetchColumn();
            return $propietario;     
        }
}

function getAdminIdByUserName($username)
{
    $connessione = new PDO(
        "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
        $GLOBALS['dbuser'],
        $GLOBALS['dbpassword']
    );
    $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepara la query per recuperare l'id_admin
    $query = $connessione->prepare("SELECT id_admin FROM admin WHERE username = :username");
    $query->bindParam(':username', $username);

    // Esegue la query
    $query->execute();

    // Ottiene il risultato della query
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Verifica se è stato trovato un risultato
    if ($result) {
        return $result['id_admin'];
    } else {
        return null; // Oppure un valore di default, a seconda delle tue esigenze
    }
}


// Funzione per ottenere il peso di un file
function getFileSize($filePath) {
    $sizeInBytes = filesize($filePath);
    $sizeInMBytes = round(($sizeInBytes / 1024), 2);
    return $sizeInMBytes . " KB";
}

// Funzione per ottenere il tipo di un file (file o directory)
function getFileType($filePath) {
    if (is_dir($filePath)) {
        return 'Directory';
    } else {
        return 'File';
    }
}

// Ottieni la lista dei file e delle directory nella cartella indicata
$files = scandir($path);
$files = array_diff($files, array('.', '..')); // Rimuovi le voci relative alla cartella corrente e alla cartella superiore
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Andrea's drive</title>
    <link rel="stylesheet" href="CSS/delete.css">
</head>
<body>
    <div class="titolo">Delete</div>
    <button onclick="location.href='dashboard.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&path=<?php echo $path ?>'">dashboard</button>
    <?php if ($error) { ?>
        <div class="error-message" style="color:red;">Si è verificato un errore durante l'eliminazione.</div>
    <?php } ?>
    <form name="delete_form" class="content" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username."&admin=".$adminId."&path=".$path;?>" method="post">
        <table>
            <tr>
                <th>Nome</th>
                <th>Peso</th>
                <th>Autore</th> <!-- Nuova colonna -->
                <th>Seleziona</th>
            </tr>
            <?php foreach ($files as $file) { ?>
            <tr>
                <td style="border-left-color:transparent;"><?php echo $file; ?></td>
                <td><?php echo getFileSize($path . '/' . $file); ?></td>
                <td><?php
                    try {
                        $connessione = new PDO(
                            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
                            $GLOBALS['dbuser'],
                            $GLOBALS['dbpassword']
                        );
                        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        echo "Errore di connessione al database: " . $e->getMessage();
                        return; // Esce dalla funzione in caso di errore di connessione
                    }
                    echo trovaAutore($file, $adminId, $path, $connessione); 
                ?></td> <!-- Aggiunta della colonna "Autore" -->
                <td>
                    <input type="checkbox" value="<?php echo $file; ?>" name="selectedFiles[]" class="checkbox-input" style="cursor:pointer;">
                </td>
            </tr>
            <?php } ?>
        </table>
        <input type="submit" name="delete" value="Elimina" class="delete-button" style="cursor:pointer;">
    </form>
</body>
</html>


