<?php

    define("DEFAULT_MISSING_IMAGE", "../img/missing-image.png");
    $id_image_container = 0; 

    function returnImageComponent($imageURL = DEFAULT_MISSING_IMAGE){
        global $id_image_container;
        ob_start(); 
    ?>
        <div class="imageComponentContainer" id="<?=$id_image_container;?>">
            <img src="<?=$imageURL?>" alt="carousel_img" id="<?= 'img' . $id_image_container ?>">
            <input type="text" name="image_url[]" onchange="onChangeAddItemToCarouselList('<?= 'img' . $id_image_container ?>', '<?= 'text' . $id_image_container ?>', '<?= 'file' . $id_image_container ?>', true)" value="<?= $imageURL ?>"  id="<?= 'text' . $id_image_container ?>">
            <input type="file" name="image_url[]" accept="image/*" onchange="onChangeAddItemToCarouselList('<?= 'img' . $id_image_container ?>', '<?= 'file' . $id_image_container ?>', '<?= 'text' . $id_image_container ?>', true)" id="<?= 'file' . $id_image_container ?>"> 
            <button class="deleteBtn" onclick="removeImageFromCarouselId(event, '<?= $id_image_container++;?>')">DELETE</button>
        </div>
    <?php 
        $returnResult = ob_get_clean(); 
        return $returnResult; 
    }

?>