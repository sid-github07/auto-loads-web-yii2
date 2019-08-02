<?php

use frontend\controllers\MyAnnouncementController;
use odaialali\yii2toastr\ToastrAsset;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var string $tab
 * @var null|string $token
 * @var null|array $loadCities
 * @var null|array $loadCitiesNames
 * @var ActiveDataProvider $loadDataProvider
 * @var null|array $carTransporterCities
 * @var null|array $carTransporterCitiesNames
 * @var ActiveDataProvider $carTransporterDataProvider
 */

ToastrAsset::register($this);
$this->title = Yii::t('seo', 'TITLE_MY_ANNOUNCEMENTS');
if (Yii::$app->request->isAjax) {
    $this->title .= Yii::$app->params['titleEnding'];
}

Pjax::begin(['id' => 'toastr-pjax']);
echo ToastrFlash::widget([
    'options' => [
        'closeButton' => true,
        'debug' => false,
        'newestOnTop' => true,
        'progressBar' => false,
        'positionClass' => 'toast-top-center',
        'preventDuplicates' => true,
        'showDuration' => 0,
        'hideDuration' => 1000,
        'timeOut' => 45000,
        'extendedTimeOut' => 8000,
        'onShown' => new JsExpression(
            "function () {
                        $('.alert-container').append($('#toast-container'));
                    }"
        ),
    ],
]);
Pjax::end();
?>
    <div class="my-announcement-index">
        <h1>
            <?php echo $this->title; ?>
            <?php if (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription()): ?>
                <span class="free-announcement-per-week">
                    <?php echo Yii::t('element', 'IA-C-1c'); ?>
                </span>
            <?php endif; ?>
        </h1>
        <?php echo Tabs::widget([
            'navType' => 'nav-tabs tabs-navigation',
            'items' => [
                [
                    'label' => Yii::t('element', 'C-T-37a'),
                    'content' => Yii::$app->controller->renderPartial('my-loads', [
                        'token' => $token,
                        'cities' => $loadCities,
                        'citiesNames' => $loadCitiesNames,
                        'dataProvider' => $loadDataProvider,
                    ]),
                    'active' => $tab === MyAnnouncementController::TAB_MY_LOADS,
                    'linkOptions' => [
                        'id' => 'C-T-37a',
                        'title' => Yii::t('element', 'C-T-37a'),
                        'onclick' => 'changeTabUrl(event, "' . MyAnnouncementController::TAB_MY_LOADS . '", true)',
                    ],
                ],
                [
                    'label' => Yii::t('element', 'C-T-37b'),
                    'content' => Yii::$app->controller->renderPartial('my-car-transporters', [
                        'cities' => $carTransporterCities,
                        'citiesNames' => $carTransporterCitiesNames,
                        'dataProvider' => $carTransporterDataProvider,
                    ]),
                    'active' => $tab === MyAnnouncementController::TAB_MY_CAR_TRANSPORTERS,
                    'linkOptions' => [
                        'id' => 'C-T-37b',
                        'title' => Yii::t('element', 'C-T-37b'),
                        'onclick' => 'changeTabUrl(event, "' . MyAnnouncementController::TAB_MY_CAR_TRANSPORTERS . '", true)',
                    ],
                ],
            ],
        ]); ?>
    </div>
<?php
$this->registerJsFile(Url::base() . '/dist/js/site/url.js', ['depends' => [JqueryAsset::className()]]);
