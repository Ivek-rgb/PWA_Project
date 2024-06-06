
<?php 

    function returnCustomHeader($text, $headerType, $displayHr = false){
        ob_start();?>
            <div class="customHeaderText" style="display: flex; align-items:center; position:relative; margin: 10px 0px;">
                <div style="background-color: #81C332; height: 100%; width: 5px; position:absolute;"></div>
                <?= "<$headerType style=\"margin:0 5px 0 20px; padding: 5px 0;\">$text</$headerType>" ?>
                <?php if($displayHr):?>
                    <hr style="display: block; flex: 1;">
                <?php endif;?>
            </div>
        <?php
        return ob_get_clean(); 
    }

?>


