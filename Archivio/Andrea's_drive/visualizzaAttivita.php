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

function getAdminId($adminId)
{
    try {
        // Stabilisco la connessione al database
        $connessione = new PDO(
            "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
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

function isAnAccount($username) {
    try {
        $connessione = new PDO(
            "mysql:host=".$GLOBALS['dbhost'].";dbname=".$GLOBALS['dbname'],
            $GLOBALS['dbuser'],
            $GLOBALS['dbpassword']
        );
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cerca l'username nella tabella admin
        $statement = $connessione->prepare("SELECT * FROM admin WHERE username = :username");
        $statement->execute([':username' => $username]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        // Cerca l'username nella tabella user
        $statement = $connessione->prepare("SELECT * FROM user WHERE username = :username");
        $statement->execute([':username' => $username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Verifica se l'username esiste in almeno una delle tabelle
        if ($admin || $user) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}


try {
    // Stabilisco la connessione al database
    $connessione = new PDO(
        "mysql:host=" . $GLOBALS['dbhost'] . ";dbname=" . $GLOBALS['dbname'],
        $GLOBALS['dbuser'],
        $GLOBALS['dbpassword']
    );
    $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera il valore id_admin corrispondente a $adminId
    $id_admin = getAdminId($adminId);

    if ($id_admin !== false) {
        // Recupera tutti i record dalla tabella `attivita` con id_admin uguale a $id_admin
        $stmt = $connessione->prepare("SELECT * FROM `attivita` WHERE id_admin = ?");
        $stmt->execute([$id_admin]);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica se sono presenti attività
        if ($activities) {
            ?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="CSS/VisualizzaAttivita.css">
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
                    <table>
                        <tr>
                            <th>Data</th>
                            <th>Sorgente</th>
                            <th>Azione</th>
                            <th>Destinazione</th>
                        </tr>
                        <?php
                        foreach ($activities as $activity) {
                            ?>
                            <tr>
                                <td style="border-left: 1px solid transparent"><?php echo $activity['data'] ?></td>
                                <td>
                                    <div class="sorgente" onclick="location.href='utente.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&subject=<?php echo $activity['sorgente'] ?>'">
                                        <?php echo $activity['sorgente'] ?>
                                    </div>
                                </td>
                                <td><?php echo $activity['azione'] ?></td>
                                <td><?php
                                    if(isAnAccount($activity['destinazione'])){
                                        ?>
                                            <div class="sorgente" onclick="location.href='utente.php?username=<?php echo $username ?>&admin=<?php echo $adminId ?>&subject=<?php echo $activity['destinazione'] ?>'">
                                                <?php echo $activity['destinazione'] ?>
                                            </div>
                                        <?php
                                    }else{
                                        echo $activity['destinazione'];
                                    } ?>
                                </td> 
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </body>

            </html>
            <?php
        } else {
            echo 'Nessuna attività presente nella tabella con id_admin corrispondente.';
        }
    } else {
        echo 'Nessun id_admin corrispondente trovato.';
    }
} catch (PDOException $e) {
    echo $e->getMessage();
    return false;
}
?>
