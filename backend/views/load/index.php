<?php

use common\models\Load;
use common\models\LoadCity;
use kartik\icons\Icon;
use nterms\pagesize\PageSize;
use odaialali\yii2toastr\ToastrAsset;
use odaialali\yii2toastr\ToastrFlash;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var array $countries */
/** @var array $loadTypes */
/** @var ActiveDataProvider $dataProvider */

ToastrAsset::register($this);
Icon::map($this, Icon::FI);
$this->title = Yii::t('seo', 'TITLE_ADMIN_LOADS');
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(['id' => 'toastr-pjax']);
    echo ToastrFlash::widget([
        'options' => [
            'closeButton' => true,
            'debug' => false,
            'newestOnTop' => true,
            'progressBar' => false,
            'positionClass' => 'toast-top-center',
            'preventDuplicates' => true,
            'showDuration' => 0, // how long it takes to show the alert in milliseconds
            'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
            'timeOut' => 45000, // how long the alert must be visible to user in milliseconds
            'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
            'onShown' => 'function() { ' .
                'var containerWidth = $(".content").width();' .
                '$(".alert-container").append($("#toast-container"));' .
                '$(".toast").css("width", containerWidth, "important");' .
                '$(".toast").css("width", "+=70px");' .
                '}',
        ]
    ]);
Pjax::end();

$this->registerJs(
    'var actionLoadAdvForm = "' . Url::to(['load/load-adv-form', 'lang' => Yii::$app->language]) . '";' .
    'var actionLoadOpenContactsForm = "' . Url::to(['load/open-contacts-form', 'lang' => Yii::$app->language]) . '";',
    View::POS_BEGIN);
?>

<div class="load-index">
    <div class="widget widget-loads-list">
        <div class="widget-heading">
            <span id="IA-C-1a"><?php echo Yii::t('element', 'IA-C-1a'); ?></span>
        </div>
        
        <div class="widget-content">
            <?php echo Yii::$app->controller->renderPartial('/load/filtration/form', [
                'load' => $load,
                'loadCity' => $loadCity,
                'loadTypes' => $loadTypes,
                'countries' => $countries,
                'action' => Url::to([
                    'load/index',
                    'lang' => Yii::$app->language,
                ]),
            ]); ?>
            
            <div class="text-right page-size-dropdown select">
                <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                <?php echo PageSize::widget([
                    'label' => Yii::t('element', 'A-C-324'),
                    'defaultPageSize' => Load::SECOND_PAGE_SIZE,
                    'sizes' => Load::getPageSizes(),
                    'template' => '{label}{list}',
                    'options' => [
                        'id' => 'A-C-325',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-324',
                    ],
                ]); ?>
            </div>

            <?php echo Yii::$app->controller->renderPartial('/load/partial/list', [
                'dataProvider' => $dataProvider,
            ]); ?>
        </div>
    </div>
</div>
