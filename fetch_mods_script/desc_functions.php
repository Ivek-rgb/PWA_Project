<script>
    function expandColapseParagraph(paragraphNumber){
        
        let exactParagraph = document.getElementsByClassName("secondPart")[paragraphNumber - 1];
        let textChange = document.getElementsByClassName("expandCollapse")[paragraphNumber - 1]; 

        if(exactParagraph.style.display == "none"){
            exactParagraph.style.display = "block"; 
            textChange.textContent = "Read less"; 
        }else{
            exactParagraph.style.display = "none";
            textChange.textContent = "Read more";  
        }
    }
</script>

<style>
    .expandCollapse{
        color: green;
    }

    .expandCollapse:hover{
        color: #81C332;
        transition: 0.7s;
    }
</style>

<?php 
    // this here will keep track of all expendable trackers on site should there arise need to be more 
    $expendablePCounter = 0; 
    function returnShearedParagraph($textToReturn, $maxRows = 10){
        global $expendablePCounter;
        $expendablePCounter++;   
        $nwLineCounter = 0; 
        $pattern = "/<br\s*\/?>/i";
        $result = preg_replace_callback($pattern, function($matches) use (&$nwLineCounter, &$maxRows){
            $nwLineCounter++; 
            if($nwLineCounter == $maxRows){
                return '</span><span class="secondPart" style="display:none;">' . $matches[0]; 
            }
            return $matches[0]; 
        }, $textToReturn);
        ob_start();?>
            <p class="descItself">
                <span>
                    <?= $result ?>
                </span>
            </p>
            <?php if($nwLineCounter >= $maxRows):?>
            <p class="expandCollapse" style="text-decoration: underline; font-weight:bolder; cursor:pointer;"
            onclick="expandColapseParagraph(<?=$expendablePCounter;?>)"
            >Read more</p>
            <?php endif;?>
        <?php
        $return = ob_get_clean();
        return $return; 
    }
?>