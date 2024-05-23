<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            flex-flow: wrap;
        }
        .modDiv{
            display: flex;
            flex-direction: column;
            width: 15%;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>


<?php 
    
    // this here is used to import dependencies
    require_once 'vendor/autoload.php'; 
    require_once 'webg_functions.php'; 
    use simplehtmldom\HtmlDocument;

    $httpClient = new \simplehtmldom\HtmlWeb();
    //$response = $httpClient->load('https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2022&filter=latest&page=0');

    // here we actually get the container stuff

    // scraping the shite of the first documents
    //$divItself = new HtmlDocument();
    //$divItself->load($response->find('.mod-item', 0)->outertext); 

    // fetching attributes themselves
    //echo $divItself->find('img', 0)->getAttribute('src') . '';

    //outertext -- it's for displaying full DOM arhitecture of the shite

    // here we then do some test run's to see what's up currently 

    // we scrape 2022 version of Farming simulator mods 
    $collectionOfMods = 'https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2022&filter=latest&page='; 
    $indexPage = 0; 
    
    // here then we do some scraping buisness 
    
    $i = 0; 
    for(; $i < 5; $i++){
        $response = $httpClient->load($collectionOfMods . $i); 
        if($response === NULL) break; // failsafe for latter when we are in 'production' 
        $modDivs = $response->find('.mod-item');  
        foreach($modDivs as $mod){
            echo returnDeFangedModDiv($mod); 
        } 
    }
?>
</body>
</html>

