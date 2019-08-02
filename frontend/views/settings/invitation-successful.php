<?php

use yii\web\View;

/** @var View $this */
/** @var string $email */

$this->title = Yii::t('seo', 'TITLE_INVITATION_SUCCESSFUL');
?>

<div class="settings-invitation-successful">
    <h1 id="V-C-66n">
        <?php echo Yii::t('element', 'V-C-66n'); ?>
    </h1>
    
    <div class="step-alert-successful-container">
        <img id="V-C-66o"
             class="success-btn-image"
             src="<?php echo Yii::getAlias('@web') . '/images/success-btn.png'; ?>" 
             alt="V-C-66o">
        
        <span id="V-C-66p" class="step-alert-message">
            <?php echo Yii::t('element', 'V-C-66p', [
                'email' => $email,
            ]); ?>
        </span>
    </div>
</div>
