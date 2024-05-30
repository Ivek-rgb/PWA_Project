<?php

    require_once 'vendor/autoload.php'; 
    require_once 'webg_functions.php'; 
    require_once "./customized_header.php"; 
    use simplehtmldom\HtmlDocument;
    $httpClient = new \simplehtmldom\HtmlWeb();

    $modItems = []; 

    $collectionOfMods = 'https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2022&filter=latest&page='; 
    $indexPage = 0; 

    // clean up this later for production
    $i = 0;
    for(; $i < 5; $i++){
        $response = $httpClient->load($collectionOfMods . $i); 
        if($response === NULL) break; // failsafe for latter when we are in 'production' 
        $modDivs = $response->find('.mod-item');
        foreach($modDivs as $mod){
            array_push($modItems, $mod); 
        }   
    }

    $pageNumber = 0;
    if(isset($_GET["pageNum"]))
        $pageNumber = $_GET["pageNum"];

    // this will be later implemenetd through LIMIT in SQL database
    // how do i statically just calculate this once so it does not take mem with each new refresh to take load off server
    $numberOfItemsPerPage = 12; 
    $numberOfPages = ceil(count($modItems) / $numberOfItemsPerPage);
    $numberOfDipsplayPages = 3; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ModHub - FS22</title>
    <link rel="stylesheet" href="./style/footer.css">
    <link rel="stylesheet" href="./style/header.css">
    <link rel="stylesheet" href="./style/global.css">
    <style>
        .modCollectionContainer{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            flex-flow: wrap;
            width: 65%;
            margin: 10px 0;
        }

        .modDiv{
            display: flex;
            flex-direction: column;
            width: 22%;
            margin-bottom: 50px;
            justify-content: space-between;
        }

        .paginationDiv{
            display: flex;
            justify-content: space-around;
            width: 100%;
        }

        .paginationButton{
            background-color: transparent;
            width: 40px;
            height: 40px;
            border-radius: 0;
            
        }

        .bolderSelected{
            background-color: #E0E0E0;
            font-weight: bolder;
            cursor:default;
        }

        .unselectedPag{
            cursor: pointer;
        }

        .unselectedPag:hover{
            background-color: #202020;
            color: #81C332;
        }

        
        .paginationForm{
            width: 65%;
            display: flex;
            border: 1px solid #484848;
        }
        
        .middleCenter{
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .temporaryHeaderContainer{
            width: 65%;
        }

        .modDiv{
            background-color: #E6E4E3;
        }

        .modDiv input[type="submit"]{
            width: 100%;
            background-color: #81C332;
            color: white;
            font-weight: bolder;
            border: none;
            padding: 10px 0;
        }

        .modDiv input[type="submit"]:hover{
            cursor: pointer;
            background-color: greenyellow;
            color: #202020;
        }

        .innerPag{
            border: none;
            border-left: 1px solid #484848;
        }

        .outerPagL{
            border: none;
            border-right: 1px solid #484848;
        }

        .outerPagR{
            border: none;
            border-left: 1px solid #484848;
        }

        .lastInnerOne{
            border-right: 1px solid #484848;
        }

    </style>
</head>
<body>

    <?php 
        require_once "./header.php"; 
    ?>

    <div class="temporaryHeaderContainer">
        <?= returnCustomHeader("Mod collections", "h1", true); ?>    
    </div>

    <div class="modCollectionContainer">
        <?php 
            // this here is used to import dependencies
            
            //$response = $httpClient->load('https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2022&filter=latest&page=0');

            // here we actually get the container stuff

            // scraping the shite of the first documents
            //$divItself = new HtmlDocument();
            //$divItself->load($response->find('.mod-item', 0)->outertext); 

            // fetching attributes themselves
            //echo $divItself->find('img', 0)->getAttribute('src') . '';
            
            //outertext -- it's for displaying full DOM arhitecture of the shite

            // here we then do some test run's to see what's up currently 

            // we scrape 2022 version of Farming simulator mods 
            
            // here then we do some scraping buisness 
            $limit = (($pageNumber + 1) * $numberOfItemsPerPage) <= count($modItems) ? ($pageNumber + 1) * $numberOfItemsPerPage : count($modItems); 
            for($j = $pageNumber * $numberOfItemsPerPage; $j < $limit; $j++){
                echo returnDeFangedModDiv($modItems[$j]); 
                //echo ($pageNumber * $numberOfItemsPerPage) . " "; 
            }
        ?>
    </div>
    
    <?php
        $firstLink = FALSE;
        $lastLink = FALSE; 
        // kinda feels niggerlicious, fix this vooodo shit to run with session since it will be used anyways
        // we stick with GET equipment
        if($pageNumber <= 1){
            $starter = 0; 
            $ender = min($pageNumber + 3, $numberOfPages - 1); 
            $lastLink = ($pageNumber + 3 < $numberOfPages - 1); 
        }else if($pageNumber >= $numberOfPages - 3){
            $firstLink = ($numberOfPages - 4 > 0);
            $starter = max($numberOfPages - 4, 0); 
            $ender = $numberOfPages - 1; 
        }else{
            $starter = ($pageNumber - intval($numberOfDipsplayPages / 2) >= 0) ? $pageNumber - intval($numberOfDipsplayPages / 2) : 0;
            $ender = ($pageNumber + intval($numberOfDipsplayPages / 2) < $numberOfPages) ? $pageNumber + intval($numberOfDipsplayPages / 2) : $numberOfPages;  
            $firstLink = TRUE; 
            $lastLink = TRUE; 
        }
    ?>

    <!-- after that kinda add number that point to end and the beginning for easier navigation-->
    <div class="paginationDiv">
        <form action="fetch.php" method="GET" class="paginationForm">
            <?php if($pageNumber != 0): ?>
                <button name="pageNum" value="<?= $pageNumber - 1?>" class="paginationButton unselectedPag nearButton outerPagL">⮜</button>
            <?php endif; ?>
            <div class="middleCenter">
                <?php if($firstLink): ?>
                    <button name="pageNum" value="0" class="paginationButton unselectedPag nearButton innerPag">1...</button>    
                <?php endif; ?>
                <?php for($j = $starter; $j <= $ender; $j++): ?>
                    <button name="pageNum" value="<?=$j?>" class="paginationButton <?php if($pageNumber == $j) echo "bolderSelected"; else echo "unselectedPag"; ?> innerPag"><?=$j + 1?></button>
                <?php endfor;?>
                <?php if($lastLink): ?>
                    <button name="pageNum" value="<?= $numberOfPages - 1?>" class="paginationButton unselectedPag nearButton innerPag">...<?= $numberOfPages ?></button>    
                <?php endif; ?>
            </div>
            <?php if($pageNumber != $numberOfPages - 1): ?>
                <button name="pageNum" value="<?= $pageNumber + 1?>" class="paginationButton unselectedPag nearButton outerPagR">⮞</button>
            <?php endif; ?>
        </form>
    </div>

    <script>
        let arrOfObjects = [].slice.call(document.getElementsByClassName("innerPag"));
        arrOfObjects[arrOfObjects.length - 1].classList.add("lastInnerOne");
    </script>

    <?php 
        require_once "./footer.php"; 
    ?>

</body>
</html>