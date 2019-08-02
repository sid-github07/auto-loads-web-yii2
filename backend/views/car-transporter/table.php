<?php

use backend\controllers\ClientController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var ActiveDataProvider $dataProvider */
?>
<div class="row">
    <div class="col-xs-3">
        <span class="red-row span-block"></span><p class="color-desc"><?php echo Yii::t('app', 'deleted_records')?></p>
    </div>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterSelector' => 'select[name="per-page"]',
    'options' => ['class' => 'custom-gridview car-transporters-grid-view text-center table-responsive'],
    'summaryOptions' => [
        'id' => 'C-T-106 C-T-107',
        'class' => 'summary',
    ],
    'tableOptions' => ['class' => 'table table-striped responsive-table'],
    'afterRow' => function (CarTransporter $carTransporter) {
        $td = Html::tag('td', '', ['id' => $carTransporter->id, 'colspan' => 7]);
        return Html::tag('tr', $td, ['class' => 'hidden car-transporter-preview-row text-left expanded-row-content']);
    },
    'rowOptions' => function(CarTransporter $carTransporter)
    {
        if ($carTransporter->archived === CarTransporter::ACTIVATED) {
            return ['class' => 'red-row'];
        }
        return [];
    },
    'columns' => [
        [
            'attribute' => 'preview_count',
            'label' => Yii::t('element', 'transporter_preview_count'),
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
                $previewsCount = count($carTransporter->carTransporterPreviews);
                $previews = Html::a($previewsCount, '#', [
                    'class' => 'car-transporters-previews C-T-115',
                    'onclick' => 'showCarTransporterPreview(event, ' . $carTransporter->id . ')',
                ]);

                return $previews;
            }
        ],
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
                return $date ;
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
                'class' => 'car-transporter-load-locations-column-content city-column-content C-T-117',
                'data-title' => Yii::t('element', 'C-T-110'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $carTransporterCities = $carTransporter->carTransporterCities;
                if (empty($carTransporterCities)) {
                    return '-';
                }
                $postalCode = '';
                if (!empty($carTransporter->carTransporterCities[0]->load_postal_code)) {
                    $postalCode = '> (' . $carTransporter->carTransporterCities[0]->load_postal_code . ')';   
                }
                return reset($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_LOAD) . $postalCode;
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
                'class' => 'car-transporter-unload-locations-column-content city-column-content C-T-118',
                'data-title' => Yii::t('element', 'C-T-111'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $carTransporterCities = $carTransporter->carTransporterCities;
                if (empty($carTransporterCities)) {
                    return '-';
                }
                $postalCode = '';
                if (!empty($carTransporter->carTransporterCities[0]->unload_postal_code)) {
                    $postalCode = '> (' . $carTransporter->carTransporterCities[0]->unload_postal_code . ')';   
                }
                return reset($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_UNLOAD) . $postalCode;
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
                'class' => 'car-transporter-preview-column-content preview-column-content text-center C-T-121',
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
            }
        ],
        [
            'attribute' => 'visible',
            'label' => Yii::t('element', 'transporter_active'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'A-C-326',
                'class' => 'load-created-at-column text-left',
            ],
            'contentOptions' => [
                'class' => 'load-created-at-column-content text-left A-C-326',
                'data-title' => Yii::t('element', 'A-C-326'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                if ($carTransporter->visible === 0) {
                    $title = Yii::t('element', 'load_inactive');
                    $class = 'load-inactive-tag';
                } else {
                    $title = Yii::t('element', 'load_active');
                    $class ='load-active-tag';
                }

                if ($carTransporter->archived === CarTransporter::ACTIVATED) {
                    $title .= '<br/>' . '('. Yii::t('element', 'advertisement_deleted') . ')';
                }

                return Html::tag('p', $title, ['class' => $class]);
            }
        ],
        [
            'attribute' => '',
            'label' => null,
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'change-active-status-collumn',
                'width' => '5%',
            ],
            'contentOptions' => ['class' => 'change-active-status-collumn-content single-action-column'],
            'value' => function (CarTransporter $carTransporter) {
                if ($carTransporter->isVisible()) {
                    $check = 'check';
                    $title = Yii::t('element', 'transporter_uncheck');
                    $tag = '';
                } else {
                    $check = 'unchecked';
                    $title = Yii::t('element', 'transporter_check');
                    $tag = Html::tag('p', $title, ['class' => 'announcement-text visible-tag']);
                }

                $visibilityContent = Html::a(Html::tag('span', null, [
                        'class' => 'visible-url glyphicon glyphicon-' . $check
                    ]), [
                        'car-transporter/toggle-visibility',
                        'lang' => Yii::$app->language,
                        'id' => $carTransporter->id,
                    ], [
                        'title' => $title,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                    ]) . $tag;
                $visibilityIcon = Html::tag('div', $visibilityContent, ['class' => 'option-div']);


                $trashIcon = Html::tag('i', null, ['class' => 'fa fa-trash', 'aria-hidden' => true]);
                $removeContent =  Html::a($trashIcon, [
                    'car-transporter/remove-transporter',
                    'lang' => Yii::$app->language,
                    'id' => $carTransporter->id,
                ], [
                    'class' => 'remove-icon',
                    'data-placement' => 'top',
                    'data-toggle' => 'tooltip',
                    'title' => Yii::t('element', 'A-C-339b'),
                ]);
                $removeIcon = '';
                if ($carTransporter->archived !== CarTransporter::ARCHIVED) {
                    $removeIcon = Html::tag('div', $removeContent, ['class' => 'option-div']);
                }

                $tag = '';
                if ($carTransporter->car_pos_adv !== 0 && $carTransporter->days_adv !== 0) {
                    $tag = Html::tag('p', Yii::t('element', 'advert_advertised'), ['class' => 'announcement-text advert-tag']);
                }

                $advIcon = Html::tag('i', null, ['class' => 'fa fa-bullhorn adv-url', 'aria-hidden' => true]);
                $advIconContent = Html::a($advIcon, ['#'], [
                        'class' => 'advert_url',
                        'title' => Yii::t('element', 'advertise_load'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'onclick' => 'renderAdvertizeForm(event, ' . $carTransporter->id . ')',
                    ]) . $tag;

                $advIcon = Html::tag('div', $advIconContent, ['class' => 'option-div']);
                
                if ($carTransporter->isOpenContacts()) {
                    $tag = Html::tag('p', Yii::t('element', 'advert_open_contacts_set'), [
                        'class' => 'announcement-text advert-tag',
                    ]);
                } else {
                    $tag = '';
                }

                $openContactsIcon = Html::tag('i', null, ['class' => 'fa fa-eye adv-url', 'aria-hidden' => true]);
                $openContactsIconContent = Html::a($openContactsIcon, ['#'], [
                    'class' => 'advert-url',
                    'title' => Yii::t('element', 'load_open_contacts'),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'onclick' => 'renderOpenContactsForm(event, ' . $carTransporter->id . ')',
                ]) . $tag;

                $openContactsIcon = Html::tag('div', $openContactsIconContent, ['class' => 'option-div']);

                return $removeIcon . $visibilityIcon . $advIcon . $openContactsIcon;
            }
        ],
    ],
    'pager' => [
        'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
        'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
    ],
]);