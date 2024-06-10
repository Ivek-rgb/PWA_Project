<?php 

    function shearFileLocation($location, $returnFileName = FALSE, $splitExtension = FALSE){
        $splitArr = preg_split("/\/+(?!.*\/.*$)/", $location); 
        if(count($splitArr) == 1) return $splitArr[0]; 
        if(!$returnFileName) return $splitArr[0];
        else{
            if(!$splitExtension) return $splitArr[1];
            else{
                $secondSplit = preg_split("/\.(?!.*\..*$)/", $splitArr[1]);
                return $secondSplit;  
            }   
        } 
    }

    function checkForFormats($strOfFormats, $fileName, $allowTheese = TRUE){
        $splitArr = preg_split("/\.(?!.*\..*$)/", $fileName);
        if(count($splitArr) < 2)
            return false; 
        echo var_dump($splitArr);
        if((str_contains($strOfFormats, strtolower($splitArr[1]))) xor $allowTheese){
            die("You are uploading a potentional risk to the site. CEASE ACTIVITY NOW!");
        }
    }

?>