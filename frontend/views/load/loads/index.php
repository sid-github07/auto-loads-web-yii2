<?php

use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
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
use frontend\controllers\SubscriptionController;
use common\models\City;
use common\components\helpers\Html as AutoLoadsHtml;
use yii\data\ArrayDataProvider;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var ArrayDataProvider $expiredLoadsDataProvider
 * @var array $markers
 * @var integer $pageSize
 * @var array $countries
 * @var City|null $loadCity
 * @var City|null $unloadCity
 * @var int $openExpiredPrice
 */

MapAsset::register($this);

?>

    <div id="load-loads-index">
    <h1>
        <?php echo Html::encode($this->title); ?>
    </h1>

    <div class="row">
        <div class="menu-collapse-btn-wrapper">
            <input
                    type="button"
                    id="L-T-2"
                    class="flat-btn"
                    data-toggle="collapse"
                    data-target="#L-T-5"
                    data-load="<?php echo Yii::$app->getRequest()->get('loadCityId', 'null') ?>"
                    data-unload="<?php echo Yii::$app->getRequest()->get('unloadCityId', 'null') ?>"
                    data-type="<?php echo Yii::$app->getRequest()->get('type', 'null') ?>"
                    onclick="changeMapCollapseButtonText(this, event)"/>
            <i class="fa fa-plus-circle btn-icon map-link"></i>
            <span class="btn-text map-link"><?php echo Yii::t('element', 'L-T-2b'); ?></span>
        </div>
        <div class="menu-collapse-btn-wrapper">
            <input
                    type="button"
                    id="filter-btn"
                    class="flat-btn"
                    data-load="<?php echo Yii::$app->getRequest()->get('loadCityId', 'null') ?>"
                    data-unload="<?php echo Yii::$app->getRequest()->get('unloadCityId', 'null') ?>"
                    data-type="<?php echo Yii::$app->getRequest()->get('type', 'null') ?>"
                    data-toggle="collapse"
                    data-target="#filter"
                    onclick="changeFiltersCollapseButtonText(this, event)"/>
            <i class="fa fa-plus-circle btn-icon"></i>
            <span class="btn-text"><?php echo Yii::t('element', 'show_filters'); ?></span>
        </div>
    </div>

    <div id="L-T-5" class="map-wrapper collapse" aria-expanded="false"></div>
    <form id="filter-form">
        <div id="filter" class="filter-wrapper collapse" aria-expanded="false"></div>
    </form>

<?php if (!$dataProvider->getTotalCount()) : ?>
    <div id="notification-expired-loads" class="orange-border"
         style="margin: 10px -15px 0; padding: 10px; text-align: center">
        <?php
        $toLoadFrom = ($loadCity instanceof City) ? Yii::t('element', 'to_load_from {0}',
            [AutoLoadsHtml::getFlagIcon($loadCity->country_code, $loadCity->name, true)]) : '';
        $andUnloadAt = ($unloadCity instanceof City) ? Yii::t('element', 'and_unload_at {0}',
            [AutoLoadsHtml::getFlagIcon($unloadCity->country_code, $unloadCity->name, true)]) : '';
        if ($expiredLoadsDataProvider->getTotalCount()) {
            echo Yii::t('element', 'no_loads_check_expired', [
                'buy_credits_link' => Html::a(
                    Yii::t('element', 'adv_credits_topup'),
                    Url::to([
                            'subscription/',
                            'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER
                        ]
                    ), [
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'class' => 'credit-topup-link',
                    ]
                ),
                'subscription_info' => (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription() || Yii::$app->user->identity->service_credits) ? '' : Yii::t('element', 'subscription_info', [
                    'subscriptionEndTime' => Yii::$app->getUser()->getIdentity()->getSubscriptionEndTime(),
                    'subscriptionCredits' => Yii::$app->getUser()->getIdentity()->getSubscriptionCredits(),
                ]),
                'to_load_from' => $toLoadFrom,
                'and_unload_at' => $andUnloadAt,
                'price' => $openExpiredPrice
            ]);
        } else {
            echo Yii::t('element', 'no_loads_total', [
                'to_load_from' => $toLoadFrom,
                'and_unload_at' => $andUnloadAt,
            ]);
        }
        ?>
    </div>
