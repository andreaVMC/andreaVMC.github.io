<?php
session_start();
require 'config.php';
$userDisponibile = true;
if (isset($_POST['signin'])) {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userDisponibile = UsernameAvailable($username);
    if (signIn($nome, $cognome, $username, $password, $email)) {
        $login = true;
        // Store activity in the attivita table
        logActivity($username, "ha registrato", "il suo account admin");
    } else {
        $login = false;
    }
}

function getAdminId($adminId) {
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Recupera il valore id_admin dal record con lo stesso username di $adminId
        $stmt = $connessione->prepare("SELECT id_admin FROM admin WHERE username = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin !== false) {
            return $admin['id_admin'];
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    
    return false;
}

function logActivity($username, $azione, $destinazione)
{
    try {
        $adminId = getAdminId($username);
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

function signIn($nome, $cognome, $username, $password, $email)
{
    try {
        if (UsernameAvailable($username)) {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Creo la cartella per il nuovo utente admin
            $stmt = $connessione->prepare("INSERT INTO `directorys`(`nome`) VALUES (?)");
            $stmt->execute([$username]);
            mkdir("datas/" . $username);
            chmod("datas/" . $username, 0777);

            // Ottengo l'ID della directory appena creata
            $stmt = $connessione->prepare("SELECT `id` FROM `directorys` WHERE nome = ?");
            $stmt->execute([$username]);
            $id_directory = $stmt->fetchColumn();

            // Memorizzo il nuovo utente admin
            $stmt = $connessione->prepare("INSERT INTO `admin`(`nome`, `cognome`, `email`, `password`, `username`, `root_id`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $cognome, $email, $password, $username, $id_directory]);

            // Ottengo l'id dell'admin
            $stmt = $connessione->prepare("SELECT `id_admin` FROM `admin` WHERE `root_id` = ?");
            $stmt->execute([$id_directory]);
            $id_admin = $stmt->fetchColumn();

            // Assegno l'id admin alla cartella appena creata
            $stmt = $connessione->prepare("UPDATE `directorys` SET `admin_id`=? WHERE `id`=?");
            $stmt->execute([$id_admin, $id_directory]);
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}

function UsernameAvailable($username)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifico se il nome utente esiste già nel database
        $stmt = $connessione->prepare("SELECT COUNT(*) FROM `admin` WHERE `username` = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
        return $count == 0; // Restituisce true se il nome utente non esiste nel database, altrimenti restituisce false
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/SingIn.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Andrea's Drive</title>
</head>

<body>
    <?php
    if (!$login) {
        echo '
                    <div class="titolo">Pagina di registrazione</div>
                    <div class="signin_form">
                        <form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
                            <div class="userDataContainer">
                                <p>nome: <input type="text" name="nome" id="nome"></p>
                                <p>cognome: <input type="text" name="cognome" id="cognome"></p>
                                <p>email: <input type="text" name="email" id="email"></p>
                                <p>username: <input type="text" name="username" id="username"></p>';
        if (!$userDisponibile) {
            echo '<p style="color:red; font-size:1vw;height:1vh;">username non disponibile</p>';
        }
        echo '
                                <p>password: <input type="password" name="password" id="password"></p>
                                <input type="submit" value="Sign in" id="sign_in" name="signin" class="disabled">
                            </div>
                        </form>
                        <div class="loginContainer">
                            <p class="titoletto">accedi</p>
                            <button class="log_inButton" onclick="location.href=\'index.php\'">Log in</button>
                        </div>
                    </div>
                ';
    } else {
        echo '
                    <div class="positiveResult">
                        <p>registrazione avvenuta correttamente<br>vai alla pagina di login per continuare</p>
                        <button class="log_inButton" onclick="location.href=\'index.php\'">Log in</button>
                    </div>
                ';
    } ?>
</body>
<script src="JS/signIn.js"></script>

</html>
