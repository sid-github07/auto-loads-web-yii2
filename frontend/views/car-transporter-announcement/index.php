<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var integer $serviceCredits */
/** @var integer $subscriptionCredits */
/** @var string $subscriptionEndTime */
/** @var array $advertDayList */
/** @var array $openContactsDayList */
/** @var array $openContactsCost */

$this->title = Yii::t('seo', 'TITLE_CAR_TRANSPORTER_ANNOUNCEMENT');
?>

<div class="car-transporter-announcement">
    <h1 id="C-T-27">
        <?php echo Yii::t('element', 'C-T-27'); ?>
    </h1>

    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <?php echo Yii::$app->controller->renderPartial('announcement-form', compact(
        'carTransporter', 'carTransporterCity', 'serviceCredits', 
        'subscriptionCredits', 'subscriptionEndTime', 
        'openContactsCost', 'advertDayList', 'openContactsDayList', 'load')); ?>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);