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
        if((str_contains($strOfFormats, strtolower(preg_split("/\.(?!.*\..*$)/", $fileName)[1]))) xor $allowTheese){
            die("You are uploading a potentional risk to the site. CEASE ACTIVITY NOW!");
        }
    }

?>