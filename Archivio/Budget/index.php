<?php
    @require 'config.php';
    session_reset();
    $connessione = instauraConnessione();
    $va=00;
    if(isset($_POST['change_guadagni'])){
        $Id_record=$_POST['change_guadagni'];
        aggiornaRecordStato($Id_record,"guadagni",$connessione);
    }else if(isset($_POST['change_spese'])){
        $Id_record=$_POST['change_spese'];
        aggiornaRecordStato($Id_record,"spese",$connessione);
    }else if(isset($_POST['delete_guadagni'])){
        $Id_record=$_POST['delete_guadagni'];
        deleteRecordStato($Id_record,"guadagni",$connessione);
    }else if(isset($_POST['delete_spese'])){
        $Id_record=$_POST['delete_spese'];
        deleteRecordStato($Id_record,"spese",$connessione);
    }

    if(isset($_POST['aggiungi_guadagno'])){
        $etichetta=$_POST['etichetta'];
        $valore=$_POST['valore'];
        $data=$_POST['data'];
        $stato=$_POST['stato'];
        if(!inserisci_guadagno($connessione,$etichetta,$valore,$data,$stato)){
            //errore
        }
    }else if(isset($_POST['aggiungi_spesa'])){
        $etichetta=$_POST['etichetta'];
        $categoria_Id=$_POST['categoria'];
        $valore=$_POST['valore'];
        $data=$_POST['data'];
        $stato=$_POST['stato'];
        if(!inserisci_spesa($connessione,$etichetta,$categoria_Id,$valore,$data,$stato)){
            //errore
        }
    }else if(isset($_POST['aggiungi_categoria'])){
        $etichetta=$_POST['etichetta'];
        $grado=$_POST['grado'];
        if(!inserisci_categoria($connessione,$etichetta,$grado)){
            //errore
        }
    }else if(isset($_POST["export_guadagni"])) {
        downloadGuadagniCSV($connessione);
    }else if(isset($_POST["export_spese"])) {
        downloadSpeseCSV($connessione);
    }else if(isset($_POST["export_categorie"])) {
        downloadCategorieCSV($connessione);
    }

    if(isset($_POST['autoUpdate_submit'])){
        if($_POST['totale']!=number_format(getTotale($connessione), 2, '.', '')){
            if($_POST['totale']<number_format(getTotale($connessione), 2, '.', '')){
                inserisci_spesa($connessione,"sistema",7,number_format(getTotale($connessione), 2, '.', '')-$_POST['totale'],date('Y-m-d'),0);
            }else{
                inserisci_guadagno($connessione,"sistema",$_POST['totale']-number_format(getTotale($connessione), 2, '.', ''),date('Y-m-d'),0);
            }
        }else if(number_format(getTotale($connessione), 0, '.', '') != calcolaFish($_POST['chip500'],$_POST['chip100'],$_POST['chip50'],$_POST['chip25'],$_POST['chip10'],$_POST['chip5'],$_POST['chip1'])){
            if(calcolaFish($_POST['chip500'],$_POST['chip100'],$_POST['chip50'],$_POST['chip25'],$_POST['chip10'],$_POST['chip5'],$_POST['chip1'])<number_format(getTotale($connessione), 2, '.', '')){
                inserisci_spesa($connessione,"sistema",7,number_format(getTotale($connessione), 2, '.', '')-calcolaFish($_POST['chip500'],$_POST['chip100'],$_POST['chip50'],$_POST['chip25'],$_POST['chip10'],$_POST['chip5'],$_POST['chip1']),date('Y-m-d'),0);
            }else{
                inserisci_guadagno($connessione,"sistema",calcolaFish($_POST['chip500'],$_POST['chip100'],$_POST['chip50'],$_POST['chip25'],$_POST['chip10'],$_POST['chip5'],$_POST['chip1'])-number_format(getTotale($connessione), 2, '.', ''),date('Y-m-d'),0);
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget</title>
    <link rel="icon" href="IMG/favicon.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300&display=swap');
        body{
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            font-family: 'Roboto Condensed', sans-serif;
            scroll-behavior:smooth;
        }

        .navBar{
            /*border:1px solid red;*/
            width:35%;
            display:grid;
            grid-template-columns:repeat(3,1fr);
            margin: 2% 2%;
            padding: 1% 2%;
            border-radius:5px;
            background-color:gray; 
            position:static;
            z-index: 999;
        }

        .navBar>.cntnr{
            display:flex;
            flex-direction:column;
            align-items:center;
            /*border:1px solid green;*/
        }

        .size{
            border:1px solid red;
        }

        .navBar>.cntnr>button{
            width:80%;
            border-radius: 5px;
            padding: 2%;
        }

        .resoconti, .autoUpdate{
            /*border:1px solid green;*/
            display:flex;
            flex-direction:column;
            align-items:center;
            width:80%;
            margin:2%;
        }

        .trDefault{
            background-color:#EAEAEA;
        }

        .resoconto_mensile,.autoUpdate_box,.resoconto_media,  .esportazione{
            /*border:1px solid red;*/
            width:100%;
            display:flex;
            flex-direction:column;
            align-items:center;
            background-color:lightgray;
            padding:2% 2%;
            border-radius:5px;
            margin:2% 2%;
        }

        .autoUpdate_box>button{
            margin-top:2%;
        }

        .autoUpdate_box>form{
            width:100%;
            display:flex;
            flex-direction:column;
            align-items:center;
        }

        form>input{
            margin-top:2%;
            text-align: center;
            /*border: 1px solid #333;*/
            border-radius: 5px;
            padding: 0.5% 0.5%;
        }

        .chips{
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap:10px;
            width:100%;
            /*border: 1px solid red;*/
        }

        .autoUpdate_box>.totale{
            font-weight:700;
            margin-top:2%;
        }

        
        .chip{
            /*border: 1px solid #333;*/
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height:100%;
        }

        .chip>.fish{
            /*border:1px solid green;*/
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .chip>.fish>img{
            width:80%;
            /*margin-top:-75%;*/
        }

        .chip>input{
            margin-top:2%;
            width: 90%;
            text-align: center;
            /*border: 1px solid #333;*/
            border-radius: 5px;
            padding: 2% 2%;
        }

        .mensile_tabelle,.media_tabelle{
            /*border:1px solid purple;*/
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }

        .mediaTb,.mensileTb{
            width:40%;
        }

        .container_cronologia{
            /*border: 1px solid red;*/
            width: 80%;
            border-radius:5px;
            background-color: lightgray;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            padding: 2% 2%;
        }

        .titolo_cronologia{
            margin-bottom: 2%;
        }

        .container_tabelle{
            width: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }

        .container_guadagni{
            /*border: 1px solid red;*/
            width: 40%;
            display: flex;
            flex-direction:column;
        }

        .addRecord>button{
            margin:2% 2%;
        }

        .container_spese{
            /*border: 1px solid red;*/
            width: 40%;
            display: flex;
            flex-direction:column;
        }



        table{
            width: 100%;
            background-color: #222222;
            padding:2% 2%;
            border-radius:5px
        }

        td{
            text-align: center;
        }

        button{
            background-color: #222222;
            color:white;
            border-radius:5px;
            border: 1px solid transparent;
            cursor:pointer;
            transition-duration:0.2s;
        }

        button:hover{
            border-color:white;
        }

        button:active{
            border-color:#222222;
            color:#222222;
            background-color:white;
        }

        .Window_input {
            background-color: #F8F4F9;
            position: fixed;
            top:-200%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 25%;
            /*height: 75vh;*/
            /*display: none;*/
            border-radius:5px;
            box-shadow: 0px 0px 2000px black;
            transition-duration:0.5s;
            display:flex;
            flex-direction:column;
            align-items:center;
            padding: 2% 2%;
        }

        .Window_input>.top{
            display:grid;
            width:100%;
            grid-template-columns: 80% 20%;
            /*border:1px solid red;*/
            padding: 1% 1%;
        }

        .Window_input>.top>div{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
        }

        .Window_input>.top>div>img{
            width: 25%;
            cursor:pointer;
        }

        .Window_input>.top>.titolo_window{
            font-size:150%;
            font-weight:700;
        }

        .Window_input>.input{
            /*border:1px solid red;*/
            padding:2% 2%;
            margin-top:5%;
            width: 80%;
            height: 40%;
            display:flex;
            flex-direction:column;
            align-items:center;
        }

        .Window_input>.input>.form_input{
            /*border:1px solid green;*/
            display:flex;
            flex-direction:column;
            height:100%;
        }

        .Window_input>.input>.form_input>input{
            margin-bottom:10%;
            font-size:125%;
            border:1px solid black;
            border-radius:5px;
            padding:2% 2%;
        }

        .Window_input>.input>.form_input>select{
            margin-bottom:10%;
            font-size:125%;
            border:1px solid black;
            border-radius:5px;
            padding:2% 2%;
        }

        .esportazione{
            margin-top:2%;
            margin-bottom:5%;
        }

        /*controlla larghezza schermo, quando è troppo poca
        fami mettere tabelle guadagni e consumi in colonna,
        cambiando orientamento del flex di .container_tabelle*/
    </style>
</head>
<body onbeforeunload="saveScrollPosition()">
    <div class="titolo"><h1>Budget</h1></div>
    <div class="navBar" id="navBar"><!--bottoni in orizzontale per muoversi tra le varie parti-->
        <div class="cntnr">
            <button onclick="getElementById('resoconti').scrollIntoView({ behavior: 'smooth' });">resoconti</button>    
        </div>
        <div class="cntnr" onclick="getElementById('resoconti').scrollIntoView({ behavior: 'smooth' });">
            <button>cronologia</button>    
        </div>
        <div class="cntnr" onclick="getElementById('esporta').scrollIntoView({ behavior: 'smooth' });">
            <button>esporta</button>    
        </div>
    </div>

    <div class="autoUpdate">
        <div class="autoUpdate_box">
            <div class="titolo"><h1>Aggiorna conto</h1></div>
                <form name="autoUpdate_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                    <div class="chips"> 
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 500);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,500) ?>" name="chip500">
                        </div>
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 100);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,100) ?>" name="chip100">
                        </div>
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 50);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,50) ?>" name="chip50">
                        </div>
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 25);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,25) ?>" name="chip25">
                        </div>
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 10);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,10) ?>" name="chip10">
                        </div>
                        
                        <div class="chip">
                            <div class="fish">
                                <?php
                                    echo generateChips($connessione, 5);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,5) ?>" name="chip5">
                        </div>

                        <div class="chip">
                            <div class="fish"> <!--funzione generatechips(value)-->
                                <?php
                                    echo generateChips($connessione, 1);
                                ?>
                            </div>
                            <input type="number" value="<?php echo getChips($connessione,1) ?>" name="chip1">
                        </div>
                        <input type="submit" name="autoUpdate_submit" style="display:none;" id="chips_submit">
                    </div>
                    <input class="totale" type="number" name="totale" step="0.01" min="0" placeholder="0.00" value="<?php echo isset($_POST['totale']) ? htmlspecialchars($_POST['totale']) : number_format(getTotale($connessione), 2, '.', ''); ?>"> <!--get totale-->
                </form>
                <button onclick="document.getElementById('chips_submit').click();">aggiorna</button>
            </div>
        </div>
    </div>


    <div class="resoconti" id="resoconti">
        <!--spesi e guadagnati questo mese-->
        <!--in cosa li hai spesi-->
        <!--paragone guadagni e spesi questo mese con la media-->
        <!--paragone spese per categorie mensile con la media-->

        <div class="resoconto_mensile">
            <div class="titolo"><h1>Mensile</h1></div>
            <div class="mensile_tabelle">
                <div class="mensileTb">
                    <table>
                        <tr style="background-color:lightgray;">
                            <th>guadagnato</th>
                            <th>spese</th>
                            <th>diff.</th>
                        </tr>
                        <!--prima dati poi seconda riga percentuali-->

                        <tr class="trDefault">
                            <td><?php echo number_format(getGuadagnatoMonth($connessione),2)."€"; ?></td>
                            <td><?php echo number_format(getSpesoMonth($connessione),2)."€"; ?></td>
                            <?php
                                $color="";
                                if(getDiffMonth($connessione)<0){
                                    $color="#F71735"; // negativo
                                }else{
                                    $color="#93FF96";
                                }
                            ?>
                            <td style="background-color:<?php echo $color ?>;">
                                <?php echo number_format(getDiffMonth($connessione),2)."€"; ?>
                            </td>
                        </tr>

                        <tr class="trDefault">
                            <td><?php echo number_format(getGuadagnatoMonthPerc($connessione),2)."%" ?></td>
                            <td><?php echo number_format(getSpesoMonthPerc($connessione),2)."%" ?></td>
                            <td style="background-color:<?php echo $color ?>;">
                                <?php echo number_format(getDifMonthPerc($connessione),2)."%"; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="mensileTb">
                    <table>
                        <tr style="background-color:lightgray;">
                            <th>non neces.</th>
                            <th>neces.</th>
                            <th>invest.</th>
                        </tr>

                        <tr class="trDefault">
                           <td><?php echo number_format(getTotGradoMonth($connessione,0) ,2)."€"; /*non necessaria*/?></td>
                           <td><?php echo number_format(getTotGradoMonth($connessione,1),2)."€"; /*necessaria*/?></td>
                           <td><?php echo number_format(getTotGradoMonth($connessione,2),2)."€"; /*investimento*/?></td> 
                        </tr>

                        <tr class="trDefault">
                           <td><?php echo number_format(getTotGradoMonthPerc($connessione,0),2)."%" ?></td>
                           <td><?php echo number_format(getTotGradoMonthPerc($connessione,1),2)."%" ?></td>
                           <td><?php echo number_format(getTotGradoMonthPerc($connessione,2),2)."%" ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="resoconto_media">
            <div class="titolo"><h1>Paragone</h1></div>
            <div class="media_tabelle">
                <div class="mediaTb">
                    <table>
                        <tr style="background-color:lightgray;">
                            <th></th>
                            <th>guadagno</th>
                            <th>spesa</th>
                            <th>diff.</th>
                        </tr>
                        <tr class="trDefault">
                            <th style="background-color:lightgray;">mese</th>
                            <td><?php echo number_format(getGuadagnatoMonthPerc($connessione),2)."%" ?></td>
                            <td><?php echo number_format(getSpesoMonthPerc($connessione),2)."%" ?></td>
                            <td style="background-color:<?php echo $color ?>;">
                                <?php echo number_format(getDifMonthPerc($connessione),2)."%"; ?>
                            </td>
                        </tr>
                        <?php
                            $color="";
                            if(getMediaDifAllMonthPerc($connessione)<0){
                                $color="#F71735"; // negativo
                            }else{
                                $color="#93FF96";
                            }
                        ?>
                        <tr class="trDefault">
                            <th style="background-color:lightgray;">media</th>
                            <td><?php echo number_format(getMediaGuadagnoAllMonthPerc($connessione),2)."%" ?></td>
                            <td><?php echo number_format(getMediaSpesaAllMonthPerc($connessione),2)."%" ?></td>
                            <td style="background-color:<?php echo $color; ?>"><?php echo number_format(getMediaDifAllMonthPerc($connessione),2)."%" ?></td>
                        </tr>


                        <tr class="trDefault">
                            <td style="background-color:lightgray;"></td>
                            <td><?php echo number_format(getMediaGuadagnoAllMonth($connessione),2)."€" ?></td>
                            <td><?php echo number_format(getMediaSpesaAllMonth($connessione),2)."€" ?></td>
                            <td style="background-color:<?php echo $color; ?>"><?php echo number_format(getMediaDifAllMonth($connessione),2)."€" ?></td>
                        </tr>
                    </table>
                </div>
                <div class="mediaTb">
                    <table>
                        <tr style="background-color:lightgray;">
                            <th></th>
                            <th>non neces.</th>
                            <th>neces.</th>
                            <th>invest.</th>
                        </tr>
                        <tr class="trDefault">
                            <th style="background-color:lightgray;">mese</th>
                            <td><?php echo number_format(getTotGradoMonthPerc($connessione,0),2)."%" ?></td>
                            <td><?php echo number_format(getTotGradoMonthPerc($connessione,1),2)."%" ?></td>
                            <td><?php echo number_format(getTotGradoMonthPerc($connessione,2),2)."%" ?></td>
                        </tr>
                        <tr class="trDefault">
                            <th style="background-color:lightgray;">media</th>
                            <td><?php echo number_format(getTotGradoAllPerc($connessione,0),2)."%"; ?></td>
                            <td><?php echo number_format(getTotGradoAllPerc($connessione,1),2)."%"; ?></td>
                            <td><?php echo number_format(getTotGradoAllPerc($connessione,2),2)."%"; ?></td>
                        </tr>
                        <tr class="trDefault">
                            <th style="background-color:lightgray;"></th>
                            <td><?php echo number_format(getTotGradoAll($connessione,0),2)."€"; ?></td>
                            <td><?php echo number_format(getTotGradoAll($connessione,1),2)."€"; ?></td>
                            <td><?php echo number_format(getTotGradoAll($connessione,2),2)."€"; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="container_cronologia" id="cronologia">
        <div class="titolo_cronologia">
            <h2>Cronologia</h2>
        </div>
        <div class="container_tabelle">
            <div class="container_guadagni">
                <div class="addRecord">
                    <button onclick="windowAggiungiGuadagno()">aggiugni</button>
                </div>
                    <table>
                        <tr style="background-color: lightgray">
                            <th>etichetta</th>
                            <th>valore</th>
                            <th>data</th>
                            <th>stato</th>
                        </tr>
                    <?php    
                        $stmt = $connessione->prepare("SELECT * FROM `guadagni` ORDER BY data DESC");
                        $stmt->execute([]);
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $color="";
                        foreach ($results as $result){
                            if($result['stato']==0){
                                $color="#93FF96"; //confermato
                            }else if($result['stato']==1){
                                $color="#FF8811"; //da confermare
                            }else if($result['stato']==2){
                                $color="#BADEFC"; //previsto
                            }else {
                                $color="#F71735"; //errore
                            }
                            
                            echo "<tr style='background-color:$color'>";

                            echo "<td>".$result['etichetta']."</td>";
                            echo "<td>".$result['valore']."€</td>";
                            echo "<td>".$result['data']."</td>";
                            echo "<td>
                                <form style='display:none;' action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                                    <input type='submit' id='change_guadagni" . $result['Id'] . "' name='change_guadagni' value='" . $result['Id'] . "'>
                                    <input type='submit' id='delete_guadagni" . $result['Id'] . "' name='delete_guadagni' value='" . $result['Id'] . "'>
                                </form>
                                <button onclick=\"document.getElementById('change_guadagni" . $result['Id'] . "').click()\">🔃</button>
                                <button onclick=\"document.getElementById('delete_guadagni" . $result['Id'] . "').click()\">🗑️</button>
                            </td>";
                            echo "</tr>";
                        }
                    ?>
                    </table>
            </div>
            <div class="container_spese">
            <div class="addRecord">
                    <button onclick="windowAggiungiSpesa()">aggiugni</button>
                    <button onclick="windowAggiungiCategoria()">categoria</button>
                </div>
                <table>
                    <tr style="background-color: lightgray">
                        <th>etichetta</th>
                        <th>categoria</th>
                        <th>valore</th>
                        <th>data</th>
                        <th>stato</th>
                    </tr>
                    <?php    
                        $stmt = $connessione->prepare("SELECT * FROM `spese` ORDER BY data DESC LIMIT 10");
                        $stmt->execute([]);
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $color="";
                        foreach ($results as $result){
                            if($result['stato']==0){
                                $color="#93FF96"; //confermato
                            }else if($result['stato']==1){
                                $color="#FF8811"; //da confermare
                            }else if($result['stato']==2){
                                $color="#BADEFC"; //previsto
                            }else {
                                $color="#F71735"; //errore
                            }


                            echo "<tr style='background-color:$color'>";
                            echo "<td>".$result['etichetta']."</td>";


                            if(getGradoCategoria($result['Id_categoria'],$connessione)==0){
                                $color="#EE6352"; //non necessaria
                            }else if(getGradoCategoria($result['Id_categoria'],$connessione)==1){
                                $color="#59CD90"; //necessaria
                            }else if(getGradoCategoria($result['Id_categoria'],$connessione)==2){
                                $color="#3FA7D6"; //investimento
                            }else {
                                $color="#F71735"; //errore
                            }
                            echo "<td style='background-color:".$color."'>".
                                getNameCategoria($result['Id_categoria'],$connessione).
                            "</td>";

                            echo "<td>".$result['valore']."€</td>";
                            echo "<td>".$result['data']."</td>";
                            echo "<td>
                                <form style='display:none;' action='" . $_SERVER['PHP_SELF'] . "' method='POST'>
                                    <input type='submit' id='change_spese" . $result['Id'] . "' name='change_spese' value='" . $result['Id'] . "'>
                                    <input type='submit' id='delete_spese" . $result['Id'] . "' name='delete_spese' value='" . $result['Id'] . "'>
                                </form>
                                <button onclick=\"document.getElementById('change_spese" . $result['Id'] . "').click()\">🔃</button>
                                <button onclick=\"document.getElementById('delete_guadagni" . $result['Id'] . "').click()\">🗑️</button>
                            </td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div class="esportazione" id="esporta">
        <div class="titolo"><h1>Esporta dati</h1></div>
        <!--scarica file pdf e csv con tutti i dati di transazione-->
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <button type="submit" name="export_guadagni">guadagni</button>
            <button type="submit" name="export_spese">spese</button>
            <button type="submit" name="export_categorie">categorie</button>
        </form>
    </div>

    <div class="Window_input aggiungi_guadagni">
        <div class="top">
            <div class="titolo_window">Aggiugni Guadagni</div>
            <div onclick=" windowAggiungiGuadagno()">
                <img src="IMG/x-button.png">
            </div>
        </div>
        <div class="input">
            <form class="form_input" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input placeholder="etichetta" name="etichetta" type="text">
                <input placeholder="valore" type="number" name="valore" step="0.01" pattern="[0-9]+([,\.][0-9]+)?">
                <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>"> 
                <select name="stato">
                        <option value="0">ricevuto</option>
                        <option value="1">attesa</option>
                        <option value="2">previsto</option>
                </select>
                <input id="aggiungi_guadagno" placeholder="input" type="submit" style="display:none;" name="aggiungi_guadagno">
            </form>
            <button onclick="document.getElementById('aggiungi_guadagno').click()">aggiungi</button>
        </div>
    </div>

    <div class="Window_input aggiungi_spese">
        <div class="top">
            <div class="titolo_window">Aggiugni Spese</div>
            <div onclick=" windowAggiungiSpesa()">
                <img src="IMG/x-button.png">
            </div>
        </div>
        <div class="input">
            <form class="form_input" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input placeholder="etichetta" name="etichetta" type="text">
                <select name="categoria">
                    <?php
                        $stmt = $connessione->prepare("SELECT * FROM `categorie`");
                        $stmt->execute([]);
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($results as $result){
                            echo "<option value=\"".$result['Id']."\">".$result['nome']."</option>";
                        }
                    ?>
                </select>
                <input placeholder="valore" type="number" name="valore" step="0.01" pattern="[0-9]+([,\.][0-9]+)?">
                <input placeholder="data" type="date" name="data" value="<?php echo date('Y-m-d'); ?>">
                <select name="stato">
                        <option value="0">ricevuto</option>
                        <option value="1">attesa</option>
                        <option value="2">previsto</option>
                </select>
                <input id="aggiungi_spesa" placeholder="input" type="submit" style="display:none;" name="aggiungi_spesa">
            </form>
            <button onclick="document.getElementById('aggiungi_spesa').click()">aggiungi</button>
        </div>
    </div>

    <div class="Window_input aggiungi_categorie">
        <div class="top">
            <div class="titolo_window">Aggiugni Categorie</div>
            <div onclick=" windowAggiungiCategoria()">
                <img src="IMG/x-button.png">
            </div>
        </div>
        <div class="input">
            <form class="form_input" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input placeholder="etichetta" type="text" name="etichetta">
                <select name="grado">
                    <option value="0">non necessario</option>
                    <option value="1">necessario</option>
                    <option value="2">investimento</option>
                </select>
                <input id="aggiungi_categoria" placeholder="input" type="submit" style="display:none;" name="aggiungi_categoria">
            </form>
            <button onclick="document.getElementById('aggiungi_categoria').click()">aggiungi</button>
        </div>
    </div>

    <script>
        

        window.addEventListener('scroll', function() {
            var navbar = document.getElementById('navBar');
            console.log(window.scrollY);
            // Verifica se la posizione verticale dello scroll è maggiore di un certo valore (ad esempio, 100 pixel)
            if (window.scrollY > 1) {
                navbar.style.position = 'fixed';
            } else {
                navbar.style.position = 'static';
            }
        });


        // Funzione per salvare la posizione corrente dello scroll
        function saveScrollPosition() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        }

        // Funzione per ripristinare la posizione dello scroll
        function restoreScrollPosition() {
            const scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
            }
        }

        // Registra l'evento "beforeunload" per salvare la posizione prima di ricaricare la pagina
        window.addEventListener('beforeunload', saveScrollPosition);

        // Ripristina la posizione dello scroll dopo il caricamento della pagina
        window.addEventListener('load', restoreScrollPosition);

        function windowAggiungiGuadagno() {
    var guadagniElements = document.getElementsByClassName('aggiungi_guadagni');
    var speseElements = document.getElementsByClassName('aggiungi_spese');
    var categorieElements = document.getElementsByClassName('aggiungi_categorie');

    for (var i = 0; i < guadagniElements.length; i++) {
        if (guadagniElements[i].style.top === '50%') {
            guadagniElements[i].style.top = '-200%';
        } else {
            guadagniElements[i].style.top = '50%';
        }
    }

    // Assicurati che le altre due classi siano impostate su -200%
    for (var j = 0; j < speseElements.length; j++) {
        speseElements[j].style.top = '-200%';
    }

    for (var k = 0; k < categorieElements.length; k++) {
        categorieElements[k].style.top = '-200%';
    }
}

