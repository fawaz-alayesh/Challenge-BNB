<?php
// Je hebt een database nodig om dit bestand te gebruiken....
include "database.php";
if (!isset($db_conn)) { //deze if-statement checked of er een database-object aanwezig is. Kun je laten staan.
    return;
}

$database_gegevens = null;
$poolIsChecked = false;
$bathIsChecked = false;
$bbqIsChecked = false;
$wifiIsChecked = false;
$fireplaceIsChecked = false;
$dishwasherIsChecked = false;
$bikeIsChecked = false;

$sqlQuery = "SELECT * FROM homes"; //Selecteer alle huisjes uit de database

if (isset($_GET['filter_submit'])) {

    if ($_GET['faciliteiten'] == "ligbad") { // Als ligbad is geselecteerd filter dan de zoekresultaten
        $bathIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes`WHERE bath_present=1"; // query die zoekt of er een BAD aanwezig is.
    }

    if ($_GET['faciliteiten'] == "zwembad") {
        $poolIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes` WHERE pool_present=1"; // query die zoekt of er een ZWEMBAD aanwezig is.
    }
    if ($_GET['faciliteiten'] == "bbq") {
        $bbqIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes` WHERE bbq_present=1"; // query die zoekt of er een bbq aanwezig is.
    }
    if ($_GET['faciliteiten'] == "wifi") {
        $wifiIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes`WHERE wifi_present=1"; // query die zoekt of er een wifi aanwezig is.
    }
    if ($_GET['faciliteiten'] == "fireplace_present") {
        $fireplaceIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes`WHERE fireplace_present=1"; // query die zoekt of er een fireplace aanwezig is.
    }
    if ($_GET['faciliteiten'] == "dishwasher_present") {
        $dishwasherIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes`WHERE dishwasher_present=1"; // query die zoekt of er een dishwasher aanwezig is.
    }
    if ($_GET['faciliteiten'] == "bike_rental") {
        $bikeIsChecked = true;

        $sqlQuery = "SELECT * FROM `homes`WHERE bike_rental=1"; // query die zoekt of er een bike aanwezig is.
    }
}


