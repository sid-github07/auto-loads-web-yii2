<?php

use backend\controllers\ClientController;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var ActiveDataProvider $dataProvider */
?>

<div class="responsive-table-wrapper">
    <div class="row">
        <div class="col-xs-3">
            <span class="red-row span-block"></span><p class="color-desc"><?php echo Yii::t('app', 'deleted_records')?></p>
        </div>
    </div>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterSelector' => 'select[name="per-page"]',
        'options' => [
            'class' => 'custom-gridview loads-list-gridview text-center table-responsive',
        ],
        'tableOptions' => [
            'class' => 'table table-striped responsive-table'
        ],
        'afterRow' => function (Load $load) {
            return Html::beginTag('tr', ['class' => 'hidden load-preview-row text-left expanded-row-content']) .
                Html::beginTag('td', ['id' => $load->id, 'colspan' => '7']) .
                Html::endTag('td') .
                Html::endTag('tr');
        },
        'rowOptions' => function(Load $load)
        {
            if ($load->status === Load::INACTIVE) {
                return ['class' => 'red-row'];
            }
            return [];
        },
        'columns' => [
            [
                'attribute' => 'preview_count',
                'label' => Yii::t('element', 'load_preview_count'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-326',
                    'class' => 'load-created-at-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'load-created-at-column-content text-left A-C-326',
                    'data-title' => Yii::t('element', 'A-C-326'),
                ],
                'value' => function (Load $load) {
                    $previewsCount = count($load->loadPreviews);
                    $previewsText = Html::a($previewsCount, '#', [
                        'class' => 'load-previews A-C-327',
                        'onclick' => 'previews(event, ' . $load->id . ')',
                    ]);

                    return  $previewsText;
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('element', 'A-C-326'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-326',
                    'class' => 'load-created-at-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'load-created-at-column-content text-left A-C-326',
                    'data-title' => Yii::t('element', 'A-C-326'),
                ],
                'value' => function (Load $load) {
                    $timestamp = $load->addDateOffset('created_at');
                    if (is_string($timestamp)) {
                        return $timestamp;
                    }

                    $date = Load::convertTimestampToDateText($timestamp);
                    $dateText = Html::tag('div', $date);

                    $previewsCount = count($load->loadPreviews);
                    $previewsText = Html::a($previewsCount . ' ' . Yii::t('app', 'VIEWS'), '#', [
                        'class' => 'load-previews A-C-327',
                        'onclick' => 'previews(event, ' . $load->id . ')',
                    ]);

                    return  $dateText;
                }
            ],
            [
                'attribute' => 'date',
                'label' => Yii::t('element', 'A-C-328'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-328',
                    'class' => 'load-date-column text-center',
                ],
                'contentOptions' => [
                    'class' => 'load-date-column-content text-center A-C-329',
                    'data-title' => Yii::t('element', 'A-C-328'),
                ],
                'value' => function (Load $load) {
                    $timestamp = $load->addDateOffset('date');
                    if (is_string($timestamp)) {
                        return $timestamp;
                    }

                    $date = Load::convertTimestampToDateText($timestamp, false);
                    return $date;
                }
            ],
            [
                'attribute' => 'loadCity',
                'label' => Yii::t('element', 'A-C-330'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-330',
                    'class' => 'load-city-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'load-city-column-content city-column-content A-C-332',
                    'data-title' => Yii::t('element', 'A-C-330'),
                ],
                'value' => function (Load $load) {
                    $cities = [];
                    foreach ($load->loadCities as $city) {
                        if ($city->type === LoadCity::LOADING) {
                            $city->addCitiesToCountryList($cities);
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
                'label' => Yii::t('element', 'A-C-333'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-333',
                    'class' => 'unload-city-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'unload-city-column-content city-column-content A-C-335',
                    'data-title' => Yii::t('element', 'A-C-333'),
                ],
                'value' => function (Load $load) {
                    $cities = [];
                    foreach ($load->loadCities as $city) {
                        if ($city->type === LoadCity::UNLOADING) {
                            $city->addCitiesToCountryList($cities);
                        }
                    }
                    $postalCode = '';
                    if (!empty($load->loadCities[0]->unload_postal_code)) {
                        $postalCode = ' ('. $load->loadCities[0]->unload_postal_code .')';
                    }
                    return LoadCity::getFormattedCities($cities). $postalCode;
                }
            ],
            [
                'attribute' => 'load',
                'label' => Yii::t('element', 'A-C-336'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-336',
                    'class' => 'load-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'load-column-content A-C-337 text-left',
                    'data-title' => Yii::t('element', 'A-C-336'),
                ],
                'value' => function (Load $load) {
                    return LoadCar::getLoadInfo($load);
                }
            ],	
            [
                'attribute' => 'company',
                'label' => Yii::t('element', 'A-C-338'),
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-338',
                    'class' => 'company-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'company-column-content A-C-338 text-left',
                    'data-title' => Yii::t('element', 'A-C-338'),
                ],
                'value' => function (Load $load) {
                    if (!isset($load->user)) {
                        return null; // NOTE: not registered user can create load, therefore user is not set
                    }

                    $company = $load->user->getCompany();
                    if (is_null($company)) {
                        return null;
                    }

                    return Html::a($company->getTitleByType(), [
                        'client/company',
                        'lang' => Yii::$app->language,
                        'id' => $company->id,
                        'tab' => ClientController::TAB_COMPANY_INFO,
                    ]);
                }
            ],
            [
                'attribute' => 'preview',
                'label' => '',
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-339',
                    'class' => 'preview-column text-center',
                ],
                'contentOptions' => [
                    'class' => 'preview-column-content A-C-339 text-center',
                    'data-title' => Yii::t('element', 'A-C-339a'),
                ],
                'value' => function (Load $load) {
                    $caretIcon = Html::tag('i', null, ['class' => 'fa fa-caret-down', 'aria-hidden' => true]);
                    return Html::a($caretIcon, '#', [
                        'class' => 'preview-icon closed',
                        'onclick' => 'loadPreview(event, ' . $load->id . ')',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('element', 'A-C-339a'),
                    ]);
                }
            ],
            [
                'attribute' => 'active',
                'format' => 'raw',
                'headerOptions' => [
                    'id' => 'A-C-326',
                    'class' => 'load-created-at-column text-left',
                ],
                'contentOptions' => [
                    'class' => 'load-created-at-column-content text-left A-C-326',
                    'data-title' => Yii::t('element', 'A-C-326'),
                ],
                'value' => function (Load $load) {
                    if ($load->active === 0) {
                        $title = Yii::t('element', 'load_inactive');
                        $class = 'load-inactive-tag';
                    } else {
                        $title = Yii::t('element', 'load_active');
                        $class = 'load-active-tag';

                    }

                    if ($load->status === Load::INACTIVE) {
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
                'value' => function (Load $load) {
                    if ($load->isActivated()) {
                        $check = 'check';
                        $title = Yii::t('element', 'load_uncheck');
                        $tag = '';
                    } else {
                        $check = 'unchecked';
                        $title = Yii::t('element', 'load_check');
                        $tag = Html::tag('p', $title, ['class' => 'announcement-text visible-tag']);
                    }

                    $visibilityContent = Html::a(Html::tag('span', null, [
                            'class' => 'visible-url glyphicon glyphicon-' . $check
                        ]), [
                            'load/toggle-visibility',
                            'lang' => Yii::$app->language,
                            'id' => $load->id,
                        ], [
                            'title' => $title,
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]) . $tag;
                    $visibilityIcon = Html::tag('div', $visibilityContent, ['class' => 'option-div']);


                    $trashIcon = Html::tag('i', null, ['class' => 'fa fa-trash', 'aria-hidden' => true]);
                    $removeContent =  Html::a($trashIcon, [
                        'load/remove-load',
                        'lang' => Yii::$app->language,
                        'id' => $load->id,
                    ], [
                        'class' => 'remove-icon',
                        'data-placement' => 'top',
                        'data-toggle' => 'tooltip',
                        'title' => Yii::t('element', 'A-C-339b'),
                    ]);
                    if ($load->status === Load::ACTIVATED) {
                        $removeIcon = Html::tag('div', $removeContent, ['class' => 'option-div']);
                    } else {
                        $removeIcon = '';
                    }

                    $tag = '';
                    if ($load->car_pos_adv !== 0 && $load->days_adv !== 0) {
                        $tag = Html::tag('p', Yii::t('element', 'advert_advertised'), ['class' => 'announcement-text advert-tag']);
                    }

                    $advIcon = Html::tag('i', null, ['class' => 'fa fa-bullhorn adv-url', 'aria-hidden' => true]);
                    $advIconContent = Html::a($advIcon, ['#'], [
                            'class' => 'advert-url',
                            'title' => Yii::t('element', 'advertise_load'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'onclick' => 'renderAdvertizeForm(event, ' . $load->id . ')',
                    ]) . $tag;

                    $advIcon = Html::tag('div', $advIconContent, ['class' => 'option-div']);

                    if ($load->isOpenContacts()) {
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
                        'onclick' => 'renderOpenContactsForm(event, ' . $load->id . ')',
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
    ]); ?>
</div>
<?php
Modal::begin([
    'id' => 'previews-modal',
    'header' => Yii::t('element', 'A-C-327'),
    'size' => 'modal-lg',
]);
    Pjax::begin(['id' => 'loads-pjax']);
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
    'var actionPreviewsLoads = "' . Url::to(['load/previews', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionLoadPreview = "' . Url::to(['load/load-preview', 'lang' => Yii::$app->language]) . '";',
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/url.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/index.js', ['depends' => JqueryAsset::className()]);