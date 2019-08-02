<?php

use common\models\Load;
use common\models\LoadCar;
use yii\bootstrap\ActiveForm;

/** @var Load $load */

$form = ActiveForm::begin([
    'id' => 'load-editing-form',
    'action' => ['my-load/load-editing', 'lang' => Yii::$app->language, 'token' => $load->token],
    'options' => ['onsubmit' => 'editLoad(event, ' . $load->id . ', this)'],
]); ?>

    <div class="body-content">
        <div class="row">
            <div class="required-fields-text col-xs-12">
                <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
            </div>

            <?php
                echo $form->field($load, 'payment_method', [
                    'options' => [
                        'class' => 'load-payment-method-selection ' . ($load->isPaymentMethodForAllLoad() ? 'col-sm-6 col-xs-12' : 'col-xs-12'),
                    ],
                    'inputOptions' => [
                        'id' => 'IA-C-4',
                        'onchange' => 'changeElementsVisibility(this)',
                    ],
                ])
                    ->dropDownList(Load::getTranslatedPaymentMethods())
                    ->label(Yii::t('element', 'IA-C-4'));

                echo $form->field($load, 'price', [
                    'options' => [
                        'class' => 'load-price col-sm-6 col-xs-12' . (!$load->isPaymentMethodForAllLoad() ? ' hidden' : ''),
                    ],
                    'inputOptions' => ['class' => 'IA-C-12 form-control'],
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
            ?>
        </div>

        <?php echo Yii::$app->controller->renderAjax('/my-announcement/load-cars-editing-form', [
            'load' => $load,
            'loadCars' => empty($load->loadCars) ? [new LoadCar()] : $load->loadCars,
            'form' => $form,
            'isNew' => false,
        ]); ?>

        <div class="text-center">
            <button type="submit" id="load-editing-submit" class="primary-button">
                <i class="fa fa-floppy-o"></i> <?php echo Yii::t('text', 'SAVE_CHANGED_INFO'); ?>
            </button>
        </div>
    </div>

<?php ActiveForm::end();