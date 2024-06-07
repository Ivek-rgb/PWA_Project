
<?php

    define("DIRECTORY_UPLOAD_PATH", "../../storage/");
    session_start(); 
    require_once "/xampp/htdocs/PWA_Project/fetch_mods_script/db/database_functions/db_functions.php"; 

    require_once "../components/header.php"; 

    if(isset($_POST["userChoice"])){

        if($_POST["userChoice"] == "1"){

            $isFinished = deleteOnId($_POST["modId"],"mods_brief");

            $pathToUnlink = DIRECTORY_UPLOAD_PATH . $_POST["modId"]; 
        
            array_map("unlink", glob("$pathToUnlink/*")); 

            rmdir($pathToUnlink); 

            if($isFinished)
                echo "<p style=\"width:100%; text-align:center;\">Mod deleted!</p>";
        }else{
            header("Location: http://localhost/PWA_Project/fetch_mods_script/website/pages/mod_desc.php?modId=$_POST[modId]"); 
            exit(); 
        }

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
    <script src="../../js/window_navigation_scripts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <title>Mod deletion</title>
    <style>
        .returnDiv{
            height: 80vh;
            width: 65%;
        }

        .areYouSure{
            height: 80vh;
            width: 65%;
            display: flex;
            justify-content: center;
        }

        .mainContent{
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .optionBtn{
            width: 100%;
            font-weight: bolder;
            border: none;
            padding: 10px 0;
            margin: 5px 0;
            cursor: pointer;
        }

        .goBackBtn{
            background-color: #81C332;
            color: white;
        }

        .goBackBtn:hover{
            color:#81C332;
            background-color: rgb(246,242,241);
            transition: 0.5s;
        }
        
        .choice{
            background-color: #C8C8C8;
        }

        .choice:hover{
            background-color: gray;
        }

    </style>
</head>
<body>
    
    <div class="mainContent">

        <?php if(!isset($_POST["userChoice"])): ?>
        <div class="areYouSure">
            <form action="#" method="POST">
                <h2>Are you sure you want to remove that article?</h2>
                <input type="hidden" name="modId" value="<?= $_GET["modId"] ?>">
                <button name="userChoice" class="optionBtn choice" value="1">YES</button>
                <button name="userChoice" class="optionBtn choice" value="0">NO</button>
            </form>
        </div>
        <?php else: ?>
            <div class="returnDiv">
                <form action="http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?pageNum=0" method="GET">
                    <button class="goBack optionBtn goBackBtn">RETURN BACK</button>
                </form>
            </div>
        <?php endif; ?>

        <?php 
            require_once "../components/footer.php";
        ?>

    </div>
    
</body>
</html>