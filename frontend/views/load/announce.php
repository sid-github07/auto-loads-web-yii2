<?php

use common\models\Load;
use common\models\LoadCity;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var integer $serviceCredits */
/** @var integer $subscriptionCredits */
/** @var string $subscriptionEndTime */
/** @var array $advertDayList */
/** @var array $openContactsDayList */
/** @var array $openContactsCost */

?>

<div class="load-announce">
    <h1 id="IA-C-1">
        <?php echo Yii::t('element', 'IA-C-1'); ?>
        <?php if (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription()): ?>
            <span class="free-announcement-per-week">
                <?php echo Yii::t('element', 'IA-C-1c'); ?>
            </span>
        <?php endif; ?>
    </h1>

    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>
    <?php echo Yii::$app->controller->renderPartial('_announce-load-form', [
        'cities' => [],
        'load' => $load,
        'loadCity' => $loadCity,
        'serviceCredits' => $serviceCredits,
        'subscriptionCredits' => $subscriptionCredits,
        'subscriptionEndTime' => $subscriptionEndTime,
        'openContactsCost' => $openContactsCost,
        'advertDayList' => $advertDayList,
        'openContactsDayList' => $openContactsDayList,
    ]); ?>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
