<?php

use common\components\Model;
use common\models\Load;
use common\models\LoadCar;
use kartik\icons\Icon;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var ActiveForm $form */
/** @var Load $load */
/** @var LoadCar[] $loadCars */
/** @var string $widgetContainer */
/** @var string $widgetBody */
/** @var string $widgetItem */

DynamicFormWidget::begin([
    'widgetContainer' =>  $widgetContainer,
    'widgetBody' => '.' . $widgetBody,
    'widgetItem' => '.' . $widgetItem,
    'limit' => LoadCar::QUANTITY_MAX_VALUE,
    'min' => LoadCar::QUANTITY_MIN_VALUE,
    'insertButton' => '.add-load-car-model',
    'deleteButton' => '.remove-load-car-model',
    'model' => current($loadCars),
    'formId' => $form->options['id'],
    'formFields' => [
        'quantity',
        'model',
        'price',
        'state',
    ],
]);
                
    echo Html::beginTag('div', ['class' => $widgetBody]);

        foreach ($loadCars as $i => $loadCar) {

            $loadCar->scenario = LoadCar::SCENARIO_ANNOUNCE_CLIENT;
            $editStructureColumnGrid = (!$load->isNewRecord && $load->type == Load::TYPE_PARTIAL && $load->payment_method == Load::FOR_CAR_MODEL ? 'col-lg-3 col-md-6 col-sm-6 col-xs-12' : 'col-lg-4 col-md-4 col-sm-4 col-xs-12');

            echo Html::beginTag('div', ['class' => $widgetItem . ' row']);

                if (!$loadCar->isNewRecord) {
                    echo Html::activeHiddenInput($loadCar, "[{$i}]id");
                }
                
                echo Html::beginTag('div', ['class' => 'loadcar-quantity-price-container']);
                
                    echo $form->field($loadCar, "[{$i}]quantity", [
                        'options' => [
                            'class' => 'field-loadcar-quantity ' . ($form->options['id'] == 'edit-load-form' ? $editStructureColumnGrid : 'col-lg-3 col-md-6 col-sm-6 col-xs-12') . (!$load->isNewRecord && $load->type == Load::TYPE_FULL && $load->payment_method == Load::FOR_CAR_MODEL ? ' hidden' : '') . (!$load->isNewRecord && $load->type == Load::TYPE_PARTIAL ? ' required' : ''),
                        ],
                        'inputOptions' => [
                            'class' => 'form-control IA-C-9 quantity',
                        ],
                    ])->dropDownList(LoadCar::getQuantities(), [
                        'prompt' => Yii::t('app', 'DEFAULT_PROMPT'),
                    ])->label(Yii::t('element', 'IA-C-9'));
                    
                echo Html::endTag('div');
                
                echo $form->field($loadCar, "[{$i}]model", [
                    'options' => [
                        'class' => 'field-loadcar-model ' . ($form->options['id'] == 'edit-load-form' ? $editStructureColumnGrid : 'col-lg-3 col-md-6 col-sm-6 col-xs-12') . (!$load->isNewRecord && $load->type == Load::TYPE_PARTIAL ? ' required' : ''),
                    ],
                    'inputOptions' => [
                        'class' => 'form-control IA-C-10 model',
                        'placeholder' => Yii::t('text', 'VEHICLE_MODEL_PLACEHOLDER'),
                        'maxlength' => LoadCar::MODEL_MAX_LENGTH,
                    ],
                ])->label(Yii::t('element', 'IA-C-10'));
                
                echo Html::beginTag('div', ['class' => 'loadcar-quantity-price-container']);
                
                    echo $form->field($loadCar, "[{$i}]price", [
                        'options' => [
                            'class' => 'field-loadcar-price ' . ($form->options['id'] == 'edit-load-form' ? $editStructureColumnGrid : 'col-lg-3 col-md-6 col-sm-6 col-xs-12') . (!$load->isNewRecord && $load->payment_method == Load::FOR_ALL_LOAD ? ' hidden' : ''),
                        ],
                        'inputOptions' => [
                            'class' => 'form-control IA-C-12 price',
                        ],
                        'template' =>
                            "{label}\n" .
                            "<div class=\"input-group\">" .
                                "{input}\n" .
                                "<span class=\"input-group-addon IA-C-13\">" .
                                    Yii::t('element', 'IA-C-13') .
                                "</span>" .
                            "</div>" .
                            "{hint}\n" .
                            "{error}",
                    ])->label(Yii::t('element', 'IA-C-12'));

                echo Html::endTag('div');
                

                echo $form->field($loadCar, "[{$i}]state", [
                    'options' => [
                        'class' => 'field-loadcar-state ' . ($form->options['id'] == 'edit-load-form' ? $editStructureColumnGrid : 'col-lg-3 col-md-6 col-sm-6 col-xs-12'),
                    ],
                    'inputOptions' => [
                        'class' => 'form-control IA-C-14',
                    ],
                ])->dropDownList(LoadCar::getStates(), [
                    'prompt' => Yii::t('app', 'DEFAULT_PROMPT'),
                ])->label(Yii::t('element', 'IA-C-14'));
                
                echo Html::beginTag('div', ['class' => 'add-remove-loadcar-model-buttons col-lg-1 col-md-1 col-sm-1 col-xs-12']);

                    echo Html::button('<i class="fa fa-trash" aria-hidden="true"></i>', [
                        'id' => 'IA-C-11b',
                        'class' => 'remove-load-car-model ' . (count($loadCars) > 1 ? '' : ' hidden'),
                    ]);


                echo Html::endTag('div');

            echo Html::endTag('div');
        }

    echo Html::endTag('div');
    
    echo Html::beginTag('div', ['class' => 'clearfix text-right']);
    
        echo Html::button(Icon::show('plus', '', Icon::FA) . Yii::t('element', 'IA-C-11a'), [
            'id' => 'IA-C-11a',
            'class' => 'secondary-button add-load-car-model',
        ]);
        
    echo Html::endTag('div');

