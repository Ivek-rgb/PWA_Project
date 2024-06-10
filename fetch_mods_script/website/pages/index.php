<?php 
    session_start(); 
    require_once '../components/customized_header.php';
    require_once '../components/mod_div.php';
    require_once '../../db/database_functions/db_functions.php';
    $isAdmin = $_SESSION["user_info"]["role"] == 1; 
    $accName = $_SESSION["user_info"]["username"]; 
    $_SESSION["is_admin"] = $isAdmin; 
    $helloMessage = "Welcome " . ($isAdmin ? "admin" : "user") . ", $accName";  

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <script>
        var sunIcon = 'fas fa-sun fa-lg';
        var moonIcon = 'fas fa-moon fa-lg';  
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/mod_div.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <title>Home</title>

    <style>
        .mainContainer{
            width: 65%;
        }

        .statusClock{
            position: relative;
            background-color: rgb(51, 102, 153, 0.5);
            padding: 1px 20px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden; 
        }

        #clockImage{
            position: absolute;
            width: 100%;
            left: 0;
            opacity: 0.5;
        }

        .clkText{
            color: white;
            border-right: 5px solid white;
            width: 100px;
            z-index: 20;
        }

        #statusIcon{
            color: white;
            font-size: 40px;
            z-index: 20;
        }

        .modCollectionContainer{
            width: 100%;
            display: flex;  
            justify-content: space-between;
            flex-wrap: wrap;
        }

        @media only screen and (max-width: 800px){

            .mainContainer{
                width: 100%;
            }

            .modCollectionContainer{
                width: 97%;
            }

        }

        @media only screen and (max-width: 1200px){
    
            .modDiv{
                width: 45%;
            }

        }
    </style>
</head>
<body>
   
    <?php 
        require_once "../components/header.php"; 
    ?>

    <div class="mainContainer">
        <div class="userInformation">
            
            <div class="statusClock">
                <img id="clockImage" src="../img/clock-clouds.jpg">
                <h1 class="clkText">00:00</h1>
                <i id="statusIcon"></i>
            </div>

            <?= returnCustomHeader($helloMessage, "h2", false) ?>

            <?= returnCustomHeader("Farming simulator 22 - New mods", "h2", true) ?>

            <div class="modCollectionContainer">

                <?php 

                    $modItems = getBriefPartsFromModServer(0, 4, $connection, "Farming simulator 22", "mod_date_changed", FALSE); 
                                    
                    foreach($modItems as $modBoxInfo){
                        echo returnDeFangedModDiv($modBoxInfo); 
                    }

                ?>

            </div>

            <?= returnCustomHeader("Farming simulator 19 - New mods", "h2", true) ?>

            <div class="modCollectionContainer">

                <?php 

                    $modItems = getBriefPartsFromModServer(0, 4, $connection, "Farming simulator 19", "mod_date_changed", FALSE); 
                                    
                    foreach($modItems as $modBoxInfo){
                        echo returnDeFangedModDiv($modBoxInfo); 
                    }

                ?>

            </div>

            <?= returnCustomHeader("Farming simulator 17 - New mods", "h2", true) ?>
            <div class="modCollectionContainer">

                <?php 

                    $modItems = getBriefPartsFromModServer(0, 4, $connection, "Farming simulator 17", "mod_date_changed", FALSE); 
                                    
                    foreach($modItems as $modBoxInfo){
                        echo returnDeFangedModDiv($modBoxInfo); 
                    }

                ?>

            </div>
        </div>

        <?php 

        ?>

    </div>

    <script>

        var textObject = document.querySelector(".clkText");
        var iconPart = document.querySelector("#statusIcon");

        function setClock(){
            let currTime = new Date(); 
            if( currTime.getHours() > 4 && currTime.getHours() <= 17){
                iconPart.className = sunIcon; 
            }else iconPart.className = moonIcon; 
            textObject.textContent = (Math.floor(currTime.getHours() / 10)) + "" + (currTime.getHours() % 10) + ":" + (Math.floor(currTime.getMinutes() / 10)) +  (currTime.getMinutes() % 10); 
        }

        setClock(); 
        setInterval(setClock, 500);

    </script>

    <?php 
        require_once "../components/footer.php"; 
    ?>
</body>
</html>
