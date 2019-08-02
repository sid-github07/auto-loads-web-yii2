<?php

use yii\web\View;

/** @var View $this */
/** @var string $email */

$this->title = Yii::t('seo', 'TITLE_SIGN_UP_SUCCESSFUL');
?>

<div class="site-sign-up-successful">
    <h1 id="RG-S-1">
        <?php echo Yii::t('element', 'RG-S-1'); ?>
    </h1>
    
    <div class="step-alert-successful-container">
        <img id="RG-S-2"
             class="success-btn-image"
             src="<?php echo Yii::getAlias('@web') . '/images/success-btn.png'; ?>" 
             alt="RG-S-2">
        
        <span id="RG-S-3" class="step-alert-message">
            <?php echo Yii::t('element', 'RG-S-3', [
                'email' => $email,
            ]); ?>
        </span>
    </div>
</div>