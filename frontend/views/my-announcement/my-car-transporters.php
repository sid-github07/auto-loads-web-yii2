<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var View $this */
/** @var null|array $cities */
/** @var null|array $citiesNames */
/** @var ActiveDataProvider $dataProvider */

?>
<div class="my-car-transporters-index">
    <div class="search-row-wrapper">
        <div class="row">
            <div class="col-sm-3 col-xs-12">
                <div class="form-group car-transporter-activity-container">
                    <?php echo Html::dropDownList('carTransporterActivity', Yii::$app->request->get('car-transporter-activity', CarTransporter::ACTIVE), CarTransporter::getCarTransportersListActivities(), [
                        'id' => 'MK-C-22-car-transporter',
                        'class' => 'form-control',
                        'onchange' => "changeCarTransporterTypeShowing(this.value)",
                    ]); ?>
                </div>
            </div>
            <div class="col-sm-9 col-xs-12">
                <?php $form = ActiveForm::begin(['id' => 'my-car-transporters-filter-form']); ?>
                    <?php echo Yii::$app->controller->renderPartial('/site/partial/multiple-locations', [
                        'form' => $form,
                        'model' => new CarTransporterCity([
                            'scenario' => CarTransporterCity::SCENARIO_USER_FILTERS_MY_CAR_TRANSPORTERS,
                            'loadLocations' => $cities,
                        ]),
                        'attribute' => 'loadLocations',
                        'data' => $citiesNames,
                        'label' => '',
                        'labelOptions' => [],
                        'placeholder' => Yii::t('element', 'C-T-38'),
                        'id' => 'C-T-38',
                        'unloadId' => null,
                        'onchange' => 'filterMyCarTransporters(this)',
                        'url' => Url::to([
                            'site/filter-location',
                            'lang' => Yii::$app->language,
                        ]),
                    ]); ?>
                <?php ActiveForm::end(); ?>
            </div>    
        </div>
        
        <button id="C-T-39"
                class="primary-button input-button hidden-xs"
                onclick="renderCarTransporterAnnouncementForm()"
        >
            <?php echo Yii::t('element', 'C-T-39'); ?>
        </button>

        <button id="C-T-39a"
                class="primary-button visible-xs"
                onclick="renderCarTransporterAnnouncementForm()"
        >
            <?php echo Yii::t('element', 'C-T-39'); ?>
        </button>
    </div>

    <?php
        echo Yii::$app->controller->renderPartial('my-car-transporters-table-control-buttons', ['id' => 'C-T-45']);

        Pjax::begin(['id' => 'my-car-transporters-table-pjax']);
            echo Yii::$app->controller->renderPartial('my-car-transporters-table', compact('dataProvider'));
        Pjax::end();

        echo Yii::$app->controller->renderPartial('my-car-transporters-table-control-buttons', ['id' => 'C-T-64']);
    ?>
</div>

<?php
Modal::begin([
    'id' => 'announce-car-transporter-modal',
    'header' => Yii::t('element', 'C-T-43'),
    'size' => 'modal-lg',
]);

    Pjax::begin(['id' => 'announce-car-transporter-pjax']);
    Pjax::end();

Modal::end();

Modal::begin([
    'id' => 'remove-car-transporter-modal',
    'header' => Yii::t('text', 'CAR_TRANSPORTER_REMOVAL_CONFIRMATION_QUESTION'),
]); ?>

    <div class="delete-confirmation-text">
        <?php echo Yii::t('text', 'CAR_TRANSPORTER_REMOVAL_CONFIRMATION_TEXT'); ?>
    </div>

    <div class="delete-confirmation-buttons">
        <button id="remove-car-transporter-button-yes" class="success-btn delete-btn">
            <i class="icon-check"></i> <?php echo Yii::t('element', 'CONFIRM'); ?>
        </button>

        <button class="danger-btn delete-btn" data-dismiss="modal">
            <i class="icon-cross"></i> <?php echo Yii::t('element', 'CANCEL'); ?>
        </button>
    </div>

<?php
Modal::end();

Modal::begin([
    'id' => 'adv-transporter-modal',
    'header' => Yii::t('element', 'adv_modal_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'adv-transporter-modal-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'transporter-open-contacts-modal',
    'header' => Yii::t('element', 'open_contacts_modal_title'),
    'size' => 'modal-lg',
]);
    Pjax::begin(['id' => 'transporter-open-contacts-modal-pjax']);
    Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'transporter-preview-modal',
    'header' => Yii::t('element', 'transporter_preview_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'transporter-preview-modal-pjax']);
Pjax::end();
Modal::end();
$this->registerJs(
    'var actionChangeCarTransportersPageSize = "' . Url::to([
        'my-car-transporter/change-page-size',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionTransporterAdvForm = "' . Url::to([
        'my-car-transporter/transporter-adv-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionTransporterOpenContactsForm = "' . Url::to([
        'my-car-transporter/open-contacts-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionChangeCarTransporterAvailableFromDate = "' . Url::to([
        'my-car-transporter/change-available-from-date',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var VISIBLE = "' . CarTransporter::VISIBLE . '"; ' .
    'var INVISIBLE = "' . CarTransporter::INVISIBLE . '"; ' .
    'var actionChangeCarTransportersVisibility = "' . Url::to([
        'my-car-transporter/change-visibility',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionPreviewTransporter = "' . Url::to([
        'my-car-transporter/transporter-preview-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionPreviewTransporterBuy = "' . Url::to([
        'my-car-transporter/transporter-preview-buy',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionRemoveCarTransporters = "' . Url::to([
        'my-car-transporter/remove',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionCarTransporterAnnouncementForm = "' . Url::to([
        'car-transporter-announcement/announcement-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionMyCarTransportersFiltration = "' . Url::to([
        'my-car-transporter/filtration',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionChangeCarTransporterTableFiltration = "' . Url::to([
        'my-car-transporter/change-car-transporter-table-activity',
        'lang' => Yii::$app->language,
    ]) . '"; ' ,
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/my-car-transporters.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/my-car-transporters-table.js', ['depends' => [JqueryAsset::className()]]);
