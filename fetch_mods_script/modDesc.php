<?php 

    use simplehtmldom\HtmlDocument;
    use simplehtmldom\HtmlWeb;
    require_once 'vendor/autoload.php'; 

    if(isset($_POST["descLink"])){
        
        $httpClient = new HtmlWeb(); 
        $descResponse = $httpClient->load($_POST["descLink"]); 

        $modName = $descResponse->find('.title-label', 0)->innertext;     
        $description = $descResponse->find('.top-line', 0)->innertext;
        $images = $descResponse->find('.gallery-cell'); 
        $imageLinkArr = []; 
        foreach($images as $image){
            $imageLinkArr[] = $image->find('img', 0)->getAttribute('src');
        }

    }else die("Your site could not be reached!"); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modName ?></title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
        }

    </style>
</head>
<body>

    <h1><?php echo $modName; ?></h1>    
    <p><?php echo $description; ?></p>

    <div class="imageCarousel">
        <div class="flickerViewport">
            <div class="">


            </div>
        </div>
        <?php foreach($imageLinkArr as $imageLink): ?> 
            <img src="<?php echo $imageLink ?>" alt="<?php $modName . "pic" ?>">    
        <?php endforeach; ?>
    </div>

    <script>

    </script>

</body>
</html>
