<?php
session_start();
require 'config.php';
$userDisponibile = true;
$login = false;
if (isset($_GET['username'])) {
    $usernameAdmin = $_GET['username'];
}

if (isset($_POST['signin'])) {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userDisponibile = UsernameAvailable($username);
    if ($userDisponibile) {
        if (signIn($nome, $cognome, $username, $password, $email, $usernameAdmin)) {
            $login = true;
            logActivity($usernameAdmin, "ha creato user: ", $_POST['username']);
            logActivity($usernameAdmin, "ha creato la cartella: ", $_POST['username']);
        } else {
            $login = false;
        }
    }
}

function signIn($nome, $cognome, $username, $password, $email, $usernameAdmin)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserisco i dati del nuovo utente nella tabella `user`
        $stmt = $connessione->prepare("INSERT INTO `user` (`nome`, `cognome`, `username`, `password`, `email`, `id_admin`, `id_root`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $cognome, $username, $password, $email, getAdminIdByUsername($usernameAdmin), getAdminRootIdByUsername($usernameAdmin)]);
        // Creo la directory per il nuovo utente
        $directory = "datas/" . $usernameAdmin . "/" . $username;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        chmod("datas/" . $usernameAdmin . "/" . $username, 0777);
        $query = $connessione->prepare("INSERT INTO `file`(`propietario`, `nome`, `path`, `id_admin`) VALUES (?,?,?,?)");
        $query->execute([$usernameAdmin,$username,"datas/" . $usernameAdmin,getAdminIdByUserName($usernameAdmin)]);
        return true;
        
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function getAdminRootIdByUsername($username)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerco il root_id corrispondente all'username nella tabella `admin`
        $stmt = $connessione->prepare("SELECT root_id FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin !== false) {
            return $admin['root_id']; // Restituisco il root_id corrispondente
        } else {
            return null; // Nessun corrispondente trovato
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return null; // Errore di connessione al database
    }
}

function getAdminIdByUsername($usernameAdmin) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerco l'ID corrispondente all'username nella tabella `admin`
        $stmt = $connessione->prepare("SELECT `id_admin` FROM `admin` WHERE `username` = ?");
        $stmt->execute([$usernameAdmin]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin !== false) {
            return $admin['id_admin']; // Restituisco l'ID corrispondente
        } else {
            return null; // Nessun corrispondente trovato
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return null; // Errore di connessione al database
    }
}

function UsernameAvailable($username)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifico se il nome utente esiste già nel database
        $stmt = $connessione->prepare("SELECT COUNT(*) FROM `user` WHERE `username` = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();

        return $count == 0; // Restituisce true se il nome utente non esiste nel database, altrimenti restituisce false
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function logActivity($username, $azione, $destinazione)
{
    try {
        $adminId = getAdminIdByUsername($username);
        $timestamp = date("Y-m-d H:i:s");

        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Memorizzo l'attività nella tabella attivita
        $stmt = $connessione->prepare("INSERT INTO `attivita`(`sorgente`, `azione`, `destinazione`, `data`, `id_admin`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $azione, $destinazione, $timestamp, $adminId]);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/CreaUser.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>Andrea's Drive</title>
    </head>
    <body>
        <?php
            if (!$login) {
                echo '
                    <div class="titolo">Pagina di registrazione User</div>
                    <div class="signin_form">
                        <form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF'].'?username='.$usernameAdmin) . '">
                            <div class="userDataContainer">
                                <p>nome: <input type="text" name="nome" id="nome"></p>
                                <p>cognome: <input type="text" name="cognome" id="cognome"></p>
                                <p>email: <input type="text" name="email" id="email"></p>
                                <p>username: <input type="text" name="username" id="username"></p>';
                if (!$userDisponibile) {
                    echo '<p class="error">username non disponibile</p>';
                }
                echo '
                            <p>password: <input type="password" name="password" id="password"></p>
                            <input type="submit" value="Sign in" id="sign_in" name="signin" class="disabled">
                        </div>
                    </form> 
                </div>
                <div class="loginContainer"><button class="log_inButton" onclick="location.href=\'dashboard.php?username='.$usernameAdmin.'&admin='.$usernameAdmin.'\'">dashboard</button></div>';
            } else {
                echo '
                    <div class="positiveResult">
                        <p class="correct">registrazione avvenuta correttamente</p>
                        <button class="log_inButton" onclick="location.href=\'index.php\'">Log in</button>
                        <button class="log_inButton" onclick="location.href=\'' . htmlspecialchars($_SERVER['PHP_SELF'].'?username='.$usernameAdmin) . '\'" >crea nuovo utente user</button>
                        <button class="log_inButton" onclick="location.href=\'dashboard.php?username='.$usernameAdmin.'&admin='.$usernameAdmin.'\'" >torna alla dashboard</button>
                    </div>';
            }
        ?>
    </body>
    <script src="JS/signIn.js"></script>
</html>
