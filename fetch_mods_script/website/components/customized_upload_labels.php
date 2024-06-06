<?php

    function createLabelsAndInputFields($inputFieldName, $inputFieldType, $inputFieldLabelText, $defaultValue=NULL, $required=FALSE, $onchangeFunctionCall = NULL, $componentInputId = NULL){
        ob_start();?>
            <div class="customizedInput">
                <label class="customizedLabels" for="<?=$inputFieldName?>"><?= $inputFieldLabelText ?></label>
                <?php if(preg_match("/^textarea$/",$inputFieldType)): ?>
                    <textarea name="<?= $inputFieldName ?>" id="<?= $inputFieldName ?>" <?php if(!$required) echo "required"?>><?= $defaultValue ?></textarea>
                <?php else: ?>   
                    <input class="customizedInputs" id="<?php if(isset($componentInputId)) echo $componentInputId; else echo $inputFieldName;?>" name="<?= $inputFieldName ?>" type="<?=$inputFieldType?>" value="<?php if(isset($defaultValue)) echo $defaultValue; ?>" <?php if($required) echo "required"?> onchange="<?php if(isset($onchangeFunctionCall)) echo $onchangeFunctionCall;?>">
                <?php endif; ?>
            </div>
        <?php $result = ob_get_clean(); 
        return $result; 
    }

?>