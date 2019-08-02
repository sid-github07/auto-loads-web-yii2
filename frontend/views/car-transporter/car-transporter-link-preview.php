<?php

use odaialali\yii2toastr\ToastrAsset;

/** @var string $carTransporterLink */

ToastrAsset::register($this);
?>

<div id="car-transporter-link-success-alert" class="alert-container clearfix hidden">
    <div id="toast-container" class="toast-top-center">
        <div class="toast toast-success" aria-live="polite" style="display: block;">
            <div class="toast-message"><?php echo Yii::t('element', 'C-T-8cc'); ?></div>
        </div>
    </div>
</div>

<div class="text-center">
    <textarea id="car-transporter-link-field" class="form-control form-group"><?php echo $carTransporterLink; ?></textarea>

    <button id="C-T-8dd" class="primary-button form-group" onclick="copyCarTransporterLinkToClipboard();">
        <?php echo Yii::t('element', 'C-T-8dd'); ?>
    </button>
</div>
