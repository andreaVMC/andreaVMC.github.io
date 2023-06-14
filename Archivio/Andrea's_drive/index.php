<?php
    require 'config.php';
    $error=false;
    $errore_connessione=false;
    $adminOption="";
    if (isset($_POST['login'])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $adminOption = $_POST["admin"];
        $adminOption = controllaAdmin($username,$password,$adminOption);

        if (utente_corretto($username, $password)) {
            $queryString = http_build_query([
                'username' => $username,
                'admin' => $adminOption
            ]);
            header("Location: dashboard.php?" . $queryString);
            exit();
        } else {
            $error = true;
        }
    }

    function controllaAdmin($username, $password, $adminOption) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Controllo nella tabella `admin`
            $stmt = $connessione->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
            $stmt->execute([$username, $password]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($admin !== false) {
                return $username;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $adminOption;
    }
    

    function utente_corretto($username, $password) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Controllo nella tabella `admin`
            $stmt = $connessione->prepare("SELECT * FROM `admin` WHERE `username` = ? AND `password` = ?");
            $stmt->execute([$username, $password]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($admin !== false) {
                return true; // Lo username e la password corrispondono nella tabella `admin`
            }
    
            // Controllo nella tabella `user`
            $admin_id = $_POST['admin'];
            $admin_id=getAdminIdByUsername($admin_id);
            if ($admin_id !== null){
                $stmt = $connessione->prepare("SELECT * FROM `user` WHERE `username` = ? AND `password` = ? AND `id_admin` = ?");
                $stmt->execute([$username, $password, $admin_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return ($user !== false); // Restituisce true se lo username, la password e l'admin_id corrispondono nella tabella `user`, altrimenti restituisce false
            }else{
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function getAdminIdByUsername($admin_id) {
        try {
            // Stabilisco la connessione al database
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Cerco l'ID corrispondente all'username nella tabella `admin`
            $stmt = $connessione->prepare("SELECT id_admin FROM `admin` WHERE `username` = ?");
            $stmt->execute([$admin_id]);
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
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/indirizzo.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>Andrea's Drive</title>
    </head>
    <body>
        <div class="titolo">Pagina di accesso</div>
        <div class="login_form">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <div class="userDataContainer">
                    <p>username: <input type="text" name="username" id="username"></input></p>
                    <p>password: <input type="password" name="password" id="password"></input></p>
                    <p>azienda: 
                        <?php
                            // Stabilire la connessione al database
                            $conn = new PDO(
                                "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                                $GLOBALS['dbuser'],
                                $GLOBALS['dbpassword']
                            );
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // Query per selezionare gli username dalla tabella admin
                            $stmt = $conn->query("SELECT username FROM admin");
                            $usernames = $stmt->fetchAll(PDO::FETCH_COLUMN);

                            // Creazione del menu a tendina
                            echo "<select name='admin' style='
                                eight: 80%;
                                margin-left: 4%;
                                border-color: var(--cliccabile);
                                border-radius: 5px;
                                background-color: var(--sfondo);
                                text-align: center;
                            '>";
                            foreach ($usernames as $username) {
                                echo "<option value='$username'>$username</option>";
                            }
                            echo "</select>";

                            // Chiudere la connessione al database
                            $conn = null;
                        ?>

                    </p>
                    <?php
                        if($error){
                            echo '<div  class="errore">dati errati, nessun utente riconosciuto</div>';
                        }else if($errore_connessione){
                            echo '<div  class="errore">errore di connessione</div>';
                        }
                    ?>
                    <input type="submit" value="Log in" id="log_in" name="login" class="disabled"></input>
                </div>
            </form>
            <div class="signinContainer">
                    <p class="titoletto">crea nuovo account</p>
                    <button class="sign_inButton" onclick="location.href='signIn.php'">Sign in</button>
            </div>
        </div>
    </body>
    <script src="JS/index.js"></script>
</html>