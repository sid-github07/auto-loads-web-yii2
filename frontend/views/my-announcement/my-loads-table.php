<?php

use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use common\components\Location;
use common\models\LoadPreview;
use common\components\helpers\Html as AutoLoadsHtml;
use kartik\icons\Icon;
use common\components\PotentialHaulier;
use common\components\Searches24h;
use common\components\Trucks;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 */

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'tableOptions' => ['class' => 'table responsive-table'],
    'rowOptions' => function (Load $load) {
        if ($load->date_of_expiry < time()) {
            return ['class' => 'inactive-load'];
        }
        return null;
    },
    'options' => [
        'id' => 'my-loads-grid-view',
        'class' => 'grid-view my-loads-table-extended ',
    ],
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'headerOptions' => [
                'class' => 'text-center check-all-collumn',
                'width' => '1%',
            ],
            'contentOptions' => [
                'class' => 'text-center',
                'data-title' => Yii::t('text', 'MARK_AS_CHECKED'),
            ],
        ],
        [
            'attribute' => 'Cars',
            'label' => Yii::t('element', 'advertisement'),
            'headerOptions' => ['id' => 'adv-count'],
            'format' => 'raw',
            'contentOptions' => [
                'class' => 'L-T-21',
                'data-title' => Yii::t('element', 'advertisement')
            ],
            'value' => function (Load $load) {
                $pos = $load->car_pos_adv;
                if ($pos === 0) {
                    return null;
                }
                $icon = Html::tag('div', 'directions_car', ['class' => 'material-icons ']);
                $span = Html::tag('div', $pos, ['class' => 'load-cars']);
                return $icon . $span;
            }
        ],
        [
            'attribute' => 'date',
            'label' => Yii::t('element', 'IA-C-23'),
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'load-date-collumn',
                'width' => '15%',
            ],
            'contentOptions' => [
                'class' => 'date-column-content text-center',
                'data-title' => Yii::t('element', 'IA-C-23'),
            ],
            'value' => function (Load $load) {
                $emptyDate = Html::tag('i', Yii::t('yii', '(not set)'), ['class' => 'empty-date']);
                $load->date = empty($load->date) ? 0 : date('Y-m-d', $load->date);
                return (empty($load->date) ? $emptyDate : $load->date) . DatePicker::widget([
                        'model' => $load,
                        'addon' => Html::icon('pencil', [
                            'title' => Yii::t('element', 'IA-C-23a'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]),
                        'attribute' => 'date',
                        'options' => [
                            'id' => 'MK-C-15_' . $load->id,
                            'class' => 'MK-C-15 edit-load-date-input hidden',
                            'data-id' => $load->id,
                        ],
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'startDate' => date('Y-m-d'),
                        ],
                        'clientEvents' => [
                            'change.bfhdatepicker' => 'changeLoadDate(' . $load->id . ')',
                        ],
                    ]);
            }
        ],
        [
            'attribute' => 'loadCity',
            'label' => Yii::t('element', 'IA-C-25'),
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'load-city-collumn',
                'width' => '20%',
            ],
            'contentOptions' => [
                'class' => 'load-city-collumn-content',
                'data-title' => Yii::t('element', 'IA-C-25'),
            ],
            'value' => function (Load $load) {
                $cities = [];
                foreach ($load->loadCities as $loadCity) {
                    if ($loadCity->type === LoadCity::LOADING) {
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
            'label' => Yii::t('element', 'IA-C-30'),
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'unload-city-collumn',
                'width' => '20%',
            ],
            'contentOptions' => [
                'class' => 'unload-city-collumn-content',
                'data-title' => Yii::t('element', 'IA-C-30'),
            ],
            'value' => function (Load $load) {
                $cities = [];
                foreach ($load->loadCities as $loadCity) {
                    if ($loadCity->type === LoadCity::UNLOADING) {
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
            'attribute' => 'load',
            'label' => Yii::t('element', 'IA-C-30a'),
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'load-collumn',
                'width' => '25%',
            ],
            'contentOptions' => [
                'class' => 'load-collumn-content',
                'data-title' => Yii::t('element', 'IA-C-30a'),
            ],
            'value' => function (Load $load) {
                return LoadCar::getLoadInfo($load) .
                    Html::a(null, '#', [
                        'class' => 'MKC-16 glyphicon glyphicon-pencil edit-load-icon',
                        'title' => Yii::t('element', 'MK-C-16'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'onclick' => 'renderLoadEditingForm(event, ' . $load->id . ')',
                    ]);
            }
        ],
        [
            'attribute' => 'date_of_expiry',
            'label' => Yii::t('element', 'MK-C-17'),
            'headerOptions' => [
                'class' => 'date-of-expiry-collumn',
                'width' => '15%',
            ],
            'contentOptions' => [
                'class' => 'date-of-expiry-collumn-content',
                'data-title' => Yii::t('element', 'MK-C-17'),
            ],
            'value' => function (Load $load) {
                return date('Y-m-d', $load->date_of_expiry);
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
            'value' => function (Load $load) {
                $location = Location::getGeoLocation();
                $date = date_create(date('Y-m-d H:i:s', $load->created_at));
                if ($location != null) {
                    $date->setTimeZone(new DateTimeZone($location->timeZone));
                }
                return $date->format('Y-m-d H:i:s');
            }
        ],
    ],
    'pager' => [
        'firstPageLabel' => Yii::t('text', 'FIRST_PAGE_LABEL'),
        'lastPageLabel' => Yii::t('text', 'LAST_PAGE_LABEL'),
    ],
    'afterRow' => function (Load $load) {
        if ($load->isActivated()) {
            $check = 'check';
            $invisibleLoad = '';
            $event = 'makeLoadsInvisible(event, ' . $load->id . ')';
            $title = Yii::t('element', 'MK-C-20');
        } else {
            $check = 'unchecked';
            $invisibleLoad = Html::tag('div', Yii::t('text', 'NOT_SHOWN_LOAD'), ['class' => 'not-shown-load']);
            $event = 'makeLoadsVisible(event, ' . $load->id . ')';
            $title = Yii::t('element', 'MK-C-19');
        }

        $visibilityContent = Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-' . $check]), '#',
                [
                    'title' => $title,
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'onclick' => $event,
                ]) . $invisibleLoad . Html::tag('p', $title,
                ['class' => 'announcement-text', 'onclick' => $event,]);;
        $visibilityIcon = Html::tag('div', $visibilityContent, ['class' => 'option-div']);

        $icon = Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash']);
        $removeContent = Html::a($icon, '#', [
                'title' => Yii::t('element', 'MK-C-21'),
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'onclick' => 'removeLoads(event, ' . $load->id . ')',
            ]) .
            Html::tag('p', Yii::t('element', 'MK-C-21'),
                ['class' => 'announcement-text', 'onclick' => 'removeLoads(event, ' . $load->id . ')',]);
        $removeIcon = Html::tag('div', $removeContent, ['class' => 'option-div']);

        $advIcon = '';
        if ($load->days_adv === 0 && $load->car_pos_adv === 0) {
            $icon = Html::tag('span', null, ['class' => 'fa fa-bullhorn']);
            $advIconContent = Html::a($icon, '#', [
                    'title' => Yii::t('element', 'advertise_load'),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'onclick' => 'renderAdvertizeForm(event, ' . $load->id . ')',
                ]) . Html::tag('p', Yii::t('element', 'advertise_load'), [
                    'class' => 'announcement-text',
                    'onclick' => 'renderAdvertizeForm(event, ' . $load->id . ')',
                ]);

            $advIcon = Html::tag('div', $advIconContent, ['class' => 'option-div']);
        }

        $openContactIcon = '';
        if (!$load->isOpenContacts()) {
            $icon = Html::tag('span', null, ['class' => 'fa fa-eye']);
            $openContactIconContent = Html::a($icon, '#', [
                    'title' => Yii::t('element', 'set_open_contacts'),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'onclick' => 'renderLoadOpenContactsForm(event, ' . $load->id . ')',
                ]) . Html::tag('p', Yii::t('element', 'set_open_contacts'), [
                    'class' => 'announcement-text',
                    'onclick' => 'renderLoadOpenContactsForm(event, ' . $load->id . ')',
                ]);

            $openContactIcon = Html::tag('div', $openContactIconContent, ['class' => 'option-div']);
        }

        $icon = Html::tag('span', null, ['class' => 'fa fa-search']);
        $previewIconContent = Html::a($icon, '#', [
                'title' => Yii::t('element', 'load_previews'),
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'onclick' => 'renderPreviewForm(event, ' . $load->id . ')',
            ]) . Html::tag('p', Yii::t('element', 'load_previews') .
                AutoLoadsHtml::getBadge(
                    null,
                    LoadPreview::find()->where(['load_id' => $load->id])->count(),
                    false,
                    true,
                    ['style' => 'margin-left: 3px; margin-top: -1px']
                ),
                ['class' => 'announcement-text', 'onclick' => 'renderPreviewForm(event, ' . $load->id . ')',]);
        $previewIcon = Html::tag('div', $previewIconContent, ['class' => 'option-div']);

        if ($openContactIcon || $advIcon) {
            $advGroupHtml = Html::tag(
                'div',
                Html::tag('span', null,
                    ['class' => 'fa fa-bullhorn', 'style' => 'color: rgba(0,0,0,.54)']) . Html::tag('p',
                    Yii::t('element', 'advertise_load')) . Html::tag('span', '', ['class' => 'caret']) . Html::tag('ul',
                    Html::tag('li', $openContactIcon) . Html::tag('li', $advIcon)),
                ['class' => 'option-div simple-dropdown']
            );
        } else {
            $advGroupHtml = '';
        }

        $potentialsCount = (new PotentialHaulier($load))->getCountOfPotential();
        $searches24hCount = (new Searches24h($load))->getCountOfSearchesLast24h();
        $trucksCount = (new Trucks($load))->getCountOfTrucks();
        $buttons = [
            'potential-hauliers' => [
                'text' => trim(sprintf('%s %s', Yii::t('element', 'Potential hauliers'),
                    ($potentialsCount ? $potentialsCount : ''))),
                'icon' => Icon::show('truck', [], Icon::FA),
                'options' => [
                    'onclick' => 'renderCustomLoadForm(' . $load->id . ', actionLoadPotentialHauliers, "load-potential-haulier-modal")',
                    'class' => 'option-div',
                ]
            ],
            'searches-in-24h' => [
                'text' => AutoLoadsHtml::getBadge(Yii::t('element', 'Searches in 24h'),
                    $searches24hCount, false),
                'icon' => Icon::show('clock-o', [], Icon::FA),
                'options' => [
                    'onclick' => 'renderCustomLoadForm(' . $load->id . ', actionLoadSearchesIn24h, "load-searches-in-24-modal")',
                    'class' => 'option-div',
                ]
            ],
            'trucks' => [
                'text' => AutoLoadsHtml::getBadge(Yii::t('element', 'Trucks'),
                    $trucksCount, false),
                'icon' => Icon::show('truck', [], Icon::FA),
                'options' => [
                    'onclick' => 'renderCustomLoadForm(' . $load->id . ', actionLoadTrucks, "load-trucks-modal")',
                    'class' => 'option-div',
                ]
            ]
        ];
        $buttonsRenderedList = [];
        foreach ($buttons as $btn) {
            if (isset($btn['condition']) && !$btn['condition']) {
                continue;
            }
            $text = Html::tag('p', $btn['text'], ['class' => 'announcement-text']);
            $buttonsRenderedList[] = Html::tag('div', Html::a($btn['icon']) . $text, $btn['options']);
        }

        $toRender = $visibilityIcon . $removeIcon . $advGroupHtml . $previewIcon . implode($buttonsRenderedList);
        return Html::tag('tr', Html::tag('td', Html::tag('div', $toRender, [
            'style' => 'display: flex; justify-content: space-around; flex-wrap: wrap;'
        ]), ['colspan' => 9]), ['class' => 'buttons-menu']);
    }
]);