<?php

    define("URL_DELETE", "../admin/mod_delete.php");
    define("URL_EDIT", "../admin/mod_upload_change.php"); 

    function returnDeFangedModDiv($sqliResult){

        $imageAttr = $sqliResult['mod_thumbnail']; 
        $modName = $sqliResult['mod_name']; 
        $modAuthors = $sqliResult['mod_author']; 
        $furtherModLink = $sqliResult['id']; 

        ob_start(); 
        ?>
        <div class="modDiv">
            <img class="modBannerImage" src="<?php echo $imageAttr ?>" alt="<?php echo $modName . "pic" ?>">
            <h3 class="modName"><?php echo $modName ?></h3>
            <p class="authors"><?php echo $modAuthors ?></p>
            
            <div class="buttonOptions">
                <form action="../pages/mod_desc.php" method="GET">
                    <input name="modId" type="hidden" value="<?= $furtherModLink ?>">
                    <input class="optionBtn moreInfoBtn" type="submit" value="MORE INFO">
                </form>

                <form action="<?= URL_DELETE ?>" method="GET">
                    <input name="modId" type="hidden" value="<?= $furtherModLink ?>">
                    <input class="optionBtn deleteBtn" type="submit" value="DELETE">
                </form> 

                <form action="<?= URL_EDIT ?>" method="GET">
                    <input name="modId" type="hidden" value="<?= $furtherModLink ?>">
                    <input class="optionBtn editBtn" type="submit" value="EDIT">
                </form> 
            </div>
        </div>
        <?php
        $returnHtml = ob_get_clean(); 
        return $returnHtml; 
    }

?>
