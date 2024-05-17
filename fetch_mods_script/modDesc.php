<?php 

    use simplehtmldom\HtmlDocument;
    use simplehtmldom\HtmlWeb;
    require_once 'vendor/autoload.php'; 

    if(isset($_POST["descLink"])){
        
        $httpClient = new HtmlWeb(); 
        $descResponse = $httpClient->load($_POST["descLink"]); 

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
            justify-content: center;
            justify-items: center;
        }

        .imageCarousel img{
            width: 100%;
            position: absolute;
        }

        .flickerViewport{
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            width: 60%;
            cursor: grab;
        }

        .imageContainer{
            display: flex;
            justify-content: center;
        }

        .imageCarousel{
            display: block;
            position: absolute;
            transition: 0.2s;
            width: 100%;
        }

        .imageCell{
            position: absolute;
            width: 100%;
        }

        .unselectable{
            pointer-events: none;
            user-select: none; /* Prevent selection */
            -moz-user-select: none; /* Firefox */
            -webkit-user-select: none; /* Safari and Chrome */
            -ms-user-select: none; /* Internet Explorer/Edge */ 
        }

    </style>
</head>
<body>

    <h1><?php echo $modName; ?></h1>    
    <p><?php echo $description; ?></p>
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
    </div>

    <!-- Scripts can be hidden in other maps that the user cannot access on server -->
    <script>
            // hide this in other directory when used so they will not be able to access the source code for the image slider
            window.onload = function(){
                let imageHeight = document.getElementsByClassName("additionalImages")[0].offsetHeight;
                let imageWidth = document.getElementsByClassName("additionalImages")[0].offsetWidth; 
                document.querySelector("#flickerWindow").style.height = imageHeight + "px";
                document.querySelector("#flickerWindow").style.width = imageWidth + "px";
            }; 

            window.addEventListener("resize", function(){
                let imageHeight = document.getElementsByClassName("additionalImages")[0].offsetHeight;
                let imageWidth = document.getElementsByClassName("additionalImages")[0].offsetWidth; 
                document.querySelector("#flickerWindow").style.height = imageHeight + "px";
                document.querySelector("#flickerWindow").style.width = imageWidth + "px";
            });

            var oldMouseCoordinateX; 
            var mouseCoordinateX;

            document.addEventListener("mousemove", function(event){
                mouseCoordinateX = event.clientX; 
            })
            
            var savedValue = 0;
            var numOfImages = document.getElementsByClassName("additionalImages").length;
            console.log(numOfImages);

            // it has been grabbed try to flick it, bruhman still i need a flick animation itself to work tho!
            document.querySelector("#flickerWindow").addEventListener("mousedown", function(event){

                var toBeTranslated = 0; 
                var imageCarousel = document.getElementsByClassName("imageCarousel")[0]; 
                var touchedElement = document.querySelector("#flickerWindow"); 

                var calibrationSubtraction = ((mouseCoordinateX - touchedElement.getBoundingClientRect().left)/touchedElement.offsetWidth); 
                var listenInterval = setInterval(function(){

                    let boundingBox = touchedElement.getBoundingClientRect(); 

                    let relativeCoordinateX = mouseCoordinateX - boundingBox.left; 

                    let widthOfTouchedElement = touchedElement.offsetWidth; 
                    let percentageTouched = relativeCoordinateX/widthOfTouchedElement;

                    
                    toBeTranslated = ((percentageTouched - calibrationSubtraction) * 70); 
                    console.log(toBeTranslated + " calibrated var");
                    imageCarousel.style.left = ((savedValue + toBeTranslated) + "%");
                }, 10);
                
                document.querySelector("#flickerWindow").addEventListener("mouseup", function(event){
                    clearInterval(listenInterval);
                    console.log(toBeTranslated + " last heard var");
                    if(toBeTranslated <= -3 && Math.abs(savedValue / 101) < numOfImages - 1){
                        imageCarousel.style.left = (savedValue - 101) + "%";
                    }
                    else if(toBeTranslated >= 3 && Math.abs(savedValue / 101) > 0)
                        imageCarousel.style.left = (savedValue + 101) + "%";
                    else{
                        imageCarousel.style.left = savedValue + "%"; 
                    }
                    savedValue = parseFloat(imageCarousel.style.left.split("%")[0]);
                }, {once: true});

            });


    </script>

</body>
</html>
