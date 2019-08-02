<?php

use backend\controllers\ClientController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;
use yii\web\View;

/** @var ActiveDataProvider $dataProvider */

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterSelector' => 'select[name="per-page"]',
    'options' => ['class' => 'custom-gridview car-transporters-grid-view text-center'],
    'summaryOptions' => [
        'id' => 'C-T-106 C-T-107',
        'class' => 'summary',
    ],
    'tableOptions' => ['class' => 'table table-striped responsive-table'],
    'afterRow' => function (CarTransporter $carTransporter) {
        $td = Html::tag('td', '', ['id' => $carTransporter->id, 'colspan' => 7]);
        return Html::tag('tr', $td, ['class' => 'hidden car-transporter-preview-row text-left expanded-row-content']);
    },
    'columns' => [
        [
            'attribute' => 'created_at',
            'label' => Yii::t('element', 'C-T-108'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-108',
                'class' => 'car-transporter-created-at-column text-left',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-created-at-column-content text-left C-T-114 C-T-115',
                'data-title' => Yii::t('element', 'C-T-108'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $dateText = Yii::$app->datetime->convertToText($carTransporter->created_at);
                $date = Html::tag('div', $dateText);

                $previewsCount = count($carTransporter->carTransporterPreviews);
                $previewsText = $previewsCount . ' ' . Yii::t('app', 'VIEWS');
                $previews = Html::a($previewsText, '#', [
                    'class' => 'car-transporters-previews C-T-115',
                    'onclick' => 'showCarTransporterPreview(event, ' . $carTransporter->id . ')',
                ]);

                return $date . $previews;
            }
        ],
        [
            'attribute' => 'available_from',
            'label' => Yii::t('element', 'C-T-109'),
            'headerOptions' => [
                'id' => 'C-T-109',
                'class' => 'car-transporter-available-from-column text-center',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-available-from-column-content text-center C-T-116',
                'data-title' => Yii::t('element', 'C-T-109'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $carTransporter->convertAvailableFromDate();
                return $carTransporter->available_from;
            }
        ],
        [
            'attribute' => 'loadLocations',
            'label' => Yii::t('element', 'C-T-110'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-110',
                'class' => 'car-transporter-load-locations-column text-left',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-load-locations-column-content text-left C-T-117',
                'data-title' => Yii::t('element', 'C-T-110'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $carTransporterCities = $carTransporter->carTransporterCities;
                if (empty($carTransporterCities)) {
                    return '-';
                }
                return reset($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_LOAD);
            }
        ],
        [
            'attribute' => 'unloadLocations',
            'label' => Yii::t('element', 'C-T-111'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-111',
                'class' => 'car-transporter-unload-locations-column text-left',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-unload-locations-column-content text-left C-T-118',
                'data-title' => Yii::t('element', 'C-T-111'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $carTransporterCities = $carTransporter->carTransporterCities;
                if (empty($carTransporterCities)) {
                    return '-';
                }
                return reset($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_UNLOAD);
            }
        ],
        [
            'attribute' => 'quantity',
            'label' => Yii::t('element', 'C-T-112'),
            'headerOptions' => [
                'id' => 'C-T-112',
                'class' => 'car-transporter-quantity-column text-center',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-quantity-column-content text-center C-T-119',
                'data-title' => Yii::t('element', 'C-T-112'),
            ],
        ],
        [
            'attribute' => 'company',
            'label' => Yii::t('element', 'C-T-113'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-113',
                'class' => 'car-transporter-company-column text-left',
            ],
            'contentOptions' => [
                'class' => 'car-transporter-company-column-content text-left C-T-120',
                'data-title' => Yii::t('element', 'C-T-113'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $company = $carTransporter->user->getCompany();
                if (is_null($company)) {
                    return null;
                }

                return Html::a($company->getTitleByType(), [
                    'client/company',
                    'lang' => Yii::$app->language,
                    'id' => $company->id,
                    'tab' => ClientController::TAB_COMPANY_INFO,
                ], [
                    'target' => '_blank',
                ]);
            }
        ],
        [
            'attribute' => 'preview',
            'label' => '',
            'format' => 'raw',
            'headerOptions' => ['class' => 'car-transporter-preview-column text-center'],
            'contentOptions' => [
                'class' => 'car-transporter-preview-column-content text-center C-T-121',
                'data-title' => '',
            ],
            'value' => function (CarTransporter $carTransporter) {
                $caretIcon = Html::tag('i', null, ['class' => 'fa fa-caret-down']);
                return Html::a($caretIcon, '#', [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t('element', 'C-T-121'),
                    'class' => 'car-transporter-preview-icon',
                    'onclick' => 'collapseCarTransporterPreview(event, ' . $carTransporter->id . ')',
                ]);
            },
        ],
    ],
]); ?>
</div>
<?php
Modal::begin([
    'id' => 'car-transporter-previews-modal',
    'header' => Yii::t('element', 'C-T-115'),
]);

    Pjax::begin(['id' => 'car-transporter-previews-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionPreviews = "' . Url::to([
        'car-transporter/previews',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionContactInfoPreview = "' . Url::to([
        'car-transporter/contact-info-preview',
        'lang' => Yii::$app->language,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/table.js', ['depends' => [JqueryAsset::className()]]);

