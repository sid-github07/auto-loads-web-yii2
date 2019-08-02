<?php

use common\models\LoadCar;
use common\models\LoadCity;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;
use common\components\Location;

$this->title = Yii::t('seo', 'TITLE_LOAD_SUGGESTIONS');
?>
<div class="responsive-table-wrapper roundtrips-table-wrapper">
    <?php if (empty($suggestions['direct']) && empty($suggestions['additional']) && empty($suggestions['fullUnload']) && !$dataProvider->getTotalCount()): ?>
        <div class="filter-result-heading">
            <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1m'); ?></h2>
        </div>
    <?php endif; ?>
    <?php if (!empty($suggestions['direct'])): ?>
        <div class="filter-result-heading">
            <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1l'); ?></h2>
        </div>
        <h4><?php echo Yii::t('element', 'KP-C-1g'); ?></h4>
        <?php echo Yii::$app->controller->renderPartial('/load/search/results/direct', [
            'sectionClass' => 'direct-loads',
            'noResults' => Yii::t('alert', 'NO_DIRECT_TRANSPORTATION_RESULTS'),
            'directLoads' => $suggestions['direct'],
            'loads' => $loads,
            'showHideButton' => false,
        ]); ?>
    <?php endif; ?>

    <?php if (!empty($suggestions['additional'])): ?>
        <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
            'id' => 'IK-C-30',
            'sectionClass' => 'devious-loads',
            'headingClass' => 'devious-loads-heading',
            'noResults' => 'IK-C-30a',
            'groups' => $suggestions['additional'],
            'loads' => $loads,
            'showHideButton' => false,
        ]); ?>
    <?php endif; ?>

    <?php if (!empty($suggestions['fullUnload'])): ?>
        <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
            'id' => 'IK-C-32',
            'sectionClass' => 'devious-loads',
            'headingClass' => 'devious-loads-heading',
            'noResults' => 'IK-C-30a',
            'groups' => $suggestions['fullUnload'],
            'loads' => $loads,
            'showHideButton' => false,
        ]); ?>
    <?php endif; ?>                
</div>
<?php if ($dataProvider->getTotalCount()) : ?>
<div class="responsive-table-wrapper roundtrips-table-wrapper">
        <div class="filter-result-heading">
            <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1k'); ?></h2>
        </div>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table responsive-table table--striped'
        ],
        'options' => [
            'class' => 'grid-view',
        ],
        'afterRow' => function (LoadCity $loadCity) {
            $td = Html::tag('td', '', [
                'id' => 'load-preview-' . $loadCity->load->id,
                'class' => 'expanded-load-preview-content',
                'colspan' => 7,
            ]);
            return Html::tag('tr', $td, ['class' => 'hidden']);
        },
        'summary' => false,
        'emptyText' => Html::tag('div', Yii::t('app', 'EMPTY_LOADS_TABLE_TEXT'), ['class' => 'empty-loads-text-wrapper']) .
            Html::a(Yii::t('app', 'SEARCH_IN_MORE_DETAILED_SEARCH'), [
                'load/search',
                'lang' => Yii::$app->language,
                'cityId' => isset($city) ? $city->id : null,
            ], [
                'class' => 'primary-button detail-search-btn',
            ]),
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
        'columns' => [
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
                    if (!empty($load->loadCities[0]->load_postal_code)) {
                        $postalCode = ' ('. $load->loadCities[0]->load_postal_code .')';
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
                    if (!empty($load->loadCities[0]->unload_postal_code)) {
                        $postalCode = ' ('. $load->loadCities[0]->unload_postal_code .')';
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
                'attribute' => 'createdAt',
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
                        'onclick' => 'collapseLoadPreview(event, ' . $loadCity->load->id .');',
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
    ]); ?>
</div>
<?php endif; ?>
<?php

Modal::begin([
    'id' => 'load-link-preview-modal',
    'header' => Yii::t('element', 'IA-C-55a'),
]);

    Pjax::begin(['id' => 'load-link-preview-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionLoadPreview = "' . Url::to(['load/preview', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreviewLoadInfo = "' . Url::to(['load/preview-load-info', 'lang' => Yii::$app->language]) . '";'.
	'var actionPreviewLoadLink = "' . Url::to(['load/preview-load-link', 'lang' => Yii::$app->language]) . '"; ',  
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/load/search-results.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/loads.js', ['depends' => [JqueryAsset::className()]]);