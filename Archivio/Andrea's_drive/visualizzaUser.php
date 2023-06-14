<?php
    @require 'config.php';
    session_start();
    
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
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/VisualizzaUser.css">
        <title>Andrea's Drive</title>
    </head>
    <body>
        <div class="nav">
            <?php
                if ($username === $adminId) {
                    echo '<p class="nomeUtente">admin: ' . $username . '</p>';
                } else {
                    echo '<p class="nomeUtente">user: ' . $username . '<br>admin: ' . $adminId . '</p>';
                }
            ?>
            <p class="log_inButton" onclick="location.href='dashboard.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>'">dashboard</p>
        </div>
        <div class="content">
            <?php
            try {
                // Stabilisco la connessione al database
                $connessione = new PDO(
                    "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
                    $GLOBALS['dbuser'],
                    $GLOBALS['dbpassword']
                );
                $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Recupera il valore id_admin corrispondente a $adminId
                $id_admin = getAdminId($adminId);

                if ($id_admin !== false) {
                    // Recupera tutti i record dalla tabella `user` con id_admin uguale a $id_admin
                    $stmt = $connessione->prepare("SELECT * FROM `user` WHERE id_admin = ?");
                    $stmt->execute([$id_admin]);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Verifica se sono presenti utenti
                    if ($users) {
                        echo '<table>';
                        echo '<tr>';
                        echo '<th>ID utente</th>';
                        echo '<th>Nome</th>';
                        echo '<th>Cognome</th>';
                        echo '<th>Username</th>';
                        echo '<th>Email</th>';
                        if ($username === $adminId) {
                            echo '<th>Password</th>';
                        }
                        echo '</tr>';

                        foreach ($users as $user) {
                            echo '<tr onclick="location.href=\'utente.php?username='.$username.'&admin='.$adminId.'&subject='.$user['username'].'\'">';
                            echo '<td style="border-left: 1px solid transparent">' . $user['id'] . '</td>';
                            echo '<td>' . $user['nome'] . '</td>';
                            echo '<td>' . $user['cognome'] . '</td>';
                            echo '<td>' . $user['username'] . '</td>';
                            echo '<td>' . $user['email'] . '</td>';
                            if ($username === $adminId) {
                                echo '<td><div class="password">' . $user['password'] . '</div></td>';
                            }
                            echo '</tr>';
                        }

                        echo '</table>';
                    } else {
                        echo '<p class="nobody">Nessun utente,<br>puoi crearli andando nella sezione<br>"crea user" della tua dashboard</p>';
                    }
                } else {
                    echo 'Nessun id_admin corrispondente trovato.';
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
            ?>
        </div>
    </body>
</html>
