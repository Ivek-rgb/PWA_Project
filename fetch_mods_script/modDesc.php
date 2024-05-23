<?php 

    use simplehtmldom\HtmlDocument;
    use simplehtmldom\HtmlWeb;
    require_once 'vendor/autoload.php'; 
    require_once './customized_header.php'; 

    // loads scraped things off the site
    // will be later used to just nick from the designated SQL database
    if(isset($_GET["descLink"])){
        // when you create DB for your project just fill the variables here so it's like 100 times easier to pull them out 
        // main content
        $httpClient = new HtmlWeb(); 
        $descResponse = $httpClient->load($_GET["descLink"]); 

        $modName = $descResponse->find('.title-label', 0)->innertext;     
        $description = $descResponse->find('.top-line', 0)->innertext;
        $images = $descResponse->find('.gallery-cell'); 
        $imageLinkArr = []; 
        
        foreach($images as $image){
            $imageLinkArr[] = $image->find('img', 0)->getAttribute('src');
        }
        $carouselCount = 0; 

        // side content
        $detailsTable = $descResponse->find('.table-game-info')[0]->find('.table-row');
        $modDetails = []; 
        foreach($detailsTable as $rowUnit){
            $cells = $rowUnit->find('.table-cell');
            $boldPart = $cells[0]->textcontent; 
            array_push($modDetails, '<b>' . $cells[0]->plaintext .'</b>   ' . $cells[1]->plaintext);
        }
    }else die("Your site could not be reached!"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modName ?></title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgb(246,242,241);
        }

        .imageCarousel img{
            width: 100%;
            height: auto;
            position: absolute;
        }

        .flickerViewport{
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            justify-content: end;
            width: 100%;
            margin-top: 20px;
            cursor: grab;
        }

        .imageContainer{
            display: flex;
            justify-content: center;
            flex-direction: column;
            width: 100%;
        }

        .imageCarousel{
            top: 0;
            display: block;
            position: absolute;
            width: 100%;
            transition: all 0s ease;
        }

        .imageCell{
            position: absolute;
            width: 100%;
        }

        .unselectable{
            pointer-events: none;
            user-select: none;
        }

        #dotContainer{
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            background-color: rgba(40, 40, 40, 0.57);
            padding: 10px 0;
            z-index: 20;
            cursor:default;
        }

        .selected{
            background-color: white;
        }

        .dot{
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid white;
            margin: 0 5px;
            cursor: pointer;
        }

        hr{
            background-color: #D0D0D0;
            border-color: #D0D0D0;
        }

        .informationAndDownload{
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* externalize this to default style font map for whole web app*/

        h1, h2, h3, h4{
            color: #282828;
        }

        .description p{
            color: #888888;
        }

        .mainMidContainer{
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mainImageContainer{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .mainImageContainer img{
            width: 250px;
            border-radius: 10px;
        }

        .horizontal50Container{
            display: flex;
            width: 100%;
        }

        .detailPointsDiv{
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .innerDetailContainer{
            width: 100%;
        }
        
        .innerDetailContainer p{
            margin: 12px 0;
            width: 100%;
            color: #282828;
        }

        .horizontal50Container>div{
            width: 50%;
        }
        
        .downloadLink{
            max-width: 350px;
        }
        
        .downloadLink a{
            text-decoration: none;
            display: flex;
            text-align: center;
        }

        .downloadLink a:hover #download{
            background-color:greenyellow;
            color: #282828;
            transition: 1s;
        }

        .downloadLink h3{
            padding: 12px 0;
        }

        #fileType{
            width: 30%;
            background-color: #282828;
            color: white;
        }

        #download{
            background-color: #81C332;
            color: white;
            width: 70%;
        }

        
        @media only screen and (max-width: 1200px){
            .mainMidContainer{
                width: 80%;
            }

            .horizontal50Container{
                flex-direction: column;
                width: 100%;
            }

            .horizontal50Container{
                justify-content: center;
                align-items: center;
            }

            .detailPointsDiv{
                width: 100%;
                text-align: center;
            }

            .description{
                width: 100%;
            }
        }

        @media only screen and (max-width: 600px){

            .mainMidContainer{
                width: 100%;
            }

            .downloadLink{
                margin: 0 auto;
                max-width: none;
                margin: 0 5px;
            }

        }

    </style>
</head>
<body>

    <div class="mainMidContainer">

        <div class="informationAndDownload">
            <?= returnCustomHeader($modName, "h1") ?>

            <div class="details">
                <?= returnCustomHeader("Mod details", "h3", true);?>
                <div class="horizontal50Container">
                    <div class="mainImageContainer">
                        <img src="<?= $_GET["mainPhoto"]; ?>" alt="<?= $modName?>">
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
                    <a href="">
                        <h3 id="fileType">ZIP</h3>
                        <h3 id="download">DOWNLOAD</h3>
                    </a>
                </div>

            </div>
            
            <div class="description">
                <?= returnCustomHeader("Mod description", "h3", true);?>
                <?php 
                    require_once './desc_functions.php'; 
                    echo returnShearedParagraph($description);                
                ?>
            </div>
            
        </div>
        <div class="imageContainer">
            <?= returnCustomHeader("Mod gallery", "h2", true);?>
            <div class="flickerViewport" id="flickerWindow">
                <div class="imageCarousel" style="left: 0%;">
                    <?php foreach($imageLinkArr as $imageLink): ?> 
                        <div class="imageCell" style="left: <?php echo ($carouselCount * 101) ?>%;">
                            <img class="additionalImages unselectable" src="<?php echo $imageLink ?>" alt="<?php $modName . "pic" ?>" unselectable="on" >    
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

            var mouseCoordinateX, mouseCoordinateY; 

            document.addEventListener("mousemove", function(event){
                mouseCoordinateX = event.clientX; 
                mouseCoordinateY = event.clientY; 
            })
            
            var savedValue = 0;
            var numOfImages = document.getElementsByClassName("additionalImages").length;
            var imageCarousel = document.getElementsByClassName("imageCarousel")[0]; 
            var transitionEngaged = false; 

            // somehow retarded Chrome keeps shitting over the fucking slider animation -> how in the God's name do i fix that
            // how do i even fix this then 
            document.querySelector("#flickerWindow").addEventListener("mousedown", function(event){

                var toBeTranslated = 0; 
                var touchedElement = document.querySelector("#flickerWindow");

                var calibrationSubtraction = ((mouseCoordinateX - touchedElement.getBoundingClientRect().left)/touchedElement.offsetWidth); 
                var calibratedHeightForDots = (mouseCoordinateY - touchedElement.getBoundingClientRect().top); 

                if(!transitionEngaged  && (calibratedHeightForDots < touchedElement.offsetHeight - document.querySelector("#dotContainer").offsetHeight)){
                    
                    console.log("Picture clamped!");

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
                        console.log("Picture relased!");
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