if (is_object($db_conn->query($sqlQuery))) { //deze if-statement controleert of een sql-query correct geschreven is en dus data ophaalt uit de DB
    $database_gegevens = $db_conn->query($sqlQuery)->fetchAll(PDO::FETCH_ASSOC); //deze code laten staan
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="https://kit.fontawesome.com/e28345425a.js" crossorigin="anonymous"></script>
    <link href="style.css" rel="stylesheet">
    
    <link rel = "icon" href =  
     "images/FDlogo.png" 
     type = "image/x-icon" > 
</head>

<body>

    <header>
    <section>
            
        <div class="header">
        <div class="load">
        </div>
            <img src="images/FDlogo.png" alt="" class="logo">
            <h1>FD Rental</h1>
           <h2>Een plek waar je thuis voelt</h2>
           <p>5 sterren service</p>
           <span>
           <i class="fas fa-star"></i>
           <i class="fas fa-star"></i>
           <i class="fas fa-star"></i>
           <i class="fas fa-star"></i>
           <i class="fas fa-star"></i>
           </span>
        </div>
    </section>
   
    </header>
       <audio class="audio"  loop autoplay>
      <source src="headermusic.mp3" type="audio/mpeg">
      </audio>
    <main>
    
        <div class="left">
           
            <form action="" method="POST">
            <div class="book">
                <h3>Reservering maken</h3>
                <div class="form-control">
                    <label for="aantal_personen">Vakantiehuis</label>
                    <select name="gekozen_huis" id="gekozen_huis">
                        <option value="1">IJmuiden Cottage</option>
                        <option value="2">Assen Bungalow</option>
                        <option value="3">Espelo Entree</option>
                        <option value="4">Weustenrade Woning</option>
                    </select>
                </div>
               
                <div class="form-control">
                    <label for="aantal_personen">Aantal personen</label>
                    <input type="number" name="aantal_personen" id="aantal_personen" value="1">
                </div>
                <div class="form-control">
                    <label for="aantal_dagen">Aantal dagen</label>
                    <input type="number" name="aantal_dagen" id="aantal_dagen" value="1">
                </div>
                <div class="form-control">
                    <h5>Beddengoed</h5>
                    <label for="beddengoed_ja">Ja</label>
                    <input type="radio" id="beddengoed_ja" name="beddengoed"  value="ja" >
                    <label for="beddengoed_nee">Nee</label>
                    <input type="radio" id="beddengoed_nee" name="beddengoed" value="nee">
                </div>
                <input  class="submit" type="submit" value="Reserveer huis" name="submit"></input>
            </div>
            <?php  if (isset($database_gegevens) && $database_gegevens != null) : ?>
                <?php foreach ($database_gegevens as $huisje) : ?>  
                 <?php
                 $gekozen=[1 => 55.00 , 2 => 150.00 , 3 => 300.00 ,4 => 75.00]; ///// array met id => waarde(prijs)
                 $beddenprijs=[1 => 10.00, 2 => 0.00, 3 => 0.00, 4 => 0.00]; ////// array met gekozenvalue => beddenprijs
                 if(isset($_POST['aantal_dagen']) && $_POST['aantal_personen'] != null){
                    $aantal_dagen = $_POST['aantal_dagen'];
                    $aantal_personen = $_POST['aantal_personen'];
                    $gekozenhuis = $_POST['gekozen_huis'] ;
                    $nummerhuis = $gekozen[$gekozenhuis]; 
                    $bedden= $beddenprijs[$gekozenhuis];  
                    
                    }

                ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="currentBooking">
                <div class="bookedHome"></div>
                <div class="totalPriceBlock">Totale prijs &euro;<span class="totalPrice"><?php if(isset($_POST['submit'])){
                     if(isset($_POST['beddengoed']) && $_POST['beddengoed'] == "ja"){
                        echo $all=($nummerhuis * $aantal_dagen) * $aantal_personen + ($bedden*$aantal_personen);
                     } elseif(isset($_POST['beddengoed']) && $_POST['beddengoed'] == "nee"){
                         echo $totaal=($nummerhuis * $aantal_dagen) * $aantal_personen ;}
                 
                }
                else{
                    echo"";
                }  ?></span></div>
            </div>
        </div>
        </form>
        <div class="right">
            <div class="filter-box">
                <form class="filter-form">
                    <div class="form-control">
                        
                    </div>
                    <div class="form-control">
                        <label for="ligbad"><i class="fas fa-bath"></i>Ligbad</label>
                        <input type="radio" id="ligbad" name="faciliteiten" value="ligbad" checked <?php if ($bathIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="zwembad"><i class="fas fa-swimming-pool"></i>Zwembad</label>
                        <input type="radio" id="zwembad" name="faciliteiten" value="zwembad" <?php if ($poolIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="bbq"><i class="fas fa-fire"></i>BBQ</label>
                        <input type="radio" id="bbq" name="faciliteiten" value="bbq" <?php if ($bbqIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="wifi"><i class="fas fa-wifi"></i>Wifi</label>
                        <input type="radio" id="wifi" name="faciliteiten" value="wifi" <?php if ($wifiIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="fireplace"><i class="fas fa-fire-alt"></i>Vuurplaats</label>
                        <input type="radio" id="fireplace" name="faciliteiten" value="fireplace" <?php if ($fireplaceIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="dishwasher"><i class="fas fa-tint"></i>Vaatwasser</label>
                        <input type="radio" id="dishwasher" name="faciliteiten" value="dishwasher" <?php if ($dishwasherIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="bike"><i class="fas fa-bicycle"></i>Fiets</label>
                        <input type="radio" id="bike" name="faciliteiten" value="bike"  <?php if ($bikeIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="buttons">
                    <button class="filter" type="submit" name="filter_submit">Filter</button>
                    <a id="reset" href="index.php">Reset Filters</a>
                    </div>
                   
                </form>
                <div class="homes-box">
                    <?php if (isset($database_gegevens) && $database_gegevens != null) : ?>
                        <?php foreach ($database_gegevens as $huisje) : ?>
                            <div class="homesinfo">
                            <h2>
                                <?php echo $huisje['name']; ?>
                            </h2>
                            <div>
                                 <img class="images" src="images/<?php echo $huisje['image'];?>" style="width: 473.997; height: 315px;">
                            </div>
                            <p>
                                <?php echo $huisje['description']; ?>
                            </p>
                            <div class="kenmerken">
                                <h4>Kenmerken</h4>
                                <ul>
                                   <li>
                                   <?php echo"Price per person: €"; echo $huisje['price_p_p_p_n'];?>
                                   </li>
                                   <li>
                                    <?php echo"Bed-sheets price: €"; echo $huisje['price_bed_sheets']; ?>
                                   </li>
                                    <?php
                                    if ($huisje['bath_present'] ==  1) {
                                        echo "<li>Er is ligbad!</li>";
                                    }
                                    ?>


                                    <?php
                                    if ($huisje['pool_present'] ==  1) {
                                        echo "<li>Er is zwembad!</li>";
                                    }
                                    ?>

                                </ul>
                            </div>

                            </div>
                            

                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div id="mapid"></div>
                   

            </div>
        </div>
       
       
    </main>
    <footer>
        <div></div>
        <div class="copyright">copyright FD Rental</div>
        <div></div>

    </footer>
    <script src="js/map_init.js"></script>
    <script>
        // De verschillende markers moeten geplaatst worden. Vul de longitudes en latitudes uit de database hierin
        var coordinates = [ 
            [52.44902,4.61001], 
            [52.99864,6.64928],
            [52.30340,6.36800],
            [50.89720,5.90979]

        ];
       
         
        var bubbleTexts = [
           "IJmuiden Cottage" ,
           "Assen Bungalow",
           "Espelo Entree",
           "Weustenrade Woning"

        ];

    </script>
    <script src="js/place_markers.js"></script>
</body>

</html>