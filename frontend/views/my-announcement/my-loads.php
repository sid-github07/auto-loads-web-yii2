<?php

use common\models\Load;
use common\models\LoadCity;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use kartik\icons\Icon;

/**
 * @var View $this
 * @var null|string $token
 * @var null|array $cities
 * @var null|array $citiesNames
 * @var ActiveDataProvider $dataProvider
 */

?>
    <div class="my-loads">
        <div class="search-row-wrapper">
            <div class="row">
                <div class="col-sm-3 col-xs-12">
                    <div class="form-group load-activity-container">
                        <?php echo Html::dropDownList('loadActivity',
                            Yii::$app->request->get('load-activity', Load::ACTIVE), Load::getLoadListActivities(), [
                                'id' => 'MK-C-22',
                                'class' => 'form-control',
                                'onchange' => "changeLoadsTypeShowing(this.value)",
                            ]); ?>
                    </div>
                </div>
                <div class="col-sm-9 col-xs-12">
                    <?php $form = ActiveForm::begin(['id' => 'my-loads-filter-form']); ?>
                    <?php echo Yii::$app->controller->renderPartial('/site/partial/multiple-locations', [
                        'form' => $form,
                        'model' => new LoadCity([
                            'scenario' => LoadCity::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS,
                            'myLoadsFilter' => $cities,
                        ]),
                        'attribute' => 'myLoadsFilter',
                        'data' => $citiesNames,
                        'label' => '',
                        'labelOptions' => [],
                        'placeholder' => Yii::t('element', 'MK-C-2'),
                        'id' => 'MK-C-4',
                        'unloadId' => null,
                        'onchange' => 'filterMyLoads(this)',
                        'url' => Url::to([
                            'site/filter-location',
                            'lang' => Yii::$app->language,
                            'token' => $token,
                        ]),
                    ]); ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <button id="MK-C-5" class="primary-button input-button hidden-xs" onclick="renderLoadAnnouncementForm()">
                <?php echo Yii::t('element', 'MK-C-5'); ?>
            </button>

            <button id="MK-C-5a" class="primary-button visible-xs" onclick="renderLoadAnnouncementForm()">
                <?php echo Yii::t('element', 'MK-C-5'); ?>
            </button>
        </div>

        <?php echo Yii::$app->controller->renderPartial('my-loads-table-control-buttons', ['id' => 'MK-C-13a']); ?>

        <?php Pjax::begin(['id' => 'my-loads-table-pjax']);
        echo Yii::$app->controller->renderPartial('my-loads-table', compact('dataProvider'));
        Pjax::end(); ?>

        <?php echo Yii::$app->controller->renderPartial('my-loads-table-control-buttons', ['id' => 'MK-C-13b']); ?>
    </div>

<?php Modal::begin([
    'id' => 'announce-load-modal',
    'header' => Yii::t('element', 'MK-C-11'),
    'size' => 'modal-lg',
]);

Pjax::begin(['id' => 'announce-load-pjax']);
Pjax::end();

Modal::end();

Modal::begin([
    'id' => 'load-open-contacts-modal',
    'header' => Yii::t('element', 'open_contacts_modal_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'load-open-contacts-modal-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'edit-load-modal',
    'header' => Yii::t('element', 'MK-C-16'),
    'size' => 'modal-lg',
]);

Pjax::begin(['id' => 'edit-load-pjax']);
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
    'id' => 'load-preview-modal',
    'header' => Yii::t('element', 'load_preview_title'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'load-preview-modal-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'load-potential-haulier-modal',
    'header' => Icon::show('truck', [], Icon::FA) . Yii::t('element', 'Potential hauliers'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'load-potential-haulier-modal-pjax', 'class' => 'pjax-container']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'load-searches-in-24-modal',
    'header' => Icon::show('clock-o', [], Icon::FA) . Yii::t('element', 'Searches in 24h'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'load-searches-in-24-modal-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'load-trucks-modal',
    'header' => Icon::show('truck', [], Icon::FA) . Yii::t('element', 'Trucks'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'load-trucks-modal-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'remove-load-modal',
    'header' => Yii::t('text', 'LOAD_REMOVAL_CONFIRMATION_QUESTION'),
]); ?>

    <div class="delete-confirmation-text">
        <?php echo Yii::t('text', 'LOAD_REMOVAL_CONFIRMATION_TEXT'); ?>
    </div>

    <div class="delete-confirmation-buttons">
        <button id="remove-load-button-yes" class="success-btn delete-btn">
            <i class="icon-check"></i> <?php echo Yii::t('element', 'CONFIRM'); ?>
        </button>

        <button id="remove-load-button-no" class="danger-btn delete-btn" data-dismiss="modal">
            <i class="icon-cross"></i> <?php echo Yii::t('element', 'CANCEL'); ?>
        </button>
    </div>

<?php Modal::end();


$this->registerJs(
    'var actionChangeLoadDate = "' . Url::to([
        'my-load/change-date',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadEditingForm = "' . Url::to([
        'my-load/load-editing-form',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionAdvLoad = "' . Url::to([
        'my-load/adv-load',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadAdvForm = "' . Url::to([
        'my-load/load-adv-form',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadOpenContactsForm = "' . Url::to([
        'my-load/open-contacts-form',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionPreviewLoad = "' . Url::to([
        'my-load/load-preview-form',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadPotentialHauliers = "' . Url::to([
        'my-load/load-form-potential-hauliers',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadSearchesIn24h = "' . Url::to([
        'my-load/load-form-searches-in-24',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionLoadTrucks = "' . Url::to([
        'my-load/load-form-trucks',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionPreviewLoadBuy = "' . Url::to([
        'my-load/load-preview-buy',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionAdvLoad = "' . Url::to([
        'my-load/adv-load',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .

    'var ACTIVATED = "' . Load::ACTIVATED . '"; ' .
    'var NOT_ACTIVATED = "' . Load::NOT_ACTIVATED . '"; ' .
    'var actionChangeLoadsVisibility = "' . Url::to([
        'my-load/change-visibility',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionRemoveLoads = "' . Url::to([
        'my-load/remove',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ' .
    'var actionChangeLoadsPageSize = "' . Url::to([
        'my-load/change-page-size',
        'lang' => Yii::$app->language,
        'token' => $token,
    ], true) . '"; ' .
    'var actionLoadAnnouncementForm = "' . Url::to([
        'load-announcement/announcement-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionMyLoadsFiltration = "' . Url::to([
        'my-load/filtration',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionChangeLoadTableFiltration = "' . Url::to([
        'my-load/change-load-table-activity',
        'lang' => Yii::$app->language,
        'token' => $token,
    ]) . '"; ',
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/my-loads.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/load-cars-editing-form.js',
    ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/my-loads-table.js',
    ['depends' => [JqueryAsset::className()]]);
