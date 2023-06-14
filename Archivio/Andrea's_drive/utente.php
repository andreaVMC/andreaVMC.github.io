<?php 
@require 'config.php';

if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    echo "Username non specificato.";
}

if (isset($_GET['admin'])) {
    $adminId = $_GET['admin'];
} else {
    echo "ID admin non specificato.";
}

if (isset($_GET['subject'])) {
    $subject = $_GET['subject'];
} else {
    echo "subject non specificato.";
}

if (isset($_POST['privilegio'])) {
    changePrivilegio($subject);
}

function changePrivilegio($subject) {
    try {
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'utente nella tabella user
        $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
        $statement->execute([':username' => $subject]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $newPrivilegio = ($user['privilegio'] == 1) ? 0 : 1;

            // Aggiorna il campo privilegio
            $statement = $connessione->prepare("UPDATE user SET privilegio = :privilegio WHERE username = :username");
            $statement->execute([':privilegio' => $newPrivilegio, ':username' => $subject]);
        } else {
            echo "errore";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


function takeInfos($subject){
    try{
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'username nella tabella admin
        $statement = $connessione->prepare("SELECT * FROM admin WHERE username = :username");
        $statement->execute([':username' => $subject]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        if($admin){
            // Memorizza i campi dell'admin in variabili globali
            $GLOBALS['id'] = $admin['id_admin'];
            $GLOBALS['id_admin'] = $admin['id_admin'];
            $GLOBALS['Username'] = $admin['username'];
            $GLOBALS['Password'] = $admin['password'];
            $GLOBALS['email'] = $admin['email'];
            $GLOBALS['nome'] = $admin['nome'];
            $GLOBALS['cognome'] = $admin['cognome'];
        } else {
            // Cerca l'username nella tabella user
            $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
            $statement->execute([':username' => $subject]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if($user){
                // Memorizza i campi dell'utente in variabili globali
                $GLOBALS['id'] = $user['id'];
                $GLOBALS['id_admin'] = $user['id_admin'];
                $GLOBALS['Username'] = $user['username'];
                $GLOBALS['Password'] = $user['password'];
                $GLOBALS['email'] = $user['email'];
                $GLOBALS['nome'] = $user['nome'];
                $GLOBALS['cognome'] = $user['cognome'];
                $GLOBALS['privilegio'] = $user['privilegio'];
                // ... Memorizza gli altri campi desiderati dell'utente
            } else {
                // Nessun record corrispondente trovato
                return false;
            }
        }
                
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    
    return true;
}

function isAdmin($username,$adminId){
    if($username==$adminId){
        return true;
    }
    return false;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Andrea's drive</title>
    <link rel="stylesheet" href="CSS/utente.css">
</head>
<body>
    <div class="intestazione">
        <div class="user">
            <div class="content">
                <?php 
                    if(isAdmin($username,$adminId)){
                        echo "admin: ".$adminId;
                    }else{
                        echo "user: ".$username."<br>";
                        echo "admin: ".$adminId;
                    }
                ?>
            </div>
        </div>
        <div class="titolo">
            <?php
                takeInfos($subject);
                echo $subject;
            ?>
        </div>
        <div class="actions">
            <button class="log_inButton" onclick="location.href='dashboard.php?username=<?php echo $username; ?>&admin=<?php echo $adminId; ?>'">dashboard</button>
            <button onclick="window.history.back()">go back</button>
        </div>
    </div>
    <?php if($id!=""){?>
    <table>
        <tr>
            <td>admin Id:</td>
            <td>
                <?php
                    echo $id_admin;
                ?>
            </td>
        </tr>
        <tr>
            <td>Id utente:</td>
            <td>
                <?php
                    echo $id;
                ?>
            </td>
        </tr>
        <tr>
            <td>nome:</td>
            <td>
                <?php
                    echo $nome;
                ?>
            </td>
        </tr>
        <tr>
            <td>cognome:</td>
            <td>
                <?php
                    echo $cognome;
                ?>
            </td>
        </tr>
        <tr>
            <td>email:</td>
            <td>
                <?php
                    echo $email;
                ?>
            </td>
        </tr>
        <tr>
            <td style="border-bottom-color:transparent;">pasword:</td>
            <td style="border-bottom-color:transparent;" class="password">
                <?php
                    if(isAdmin($username,$adminId) || $username==$subject){
                        echo $Password;
                    }else{
                        echo "dati riservati";
                    }
                ?>
            </td>
        </tr>
    </table>
    <?php }else{ ?>
        <div class="eliminato">Utente non più disponibile</div>
    <?php } ?>
    <?php if(isAdmin($username,$adminId) && $subject!=$adminId && $id!="") { ?>
        <form class="form" method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF'])."?username=".$username."&admin=".$adminId."&subject=".$subject?>">
            <input type="submit" value="
                <?php
                    if(!$privilegio){
                        echo "dai privilegi file";
                    }else{
                        echo "rimuovi privilegi file";
                    }
                ?>
            " class="privilegio" name="privilegio">
        </form>
    <?php } ?>
</body>
</html>