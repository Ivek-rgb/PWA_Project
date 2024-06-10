<?php 
    if(isset($_POST["signOut"])){
        session_destroy(); 
        header("Location: http://localhost/PWA_Project/login.php"); 
        exit(); 
    }

    if(isset($_POST["game"])){
        $_SESSION["game"] = $_POST["game"]; 
        header("Location: http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?page=0");
        exit();  
    }

    if(isset($_POST["upload"])){
        header("Location: http://localhost/PWA_Project/fetch_mods_script/website/admin/mod_upload.php");
        exit(); 
    }

    if(isset($_POST["admin"])){
        header("Location: http://localhost/PWA_Project/fetch_mods_script/website/admin/admin.php");
        exit();
    }
?>

<div class="headerDiv">
    <div class="logoPart">
        <img src="../img/FS_logo.png" alt="main_logo">
        <form action="#" method="POST">
            <button name="signOut" value="1"><i class="fas fa-sign-out-alt"></i></button>
        </form>
    </div>
    <div class="navContainer">
        <?php if(isset($_SESSION["user_info"])): ?>
        <a href="../pages/index.php"><i class="fas fa-home"></i><p>HOME</p></a>
        <?php endif; ?>
        <form action="#" method="POST">
            <button name="game" value="Farming simulator 22">MODS - FS22</button>
            <button name="game" value="Farming simulator 19">MODS - FS19</button>
            <button name="game" value="Farming simulator 17">MODS - FS17</button>
            <?php if($_SESSION["is_admin"]): ?>
                <button name="upload" value="1">UPLOAD</button>
            <?php endif; ?>
        </form>
        
    </div>
</div>