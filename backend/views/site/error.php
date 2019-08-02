<?php

/* @var $this View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->context->layout = 'login';
$this->title = $name;
?>
<div class="site-error">
    <div class="error-page-container text-center">
        <h1 class="error-number"><?php echo Yii::$app->errorHandler->exception->statusCode; ?></h1>
        
        <div class="error-message"><?= nl2br(Html::encode($message)); ?></div>
        
        <div class="error-sub-message">
            <p><?php echo Yii::t('alert', 'ERROR_MESSAGE_OCCURRED_ERROR'); ?></p>
            
            <p><?php echo Yii::t('alert', 'ERROR_MESSAGE_CONTACT_US'); ?></p>
        </div>
        
        <?php if(Yii::$app->errorHandler->exception->statusCode === 403) : ?>
            <a href="<?php echo Url::to(['site/logout', 'lang' => Yii::$app->language]); ?>" 
               class="primary-button-alt home-btn"
               data-method="post"
            >
                <?php echo Icon::show('sign-in', '', Icon::FA) . Yii::t('text', 'LOGIN'); ?>
            </a>
        <?php else: ?>
            <a href="<?php echo Url::to(['site/index', 'lang' => Yii::$app->language, ]); ?>" 
               class="primary-button-alt home-btn"
            >
                <?php echo Yii::t('text', 'TO_HOME'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
