<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

$this->title = Yii::t('seo', 'TITLE_SITE_ERROR');
?>
<div class="site-error">
    <div class="error-message-wrap clearfix">
        <div class="error-message-wrapper row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <img id="error-image" 
                     src="<?php echo Yii::getAlias('@web') . '/images/error.png'; ?>" 
                />
            </div>

            <div class="error-message-container col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="error-404"><?php echo (isset(Yii::$app->errorHandler->exception->statusCode) ? Yii::$app->errorHandler->exception->statusCode : "500"); ?></div>
                <div class="error-main-message"><?= nl2br(Html::encode($message)) ?></div>
            </div>
        </div>
        
        <div class="error-sub-message-container clearfix">
            <p class="error-sub-message">
                <?php echo Yii::t('alert', 'ERROR_MESSAGE_OCCURRED_ERROR'); ?>
            </p>

            <p class="error-sub-message">
                <?php echo Yii::t('alert', 'ERROR_MESSAGE_CONTACT_US'); ?>
            </p>
        </div>
    </div>
</div>