<?php

use odaialali\yii2toastr\ToastrAsset;

/** @var string $loadLink */

ToastrAsset::register($this);
?>

<div id="load-link-success-alert" class="alert-container clearfix hidden">
    <div id="toast-container" class="toast-top-center">
        <div class="toast toast-success" aria-live="polite" style="display: block;">
            <div class="toast-message"><?php echo Yii::t('element', 'IA-C-55c'); ?></div>
        </div>
    </div>
</div>

<div class="text-center">
    <textarea id="load-link-field" class="form-control form-group"><?php echo $loadLink; ?></textarea>

    <button id="IA-C-55b" class="primary-button form-group" onclick="copyLoadLinkToClipboard();">
        <?php echo Yii::t('element', 'IA-C-55b'); ?>
    </button>
</div>
