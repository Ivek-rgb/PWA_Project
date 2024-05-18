<?php

    use simplehtmldom\HtmlDocument;
    require_once 'vendor/autoload.php'; 

    // this can later be used to 
    function returnDeFangedModDiv($DOMelem){

        $imageAttr = $DOMelem->find('img', 0)->getAttribute('src'); 
        $textContent = $DOMelem->find('.mod-item__content', 0);
        $modName = $textContent->find('h4', 0)->innertext; 
        $modAuthors = $textContent->find('p', 0)->innertext; 
        $furtherScrapingLink = $DOMelem->find('.button-buy', 0)->getAttribute('href'); 

        // GET will be used to fetch since it can be replicated using a link 
        // this will be good when hyperlinking filters
        ob_start(); 
        ?>
        <div class="modDiv">
            <img class="modBannerImage" src="<?php echo $imageAttr ?>" alt="<?php echo $modName . "pic" ?>">
            <h3 class="modName"><?php echo $modName ?></h3>
            <p class="authors"><?php echo $modAuthors ?></p>
            <form action="modDesc.php" method="GET">
                <input style="display: none;" name="descLink" type="text" value="<?php echo "https://www.farming-simulator.com/" . $furtherScrapingLink; ?>">
                <input type="submit" value="MORE INFO">
            </form>
        </div>
        <?php
        $returnHtml = ob_get_clean(); 
        return $returnHtml; 
    }


?>
