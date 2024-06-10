<?php
    session_start(); 
    require_once '../vendor/autoload.php'; 
    require_once '../components/mod_div.php'; 
    require_once '../components/customized_header.php'; 
    require_once '../../db/database_functions/db_functions.php'; 
    use simplehtmldom\HtmlDocument;
   
    if(!isset($_SESSION["user_info"]))
        $_SESSION["is_admin"] = false; 

    $connection = openConnection(); 

    $pageNumber = 0;
    if(isset($_GET["pageNum"]))
        $pageNumber = $_GET["pageNum"];

    $game = "Farming simulator 22"; 
    if(isset($_SESSION["game"]))
        $game = $_SESSION["game"];

    $totalNumberOfMods = getModCountFromServer(NULL, $game);
    $numberOfItemsPerPage = 12; 
    $numberOfPages = ceil($totalNumberOfMods / $numberOfItemsPerPage);
    $numberOfDipsplayPages = 3; 

    $randomFeaturedModInfo = getRandomFeaturedMod($connection, $game); 
    $selectedModImages = explode(" [:|:] ", $randomFeaturedModInfo["mod_imgs"]); 
    $selectedImage = $selectedModImages[rand(0, count($selectedModImages) - 1)]; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ModHub - <?= $game ?></title>
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/fetch.css">
    <link rel="stylesheet" href="../style/mod_div.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .featuredModsAndFilters{
            width: 100%;
            margin: 0px 0px 0px 0px;
            position: relative;
            height: 400px;
            background-image: url(<?= $selectedImage ?>);
            background-size: cover;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body>

    <?php 
        require_once "../components/header.php"; 
    ?>

    <div class="mainDataContainer">

        <?= returnCustomHeader("Mod collections", "h1", true); ?>    
        <div class="featuredModsAndFilters">

            <div class="featuredModBanner">
                <div class="InfoAndSearch">
                    <div class="infoDiv">
                        <h4 style="color:#81C332;">FEATURED MOD</h4>
                        <h2><?= $randomFeaturedModInfo["mod_name"] ?></h2>
                        <p style="color: #888888;"><?= $randomFeaturedModInfo["mod_game"] ?></p>
                        <p style="color: #888888;">By: <?= $randomFeaturedModInfo["mod_author"] ?></p>
                    </div>
                    <form action="../pages/mod_desc.php" method="GET">
                        <input name="modId" type="hidden" value="<?= $randomFeaturedModInfo["id"] ?>">
                        <input style="margin: 0; " class="moreInfoBtn optionBtn" type="submit" value="MORE INFO">
                    </form>
                    <form action="./fetch.php?pageNum=0" method="GET" class="searchForm">
                        <?php if(isset($_GET["filter"])): ?>
                            <input type="hidden" name="filter" value="<?php 
                            echo $_GET["filter"];  
                        ?>">
                        <?php endif; ?>
                        <div class="modSearch">
                            <input type="text" name="search" placeholder="Search Mods">
                            <i class="fa fa-solid fa-search fa-sm fa-fw"></i>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="additionalFilters">
            <div class="categoryFilterDiv">
                <p class="appear">CATEGORY</p>
                <?php $listOfAllCategories = fetchAllCategories($connection); ?>
                <div class="categoryList">
                    <a href="http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?pageNum=0">NO FILTER</a>
                    <?php foreach($listOfAllCategories as $categories): ?>
                        <a href="http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?pageNum=0&filter=mod_category:<?= $categories["id"] ?>"><?= strtoupper($categories["category_name"])?></a>
                    <?php endforeach; ?>
                </div>
                <i class="fas fa-caret-up fa-lg triangleUp"></i>
            </div>
        </div>

    </div>

    <div class="modCollectionContainer">

        <?php 

            if(!preg_match("/\d+/", $pageNumber))
                $pageNumber = 0; 
                
            $modItems = getBriefPartsFromModServer($pageNumber * $numberOfItemsPerPage, $numberOfItemsPerPage, $connection, $game, "mod_date_changed", FALSE); 
                            
            $limit = (($pageNumber + 1) * $numberOfItemsPerPage) <= $totalNumberOfMods ? ($pageNumber + 1) * $numberOfItemsPerPage : $totalNumberOfMods; 
                
            foreach($modItems as $modBoxInfo){
                echo returnDeFangedModDiv($modBoxInfo); 
            }

        ?>

    </div>
    
    <?php
        
        $firstLink = FALSE;
        $lastLink = FALSE; 
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


    <div class="paginationDiv">
        <form action="fetch.php" method="GET" class="paginationForm">
            <button name="pageNum" value="<?= $pageNumber - 1?>" class="paginationButton unselectedPag nearButton outerPagL" <?php if($pageNumber <= 0) echo "disabled=true"; ?>>⮜</button>
            <div class="middleCenter">
                
                <?php if(isset($_GET["filter"])): ?>
                    <input type="hidden" name="filter" value="<?php 
                            echo $_GET["filter"];  
                    ?>">
                <?php endif;?>

                <?php if(isset($_GET["search"])): ?>
                    <input type="hidden" name="search" value="<?php 
                            echo $_GET["search"];  
                    ?>">
                <?php endif;?>

                <?php if($firstLink): ?>
                    <button name="pageNum" value="0" class="paginationButton unselectedPag nearButton innerPag">1</button>    
                <?php endif; ?>
                <?php for($j = $starter; $j <= $ender; $j++): ?>
                    <button name="pageNum" value="<?=$j?>" class="paginationButton <?php if($pageNumber == $j) echo "bolderSelected"; else echo "unselectedPag"; ?> innerPag"><?=$j + 1?></button>
                <?php endfor;?>
                <?php if($lastLink): ?>
                    <button name="pageNum" value="<?= $numberOfPages - 1?>" class="paginationButton unselectedPag nearButton innerPag"><?= $numberOfPages ?></button>    
                <?php endif; ?>
            </div>
            <button name="pageNum" value="<?= $pageNumber + 1?>" class="paginationButton unselectedPag nearButton outerPagR" <?php if($pageNumber >= $numberOfPages - 1) echo "disabled=true"; ?>>⮞</button>
        </form>
    </div>

    <script>
        let arrOfObjects = [].slice.call(document.getElementsByClassName("innerPag"));
        arrOfObjects[arrOfObjects.length - 1].classList.add("lastInnerOne");
    </script>

    <?php 
        require_once "../components/footer.php"; 
    ?>

</body>
</html>

<?php 
    mysqli_close($connection); 
?>