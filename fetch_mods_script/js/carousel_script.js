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