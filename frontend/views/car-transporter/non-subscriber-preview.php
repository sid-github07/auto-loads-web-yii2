<?php

use common\models\CarTransporter;
use yii\helpers\Url;

/** @var CarTransporter $carTransporter */
/** @var boolean $showInfo */
?>

<div class="search-results-load-code">
    <?php echo '#' . $carTransporter->code; ?>
</div>

<?php if ($showInfo === 'true'): ?>
    <div class="load-info"><?php echo Yii::t('element', 'C-T-8b') . ': ' . $carTransporter->quantity; ?></div>
<?php endif; ?>

<div class="row text-center nonsub-preview">
        <?php echo Yii::t('element', 'You do not have any active subscriptions or sufficient amount of credits <b>(required {0} credits)</b> to view this data', $creditsCost) ?>
        <div class="top-up-or-subscribe-buttons">
            <a href="<?php echo \yii\helpers\Url::to(['subscription/index', 'lang' => Yii::$app->language])?> "class="subscribe-btn">
            <span class="action-button-icon">
                <i class="fa fa-briefcase"></i>
            </span>
                <span class="action-button-label"><?php echo Yii::t('element', 'C-T-8k');?></span>
            </a>
            <a href="<?php echo Url::to(['subscription/', 'tab' => \frontend\controllers\SubscriptionController::TAB_CREDIT_TOP_UP_ORDER]);?>" class="top-up-btn">
            <span class="action-button-icon">
                <i class="fa fa-credit-card"></i>
            </span>
                <span class="action-button-label"><?php echo Yii::t('app', 'Buy Service Credits')?></span>
            </a>
        </div>
</div>