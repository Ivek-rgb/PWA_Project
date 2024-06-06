var latestId = 0; 

window.addEventListener("load", function(event){
    let currentContainers = document.getElementsByClassName("imageComponentContainer");
    if(currentContainers.length > 0)
        latestId = currentContainers[currentContainers.length - 1].id;
    console.log(latestId);
}); 

function removeImageFromCarouselId(event, idOfContainer){
    event.preventDefault(); 
    let selectedContainer = document.getElementById(idOfContainer); 
    selectedContainer.parentElement.removeChild(selectedContainer); 
}

function removeImageFromCarouselDOM(event, containerDOMElement){
    event.preventDefault(); 
    let id = containerDOMElement.id; 
    if(id == latestId)
        return; 
    containerDOMElement.parentElement.removeChild(containerDOMElement);
}

function onChangeAddItemToCarouselList(imageDOM, input, otherInput, usingIDs = false){
    if(usingIDs){
        input = document.getElementById(input);
        otherInput = document.getElementById(otherInput); 
        imageDOM = document.getElementById(imageDOM); 
    }
    input.setAttribute('required', 'required'); 
    otherInput.removeAttribute('required'); 
    otherInput.value = ""; 
    if(imageDOM.parentElement.id == latestId) onChangeDefaultAddNew(); 
    if(input.type == "text"){
        imageDOM.src = input.value; 
    }else{
        if(input.files && input.files[0]){
            var reader = new FileReader();
            reader.onload = function (e) {
                imageDOM.setAttribute('src', e.target.result); 
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
}

function onChangeDefaultAddNew(){
    let imageContainer = document.createElement("div");
    let imageComponent = document.createElement("img");
    let textInput = document.createElement("input");
    let fileInput = document.createElement("input");
    let deleteButton = document.createElement("button");
    imageContainer.append(imageComponent);
    imageContainer.append(textInput);
    imageContainer.append(fileInput);
    imageContainer.append(deleteButton);
    imageContainer.classList.add("imageComponentContainer");
    imageContainer.id = (++latestId);  
    imageComponent.src="";  
    textInput.type = "text";
    textInput.value = ""; 
    textInput.name = "image_url[]";
    textInput.onchange = () => onChangeAddItemToCarouselList(imageComponent, textInput, fileInput);
    fileInput.type = "file";
    fileInput.name = "image_url[]";
    fileInput.accept = "image/*"; 
    fileInput.onchange = () => onChangeAddItemToCarouselList(imageComponent, fileInput, textInput);
    deleteButton.classList.add("deleteBtn"); 
    deleteButton.onclick = () => removeImageFromCarouselDOM(event, imageContainer);  
    deleteButton.innerHTML = "DELETE";
    document.getElementsByClassName("galleryContainer")[0].append(imageContainer);
}


function onChangeClearOther(idOfOther){
    document.querySelector("#" + idOfOther).value = "";
    document.querySelector("#" + idOfOther).removeAttribute('required');  
}

function changePreviewImage(input, imageDOMID, idOfOther, isURLInput = false){
    onChangeClearOther(idOfOther); 
    input.setAttribute('required', 'required');
    let previewChangeImage = document.getElementById(imageDOMID); 
    if(isURLInput){
        previewChangeImage.setAttribute('src', input.value); 
        return; 
    }
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function (e) {
            previewChangeImage.setAttribute('src', e.target.result); 
        }
        reader.readAsDataURL(input.files[0]);
    }
}