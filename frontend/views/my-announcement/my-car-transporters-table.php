<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use dosamigos\datepicker\DatePicker;
use dosamigos\editable\Editable;
use kartik\icons\Icon;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\components\Location;

/** @var ActiveDataProvider $dataProvider */

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'tableOptions' => ['class' => 'table responsive-table'],
    'rowOptions' => function (CarTransporter $carTransporter) {
        if ($carTransporter->date_of_expiry < time()) {
            return ['class' => 'inactive-load'];
        }
        return null;
    },
    'options' => [
        'id' => 'my-car-transporters-grid-view',
        'class' => 'grid-view my-loads-table',
    ],
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'headerOptions' => [
                'class' => 'text-center check-all-column',
                'width' => '1%',
            ],
            'contentOptions' => [
                'class' => 'C-T-51 text-center',
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
            'value' => function (CarTransporter $transporter) {
                $pos = $transporter->car_pos_adv;
                if ($pos === 0) {
                    return null;
                }
                $icon = Html::tag('div', 'directions_car', ['class' => 'material-icons ']);
                $span = Html::tag('div', $pos, ['class' => 'load-cars']);
                return $icon . $span;
            }
        ],
        [
            'attribute' => 'available_from',
            'label' => Yii::t('element', 'C-T-46'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-46',
                'class' => 'car-transporter-available-from-column text-center',
                'width' => '15%',
            ],
            'contentOptions' => [
                'class' => 'date-column-content text-center',
                'data-title' => Yii::t('element', 'C-T-46'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                if (empty($carTransporter->available_from)) {
                    $carTransporter->available_from = 0;
                } else {
                    $carTransporter->available_from = date('Y-m-d', $carTransporter->available_from);
                }

                $emptyDate = Html::tag('i', Yii::t('yii', '(not set)'), ['class' => 'empty-date']);
                return (empty($carTransporter->available_from) ? $emptyDate : $carTransporter->available_from) .
                    DatePicker::widget([
                        'model' => $carTransporter,
                        'addon' => Html::icon('pencil', [
                            'title' => Yii::t('element', 'C-T-46a'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                        ]),
                        'attribute' => 'available_from',
                        'options' => [
                            'id' => 'C-T-52_' . $carTransporter->id,
                            'class' => 'C-T-52 edit-car-transporter-available-from-input hidden',
                            'data-id' => $carTransporter->id,
                        ],
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'startDate' => date('Y-m-d'),
                        ],
                        'clientEvents' => [
                            'change.bfhdatepicker' => 'changeCarTransporterAvailableFromDate(' . $carTransporter->id . ')',
                        ],
                    ]);
            }
        ],
        [
            'attribute' => 'loadCity',
            'label' => Yii::t('element', 'C-T-47'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-47',
                'class' => 'load-city-column',
                'width' => '20%',
            ],
            'contentOptions' => [
                'class' => 'C-T-53 load-city-column-content',
                'data-title' => Yii::t('element', 'C-T-47'),
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
                return current($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_LOAD) . $postalCode;
            }
        ],
        [
            'attribute' => 'unloadCity',
            'label' => Yii::t('element', 'C-T-48'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-48',
                'class' => 'unload-city-column',
                'width' => '20%',
            ],
            'contentOptions' => [
                'class' => 'C-T-54 unload-city-column-content',
                'data-title' => Yii::t('element', 'C-T-48'),
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
                return current($carTransporterCities)->formatTableCities(CarTransporterCity::TYPE_UNLOAD) . $postalCode;
            }
        ],
        [
            'attribute' => 'quantity',
            'label' => Yii::t('element', 'C-T-49'),
            'format' => 'raw',
            'headerOptions' => [
                'id' => 'C-T-49',
                'class' => 'quantity-column text-center',
                'width' => '25%',
            ],
            'contentOptions' => [
                'class' => 'C-T-55 quantity-column-content text-center',
                'data-title' => Yii::t('element', 'C-T-49'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                $editIcon = Html::tag('div', Icon::show('pencil', [], Icon::BSG), [
                    'class' => 'edit-quantity-icon',
                    'onclick' => 'editQuantity(event,'.  $carTransporter->id .')',
                ]); //TODO: Add event to open inline editor on edit icon click
                return Editable::widget([
                    'model' => $carTransporter,
                    'attribute' => 'quantity',
                    'url' => [
                        'my-car-transporter/change-quantity',
                        'lang' => Yii::$app->language,
                        'id' => $carTransporter->id,
                    ],
                    'type' => 'select2',
                    'mode' => 'inline',
                    'options' => ['id' => 'w' . $carTransporter->id],
                    'clientOptions' => [
                        /* @see http://vitalets.github.io/x-editable/docs.html documentation for more information */
                        'toggle' => 'manual',
                        'emptytext' => Yii::t('element', 'C-T-29'),
                        'emptyclass' => 'editable editable-click', // FIXME
                        'highlight' => null,
                        'showbuttons' => false,
                        'select2' => ['width' => '200px'], // FIXME
                        'source' => CarTransporter::getEditableQuantities(),
                    ],
                ]) . $editIcon;
            }
        ],
        [
            'attribute' => 'date_of_expiry',
            'label' => Yii::t('element', 'C-T-50'),
            'headerOptions' => [
                'id' => 'C-T-50',
                'class' => 'date-of-expiry-column',
                'width' => '15%',
            ],
            'contentOptions' => [
                'class' => 'C-T-56 date-of-expiry-column-content',
                'data-title' => Yii::t('element', 'C-T-50'),
            ],
            'value' => function (CarTransporter $carTransporter) {
                return date('Y-m-d', $carTransporter->date_of_expiry);
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
            'value' => function (CarTransporter $carTransporter) {
                $location = Location::getGeoLocation();
                $date = date_create(date('Y-m-d H:i:s', $carTransporter->created_at));
                if ($location != null) {
                    $date->setTimeZone(new DateTimeZone($location->timeZone));
                }
                return $date->format('Y-m-d H:i:s');
            }
        ],
        [
            'attribute' => '',
            'label' => null,
            'format' => 'raw',
            'headerOptions' => [
                'class' => 'change-car-transporter-visibility-column',
                'width' => '2%',
            ],
            'contentOptions' => ['class' => 'C-T-57 change-car-transporter-visibility-column-content single-action-column'],
            'value' => function (CarTransporter $carTransporter) {
                if ($carTransporter->isVisible()) {
                    $icon = 'check';
                    $invisibleText = '';
                    $event = 'makeCarTransporterInvisible(event, ' . $carTransporter->id . ')';
                    $title = Yii::t('element', 'C-T-57a');
                } else {
                    $icon = 'unchecked';
                    $invisibleText = Html::tag('div', Yii::t('text', 'NOT_SHOWN_LOAD'), ['class' => 'not-shown-load']);
                    $event = 'makeCarTransporterVisible(event, ' . $carTransporter->id . ')';
                    $title = Yii::t('element', 'C-T-57b');
                }
//
//                return Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-' . $icon]), '#', [
//                    'title' => $title,
//                    'data-toggle' => 'tooltip',
//                    'data-placement' => 'top',
//                    'onclick' => $event,
//                ]) . $invisibleText;
//
//
//                $icon = Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash']);
//                return Html::a($icon, '#', [
//                    'title' => Yii::t('element', 'C-T-58'),
//                    'data-toggle' => 'tooltip',
//                    'data-placement' => 'top',
//                    'onclick' => 'removeCarTransporters(event, ' . $carTransporter->id . ')',
//                ]);
//
//                if ($carTransporter->days_adv === 0 && $carTransporter->car_pos_adv === 0) {
//                    $icon = Html::tag('span', null, ['class' => 'fa fa-bullhorn']);
//                    return Html::a($icon, '#', [
//                        'title' => Yii::t('element', 'advertise_load'),
//                        'data-toggle' => 'tooltip',
//                        'data-placement' => 'top',
//                        'onclick' => 'renderAdvertizeTransportForm(event, ' . $carTransporter->id . ')',
//                    ]);
//                }

                //----------------------------------
                $visibilityContent = Html::a(Html::tag('span', null, ['class' => 'glyphicon glyphicon-' . $icon]), '#', [
                        'title' => $title,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'onclick' => $event,
                    ]) . $invisibleText .  Html::tag('p', $title, ['class' => 'announcement-text', 'onclick' => $event,]);;
                $visibilityIcon = Html::tag('div', $visibilityContent, ['class' => 'option-div']);

                $icon = Html::tag('span', null, ['class' => 'glyphicon glyphicon-trash']);
                $removeContent =  Html::a($icon, '#', [
                        'title' => Yii::t('element', 'MK-C-21'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'onclick' => 'removeLoads(event, ' . $carTransporter->id . ')',
                    ]) .
                    Html::tag('p', Yii::t('element', 'MK-C-21'), ['class' => 'announcement-text', 'onclick' => 'removeCarTransporters(event, ' . $carTransporter->id . ')',]);
                $removeIcon = Html::tag('div', $removeContent, ['class' => 'option-div']);

                $advIcon = '';
                if ($carTransporter->days_adv === 0 && $carTransporter->car_pos_adv === 0) {
                    $icon = Html::tag('span', null, ['class' => 'fa fa-bullhorn']);
                    $advIconContent = Html::a($icon, '#', [
                            'title' => Yii::t('element', 'advertise_load'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'onclick' => 'renderAdvertizeForm(event, ' . $carTransporter->id . ')',
                        ]) .   Html::tag('p', Yii::t('element', 'advertise_load'), ['class' => 'announcement-text',  'onclick' => 'renderAdvertizeTransportForm(event, ' . $carTransporter->id . ')',]);

                    $advIcon = Html::tag('div', $advIconContent, ['class' => 'option-div']);
                }
                
                $openContactIcon = '';
                if (!$carTransporter->isOpenContacts()) {
                    $icon = Html::tag('span', null, ['class' => 'fa fa-eye']);
                    $openContactIconContent = Html::a($icon, '#', [
                        'title' => Yii::t('element', 'open_contacts'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'onclick' => 'renderTransporterOpenContactsForm(event, ' . $carTransporter->id . ')',
                    ]) .  Html::tag('p', Yii::t('element', 'set_open_contacts'), [
                        'class' => 'announcement-text', 
                        'onclick' => 'renderTransporterOpenContactsForm(event, ' . $carTransporter->id . ')'
                    ]);

                    $openContactIcon = Html::tag('div', $openContactIconContent, ['class' => 'option-div']);
                }

                $icon = Html::tag('span', null, ['class' => 'fa fa-search']);
                $previewIconContent = Html::a($icon, '#', [
                    'title' => Yii::t('element', 'transporter_preview'),
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'onclick' => 'renderTransporterPreviewForm(event, ' . $carTransporter->id . ')',
                ]) .   Html::tag('p', Yii::t('element', 'transporter_preview'), [
                    'class' => 'announcement-text', 
                    'onclick' => 'renderTransporterPreviewForm(event, ' . $carTransporter->id . ')'
                ]);

                $previewIcon = Html::tag('div', $previewIconContent, ['class' => 'option-div']);

                return $visibilityIcon . $removeIcon . $advIcon . $openContactIcon . $previewIcon;
            }
        ],
    ],
    'pager' => [
        'firstPageLabel' => Yii::t('text', 'FIRST_PAGE_LABEL'),
        'lastPageLabel' => Yii::t('text', 'LAST_PAGE_LABEL'),
    ],
]);