function windowAggiungiSpesa() {
    var guadagniElements = document.getElementsByClassName('aggiungi_guadagni');
    var speseElements = document.getElementsByClassName('aggiungi_spese');
    var categorieElements = document.getElementsByClassName('aggiungi_categorie');

    for (var i = 0; i < speseElements.length; i++) {
        if (speseElements[i].style.top === '50%') {
            speseElements[i].style.top = '-200%';
        } else {
            speseElements[i].style.top = '50%';
        }
    }

    // Assicurati che le altre due classi siano impostate su -200%
    for (var j = 0; j < guadagniElements.length; j++) {
        guadagniElements[j].style.top = '-200%';
    }

    for (var k = 0; k < categorieElements.length; k++) {
        categorieElements[k].style.top = '-200%';
    }
}

function windowAggiungiCategoria() {
    var guadagniElements = document.getElementsByClassName('aggiungi_guadagni');
    var speseElements = document.getElementsByClassName('aggiungi_spese');
    var categorieElements = document.getElementsByClassName('aggiungi_categorie');

    for (var i = 0; i < categorieElements.length; i++) {
        if (categorieElements[i].style.top === '50%') {
            categorieElements[i].style.top = '-200%';
        } else {
            categorieElements[i].style.top = '50%';
        }
    }

    // Assicurati che le altre due classi siano impostate su -200%
    for (var j = 0; j < guadagniElements.length; j++) {
        guadagniElements[j].style.top = '-200%';
    }

    for (var k = 0; k < speseElements.length; k++) {
        speseElements[k].style.top = '-200%';
    }
}



    </script>
