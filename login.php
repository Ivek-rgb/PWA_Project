<?php 
    require_once "./fetch_mods_script/db/database_functions/db_functions.php";

    $error = 0; 

    if(isset($_POST["login"])){

        $resultSet = fetchUserByUsername($_POST["username"]);
        
        $error += intval($resultSet == null); 
        if($error != 1 and !password_verify($_POST["password"], $resultSet["password"])) $error = 2; 

        if($error == 0){
            session_start(); 
            $_SESSION["user_info"] = $resultSet;
            header("Location: http://localhost/PWA_Project/fetch_mods_script/website/pages/index.php");
            exit(); 
        }   

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="./fetch_mods_script/website/style/global.css">
    <link rel="stylesheet" href="./fetch_mods_script/website/style/login_register.css">
    
    <title>Login</title>

    <style>

      .loginMessage{
            width: 100%;
            padding: 10px 0px;
            text-align: center;
            font-weight: bolder;
            display: none;
            color: red;
            <?php if($error != 0) echo "display:block;";?>
        }



    </style>
</head>
<body>

    <div class="mainContent">

        <div class="registerEntry">

            <form action="login.php" method="POST" name="register">
                <img src="./fetch_mods_script/website/img/FS_logo.png"> 
                <h3>LOGIN</h3>
                <label for="username">Username:</label>

                <input type="text" name="username" id="username">

                <label for="password">Password:</label>

                <input type="password" name="password" id="password">

                <?php if($error != 0): ?>
                    <p class="loginMessage">
                        <?php 
                            switch($error){
                                default: 
                                    echo "User Name or Password does not match!"; 
                            } 
                        ?>
                    </p>
                <?php endif; ?>

                <button type="submit" name="login" value="1">LOGIN</button>
            </form>
            
            <form action="index.php">
                <button type="submit" class="register">REGISTER</button>
            </form>
            <a id="guestLogin" href="http://localhost/PWA_Project/fetch_mods_script/website/pages/fetch.php?page=0">View as guest</a>
        </div>


    </div>

    <script>

        $(function(){

            $("form[name='register']").validate({

                rules:{

                    username:{

                        required: true,

                        minlength: 6,

                        maxlength: 40

                    },

                    password:{

                        required: true,

                        minlength: 8,

                        maxlength: 128

                    },


                },

                messages:{

                    username:{

                        required: "**Username must not be empty",

                        minlength: "**Username must have min 6 and max 15 characters",

                        maxlength: "**Username must have min 6 and max 15 characters"

                    },

                    password:{

                        required: "**Password must not be empty",

                        minlength: "**Password must be 8 or more characters",

                        maxlength: "**Password must be less than 128 characters"

                    },

    
                },

                submitHandler: function(form){
                    form.submit();
                }

            });

        });

    </script>


</body>
</html>