<?php endif; ?>

    <div class="paginator-options">
        <span class="posts-per-page-select">
            <label id="L-T-14" class="page-size-filter-label">
                <?php echo Yii::t('element', 'L-T-14'); ?>
            </label>
            <?php echo Html::dropDownList('per-page', $pageSize, Load::getPageSizes(), [
                'id' => 'L-T-15',
                'onchange' => 'changePageSize(this);',
            ]); ?>
        </span>
    </div>

<?php if ($dataProvider->getTotalCount()) : ?>
    <div class="responsive-table-wrapper roundtrips-table-wrapper">
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => [
                'class' => 'table responsive-table table--striped'
            ],
            'options' => [
                'class' => 'grid-view',
            ],
            'rowOptions' => function ($loadCity) {
                $pos = $loadCity->load->car_pos_adv;
                if ($loadCity->load->isOpenContacts()) {
                    return ['class' => 'blue-border'];
                } elseif ($pos !== 0 && is_numeric($pos)) {
                    return ['class' => 'orange-border'];
                }
                return [];
            },
            'afterRow' => function (LoadCity $loadCity) {
                $load = $loadCity->load;
                $seo = Yii::t('seo', 'load_details', [
                    'details' => LoadCar::getLoadShortInfo($load),
                    'load' => $load->getFullCityInfo(\common\models\LoadCity::LOADING),
                    'unload' => $load->getFullCityInfo(\common\models\LoadCity::UNLOADING),
                ]);
                $content = Html::tag('div', '', ['class' => 'content clearfix']) . Html::tag('div', $seo,
                        ['style' => 'color: #ccc; text-align: left']);
                $td = Html::tag('td', $content, [
                    'id' => 'load-preview-' . $loadCity->load->id,
                    'class' => 'expanded-load-preview-content',
                    'colspan' => 7,
                ]);

                return Html::tag('tr', $td, ['class' => 'hidden']);
            },
            'summary' => false,
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
                    'value' => function (LoadCity $loadCity) {
                        $pos = $loadCity->load->car_pos_adv;
                        if ($pos === 0 || is_numeric($pos) === false) {
                            return '';
                        }
                        $icon = Html::tag('div', 'directions_car', [
                            'class' => 'material-icons ',
                            'title' => Yii::t('app', 'LOAD_IS_ADVERTISED'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]);
                        $span = Html::tag('div', $pos, ['class' => 'load-cars']);
                        return $icon . $span;
                    }
                ],
                [
                    'attribute' => 'date',
                    'label' => Yii::t('element', 'L-T-16'),
                    'headerOptions' => ['id' => 'L-T-16'],
                    'contentOptions' => [
                        'class' => 'L-T-21',
                        'data-title' => Yii::t('element', 'L-T-16')
                    ],
                    'value' => function (LoadCity $loadCity) {
                        return $loadCity->load->convertTimestampToDate();
                    }
                ],
                [
                    'attribute' => 'loadCity',
                    'label' => Yii::t('element', 'L-T-17'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-17'],
                    'contentOptions' => [
                        'class' => 'L-T-22 load-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-17')
                    ],
                    'value' => function (LoadCity $model) {
                        $cities = [];
                        foreach ($model->load->loadCities as $loadCity) {
                            if ($loadCity->isLoadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($model->load->loadCities[0]->load_postal_code)) {
                            $postalCode = ' (' . $model->load->loadCities[0]->load_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'unloadCity',
                    'label' => Yii::t('element', 'L-T-18'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-18'],
                    'contentOptions' => [
                        'class' => 'L-T-23 unload-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-18')
                    ],
                    'value' => function (LoadCity $model) {
                        $cities = [];
                        foreach ($model->load->loadCities as $loadCity) {
                            if ($loadCity->isUnloadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($model->load->loadCities[0]->unload_postal_code)) {
                            $postalCode = ' (' . $model->load->loadCities[0]->unload_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'loadInfo',
                    'label' => Yii::t('element', 'L-T-19'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-19'],
                    'contentOptions' => [
                        'class' => 'L-T-24',
                        'data-title' => Yii::t('element', 'L-T-19')
                    ],
                    'value' => function (LoadCity $loadCity) {
                        return LoadCar::getLoadInfo($loadCity->load);
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
                    'value' => function (LoadCity $loadCity) {
                        $location = Location::getGeoLocation();
                        $date = date_create(date('Y-m-d H:i:s', $loadCity->load->created_at));
                        $date->setTimeZone(new DateTimeZone($location->timeZone));
                        return $date->format('Y-m-d H:i:s');
                    }
                ],
                [
                    'attribute' => 'supplier',
                    'label' => Yii::t('element', 'L-T-20'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-20', 'class' => 'text-center'],
                    'contentOptions' => [
                        'class' => 'L-T-25 load-preview-content text-center',
                        'data-title' => Yii::t('element', 'L-T-20')
                    ],
                    'value' => function (LoadCity $loadCity) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-caret-down']);
                        return Html::a($icon, '#', [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => Yii::t('element', 'L-T-25'),
                            'class' => 'load-preview-icon',
                            'onclick' => 'collapseLoadPreview(event, ' . $loadCity->load->id . ');',
                        ]);
                    }
                ],
                [
                    'attribute' => 'link',
                    'label' => Yii::t('element', 'L-T-20a'),
                    'format' => 'raw',
                    'headerOptions' => [
                        'id' => 'L-T-20a',
                        'class' => 'text-center',
                    ],
                    'contentOptions' => [
                        'class' => 'L-T-25a load-link-content text-center',
                        'data-title' => Yii::t('element', 'L-T-20a'),
                    ],
                    'value' => function (LoadCity $loadCity) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-share-alt']);
                        return Html::a($icon, '#', [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => Yii::t('element', 'L-T-20b'),
                            'class' => 'load-link-icon',
                            'onclick' => 'showLoadLink(event, ' . $loadCity->load->id . ');',
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
<?php elseif ($expiredLoadsDataProvider->getTotalCount()) : ?>
    <div class="responsive-table-wrapper roundtrips-table-wrapper">
        <?php echo GridView::widget([
            'dataProvider' => $expiredLoadsDataProvider,
            'tableOptions' => [
                'class' => 'table responsive-table inline table--striped'
            ],
            'options' => [
                'class' => 'grid-view',
                'id' => 'expired-loads'
            ],
            'afterRow' => function (array $array) {
                $load = Load::findOne($array['load_id']);
                $seo = Yii::t('seo', 'load_details', [
                    'details' => LoadCar::getLoadShortInfo($load),
                    'load' => $load->getFullCityInfo(\common\models\LoadCity::LOADING),
                    'unload' => $load->getFullCityInfo(\common\models\LoadCity::UNLOADING),
                ]);
                $content = Html::tag('div', '', ['class' => 'content clearfix']) . Html::tag('div', $seo,
                        ['style' => 'color: #ccc; text-align: left']);

                $td = Html::tag('td', $content, [
                    'id' => 'load-preview-' . $array['load_id'],
                    'class' => 'expanded-load-preview-content',
                    'colspan' => 6,
                ]);
                return Html::tag('tr', $td, ['class' => 'hidden']);
            },
            'summary' => false,
            'columns' => [
                [
                    'label' => Yii::t('element', 'times_listed'),
                    'contentOptions' => [
                        'data-title' => Yii::t('element', 'times_listed')
                    ],
                    'value' => function (array $array) {
                        return $array['times_listed'];
                    }
                ],
                [
                    'attribute' => 'loadCity',
                    'label' => Yii::t('element', 'L-T-17'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-17'],
                    'contentOptions' => [
                        'class' => 'L-T-22 load-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-17')
                    ],
                    'value' => function (array $array) {
                        $load = Load::findOne($array['load_id']);
                        $cities = [];
                        foreach ($load->loadCities as $loadCity) {
                            if ($loadCity->isLoadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($load->loadCities[0]->load_postal_code)) {
                            $postalCode = ' (' . $load->loadCities[0]->load_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'unloadCity',
                    'label' => Yii::t('element', 'L-T-18'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-18'],
                    'contentOptions' => [
                        'class' => 'L-T-23 unload-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-18'),
                    ],
                    'value' => function (array $array) {
                        $load = Load::findOne($array['load_id']);
                        $cities = [];
                        foreach ($load->loadCities as $loadCity) {
                            if ($loadCity->isUnloadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($load->loadCities[0]->unload_postal_code)) {
                            $postalCode = ' (' . $load->loadCities[0]->unload_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'supplier',
                    'label' => Yii::t('element', 'L-T-20'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-20', 'class' => 'text-center'],
                    'contentOptions' => [
                        'class' => 'L-T-25 load-preview-content text-center',
                        'data-title' => Yii::t('element', 'L-T-20')
                    ],
                    'value' => function (array $array) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-caret-down']);
                        return Html::a($icon, '#', [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => Yii::t('element', 'L-T-25'),
                            'class' => 'load-preview-icon',
                            'onclick' => 'collapseExpiredLoadPreview(event, ' . $array['load_id'] . ');',
                        ]);
                    }
                ],
                [
                    'attribute' => 'link',
                    'label' => Yii::t('element', 'L-T-20a'),
                    'format' => 'raw',
                    'headerOptions' => [
                        'id' => 'L-T-20a',
                        'class' => 'text-center',
                    ],
                    'contentOptions' => [
                        'class' => 'L-T-25a load-link-content text-center',
                        'data-title' => Yii::t('element', 'L-T-20a'),
                    ],
                    'value' => function (array $array) {
                        $icon = Html::tag('i', '', ['class' => 'fa fa-share-alt']);
                        return Html::a($icon, '#', [
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => Yii::t('element', 'L-T-20b'),
                            'class' => 'load-link-icon',
                            'onclick' => 'showLoadLink(event, ' . $array['load_id'] . ');',
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
<?php endif; ?>

<?php
Modal::begin([
    'id' => 'load-info-preview-modal',
    'header' => Yii::t('element', 'IA-C-55'),
    'size' => 'modal-lg',
]);

Pjax::begin(['id' => 'load-info-preview-pjax']);
Pjax::end();

Modal::end();

Modal::begin([
    'id' => 'load-link-preview-modal',
    'header' => Yii::t('element', 'IA-C-55a'),
]);

Pjax::begin(['id' => 'load-link-preview-pjax']);
Pjax::end();

Modal::end();

$needToOpenFilters = !$dataProvider->getTotalCount() || Yii::$app->getSession()->get('loads-filter-is-opened', false);
if ($needToOpenFilters) {
    Yii::$app->getSession()->remove('loads-filter-is-opened');
}

$this->registerJs(
    'var openMapText = "' . Yii::t('element', 'Open Map') . '";' .
    'var closeMapText = "' . Yii::t('element', 'Close Map') . '";' .
    'var TEXT_HIDE_MAP = "' . Yii::t('element', 'L-T-2a') . '"; ' .
    'var TEXT_SHOW_MAP = "' . Yii::t('element', 'L-T-2b') . '"; ' .
    'var TEXT_HIDE_FILTERS = "' . Yii::t('element', 'hide_filters') . '"; ' .
    'var TEXT_SHOW_FILTERS = "' . Yii::t('element', 'show_filters') . '"; ' .
    'var actionPreviewLoadInfo = "' . Url::to(['load/preview-load-info', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreviewExpiredLoadInfo = "' . Url::to([
        'load/preview-expired-load-info',
        'lang' => Yii::$app->language
    ]) . '"; ' .
    'var mapTranslate = "' . Yii::t('element', 'MAP_TRANSLATE') . '"; ' .
    'var actionRenderContactMap = "' . Url::to(['load/render-map-contact', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRenderMap = "' . Url::to(['load/render-map', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRenderFilters = "' . Url::to(['load/render-filters', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreviewLoadLink = "' . Url::to(['load/preview-load-link', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionLogLoadMapOpen = "' . Url::to(['load/log-open-map', 'lang' => Yii::$app->language]) . '"; ' .
    'var needToOpenFilters = ' . ($needToOpenFilters ? "true" : "false") . ';',
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/loads.js', ['depends' => [JqueryAsset::className()]]);