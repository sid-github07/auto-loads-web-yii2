<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use dosamigos\google\maps\MapAsset;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;
use common\components\Location;
use common\models\Company;
use common\models\Language;
use odaialali\yii2toastr\ToastrFlash;
use odaialali\yii2toastr\ToastrAsset;

ToastrAsset::register($this);

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var array $markers
 * @var integer $pageSize
 * @var array $countries
 */

MapAsset::register($this);
?>
    <div id="car-transporter-index">
        <h1><?php echo $this->title; ?></h1>
        <div class="menu-collapse-btn-wrapper">
            <div class="menu-collapse-btn-wrapper">
                <input
                        type="button"
                        id="C-T-2"
                        class="flat-btn"
                        data-toggle="collapse"
                        data-target="#C-T-3"
                        data-load="<?php echo Yii::$app->getRequest()->get('loadCityId', '') ?>"
                        data-unload="<?php echo Yii::$app->getRequest()->get('unloadCityId', '') ?>"
                        onclick="changeMapCollapseButtonText(this, event)"/>
                <i class="fa fa-plus-circle btn-icon map-link"></i>
                <span class="btn-text map-link"><?php echo Yii::t('element', 'C-T-2b'); ?></span>
            </div>
            <div class="menu-collapse-btn-wrapper">
                <input
                        type="button"
                        id="filter-btn"
                        class="flat-btn"
                        data-load="<?php echo Yii::$app->getRequest()->get('loadCityId', '') ?>"
                        data-unload="<?php echo Yii::$app->getRequest()->get('unloadCityId', '') ?>"
                        data-toggle="collapse"
                        data-target="#filter"
                        onclick="changeFiltersCollapseButtonText(this, event)"/>
                <i class="fa fa-plus-circle btn-icon"></i>
                <span class="btn-text"><?php echo Yii::t('element', 'show_filters'); ?></span>
            </div>
        </div>
        <div id="C-T-3" class="map-wrapper collapse" aria-expanded="false" data-load="<?php echo 1 ?>"></div>
        <form id="filter-form">
            <div id="filter" class="filter-wrapper collapse" aria-expanded="false"></div>
        </form>

        <div class="paginator-options">
        <span class="posts-per-page-select">
            <label id="C-T-12" class="page-size-filter-label">
                <?php echo Yii::t('element', 'C-T-12'); ?>
            </label>
            <?php echo Html::dropDownList('per-page', $pageSize, CarTransporter::getPageSizes(), [
                'id' => 'C-T-13',
                'onchange' => 'changePageSize(this)',
            ]); ?>
        </span>
        </div>

        <div class="responsive-table-wrapper roundtrips-table-wrapper">
            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table responsive-table table--striped'],
                'options' => ['class' => 'grid-view'],
                'rowOptions' => function ($carTransporterCity) {
                    $pos = $carTransporterCity->carTransporter->car_pos_adv;
                    $rowArray = [];
                    if ($carTransporterCity->carTransporter->isOpenContacts()) {
                        $rowArray['class'] = ['blue-border'];
                    } elseif ($pos !== 0 && is_numeric($pos)) {
                        $rowArray['class'] = ['orange-border'];
                    }
                    return $rowArray;
                },
                'afterRow' => function (CarTransporterCity $carTransporterCity) {
                    $carTransporter = $carTransporterCity->carTransporter;
                    $seo = Yii::t('seo', 'car_transporter_details', [
                        'load' => $carTransporter->getFullCityInfo(\common\models\CarTransporterCity::TYPE_LOAD),
                        'unload' => $carTransporter->getFullCityInfo(\common\models\CarTransporterCity::TYPE_UNLOAD),
                    ]);
                    $content = Html::tag('div', '', ['class' => 'content clearfix']) . Html::tag('div', $seo,
                            ['style' => 'color: #ccc; text-align: left']);

                    $td = Html::tag('td', $content, [
                        'id' => 'car-transporter-preview-' . $carTransporterCity->car_transporter_id,
                        'class' => 'expanded-load-preview-content',
                        'colspan' => 7,
                    ]);
                    return Html::tag('tr', $td,
                        ['class' => 'expanded-content-row hidden']);
                },
                'summary' => false,
                'emptyText' => Html::tag('div', Yii::t('app', 'EMPTY_CAR_TRANSPORTER_TABLE_TEXT'),
                        ['class' => 'empty-loads-text-wrapper']) .
                    ' ' . Yii::t('app', 'EMPTY_TRY') . ' ' .
                    Html::a(Yii::t('app', 'EMPTY_ANNOUNCE_LOAD_LINK'), [
                        'load/announce',
                        'lang' => Yii::$app->language,
                    ],
                        [
                            'class' => 'primary-button detail-search-btn',
                        ]) . ' ' . Yii::t('app', 'EMPTY_SEARCH_TEXT') . ' ' .
                    Html::a(Yii::t('app', 'SEARCH_IN_MORE_DETAILED_SEARCH'), [
                        'car-transporter-search/search-form',
                        'lang' => Yii::$app->language,
                    ], [
                        'class' => 'primary-button detail-search-btn',
                    ]),
                'emptyTextOptions' => ['class' => 'text-center'],
                'columns' => [
                    [
                        'attribute' => null,
                        'label' => null,
                        'headerOptions' => ['id' => 'adv-count'],
                        'format' => 'raw',
                        'contentOptions' => [
                            'class' => 'L-T-21',
                            'data-title' => Yii::t('element', 'advertisement')
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $pos = $carTransporterCity->carTransporter->car_pos_adv;
                            if ($pos === 0 || is_numeric($pos) === false) {
                                return '&nbsp;';
                            }
                            $icon = Html::tag('div', 'directions_car', [
                                'class' => 'material-icons ',
                                'title' => Yii::t('app', 'TRANSPORTER_IS_ADVERTISED'),
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                            ]);
                            $span = Html::tag('div', $pos, ['class' => 'load-cars']);
                            return $icon . $span;
                        }
                    ],
                    [
                        'attribute' => 'available_from',
                        'label' => Yii::t('element', 'C-T-14'),
                        'headerOptions' => ['id' => 'C-T-14'],
                        'contentOptions' => [
                            'class' => 'C-T-19',
                            'data-title' => Yii::t('element', 'C-T-14'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $carTransporterCity->carTransporter->convertAvailableFromDate();
                            return $carTransporterCity->carTransporter->available_from;
                        }
                    ],
                    [
                        'attribute' => 'loadLocations',
                        'label' => Yii::t('element', 'C-T-15'),
                        'format' => 'raw',
                        'headerOptions' => ['id' => 'C-T-15'],
                        'contentOptions' => [
                            'class' => 'C-T-20 city-column-content',
                            'data-title' => Yii::t('element', 'C-T-15'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $postalCode = '';
                            if (!empty($carTransporterCity->load_postal_code)) {
                                $postalCode = '> (' . $carTransporterCity->load_postal_code . ')';
                            }
                            return $carTransporterCity->formatTableCities(CarTransporterCity::TYPE_LOAD) . $postalCode;
                        }
                    ],
                    [
                        'attribute' => 'unloadLocations',
                        'label' => Yii::t('element', 'C-T-16'),
                        'format' => 'raw',
                        'headerOptions' => ['id' => 'C-T-16'],
                        'contentOptions' => [
                            'class' => 'C-T-21 city-column-content',
                            'data-title' => Yii::t('element', 'C-T-16'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $postalCode = '';
                            if (!empty($carTransporterCity->unload_postal_code)) {
                                $postalCode = '> (' . $carTransporterCity->unload_postal_code . ')';
                            }
                            return $carTransporterCity->formatTableCities(CarTransporterCity::TYPE_UNLOAD) . $postalCode;
                        }
                    ],
                    [
                        'attribute' => 'quantity',
                        'label' => Yii::t('element', 'C-T-17'),
                        'headerOptions' => ['id' => 'C-T-17'],
                        'contentOptions' => [
                            'class' => 'C-T-22',
                            'data-title' => Yii::t('element', 'C-T-17'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            if (empty($carTransporterCity->carTransporter->quantity)) {
                                return Yii::t('element', 'C-T-17a');
                            }
                            return $carTransporterCity->carTransporter->quantity;
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('element', 'A-L-L-1'),
                        'format' => 'raw',
                        'headerOptions' => [
                            'id' => 'A-C-338',
                            'class' => 'company-column text-left',
                        ],
                        'contentOptions' => [
                            'class' => 'company-column-content A-C-338 text-left',
                            'data-title' => Yii::t('element', 'A-L-L-1'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $location = Location::getGeoLocation();
                            $date = date_create(date('Y-m-d H:i:s', $carTransporterCity->carTransporter->created_at));
                            if ($location != null) {
                                $date->setTimeZone(new DateTimeZone($location->timeZone));
                            }
                            return $date->format('Y-m-d H:i:s');
                        }
                    ],
                    [
                        'attribute' => 'carrier',
                        'label' => Yii::t('element', 'C-T-18'),
                        'format' => 'raw',
                        'headerOptions' => [
                            'id' => 'C-T-18',
                            'class' => 'text-center',
                        ],
                        'contentOptions' => [
                            'class' => 'C-T-23 load-preview-content text-center',
                            'data-title' => Yii::t('element', 'C-T-18'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $id = $carTransporterCity->car_transporter_id;
                            $icon = Html::tag('i', '', [
                                'class' => 'fa fa-caret-down car-transporter-detail-view',
                                'data-transporter' => $carTransporterCity->car_transporter_id
                            ]);
                            return Html::a($icon, '#', [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => Yii::t('element', 'C-T-23'),
                                'class' => 'load-preview-icon',
                                'onclick' => 'collapseCarTransporterPreview(event, ' . $id . ');',
                            ]);
                        }
                    ],
                    [
                        'attribute' => 'link',
                        'label' => Yii::t('element', 'C-T-18a'),
                        'format' => 'raw',
                        'headerOptions' => [
                            'id' => 'C-T-18a',
                            'class' => 'text-center',
                        ],
                        'contentOptions' => [
                            'class' => 'C-T-23a car-transporter-link-content text-center',
                            'data-title' => Yii::t('element', 'C-T-18a'),
                        ],
                        'value' => function (CarTransporterCity $carTransporterCity) {
                            $icon = Html::tag('i', '', ['class' => 'fa fa-share-alt']);
                            return Html::a($icon, '#', [
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'title' => Yii::t('element', 'C-T-23a'),
                                'class' => 'car-transporter-link-icon',
                                'onclick' => 'showCarTransporterLink(event, ' . $carTransporterCity->carTransporter->id . ');'
                            ]);
                        }
                    ],
                ],
                'pager' => [
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                ],
            ]); ?>
        </div>
    </div>

<?php
Modal::begin([
    'id' => 'contact-info-preview-modal',
    'header' => Yii::t('element', 'C-T-8a'),
    'size' => 'modal-lg',
]);
Pjax::begin(['id' => 'contact-info-preview-pjax']);
Pjax::end();
Modal::end();

Modal::begin([
    'id' => 'car-transporter-link-preview-modal',
    'header' => Yii::t('element', 'C-T-8bb'),
]);

Pjax::begin(['id' => 'car-transporter-link-preview-pjax']);
Pjax::end();

Modal::end();

$needToOpenFilters = Yii::$app->getSession()->get('car-transporter-filter-is-opened', false);
if ($needToOpenFilters) {
    Yii::$app->getSession()->remove('car-transporter-filter-is-opened');
}

$this->registerJs(
    'var openMapText = "' . Yii::t('element', 'Open Map') . '";' .
    'var closeMapText = "' . Yii::t('element', 'Close Map') . '";' .
    'var TEXT_HIDE_MAP = "' . Yii::t('element', 'C-T-2a') . '"; ' .
    'var TEXT_SHOW_MAP = "' . Yii::t('element', 'C-T-2b') . '"; ' .
    'var TEXT_HIDE_FILTERS = "' . Yii::t('element', 'hide_filters') . '"; ' .
    'var TEXT_SHOW_FILTERS = "' . Yii::t('element', 'show_filters') . '"; ' .
    'var actionRenderMap = "' . Url::to(['car-transporter/render-map', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreview = "' . Url::to(['car-transporter/preview', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRenderContactMap = "' . Url::to([
        'car-transporter/render-map-contact',
        'lang' => Yii::$app->language
    ]) . '"; ' .
    'var actionRenderFilters = "' . Url::to(['car-transporter/render-filters', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreviewLink = "' . Url::to(['car-transporter/preview-link', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionLogTransporterMapOpen = "' . Url::to(['car-transporter/log-open-map']) . '"; ' .
    'var needToOpenFilters = ' . ($needToOpenFilters ? "true" : "false") . ';',
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/index.js', ['depends' => [JqueryAsset::className()]]);
