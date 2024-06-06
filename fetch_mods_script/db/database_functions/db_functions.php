<?php 

    class CouldNotProcessQueryException extends Exception{

        public function __construct($message = "Query could not be processed", $code = -84){
            parent::__construct($message, $code);
        }

    } 

    class CouldNotEstablishConnectionException extends Exception{

        public function __construct($message = "Could not establish connection to server", $code = 500){
            parent::__construct($message, $code);
        }

    }

    function openConnection(){
        $connection = mysqli_connect("localhost:8111", "root", "", "pwa_project") or die("Could not establish connection to database"); 
        if($connection === FALSE){
            throw new CouldNotEstablishConnectionException();
        } 
        return $connection; 
    }

    function establishOnNull(&$connection){
        return isset($connection) ? $connection : openConnection();  
    }

    function appendLogicalQuery($inputQuery, $filterPart, $logicalOperator, $didNotCheckWhere = TRUE){
        if($didNotCheckWhere){
            $containsWhere = preg_match("/WHERE/", $inputQuery); 
            if(!$containsWhere)
                return "$inputQuery WHERE $filterPart"; 
        }
        return "$inputQuery $logicalOperator $filterPart"; 
    }

    function appendAndOpereator($inputQuery, $filterPart){
        return appendLogicalQuery($inputQuery, $filterPart, "AND"); 
    }
    
    function appendOrOpereator($inputQuery, $filterPart){
        return appendLogicalQuery($inputQuery, $filterPart, "OR"); 
    }

    function handleFilters($inputQuery, $limitStart = 0, $limitExtend = NULL, $liveConnection){
        $paramArr = [];
        $str = "";
        if(isset($_GET["filter"])){
            $filterParams=explode(":", $_GET["filter"]); 
            $inputQuery = appendAndOpereator($inputQuery, "$filterParams[0] LIKE ?");
            $filterValue = $filterParams[1]; 
            
            if($filterParams[0] == "mod_author")
                $filterValue = "%" . htmlspecialchars($filterValue) . "%";

            array_push($paramArr, $filterValue);
            $str .= "s"; 
        }
        if(isset($_GET["search"]) and strlen($_GET["search"]) > 0){
            $inputQuery = appendAndOpereator($inputQuery, "mod_name LIKE ?");
            array_push($paramArr, '%' . htmlspecialchars($_GET["search"]) . '%');
            $str .= "s"; 
        }
        if(isset($limitExtend))
            $inputQuery .= " LIMIT $limitStart, $limitExtend";
        $preparedStatement = $liveConnection->prepare($inputQuery);
        if(strlen($str) > 0) 
            $preparedStatement->bind_param($str, ...$paramArr); 
        $preparedStatement->execute();        
        return $preparedStatement->get_result() ; 
    }

    function fetchAssocArrStuff($query, &$connection=NULL, $turnToArr = FALSE, $preparedStatement = FALSE){
        $outerOppened = isset($connection);
        $connection = establishOnNull($connection);
        if(!$preparedStatement)
            $result = mysqli_query($connection, $query);
        else $result = $query; 
        if($result === FALSE){
            if(!$outerOppened)
                mysqli_close($connection);
            throw new CouldNotProcessQueryException();
        }
        if(!$outerOppened)
            mysqli_close($connection);
        $returnVar = NULL; 
        if($turnToArr){
            $returnVar = []; 
            while($row = mysqli_fetch_assoc($result))
                array_push($returnVar, $row);
        }else $returnVar = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $returnVar; 
    }

    function deleteOnId($modId, $tableName, &$connection = NULL){
        $outerOppened = isset($connection);
        $connection = establishOnNull($connection);
        $result =  mysqli_query($connection, "DELETE FROM $tableName WHERE id = $modId");
        if($result === FALSE){
            if(!$outerOppened)
                mysqli_close($connection); 
            throw new CouldNotProcessQueryException();
        }  
        if(!$outerOppened)
            mysqli_close($connection); 
        return $result; 
    }

    function fetchAgregatedColFromDB($query, $colAlias, $getterFunction ,$connection = NULL, $preparedStatement = FALSE){
        return $getterFunction(fetchAssocArrStuff($query, $connection, FALSE, $preparedStatement)[$colAlias]);
    }

    function getModCountFromServer($connection = NULL){
        $outerOppened = isset($connection); 
        $connection = establishOnNull($connection); 
        $queryWithFiltersIncluded = handleFilters("SELECT COUNT(mods_brief.id) AS numOfMods FROM mods_brief INNER JOIN mods_detailed ON mods_brief.id = mods_detailed.mod_id",0, NULL, $connection);
        return fetchAgregatedColFromDB($queryWithFiltersIncluded, "numOfMods", function($result) { return intval($result);}  ,$connection, TRUE);
        if(!$outerOppened)
            mysqli_close($connection); 
    }

    function getBriefPartsFromModServer($limitStart, $limitExtend, &$connection = NULL){
        $outerOppened = isset($connection); 
        $connection = establishOnNull($connection); 
        $inputQuery = "SELECT mod_name, mod_author, mod_thumbnail, mods_brief.id, mod_manufacturer FROM mods_brief INNER JOIN mods_detailed ON mods_brief.id = mods_detailed.mod_id";
        $inputQuery = handleFilters($inputQuery, $limitStart, $limitExtend, $connection);
        return fetchAssocArrStuff($inputQuery, $connection, TRUE, TRUE);
        if(!$outerOppened)
            mysqli_close($connection); 
    }

    function getSpecificMod($modId, $connection = NULL){
        return fetchAssocArrStuff("SELECT mods_brief.*, mods_detailed.mod_id, mods_detailed.mod_desc, mods_detailed.mod_imgs, mods_detailed.mod_version, mods_detailed.mod_link, mods_detailed.mod_manufacturer, mods_category.category_name FROM mods_brief INNER JOIN mods_detailed ON mods_brief.id = mods_detailed.mod_id INNER JOIN mods_category ON mods_category.id = mods_brief.mod_category WHERE mods_brief.id = $modId", $connection, FALSE); 
    }

    function fetchLatestId($tableName, &$connection){
        return fetchAgregatedColFromDB("SELECT MAX(id) AS latestId FROM $tableName", "latestId", function($result) { return intval($result);}, $connection);
    }

    function fetchAllCategories(&$connection = NULL){
        return fetchAssocArrStuff("SELECT * FROM mods_category", $connection, TRUE);
    }

    function getRandomFeaturedMod($connection = NULL){
        $maxId = fetchLatestId("mods_brief", $connection); 
        return getSpecificMod(rand(1, $maxId), $connection);
    }

    function updateMod($modId, $modName, $author, $modHash, $modThumbnail, $modGame, $modCategory, $modDesc, $modImg, $modVersion, $modLink, $modManufacturer, $connection = NULL){
        $outerOppened = isset($connection);
        $connection = establishOnNull($connection);  
        $query = "UPDATE mods_brief, mods_detailed SET mod_name = ?, mod_author = ?, mod_hash = ?, mod_thumbnail = ?, mod_game = ?, mod_category = ?, mod_desc = ?, mod_imgs = ?, mod_version = ?, mod_link = ?, mod_manufacturer = ?  WHERE mods_brief.id = $modId AND mods_detailed.mod_id = $modId";
        $statement = $connection->prepare($query);
        $statement->bind_param("sssssisssss", $modName, $author, $modHash, $modThumbnail, $modGame, $modCategory, $modDesc, $modImg, $modVersion, $modLink, $modManufacturer); 
        $statement->execute();  
        if(!$outerOppened)
            mysqli_close($connection); 
    }

    function uploadMod($modName, $author, $modHash, $modThumbnail, $modGame, $modCategory, $modDesc, $modImg, $modVersion, $modLink, $modManufacturer, $connection = NULL){
        $outerOppened = isset($connection);
        $connection = establishOnNull($connection);

        $host = '127.0.0.1:8111';
        $db = 'pwa_project';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $modId = NULL; 

        try{
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->beginTransaction(); 
            
            $statement = $pdo->prepare("INSERT INTO mods_brief 
            (mod_name, mod_author, mod_hash, mod_thumbnail, mod_game, mod_category)
            VALUES (:mod_name, :mod_author, :mod_hash, :mod_thumbnail, :mod_game, :mod_category)");

            $statement->execute(["mod_name" => $modName, "mod_author" => $author
            , "mod_hash" => $modHash, "mod_thumbnail" => $modThumbnail, "mod_game" => $modGame, "mod_category" => $modCategory]); 

            $modId = $pdo->lastInsertId();
            
            $statement = $pdo->prepare("INSERT INTO mods_detailed (mod_id, mod_desc, mod_imgs, mod_version, mod_link, mod_manufacturer) 
            VALUES (:mod_id, :mod_desc, :mod_imgs, :mod_version, :mod_link, :mod_manufacturer)"); 
            
            $statement->execute(["mod_id" => $modId, "mod_desc" => $modDesc, "mod_imgs" => $modImg, 
            "mod_version" => $modVersion, "mod_link" => $modLink, "mod_manufacturer" => $modManufacturer]); 

            $pdo->commit(); 
        }catch(Exception $e){
            echo $e->getMessage(); 
            $pdo->rollBack(); 
        }

        if(!$outerOppened)
            mysqli_close($connection);

        return $modId; 
    }


    
?>