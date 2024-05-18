<?php 

    use simplehtmldom\HtmlDocument;
    use simplehtmldom\HtmlWeb;
    require_once 'vendor/autoload.php'; 

    if(isset($_GET["descLink"])){
        
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
            width: 100%;
            cursor: grab;
        }

        .imageContainer{
            display: flex;
            justify-content: center;
            flex-direction: column;
            width: 65%;
        }

        .imageCarousel{
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
            background-color: #282828;
            padding: 10px 0;
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
            width: 100%;
            background-color: #D0D0D0;
            border-color: #D0D0D0;
        }

        .informationAndDownload{
            display: flex;
            flex-direction: column;
            width: 60%;
        }

        /* externalize this to default style font map for whole web app*/

        h1, h2, h3, h4{
            color: #282828;
        }

        p{
            color: #888888;
        }

    </style>
</head>
<body>

    <div class="informationAndDownload">
        <h1><?php echo $modName; ?></h1>

        <div class="detalis">

        </div>
        
        <div class="description">
            <h3>Mod description</h3>
            <p><?php echo $description; ?></p>
        </div>
        <hr>
    </div>
    
    <div class="imageContainer">
        <div class="flickerViewport" id="flickerWindow">
            <div class="imageCarousel" style="left: 0%;">
                <?php foreach($imageLinkArr as $imageLink): ?> 
                    <div class="imageCell" style="left: <?php echo ($carouselCount * 101) ?>%;">
                        <img class="additionalImages unselectable" src="<?php echo $imageLink ?>" alt="<?php $modName . "pic" ?>" unselectable="on" >    
                    </div>
                    <?php $carouselCount++;  ?> 
                <?php endforeach; ?>
            </div>
        </div>
        <div id="dotContainer">
            <?php for($i = 0; $i < $carouselCount; $i++):?>
                <div id="a<?php echo (-101 * $i); ?>" class="dot" onclick="moveImageByButton(<?php echo (-101 * $i); ?>)"></div>
            <?php endfor; ?>
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

            var mouseCoordinateX;

            document.addEventListener("mousemove", function(event){
                mouseCoordinateX = event.clientX; 
            })
            
            var savedValue = 0;
            var numOfImages = document.getElementsByClassName("additionalImages").length;
            var imageCarousel = document.getElementsByClassName("imageCarousel")[0]; 
            var transitionEngaged = false; 

            document.querySelector("#flickerWindow").addEventListener("mousedown", function(event){

                var toBeTranslated = 0; 
                
                var touchedElement = document.querySelector("#flickerWindow");
                  
                var calibrationSubtraction = ((mouseCoordinateX - touchedElement.getBoundingClientRect().left)/touchedElement.offsetWidth); 
                
                if(!transitionEngaged){
                    var listenInterval = setInterval(function(){

                        let boundingBox = touchedElement.getBoundingClientRect(); 

                        let relativeCoordinateX = mouseCoordinateX - boundingBox.left; 

                        let widthOfTouchedElement = touchedElement.offsetWidth; 
                        let percentageTouched = relativeCoordinateX/widthOfTouchedElement;
                        
                        toBeTranslated = ((percentageTouched - calibrationSubtraction) * 70); 
                        imageCarousel.style.left = ((savedValue + toBeTranslated) + "%");
                        
                    }, 3);
                    
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
                        }, 230); 
                        savedValue = parseFloat(imageCarousel.style.left.split("%")[0]);
                        selectByPercentage(parseInt(savedValue / 101) * 101); 
                    }, {once: true});
                }
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
                    }, 250); 
                }
                // clear all other dots - with a FUCKING function preferably 
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
