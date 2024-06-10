<?php 

    define("DIRECTORY_UPLOAD_PATH", "../../storage/"); 
    session_start();
    require_once "/xampp/htdocs/PWA_Project/fetch_mods_script/db/database_functions/db_functions.php";
    require_once "../components/customized_upload_labels.php";
    require_once "../components/customized_header.php";
    require_once "../components/file_functions.php";

    $modDetails = []; 
    array_push($modDetails, 'Mod Game[:]mod_game[:]');
    array_push($modDetails, 'Item Manufacturer[:]mod_manufacturer[:]');
    array_push($modDetails, 'Mod Author(s)[:]mod_author[:]');
    array_push($modDetails, 'Mod Version[:]mod_version[:]');
    
    if(isset($_POST["submitBtn"])){

        $connection = openConnection();

        $modId = uploadMod("", "", "", "", "", 1, "", "", "", "", "", $connection);

        $modStorageName = NULL; 
        if(!is_dir(DIRECTORY_UPLOAD_PATH . $modId)){
            if(!mkdir(DIRECTORY_UPLOAD_PATH . $modId, 0777, true))
                die("Failed to create directory for mod");  
        }
    
        $modStorageName = DIRECTORY_UPLOAD_PATH . $modId . '/'; 

        $arrToPushToDatabase = array_filter($_POST["image_url"], function($arg) { return strlen($arg) > 0;}); 
        $tmpNames = $_FILES["image_url"]["tmp_name"];
        $imgNames = $_FILES["image_url"]["name"];

        foreach($imgNames as $imgURL){
            checkForFormats("png jpeg jpg webp pdf bmp tiff", $imgURL, TRUE); 
        }

        for($i = 0; $i < count($tmpNames); $i++){
            if($_FILES["image_url"]["size"][$i] > 0){
                $targetedPlace = $modStorageName . $_FILES["image_url"]["name"][$i];
                $newName = $targetedPlace;
                $counter = 1; 
                while(file_exists($newName)){
                    $newName = $targetedPlace;
                    $splitName = shearFileLocation($newName, TRUE, TRUE);  
                    $newName .= $modStorageName . $splitName[0] . "($counter)" . "." . $splitName[1];
                    $counter++; 
                } 
                $targetedPlace = $newName; 
                move_uploaded_file($tmpNames[$i], $targetedPlace);
                array_push($arrToPushToDatabase, $targetedPlace); 
            }      
        }

        $carouselImages = implode(" [:|:] ", $arrToPushToDatabase);
        

        $thumbnailImage = $_FILES["mod_thumbnail"]["size"]; 
        checkForFormats("png jpeg jpg webp pdf bmp tiff", $_FILES["mod_thumbnail"]["name"], TRUE); 

        $modFile = $_FILES["mod_link"]["size"]; 
        checkForFormats("php", $_FILES["mod_link"]["name"], FALSE);

        if($thumbnailImage > 0){
            $newFilePath =  $modStorageName . $_FILES["mod_thumbnail"]["name"]; 
            move_uploaded_file($_FILES["mod_thumbnail"]["tmp_name"], $newFilePath); 
            $thumbnailImage = $newFilePath; 
        }else{
            $thumbnailImage = $_POST["mod_thumbnail"]; 
        }

        if($modFile > 0){
            $newFilePath =  $modStorageName . $_FILES["mod_link"]["name"];
            move_uploaded_file($_FILES["mod_link"]["tmp_name"], $newFilePath); 
            $modFile = $newFilePath;
        }else{
            $modFile = $_POST["mod_link"]; 
        }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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
            <form action="./mod_upload.php" method="POST" enctype="multipart/form-data">
                <?= returnCustomHeader("Thumbnail preview", "h2", true) ?>
                <div class="thumbnailPart">
                    <img id="previewImage1" src="" alt="SELECT IMAGE" class="changableThumbnailImage">
                    <input id="thumbnailUpload" class="changeThumbnail" size="60" type="file" name="mod_thumbnail" onchange="changePreviewImage(this, 'previewImage1', 'mod_thumbnail')" required>
                    <h3>or</h3>
                    <?= createLabelsAndInputFields("mod_thumbnail", "text", "Mod thumbnail (link):", NULL, TRUE, "changePreviewImage(this, 'previewImage1', 'thumbnailUpload')")?>
                </div>
                <div class="otherChanges">
                    <?= returnCustomHeader("Mod information", "h2", true) ?>
                    <?php 
                        echo createLabelsAndInputFields("mod_name", "text", "Mod name:", NULL, TRUE);
                        echo createLabelsAndInputFields("mod_link", "text", "Mod link:", NULL, TRUE, "onChangeClearOther('modLinkUpload')");
                    ?>
                    <h3>or</h3>
                    <?php 
                        echo createLabelsAndInputFields("mod_link", "file", "Mod link(file):", NULL, TRUE, "onChangeClearOther('mod_link')" ,"modLinkUpload");
                        echo createLabelsAndInputFields("mod_desc", "textarea", "Mod description:", NULL); 

                        foreach($modDetails as $modDetail){
                            $infoArr = explode("[:]", $modDetail);
                            echo createLabelsAndInputFields($infoArr[1], "text", $infoArr[0] . ":", $infoArr[2]); 
                        }

                        $arrOfCategories = fetchAllCategories(); 
                    ?>
                    <div class="customizedInput">
                        <label for="" class="customizedLabels">Mod category:</label>
                        <select name="mod_category" id="" >
                            <?php foreach($arrOfCategories as $category): ?>
                                <option value="<?= $category["id"] ?>" <?php if($category["id"] == 0) echo "selected"; ?>><?= $category["category_name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?= returnCustomHeader("Gallery preview", "h2", true) ?>
                <div class="galleryContainer">
                    
                    <?php   
                        require_once "../components/image_component_system.php";
                    ?>

                    <script>
                        window.addEventListener("load", function(event){
                            onChangeDefaultAddNew(); 
                        });
                    </script>

                </div>

                <input type="submit" class="enlargedBtn" name="submitBtn" value="UPLOAD MOD">
            </form>
        </div>

        <?php 
            require_once "../components/footer.php";
        ?>
    </div>
</body>
</html>