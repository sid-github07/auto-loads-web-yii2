<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use kartik\icons\Icon;
use nterms\pagesize\PageSize;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var ActiveDataProvider $dataProvider */

Icon::map($this, Icon::FI);
$this->title = Yii::t('seo', 'TITLE_CAR_TRANSPORTERS');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="car-transporter-index">
    <div class="widget widget-car-transporters-list">
        <div class="widget-heading">
            <span id="C-T-95">
                <?php echo Yii::t('element', 'C-T-95'); ?>
            </span>
        </div>

        <div class="widget-content">
            <?php
            echo Yii::$app->controller->renderPartial('/car-transporter/filtration', [
                'carTransporter' => $carTransporter,
                'carTransporterCity' => $carTransporterCity,
                'action' => Url::to([
                    'car-transporter/index',
                    'lang' => Yii::$app->language,
                ]),
            ]); ?>

                <div class="text-right page-size-dropdown select">
                <span class="select-arrow">
                    <i class="fa fa-caret-down"></i>
                </span>
                    <?php echo PageSize::widget([
                        'label' => Yii::t('element', 'C-T-104'),
                        'defaultPageSize' => CarTransporter::PAGE_SIZE_SECOND,
                        'sizes' => CarTransporter::getPageSizes(),
                        'template' => '{label}{list}',
                        'options' => ['id' => 'C-T-105'],
                        'labelOptions' => ['id' => 'C-T-104'],
                    ]); ?>
                </div>

            <?php echo Yii::$app->controller->renderPartial('/car-transporter/table', compact('dataProvider'));
            ?>
        </div>
    </div>
</div>

<?php
Modal::begin([
    'id' => 'car-transporter-previews-modal',
    'header' => Yii::t('element', 'C-T-115'),
]);

    Pjax::begin(['id' => 'car-transporter-previews-pjax']);
    Pjax::end();

Modal::end();
Modal::begin([
    'id' => 'adv-load-modal',
    'header' => Yii::t('element', 'adv_modal_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'adv-load-modal-pjax']);
Pjax::end();
Modal::end();
Modal::begin([
    'id' => 'open-contacts-modal',
    'header' => Yii::t('element', 'open_contacts_modal_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'open-contacts-modal-pjax']);
Pjax::end();
Modal::end();
$this->registerJs(
    'var actionPreviews = "' . Url::to([
        'car-transporter/previews',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionLoadAdvForm = "' . Url::to(['car-transporter/load-adv-form', 'lang' => Yii::$app->language]) . '";' .
    'var actionTransporterOpenContactsForm = "' . Url::to(['car-transporter/open-contacts-form', 'lang' => Yii::$app->language]) . '";' .
    'var actionContactInfoPreview = "' . Url::to([
        'car-transporter/contact-info-preview',
        'lang' => Yii::$app->language,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/table.js', ['depends' => [JqueryAsset::className()]]);