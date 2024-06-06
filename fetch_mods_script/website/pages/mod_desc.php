<?php 

    use simplehtmldom\HtmlWeb;
    require_once '../vendor/autoload.php'; 
    require_once '../components/customized_header.php'; 
    require_once '../../db/database_functions/db_functions.php'; 

    define("FILTER_URL", "http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?pageNum=0&filter="); 

    if(isset($_GET["modId"])){
        
        $modResult = getSpecificMod($_GET["modId"]);

        $modName = $modResult["mod_name"];     
        $description = $modResult["mod_desc"];
        $imageLinkArr = explode(" [:|:] ", $modResult["mod_imgs"]); 
        $thumbnailImage = $modResult["mod_thumbnail"]; 
        $modDownloadLink = $modResult["mod_link"]; 
        $carouselCount = 0; 

        $modAuthors = preg_split("/\, |\,/", $modResult["mod_author"]); 
        $authorFilters = ""; 

        for($i = 0; $i < count($modAuthors); $i++){
            $authorName = $modAuthors[$i]; 
            $authorFilters .= ('<a href="' . FILTER_URL . "mod_author:$authorName\">$authorName</a>");
            if($i != count($modAuthors) - 1)
                $authorFilters .= ", "; 
        }   

        $modDetails = []; 
        array_push($modDetails, '<b>' .  'Game'  .'</b>   ' . $modResult["mod_game"]);
        array_push($modDetails, '<b>Manufacturer</b> <a href="' . FILTER_URL . "mod_manufacturer:$modResult[mod_manufacturer]\">$modResult[mod_manufacturer]</a>");
        array_push($modDetails, '<b>Category</b> <a href="' . FILTER_URL . "mod_category:$modResult[mod_category]\">$modResult[category_name]</a>");
        array_push($modDetails, '<b>Author(s)</b> ' . $authorFilters);
        array_push($modDetails, '<b>' .  'Version'  .'</b>   ' . $modResult["mod_version"]);
        array_push($modDetails, '<b>' .  'Last updated'  .'</b>   ' . $modResult["mod_date_changed"]);

    }else die("Your site could not be reached!"); 
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modName ?></title>
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/mod_desc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>

    <?php 
        require_once "../components/header.php"; 
    ?>
    <div class="mainMidContainer">

        <div class="informationAndDownload">
            <?= returnCustomHeader($modName, "h1") ?>

            <!-- TODO: if this with php once you integrate SESSION -->
            <div class="adminActions">
                <form action="http://localhost/PWA_Project/fetch_mods_script/website/admin/mod_delete.php" method="GET">
                    <input type="hidden" name="modId" value="<?= $_GET["modId"] ?>">
                    <button class="delete"> <i class="fas fa-trash fa-lg"></i></button>
                </form>
                <form action="http://localhost/PWA_Project/fetch_mods_script/website/admin/mod_upload_change.php" method="GET">
                    <input type="hidden" name="modId" value="<?= $_GET["modId"] ?>">
                    <button class="edit"><i class="fas fa-edit fa-lg"></i></button>
                </form>
            </div>
            
            <div class="details">
                <?= returnCustomHeader("Mod details", "h3", true);?>
                <div class="horizontal50Container">
                    <div class="mainImageContainer">
                        <img src="<?= $thumbnailImage ?>" alt="<?= $modName?>">
                    </div>
                    <div class="detailPointsDiv">
                        <div class="innerDetailContainer">
                            <?php foreach($modDetails as $detail): ?>
                                <p><?= $detail ?></p>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <?= returnCustomHeader("Download info", "h3", true);?>
                
                <!-- implement download links here --> 
                <div class="downloadLink">
                    <a href="<?= $modDownloadLink ?>">
                        <h3 id="fileType"><?=  strtoupper(preg_split("/\.(?!.*\..*$)/", $modDownloadLink)[1]); ?></h3>
                        <h3 id="download">DOWNLOAD</h3>
                    </a>
                </div>

            </div>
            
            <div class="description">
                <?= returnCustomHeader("Mod description", "h3", true);?>
                <?php 
                    require_once '../components/desc_functions.php'; 
                    echo returnShearedParagraph($description);                
                ?>
            </div>
            
        </div>
        <div class="imageContainer">
            <?= returnCustomHeader("Mod gallery", "h2", true);?>
            <div class="flickerViewport" id="flickerWindow">
                <div class="imageCarousel unselectable" style="left: 0%;" draggable="false">
                    <?php foreach($imageLinkArr as $imageLink): ?> 
                        <div draggable="false" class="imageCell unselectable" style="left: <?php echo ($carouselCount * 101) ?>%;">
                            <img draggable="false" class="additionalImages unselectable" src="<?php echo $imageLink ?>" alt="<?php $modName . "pic" ?>">    
                        </div>
                        <?php $carouselCount++;  ?> 
                    <?php endforeach; ?>
                </div>
                <div id="dotContainer">
                    <?php for($i = 0; $i < $carouselCount; $i++):?>
                        <div id="a<?php echo (-101 * $i); ?>" class="dot" onclick="moveImageByButton(<?php echo (-101 * $i); ?>)"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once "../components/footer.php"; ?>
    <!-- Scripts can be hidden in other maps that the user cannot access on server -->
    <script>
            // hide this in other directory when used so they will not be able to access the source code for the image slider
            window.onload = function(){
                moveImageByButton(0); 
                let imageHeight = document.getElementsByClassName("additionalImages")[0].offsetHeight;
                let imageWidth = document.getElementsByClassName("additionalImages")[0].offsetWidth; 
                document.querySelector("#flickerWindow").style.height = imageHeight + "px";
                //document.querySelector("#flickerWindow").style.width = imageWidth + "px";
                //document.querySelector("#dotContainer").style.width = imageWidth + "px"; 
            }; 

            window.addEventListener("resize", function(){
                let imageHeight = document.getElementsByClassName("additionalImages")[0].offsetHeight;
                let imageWidth = document.getElementsByClassName("additionalImages")[0].offsetWidth; 
                document.querySelector("#flickerWindow").style.height = imageHeight + "px";
                //document.querySelector("#flickerWindow").style.width = imageWidth + "px";
                //document.querySelector("#dotContainer").style.width = imageWidth + "px"; 
            });

            // this actually fixed it since             
            document.querySelector("#flickerWindow").addEventListener("dragstart", function(event){
                event.preventDefault(); 
            });

            var mouseCoordinateX, mouseCoordinateY; 

            document.addEventListener("mousemove", function(event){
                mouseCoordinateX = event.clientX; 
                mouseCoordinateY = event.clientY; 
            })
            
            var savedValue = 0;
            var numOfImages = document.getElementsByClassName("additionalImages").length;
            var imageCarousel = document.getElementsByClassName("imageCarousel")[0]; 
            var transitionEngaged = false; 

            document.querySelector("#flickerWindow").addEventListener("mousedown", function(event){

                var toBeTranslated = 0; 
                var touchedElement = document.querySelector("#flickerWindow");

                var calibrationSubtraction = ((mouseCoordinateX - touchedElement.getBoundingClientRect().left)/touchedElement.offsetWidth); 
                var calibratedHeightForDots = (mouseCoordinateY - touchedElement.getBoundingClientRect().top); 

                if(!transitionEngaged  && (calibratedHeightForDots < touchedElement.offsetHeight - document.querySelector("#dotContainer").offsetHeight)){
                    
                    var listenInterval = setInterval(function(){

                        let boundingBox = touchedElement.getBoundingClientRect(); 

                        let relativeCoordinateX = mouseCoordinateX - boundingBox.left; 

                        let widthOfTouchedElement = touchedElement.offsetWidth; 
                        let percentageTouched = relativeCoordinateX/widthOfTouchedElement;
                        
                        console.log(percentageTouched - calibrationSubtraction);

                        toBeTranslated = ((percentageTouched - calibrationSubtraction) * 70); 
                        imageCarousel.style.left = ((savedValue + toBeTranslated) + "%");

                    }, 5);

                    document.addEventListener("mouseup", function(event){
                        transitionEngaged = true; 
                        clearInterval(listenInterval);
                        listenInterval = null;
                        imageCarousel.style.transition = "all 0.2s ease"; 
                        if(toBeTranslated <= -1 && Math.abs(savedValue / 101) < numOfImages - 1){
                            imageCarousel.style.left = (savedValue - 101) + "%";
                        }
                        else if(toBeTranslated >= 1 && Math.abs(savedValue / 101) > 0){
                            imageCarousel.style.left = (savedValue + 101) + "%";
                        }else{
                            imageCarousel.style.left = savedValue + "%"; 
                        }
                        setTimeout(() =>{
                            imageCarousel.style.transition = "all 0s ease";
                            transitionEngaged = false;
                        }, 202); 
                        savedValue = parseFloat(imageCarousel.style.left.split("%")[0]);
                        selectByPercentage(parseInt(savedValue / 101) * 101); 
                    }, {once: true}, {caputre: true});
                    
                }; 

            });

            // this animation shit could be externalized into some retarded outer function 
            function moveImageByButton(lefVal){
                // animation handling 
                if(!transitionEngaged && lefVal != parseInt(imageCarousel.style.left.split("%")[0])){
                    transitionEngaged = true; 
                    imageCarousel.style.transition = "all 0.2s ease";
                    imageCarousel.style.left = lefVal + "%";  
                    savedValue = lefVal; 
                    setTimeout(()=>{
                        imageCarousel.style.transition = "all 0s ease";
                        transitionEngaged = false; 
                    }, 202); 
                }
                selectByPercentage(lefVal);
            }
            
            function selectByPercentage(percentageSelected){
                let otherDots = document.getElementsByClassName("dot"); 
                for(let dot of otherDots){
                    dot.classList.remove("selected"); 
                }
                document.querySelector("#a" + percentageSelected).classList.add("selected"); 
            }
    </script>
</body>
</html>