<?php

use common\models\Load;
use common\models\LoadCar;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Load $load */
/** @var LoadCar[] $loadCars */
/** @var ActiveForm $form */


echo Html::hiddenInput("type", $load->type, ['id' => 'loadType']);

DynamicFormWidget::begin([
    'widgetContainer' => 'load_cars_editing_container',
    'widgetBody' => '.load-cars-editing-body',
    'widgetItem' => '.load-cars-editing-item',
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
]); ?>

    <div class="load-cars-editing-body">
        <?php foreach ($loadCars as $i => $loadCar): ?>
            <?php
                $partialForCarModelShorter = ($load->isTypePartial() && $load->isPaymentMethodForCarModel() ? ' col-lg-2 col-sm-6 col-xs-12' : ' col-sm-3 col-xs-12');
                $partialForCarModel = ($load->isTypePartial() && $load->isPaymentMethodForCarModel() ? ' col-lg-3 col-sm-6 col-xs-12' : ' col-sm-4 col-xs-12');
                $hidden = ($load->isTypeFull() && $load->isPaymentMethodForCarModel() ? ' hidden' : '');
                $partial = ($load->isTypePartial() ? ' required' : '');
                if($isNew) {
                    $loadCar->scenario = LoadCar::SCENARIO_ANNOUNCE_CLIENT; 
                }
                if(!$isNew) {
                    $loadCar->scenario = LoadCar::SCENARIO_EDIT_CAR_INFO; 
                }
            ?>

            <div class="load-cars-editing-item row">
                <?php echo Html::activeHiddenInput($loadCar, "[{$i}]id"); ?>

                <div class="loadcar-quantity-price-container">
                    <?php echo $form->field($loadCar, "[{$i}]quantity", [
                        'options' => [
                            'class' => 'car-quantity field-loadcar-quantity' . (count($loadCars) > 1 ? $partialForCarModelShorter : $partialForCarModel) . $hidden . $partial,
                        ],
                        'inputOptions' => [
                            'class' => 'form-control IA-C-9',
                            'onchange' => 'validateQuantity()',
                        ],
                    ])
                        ->dropDownList(LoadCar::getQuantities(), [
                            'prompt' => Yii::t('app', 'DEFAULT_PROMPT'),
                        ])->label(Yii::t('element', 'IA-C-9')); ?>
                </div>

                <?php echo $form->field($loadCar, "[{$i}]model", [
                    'options' => [
                        'class' => 'car-model field-loadcar-model' . $partialForCarModel . $partial,
                    ],
                    'inputOptions' => [
                        'class' => 'form-control IA-C-10',
                        'placeholder' => Yii::t('text', 'VEHICLE_MODEL_PLACEHOLDER'),
                        'maxlength' => LoadCar::MODEL_MAX_LENGTH,
                        'onkeyup' => 'printRemainingCharacters(this)',
                    ],
                ])->label(Yii::t('element', 'IA-C-10')); ?>

                <div class="loadcar-quantity-price-container">
                    <?php echo $form->field($loadCar, "[{$i}]price", [
                        'options' => [
                            'class' => 'car-price field-loadcar-price' . ($hidden == '' ? $partialForCarModel : $partialForCarModelShorter) . ($load->isPaymentMethodForAllLoad() ? ' hidden' : ''),
                        ],
                        'inputOptions' => ['class' => 'form-control IA-C-12'],
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
                    ])->label(Yii::t('element', 'IA-C-12')); ?>
                </div>

                <?php echo $form->field($loadCar, "[{$i}]state", [
                    'options' => [
                        'class' => 'car-state field-loadcar-state' . $partialForCarModel,
                    ],
                    'inputOptions' => ['class' => 'form-control IA-C-14'],
                ])->dropDownList(LoadCar::getStates(), [
                    'prompt' => Yii::t('app', 'DEFAULT_PROMPT'),
                ])->label(Yii::t('element', 'IA-C-14')); ?>

                <div class="add-remove-loadcar-model-buttons col-sm-1 col-xs-12">
                    <button id="IA-C-11b" class="remove-load-car-model <?php echo (count($loadCars) > 1 ? '' : 'hidden'); ?>">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-right clearfix">
        <button id="IA-C-11a" class="secondary-button add-load-car-model">
            <i class="fa fa-plus"></i> <?php echo Yii::t('element', 'IA-C-11a'); ?>
        </button>
    </div>

<?php DynamicFormWidget::end();

$this->registerJs(
    'var LOAD_TYPE = "' . $load->type . '"; ' .
    'var TYPE_PARTIAL = "' . Load::TYPE_PARTIAL . '"; ' .
    'var TYPE_FULL = "' . Load::TYPE_FULL . '"; ' .
    'var QUANTITY_MAX_VALUE = "' . LoadCar::QUANTITY_MAX_VALUE . '"; ' .
    'var MODEL_MAX_LENGTH = "' . LoadCar::MODEL_MAX_LENGTH . '"; ' .
    'var MODEL_CHARACTERS_LEFT = "' . Yii::t('text', 'MODEL_CHARACTERS_LEFT') . '"; ' .
    'var FOR_CAR_MODEL = "' . Load::FOR_CAR_MODEL . '"; ' .
    'var FOR_ALL_LOAD = "' . Load::FOR_ALL_LOAD . '"; ' .
    'var TOTAL_QUANTITY_TOO_BIG = "' . Yii::t('app', 'LOAD_CAR_TOTAL_QUANTITY_IS_TOO_BIG', [
        'max' => LoadCar::QUANTITY_MAX_VALUE
    ]) . '"; ',
View::POS_BEGIN);