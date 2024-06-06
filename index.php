<?php 

    
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
    
    <title>Register form</title>

    <style>

       

    </style>
</head>
<body>

    <div class="mainContent">

        <form action="index.php" method="POST" name="register" class="registerEntry">
            <img src="./fetch_mods_script/website/img/FS_logo.png"> 

            <label for="username">Username:</label>

            <input type="text" name="username" id="username">

            <label for="password">Password:</label>

            <input type="password" name="password" id="password">

            <label for="passworda">Repeat password:</label>

            <input type="password" name="passworda" id="passworda">

            <button type="submit" name="register" value="1">REGISTER</button>
            <button type="submit" name="login" value="1">LOGIN</button>

        </form>



    </div>



    <script>

        $(function(){

            $("form[name='register']").validate({

                rules:{

                    username:{

                        required: true,

                        minlength: 6,

                        maxlength: 15,

                    },

                    password:{

                        required: true,

                        minlength: 8,

                        maxlength: 128

                    },

                    passworda:{

                        required: true,

                        equalTo: '#password'

                    }

                },

                messages:{

                    username:{

                        required: "**Username must not be empty",

                        minlength: "**Username must have min 6 and max 15 characters",

                        maxlength: "**Username must have min 6 and max 15 characters",

                    },

                    password:{

                        required: "**Password must not be empty",

                        minlength: "**Password must be 8 or more characters",

                        maxlength: "**Password must be less than 128 characters"

                    },

                    passworda:{

                        required: "**Password must not be empty",

                        equalTo: "**Passwords have to be same",

                    }

                },

                submitHandler: function(form){
                    form.submit();
                }

            });

        });

    </script>


</body>
</html>