DynamicFormWidget::end();

$this->registerJs(
    'var formId; ' .
    'var widgetContainer; ' .
    'var widgetBody; ' .
    'var widgetItem; ' .
    'var LoadCar = "' . Model::getClassName($loadCar) . '".toLowerCase(); ' .
    'var TYPE_PARTIAL = "' . Load::TYPE_PARTIAL . '"; ' .
    'var TYPE_FULL = "' . Load::TYPE_FULL . '"; ' .
    'var FOR_CAR_MODEL = "' . Load::FOR_CAR_MODEL . '"; ' .
    'var FOR_ALL_LOAD = "' . Load::FOR_ALL_LOAD . '"; ' .
    'var QUANTITY_MIN_VALUE = "' . LoadCar::QUANTITY_MIN_VALUE . '"; ' .
    'var QUANTITY_MAX_VALUE = "' . LoadCar::QUANTITY_MAX_VALUE . '"; ' .
    'var MODEL_MAX_LENGTH = "' . LoadCar::MODEL_MAX_LENGTH . '"; ' .
    'var CAR_MODEL_MAX_NUMBER_OF_DIGITS = "' . LoadCar::MODEL_MAX_DIGITS . '"; ' .
    'var MODEL_CHARACTERS_LEFT = "' . Yii::t('text', 'MODEL_CHARACTERS_LEFT') . '"; ' .
    'var TOO_MANY_CAR_MODEL_NUMBER_OF_DIGITS = "' . Yii::t('app', 'LOAD_CAR_TOO_MANY_NUMBER_OF_DIGITS_IN_CAR_MODEL', [
        'max' => LoadCar::MODEL_MAX_DIGITS,
    ]) . '"; ' .
    'var LOAD_CAR_MODEL_IS_REQUIRED = "' . Yii::t('app', 'LOAD_CAR_MODEL_IS_REQUIRED') . '"; ' .
    'var QUANTITY_IS_REQUIRED = "' . Yii::t('app', 'LOAD_CAR_QUANTITY_IS_REQUIRED') . '"; ' .
    'var QUANTITY_NOT_INTEGER = "' . Yii::t('app', 'LOAD_CAR_QUANTITY_IS_NOT_INTEGER') . '"; ' .
    'var QUANTITY_TOO_SMALL = "' . Yii::t('app', 'LOAD_CAR_QUANTITY_IS_TOO_SMALL', [
        'min' => LoadCar::QUANTITY_MIN_VALUE,
    ]) . '"; ' .
    'var QUANTITY_TOO_BIG = "' . Yii::t('app', 'LOAD_CAR_QUANTITY_IS_TOO_BIG', [
        'max' => LoadCar::QUANTITY_MAX_VALUE,
    ]) . '"; ' .
    'var TOTAL_QUANTITY_TOO_BIG = "' . Yii::t('app', 'LOAD_CAR_TOTAL_QUANTITY_IS_TOO_BIG', [
        'max' => LoadCar::QUANTITY_MAX_VALUE,
    ]) . '"; ',
View::POS_BEGIN);

$this->registerJsFile(Url::base() . '/dist/js/load/_car-model-validation.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/_car-quantity-validation.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/announce.js', ['depends' => [JqueryAsset::className()]]);