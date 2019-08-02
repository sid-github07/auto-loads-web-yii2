<?php

use yii\web\View;

/** @var View $this */

$this->title = Yii::t('seo', 'TITLE_RESET_PASSWORD_SUCCESSFUL');
?>
<div class="step-alert-successful-container">
    <img id="SP-C-15"
         class="password-success-btn-image"
         src="<?php echo Yii::getAlias('@web') . '/images/password-reset-successful.png'; ?>"
         alt="SP-C-15"
    >
    
    <span id="SP-C-16" class="step-alert-message">
            <?php echo Yii::t('element', 'SP-C-16'); ?>
    </span>
</div>