<?php

use yii\web\View;

/** @var View $this */
/** @var string $email */

$this->title = Yii::t('seo', 'TITLE_PASSWORD_RESET_SENT');
?>

<div class="site-password-reset-sent">
    <h1 id="SP-C-6">
        <?php echo Yii::t('element', 'SP-C-6'); ?>
    </h1>
    
    <div class="step-alert-successful-container">
        <img id="SP-C-7"
             class="email-success-btn-image"
             src="<?php echo Yii::getAlias('@web') . '/images/email-sent.png'; ?>"
             alt="SP-C-7">
        
        <span id="SP-C-8" class="step-alert-message">
            <?php echo Yii::t('element', 'SP-C-8', [
                'email' => $email,
            ]); ?>
        </span>
    </div>
</div>