</body>
</html>

<?php
    function instauraConnessione(){
        try {
            // Stabilisco la connessione al database, inclusa la porta
            $connessione = new PDO(
                "mysql:host=".$GLOBALS['dbhost'].";port=".$GLOBALS['port'].";dbname=".$GLOBALS['dbname'],
                $GLOBALS['dbuser'],
                $GLOBALS['dbpassword']
            );
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connessione;
        } catch(PDOException $e) {
            echo "Errore di connessione: " . $e->getMessage();
            return false;
        }
    }




    function aggiornaRecordStato($Id, $table, $connessione){
        $stmt = $connessione->prepare("SELECT stato FROM $table WHERE Id=?");
        $stmt->execute([$Id]);
        $result = $stmt->fetchColumn(); // Ottieni il valore di "stato"
        
        $result++;
        if($result==3){
            $result=0;
        }


        $stmt = $connessione->prepare("UPDATE $table SET stato = $result WHERE Id = ?");
        $stmt->execute([$Id]);
    }

    function deleteRecordStato($Id, $table, $connessione){
        $stmt = $connessione->prepare("DELETE FROM $table WHERE Id=?");
        $stmt->execute([$Id]);
    }
    
    function getNameCategoria($Id,$connessione){
        $stmt = $connessione->prepare("SELECT nome FROM `categorie` WHERE Id=?");
        $stmt->execute([$Id]);
        $result = $stmt->fetchColumn(); // Ottieni il valore di "stato"
        return $result;
    }

    function getGradoCategoria($Id,$connessione){
        $stmt = $connessione->prepare("SELECT grado FROM `categorie` WHERE Id=?");
        $stmt->execute([$Id]);
        $result = $stmt->fetchColumn(); // Ottieni il valore di "stato"
        return $result;
    }

    function getGuadagnatoMonth($connessione){ //bug, restituisce non numeric
        $month = date('m'); // Ottiene il mese corrente nel formato "MM"
        $stmt = $connessione->prepare("SELECT SUM(valore) AS totale_guadagni FROM `guadagni` WHERE MONTH(data) = :month");
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['totale_guadagni']==null){
            return 0;
            //return number_format((float) 0, 2);
        }
        //return number_format($result['totale_guadagni'],2);
        return $result['totale_guadagni'];
    }   

    function getSpesoMonth($connessione){
        $month = date('m'); // Ottiene il mese corrente nel formato "MM"
        $stmt = $connessione->prepare("SELECT SUM(valore) AS totale_spese FROM `spese` WHERE (MONTH(data) = :month) AND stato = 0");
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['totale_spese']==null){
            return 0;
            //return number_format(0, 2);
        }
        return $result['totale_spese'];
        //return number_format($result['totale_spese'],2);
    }
    
    function getDiffMonth($connessione){
        /*if(is_numeric(getGuadagnatoMonth($connessione))){
            echo "ok1";
        }else{
            echo "no1";
        }
        if(is_numeric(getSpesoMonth($connessione))){
            echo "ok2";
        }else{
            echo "no2";
        }*/
        $result=getGuadagnatoMonth($connessione)-getSpesoMonth($connessione); //A non-numeric value encountered why??
        return $result;
    }
    
    function getGuadagnatoMonthPerc($connessione){
        $tot=getGuadagnatoMonth($connessione)+getSpesoMonth($connessione);
        $percentuale=0;
        if($tot!=0)
            $percentuale= (getGuadagnatoMonth($connessione)*100)/$tot;
        return $percentuale;
    }

    function getSpesoMonthPerc($connessione){
        $tot=getGuadagnatoMonth($connessione)+getSpesoMonth($connessione);
        $percentuale=0;
        if($tot!=0)
            $percentuale= (getSpesoMonth($connessione)*100)/$tot;
        return $percentuale;
    }

    function getDifMonthPerc($connessione){
        $result=getGuadagnatoMonthPerc($connessione)-getSpesoMonthPerc($connessione);   
        return $result;
    }

    function getTotGradoMonth($connessione,$grado){
        $month = date('m'); // Ottiene il mese corrente nel formato "MM"
        $stmt = $connessione->prepare("SELECT * FROM `spese` WHERE (MONTH(data) = :month) AND stato = 0");
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $somma=0;

        foreach($result as $results){
            if(getGradoByIdCategoria($connessione,$results['Id_categoria'])==$grado){
                $somma+=$results['valore'];
            }
        }

        return $somma;
    }

    function getGradoByIdCategoria($connessione, $Id){
        $stmt = $connessione->prepare("SELECT * FROM `categorie` WHERE Id = :id");
        $stmt->bindParam(':id', $Id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Assicurati di restituire il valore di "grado"
        return $result['grado'];
    }
    
    function getTotGradoMonthPerc($connessione,$grado){
        $result=0;
        if(getSpesoMonth($connessione)!=0){
            $result = (getTotGradoMonth($connessione,$grado)*100)/getSpesoMonth($connessione);
        }else{
            $result = 0;
        }
        return $result;
    }

    function getMediaGuadagnoAllMonth($connessione) {
        // Ottieni la data corrente
        $dataCorrente = new DateTime();
        
        // Estrai il mese e l'anno corrente
        $meseCorrente = $dataCorrente->format('m');
        $annoCorrente = $dataCorrente->format('Y');
    
        // Query per sommare i valori, escludendo i record del mese corrente e filtrando per stato = 0
        $query = "SELECT SUM(valore) AS totale_somma 
                  FROM guadagni 
                  WHERE (YEAR(data) != :annoCorrente OR MONTH(data) != :meseCorrente) 
                  AND stato = 0";
        
        // Esegui la query
        $stmt = $connessione->prepare($query);
        $stmt->bindParam(':annoCorrente', $annoCorrente, PDO::PARAM_INT);
        $stmt->bindParam(':meseCorrente', $meseCorrente, PDO::PARAM_INT);
        $stmt->execute();
        
        // Estrai il risultato
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calcola la media dividendo la somma per il numero di mesi considerati
        return $result['totale_somma'] / totMonthGuadagno($connessione);
    }
    

    function getMediaSpesaAllMonth($connessione) {
        // Ottieni la data corrente
        $dataCorrente = new DateTime();
        
        // Estrai il mese e l'anno corrente
        $meseCorrente = $dataCorrente->format('m');
        $annoCorrente = $dataCorrente->format('Y');
    
        // Query per sommare i valori, escludendo i record del mese corrente
        $query = "SELECT SUM(valore) AS totale_somma FROM spese WHERE (YEAR(data) != :annoCorrente OR MONTH(data) != :meseCorrente) AND stato = 0";
        
        // Esegui la query
        $stmt = $connessione->prepare($query);
        $stmt->bindParam(':annoCorrente', $annoCorrente, PDO::PARAM_INT);
        $stmt->bindParam(':meseCorrente', $meseCorrente, PDO::PARAM_INT);
        $stmt->execute();
        
        // Estrai il risultato
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Restituisci la somma escludendo il mese corrente
        return $result['totale_somma']/totMonthSpesa($connessione);
    }
    

    function totMonthSpesa($connessione){
        $stmt = $connessione->prepare("SELECT MIN(data) AS min_data FROM spese");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Estrai la data minima
        $minData = new DateTime($result['min_data']);
        
        // Ottieni la data del mese scorso
        $meseScorso = new DateTime('last month');
        
        // Calcola la differenza in mesi
        $differenza = $minData->diff($meseScorso);
        
        // Restituisci la differenza in mesi come un numero intero
        return $differenza->y * 12 + $differenza->m +2;
    }

    function totMonthGuadagno($connessione){
        $stmt = $connessione->prepare("SELECT MIN(data) AS min_data FROM guadagni");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Estrai la data minima
        $minData = new DateTime($result['min_data']);
        
        // Ottieni la data del mese scorso
        $meseScorso = new DateTime('last month');
        
        // Calcola la differenza in mesi
        $differenza = $minData->diff($meseScorso);
        
        // Restituisci la differenza in mesi come un numero intero
        return $differenza->y * 12 + $differenza->m +2;
    }
    
    
    function getMediaDifAllMonth($connessione){
        $result = getMediaGuadagnoAllMonth($connessione)-getMediaSpesaAllMonth($connessione);
        return $result;
    }

    function getMediaGuadagnoAllMonthPerc($connessione){
        $result=0;
        $tot= getMediaGuadagnoAllMonth($connessione)+getMediaSpesaAllMonth($connessione);
        if($tot!=0)
            $result = (getMediaGuadagnoAllMonth($connessione)*100)/$tot;
        
        return $result;
    }

    function getMediaSpesaAllMonthPerc($connessione){
        $result=0;
        $tot= getMediaGuadagnoAllMonth($connessione)+getMediaSpesaAllMonth($connessione);
        if($tot!=0)
            $result = (getMediaSpesaAllMonth($connessione)*100)/$tot;
        return $result;
    }
    
    function getMediaDifAllMonthPerc($connessione){
        return getMediaGuadagnoAllMonthPerc($connessione)-getMediaSpesaAllMonthPerc($connessione);
    }



    /*
        trovare mese prima spesa assoluta dell' anno, variabile totale mesi (prima spesa anno - mese scorso)
        somma valori per grado e per mese di tutti i mesi dell' anno escluso il corrente
        dividere somma medie per mese con totale mesi da prima registrazione
        ritornare il valore
    */

    function getTotGradoAll($connessione, $grado) {
        $dataCorrente = new DateTime();
        $meseCorrente = $dataCorrente->format('m');
        $annoCorrente = $dataCorrente->format('Y');
    
        $stmt = $connessione->prepare("SELECT * FROM `spese` WHERE (YEAR(data) != :annoCorrente) OR (YEAR(data) = :annoCorrente AND MONTH(data) != :meseCorrente)");
        $stmt->bindParam(':annoCorrente', $annoCorrente, PDO::PARAM_STR);
        $stmt->bindParam(':meseCorrente', $meseCorrente, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $somma = 0;
    
        foreach ($result as $results) {
            if (getGradoByIdCategoria($connessione, $results['Id_categoria']) == $grado) {
                $somma += $results['valore'];
            }
        }
    
        return $somma/totMonthSpesa($connessione);
    }

    function getTotGradoAllPerc($connessione,$grado){
        $tot=0;
        for($i=0;$i<3;$i++)
            $tot+=getTotGradoAll($connessione,$i);

        $perc=0;
        if($tot!=0){
            $perc=(getTotGradoAll($connessione,$grado)*100)/$tot;
        }
        return $perc;

    }

    function inserisci_guadagno($connessione,$etichetta,$valore,$data,$stato){
        $stmt = $connessione->prepare("INSERT INTO `guadagni`(`etichetta`, `valore`, `data`, `stato`) VALUES (?,?,?,?)");
        $stmt->execute([$etichetta,$valore,$data,$stato]);
        return true;
    }

    function inserisci_spesa($connessione,$etichetta,$categoria,$valore,$data,$stato){
        $stmt = $connessione->prepare("INSERT INTO `spese`(`etichetta`, `Id_categoria`, `valore`, `data`, `stato`) VALUES (?,?,?,?,?)");
        $stmt->execute([$etichetta,$categoria,$valore,$data,$stato]);
        return true;
    }

    function inserisci_categoria($connessione,$etichetta,$grado){
        $stmt = $connessione->prepare("INSERT INTO `categorie`(`nome`, `grado`) VALUES (?,?)");
        $stmt->execute([$etichetta,$grado]);
        return true;
    }

    function downloadGuadagniCSV($connessione) {
        // Query per ottenere tutti i dati dalla tabella "guadagni"
        $query = "SELECT Id, etichetta, valore, data, stato FROM guadagni";
        $stmt = $connessione->prepare($query);
        $stmt->execute();
    
        // Verifica se ci sono risultati
        if ($stmt->rowCount() > 0) {
            // Genera il file CSV
            $filename = 'guadagni.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
    
            $output = fopen('php://output', 'w');
    
            // Intestazione CSV
            fputcsv($output, array('Id', 'etichetta', 'valore', 'data', 'stato'));
    
            // Dati CSV
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
    
            fclose($output);
            
            // Interrompi l'esecuzione del codice per evitare ulteriori output indesiderati
            exit;
        } else {
            // Nessun dato trovato
            return false;
        }
    }


    function downloadSpeseCSV($connessione) {
        // Query per ottenere tutti i dati dalla tabella "spese"
        $query = "SELECT Id, etichetta, valore, data, stato, Id_categoria FROM spese";
        $stmt = $connessione->prepare($query);
        $stmt->execute();
    
        // Verifica se ci sono risultati
        if ($stmt->rowCount() > 0) {
            // Genera il file CSV
            $filename = 'spese.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
    
            $output = fopen('php://output', 'w');
    
            // Intestazione CSV
            fputcsv($output, array('Id', 'etichetta', 'valore', 'data', 'stato', 'Id_categoria'));
    
            // Dati CSV
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
    
            fclose($output);
    
            // Interrompi l'esecuzione del codice per evitare ulteriori output indesiderati
            exit;
        } else {
            // Nessun dato trovato
            return false;
        }
    }

    function downloadCategorieCSV($connessione) {
        // Query per ottenere tutti i dati dalla tabella "categorie"
        $query = "SELECT Id, nome, grado FROM categorie";
        $stmt = $connessione->prepare($query);
        $stmt->execute();

        // Verifica se ci sono risultati
        if ($stmt->rowCount() > 0) {
            // Genera il file CSV
            $filename = 'categorie.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');

            // Intestazione CSV
            fputcsv($output, array('Id', 'nome', 'grado'));

            // Dati CSV
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }

            fclose($output);

            // Interrompi l'esecuzione del codice per evitare ulteriori output indesiderati
            exit;
        } else {
            // Nessun dato trovato
            return false;
        }
    }

    function getTotale($connessione){
        $stmt = $connessione->prepare("SELECT SUM(valore) AS totale_guadagni FROM `guadagni` WHERE `stato`=0");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_guadagni=$result['totale_guadagni'];

        $stmt = $connessione->prepare("SELECT SUM(valore) AS totale_spese FROM `spese` WHERE `stato`=0");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totale_spese=$result['totale_spese'];

        $totale=$totale_guadagni-$totale_spese;
        return $totale;
    }

    function getChips($connessione,$value){
        $totale=number_format(getTotale($connessione), 2, '.', '');
        $ctr=0;

        while($totale>=500){
            $totale-=500;
            $ctr++;
        }
        if($value==500)
            return $ctr;

        $ctr=0;
        while($totale>=100){
            $totale-=100;
            $ctr++;
        }
        if($value==100)
            return $ctr;

        $ctr=0;
        while($totale>=50){
            $totale-=50;
            $ctr++;
        }
        if($value==50)
            return $ctr;

        $ctr=0;
        while($totale>=25){
            $totale-=25;
            $ctr++;
        }
        if($value==25)
            return $ctr;

        $ctr=0;
        while($totale>=10){
            $totale-=10;
            $ctr++;
        }
        if($value==10)
            return $ctr;

        $ctr=0;
        while($totale>=5){
            $totale-=5;
            $ctr++;
        }
        if($value==5)
            return $ctr;

        $ctr=0;
        while($totale>=1){
            $totale-=1;
            $ctr++;
        }
        return $ctr;
    }

    function generateChips($connessione,$value){
        $ctr=getChips($connessione,$value);
        $result="";

        if($ctr==0)
            return '<img src="IMG/0.png" class="chip_0" style="z-index:1">';
        else{    

            for($i=1;$i<$ctr && $ctr<=5;$i++){
                $result .= "<img src='IMG/{$value}.png' class='chip_value{$value}_num{$ctr}' style='z-index:".($ctr-$i+1)."; margin-bottom:-70%;'>";
            }

            $result .= "<img src='IMG/{$value}.png' class='chip_default' style='z-index:1;'>";
        }
        return $result;
    }

    function calcolaFish($v500,$v100,$v50,$v25,$v10,$v5,$v1){
        $tot=0;
        $tot=$v500*500+$v100*100+$v50*50+$v25*25+$v10*10+$v5*5+$v1;
        return $tot;
    }
?>
