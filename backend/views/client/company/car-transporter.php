<?php

/** @var Company $company */
/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var ActiveDataProvider $dataProvider */

use backend\controllers\ClientController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\Company;
use common\models\Load;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

echo Yii::$app->controller->renderPartial('/car-transporter/filtration', [
    'carTransporter' => $carTransporter,
    'carTransporterCity' => $carTransporterCity,
    'action' => Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $company->id,
        'tab' => ClientController::TAB_COMPANY_CAR_TRANSPORTERS,
    ]),
]); ?>

<div class="text-right page-size-dropdown select">
    <span class="posts-per-page-select">
        <label id="L-T-14" class="page-size-filter-label">
            <?php echo Yii::t('element', 'C-T-104'); ?>
        </label>
        <?php echo Html::dropDownList('load-per-page', Yii::$app->request->get('car-transporter-per-page'), Load::getPageSizes(), [
            'id' => 'C-T-105',
            'onchange' => 'changeCarTransporterPageNumber(event, this);',
        ]); ?>
        <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
    </span>
</div>

<?php Pjax::begin(['id' => 'car-transporter-list-pjax']);
echo Yii::$app->controller->renderPartial('/car-transporter/table', compact('dataProvider'));
Pjax::end();
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
    'var actionLoadAdvForm = "' . Url::to([
        'car-transporter/load-adv-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionTransporterOpenContactsForm = "' . Url::to([
        'car-transporter/open-contacts-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionContactInfoPreview = "' . Url::to([
        'car-transporter/contact-info-preview',
        'lang' => Yii::$app->language,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/table.js', ['depends' => [JqueryAsset::className()]]);