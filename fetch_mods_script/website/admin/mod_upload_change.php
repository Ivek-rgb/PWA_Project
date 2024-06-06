<?php 
    define("DIRECTORY_UPLOAD_PATH", "../../storage/"); 

    require_once "/xampp/htdocs/PWA_Project/fetch_mods_script/db/database_functions/db_functions.php";
    require_once "../components/customized_upload_labels.php";
    require_once "../components/customized_header.php";
    require_once "../components/file_functions.php";

    $arrOfCategories = fetchAllCategories(); 

    if(isset($_GET["modId"])){
        
        $modResult = getSpecificMod($_GET["modId"]);

        $modId = $modResult["id"];
        $modName = $modResult["mod_name"];     
        $description = $modResult["mod_desc"];
        $imageLinkArr = explode(" [:|:] ", $modResult["mod_imgs"]); 
        $thumbnailImage = $modResult["mod_thumbnail"]; 
        $modDownloadLink = $modResult["mod_link"]; 

        $modDetails = []; 
        array_push($modDetails, 'Mod Game[:]mod_game[:]' . $modResult["mod_game"]);
        array_push($modDetails, 'Item Manufacturer[:]mod_manufacturer[:]' . $modResult["mod_manufacturer"]);
        array_push($modDetails, 'Mod Author(s)[:]mod_author[:]' . $modResult["mod_author"]);
        array_push($modDetails, 'Mod Version[:]mod_version[:]' . $modResult["mod_version"]);

    }else die("Your site could not be reached!");

    $modStorageName = NULL; 
    if(!is_dir(DIRECTORY_UPLOAD_PATH . $modId)){
        if(!mkdir(DIRECTORY_UPLOAD_PATH . $modId, 0777, true))
            die("Failed to create directory for mod");  
    }
    
    $modStorageName = DIRECTORY_UPLOAD_PATH . $modId . '/'; 
    
    if(isset($_POST["submitBtn"])){

        $arrOfIdsCategories = array_map(function($arg){ return $arg["id"]; }, $arrOfCategories);

        if(!in_array($_POST["mod_category"], $arrOfIdsCategories))
            die("Modification attempted, this will not be tolerated!"); 

        $arrToPushToDatabase = array_filter($_POST["image_url"], function($arg) { return strlen($arg) > 0;}); 
        $arrOfEnabledFiles = array_diff($arrToPushToDatabase, []);
        array_push($arrOfEnabledFiles, $_POST["mod_thumbnail"]);  
        array_push($arrOfEnabledFiles, $_POST["mod_link"]); 

        $associativeContains = []; 

        foreach($arrOfEnabledFiles as $urlLink){
            if(strlen($urlLink) > 0){
                $fileName = shearFileLocation($urlLink, true); 
                $associativeContains[$fileName] = 1; 
            }
        }

        $listOfAllFiles = array_diff(scandir($modStorageName), [".", ".."]);

        foreach($listOfAllFiles as $urlContained){
            if(!isset($associativeContains[$urlContained]))
                unlink($modStorageName .  $urlContained);
        }
     
        $tmpNames = $_FILES["image_url"]["tmp_name"];
        $names = $_FILES["image_url"]["name"]; 

        foreach($names as $imageURL){
            if(strlen($imageURL) > 0)
                checkForFormats("png jpeg jpg webp pdf bmp tiff", $imageURL, TRUE); 
        }

        for($i = 0; $i < count($tmpNames); $i++){
            if($_FILES["image_url"]["size"][$i] > 0){
                $targetedPlace = $modStorageName . $_FILES["image_url"]["name"][$i];
                $newName = $targetedPlace;
                $counter = 1; 
                while(file_exists($newName)){
                    $newName = $targetedPlace;
                    $splitName = shearFileLocation($newName, TRUE, TRUE);  
                    $newName = $modStorageName . $splitName[0] . "($counter)" . "." . $splitName[1];
                    $counter++; 
                } 
                $targetedPlace = $newName; 
                move_uploaded_file($tmpNames[$i], $targetedPlace);
                array_push($arrToPushToDatabase, $targetedPlace); 
            }      
        }

        $carouselImages = implode(" [:|:] ", $arrToPushToDatabase);

        $thumbnailImage = $_FILES["mod_thumbnail"]["size"]; 
        $modFile = $_FILES["mod_link"]["size"]; 

        if($thumbnailImage > 0){
            checkForFormats("png jpeg jpg webp pdf bmp tiff", $_FILES["mod_thumbnail"]["name"], TRUE); 
            $newFilePath =  $modStorageName . $_FILES["mod_thumbnail"]["name"]; 
            move_uploaded_file($_FILES["mod_thumbnail"]["tmp_name"], $newFilePath); 
            $thumbnailImage = $newFilePath; 
        }else{
            $thumbnailImage = $_POST["mod_thumbnail"]; 
        }

        if($modFile > 0){
            checkForFormats("php", $_FILES["mod_link"]["name"], FALSE);
            $newFilePath =  $modStorageName . $_FILES["mod_link"]["name"];
            move_uploaded_file($_FILES["mod_link"]["tmp_name"], $newFilePath); 
            $modFile = $newFilePath;
        }else{
            $modFile = $_POST["mod_link"]; 
        }

        $connection = openConnection(); 

        updateMod($modId, $_POST["mod_name"], $_POST["mod_author"], hash("md5", $_POST["mod_name"] . $_POST["mod_author"]), 
        $thumbnailImage, $_POST["mod_game"], $_POST["mod_category"], $_POST["mod_desc"], $carouselImages, $_POST["mod_version"],
        $modFile, $_POST["mod_manufacturer"]);

        mysqli_close($connection); 
        header("Location: http://localhost/PWA_Project/fetch_mods_script/website/pages/mod_desc.php?modId=$modId"); 
        exit(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/customized_input.css">
    <link rel="stylesheet" href="../style/upload_change.css">
    <script src="../../js/window_navigation_scripts.js"></script>
    <script src="../../js/carousel_add_script.js"></script>
    <title>Mod update/change</title>
    <style>
        input[type="file"]{
            cursor: pointer;
        }
    </style>
</head>
<body>      

    <div class="mainContent">
        
        <?php
            require_once "../components/header.php"; 
        ?>

        <div class="editBox">
            <form action="./mod_upload_change.php?modId=<?=$modId?>" method="POST" enctype="multipart/form-data">
                <?= returnCustomHeader("Thumbnail preview", "h2", true) ?>
                <div class="thumbnailPart">
                    <img id="previewImage1" src="<?= $thumbnailImage ?>" alt="<?= $modName ?>" class="changableThumbnailImage">
                    <input id="thumbnailUpload" class="changeThumbnail" size="60" type="file" name="mod_thumbnail" accept="image/*" onchange="changePreviewImage(this, 'previewImage1', 'mod_thumbnail')">
                    <h3>or</h3>
                    <?= createLabelsAndInputFields("mod_thumbnail", "text", "Mod thumbnail (link):", $thumbnailImage, FALSE, "changePreviewImage(this, 'previewImage1', 'thumbnailUpload')")?>
                </div>
                <div class="otherChanges">
                    <?= returnCustomHeader("Mod information", "h2", true) ?>
                    <?php 
                        echo createLabelsAndInputFields("mod_name", "text", "Mod name:", $modName, TRUE);
                        echo createLabelsAndInputFields("mod_link", "text", "Mod link:", $modDownloadLink, TRUE, "onChangeClearOther('modLinkUpload')");
                    ?>
                    <h3>or</h3>
                    <?php 
                        echo createLabelsAndInputFields("mod_link", "file", "Mod link(file):", NULL, FALSE, "onChangeClearOther('mod_link')" ,"modLinkUpload");
                        echo createLabelsAndInputFields("mod_desc", "textarea", "Mod description:", $description); 

                        foreach($modDetails as $modDetail){
                            $infoArr = explode("[:]", $modDetail);
                            echo createLabelsAndInputFields($infoArr[1], "text", $infoArr[0] . ":", $infoArr[2]); 
                        }
                        
                    ?>
                    <div class="customizedInput">
                        <label for="" class="customizedLabels">Mod category:</label>
                        <select name="mod_category" id="" >
                            <?php foreach($arrOfCategories as $category): ?>
                                <option value="<?= $category["id"] ?>" <?php if($category["id"] == $modResult["mod_category"]) echo "selected"; ?>><?= $category["category_name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?= returnCustomHeader("Gallery preview", "h2", true) ?>
                <div class="galleryContainer">
                    
                    <?php   

                        require_once "../components/image_component_system.php";
                        
                        if(!strlen($imageLinkArr[0]) == 0)
                            foreach($imageLinkArr as $imageURL){
                                echo returnImageComponent($imageURL); 
                            }

                    ?>

                    <script>
                        window.addEventListener("load", function(event){
                            onChangeDefaultAddNew(); 
                        });
                    </script>

                </div>

                <input type="submit" class="enlargedBtn" name="submitBtn" value="CHANGE MOD">
            </form>
        </div>

        <?php 
            require_once "../components/footer.php";
        ?>
    </div>
    
</body>
</html>

