<?php
session_start();
require 'config.php';
$userDisponibile = true;
$login = false;
$eliminazione = false;
$error=false;
if (isset($_GET['username'])) {
    $usernameAdmin = $_GET['username'];
}

if (isset($_POST['elimina'])) {
    $adminId = getAdminIdByUsername($usernameAdmin);
    if (eliminaUser($_POST['username'], $adminId, $usernameAdmin)) {
        $eliminazione = true;
        logActivity($usernameAdmin, "ha eliminato user: ", $_POST['username']);
    } else {
        $eliminazione = false;
        $error=true;
    }
}

function getAdminIdByUsername($username) {
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
        $stmt->execute([$username]);
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

function eliminaUser($username, $adminId, $usernameAdmin) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Elimino l'utente corrispondente all'username e all'id_admin forniti
        $stmt = $connessione->prepare("DELETE FROM `user` WHERE `username` = ? AND `id_admin` = ?");
        $stmt->execute([$username, $adminId]);
        $rowCount = $stmt->rowCount();
        return $rowCount > 0; // Ritorna true se almeno una riga è stata eliminata
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
        <link rel="stylesheet" href="CSS/eliminaUser.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>Andrea's Drive</title>
    </head>
    <body>
        <div class="titolo">Elimina User</div>
        <?php
        if (!$eliminazione) {
            ?>
            <form class="delete_form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?username='.$usernameAdmin ?>">
                <p>Username: <input type="text" name="username" class="username" id="username"></p>
                <input type="submit" value="Elimina" name="elimina" id="elimina" class="disabled">
            </form>
            <button class="log_inButton" onclick="location.href='dashboard.php?username=<?php echo $usernameAdmin ?>&admin=<?php echo $usernameAdmin ?>'">dashboard</button>
        <?php
            if($error){
                echo '<p style="color:red">utente non trovato</p>';
            }
        }else{
            echo '
            <div class="positiveResult">
                <p class="correct">eliminazione avvenuta correttamente</p>
                <button class="log_inButton" onclick="location.href=\'dashboard.php?username='.$usernameAdmin.'&admin='.$usernameAdmin.'\'">dashboard</button>
                <button class="log_inButton" onclick="location.href=\'' . htmlspecialchars($_SERVER['PHP_SELF'].'?username='.$usernameAdmin) . '\'">Elimina nuovo utente</button>    
            </div>';
        }
        ?>
    </body>
    <script src="JS/eliminaUser.js"></script>
</html>
