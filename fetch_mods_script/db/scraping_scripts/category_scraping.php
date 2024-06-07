<?php 

    require_once "../../website/vendor/autoload.php"; 
    require_once "../database_functions/db_functions.php"; 
    use simplehtmldom\HtmlDocument;
    use simplehtmldom\HtmlWeb;

    // also need a further update to this so it can search newer mods and replace older ones 

    function testOnMatoBipa(){

        $httpClient = new \simplehtmldom\HtmlWeb(); 
        $collectionOfMods = 'https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2017';

        $loadedSite  = $httpClient->load($collectionOfMods);  

        echo var_dump($loadedSite->find(".top-line",0)->find(".table-game-info")); 

        $detailsTable = $loadedSite->find('.table-game-info', 0)->find('.table-row');

        echo var_dump($detailsTable); 
    }

    function scrapeCategoriesFromSite(){

        $httpClient = new \simplehtmldom\HtmlWeb(); 
        $collectionOfMods = 'https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2017&filter=latest&page='; 
        $i = 0;

        $totoMods = 0; 

        for(;; $i++){
            $response = $httpClient->load($collectionOfMods . $i); 

            $allModDivs = $response->find('.mod-item');
            foreach($allModDivs as $modDivs){
                
                $imageAttr = $modDivs->find('img')[0]->getAttribute('src'); 
                $textContent = $modDivs->find('.mod-item__content')[0];
                $modName = $textContent->find('h4')[0]->innertext; 
                $modAuthors = str_replace("By: ", "", $textContent->find('p')[0]->plaintext); 
                $furtherScrapingLink = $modDivs->find('.button-buy')[0]->getAttribute('href'); 

                echo $modName . "<br>"; 

                if(!preg_match("/mod_id/", $furtherScrapingLink)) continue; 
    
                $descResponse = $httpClient->load("https://www.farming-simulator.com/" . $furtherScrapingLink);
                $imageLinkArr = []; 
            
                $description = $descResponse->find('.top-line')[0]->innertext;
                $images = $descResponse->find('.gallery-cell'); 
    
                foreach($images as $image){
                    $imageLinkArr[] = $image->find('img')[0]->getAttribute('src');
                }

                $detailsTable = $descResponse->find('.table-game-info');

                if(count($detailsTable) <= 0){
                    continue; 
                }

                $detailsTable = $detailsTable[0]->find('.table-row');
                $modGame = $detailsTable[0]->find('.table-cell')[1]->plaintext; 
                $modManufacturer = $detailsTable[1]->find('.table-cell')[1]->plaintext;
                $modCategory = $detailsTable[2]->find('.table-cell')[1]->plaintext;
                $modVersion = $detailsTable[5]->find('.table-cell')[1]->plaintext;  
                $downloadLink = $descResponse->find('.download-box')[0]->find('.button')[0]->getAttribute('href'); 
    
                $parsedImages = implode(" [:|:] ", $imageLinkArr); 
    
                $modMD5Hash = hash("md5", $modName . $modAuthors);
    
                $host = '127.0.0.1:8111';
                $db = 'pwa_project';
                $user = 'root';
                $pass = '';
                $charset = 'utf8mb4';
    
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

                $barebonesConnection = openConnection();
                // failsafe break (touch on your own risk)
                $result = mysqli_query($barebonesConnection, "SELECT mods_brief.*, mod_version AS numberOf FROM mods_brief INNER JOIN mods_detailed ON mods_brief.id = mods_detailed.mod_id WHERE mod_hash LIKE '$modMD5Hash'");
                $mod = mysqli_fetch_assoc($result);
                mysqli_free_result($result); 
                if($mod != null && $mod["mod_version"] == $modVersion){
                    exit("This would be it folkorinos!"); 
                }elseif($mod != null){
                    try{
                        $pdo = new PDO($dsn, $user, $pass);
                        $pdo->beginTransaction(); 

                        $statement = $pdo->prepare("
                            UPDATE mods_brief
                            SET
                                mod_name = :mod_name,
                                mod_author = :mod_author,
                                mod_hash = :mod_hash,
                                mod_thumbnail = :mod_thumbnail,
                                mod_game = :mod_game,
                            WHERE
                                id = :id
                        ");
                        $statement->bindParam(':id', $id, PDO::PARAM_INT);
                        $statement->execute(["mod_name" => $modName, "mod_author" => $modAuthors
                    , "mod_hash" => $modMD5Hash, "mod_thumbnail" => $imageAttr, "mod_game" => $modGame]);

                        
                        $statement = $pdo->prepare("
                        UPDATE mods_detailed
                        SET
                            mod_desc = :mod_desc,
                            mod_imgs = :mod_imgs,
                            mod_version = :mod_version,
                            mod_link = :mod_link,
                            mod_manufacturer = :mod_manufacturer
                        WHERE
                            mod_id = :mod_id
                        ");
                        $statement->bindParam(':mod_id', $mod["id"], PDO::PARAM_INT);
                        $statement->execute(["mod_desc" => $description, "mod_imgs" => $parsedImages, 
                        "mod_version" => $modVersion, "mod_link" => $downloadLink, "mod_manufacturer" => $modManufacturer]);
        
                        $pdo->commit(); 
                        continue; 
                    }catch(Exception $e){
                        echo $e->getMessage(); 
                        $pdo->rollBack(); 
                    }
                }
                mysqli_close($barebonesConnection);

                try{
                    
                    $pdo = new PDO($dsn, $user, $pass);
                    $pdo->beginTransaction(); 
                    
                    $statement = $pdo->prepare("SELECT id FROM mods_category WHERE category_name LIKE :mod_category"); 
                    $statement->execute(["mod_category" => $modCategory]); 

                    $result = $statement->fetch(PDO::FETCH_ASSOC); 
                    if($result == false){
                        $statement = $pdo->prepare("INSERT INTO mods_category (category_name) VALUES (:category_name)");
                        $statement->execute(["category_name" => $modCategory]);
                        $modCategory = $pdo->lastInsertId(); 
                    }else $modCategory = $result["id"]; 
                    
                    $statement = $pdo->prepare("INSERT INTO mods_brief 
                    (mod_name, mod_author, mod_hash, mod_thumbnail, mod_game, mod_category)
                    VALUES (:mod_name, :mod_author, :mod_hash, :mod_thumbnail, :mod_game, :mod_category)");
    
                    $statement->execute(["mod_name" => $modName, "mod_author" => $modAuthors
                    , "mod_hash" => $modMD5Hash, "mod_thumbnail" => $imageAttr, "mod_game" => $modGame, "mod_category" => $modCategory]);
    
                    $latestModId = $pdo->lastInsertId();
    
                    $statement = $pdo->prepare("INSERT INTO mods_detailed (mod_id, mod_desc, mod_imgs, mod_version, mod_link, mod_manufacturer) 
                    VALUES (:mod_id, :mod_desc, :mod_imgs, :mod_version, :mod_link, :mod_manufacturer)");
    
                    $statement->execute(["mod_id" => $latestModId, "mod_desc" => $description, "mod_imgs" => $parsedImages, 
                    "mod_version" => $modVersion, "mod_link" => $downloadLink, "mod_manufacturer" => $modManufacturer]);
    
                    $pdo->commit(); 

                    echo "Mod written succesfully!<br>"; 
                    $totoMods++; 
    
                }catch(Exception $e){
                    echo $e->getMessage(); 
                    $pdo->rollBack(); 
                }
                
            }

        }
        echo "<br> Total number of mods scraped and written: " . $totoMods; 
    }

    // used to find and scrape categories [deprecated] 
    function scrapeAndWriteCategories(){

        $httpsLink = new \simplehtmldom\HtmlWeb(); 
        $response = $httpsLink->load("https://www.farming-simulator.com/mods.php?lang=en&country=us&title=fs2022&filter=plows&page=0#"); 

        $categoryDiv = $response->find(".tabs-mods-category-list")[0]->find("li"); 

        $arrOfCategoires = []; 

        foreach($categoryDiv as $category){
            array_push($arrOfCategoires, $category->plaintext); 
        }

        $connection = openConnection(); 

        $newArr = array_filter($arrOfCategoires, function($element) use (&$connection){
            return !intval(mysqli_fetch_column(mysqli_query($connection, "SELECT COUNT(id) FROM mods_category WHERE category_name LIKE '$element'"), 0));
        });

        $counter = 0; 
        foreach($newArr as $category){
            $counter++; 
            echo $counter .  $category . "<br>"; 
        }
        
        mysqli_close($connection); 
    }

    //scrapeAndWriteCategories(); 
    testOnMatoBipa(); 

?>



