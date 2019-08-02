<?php

use common\models\UserServiceActive;
use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var null|integer $id Company ID */
/** @var string $tab Current tab */
/** @var array $services */
/** @var array $companyUsers List of all company users including company owner */
/** @var UserServiceActive $userServiceActive */
?>
<div class="add-company-subscription">
    <?php $form = ActiveForm::begin([
        'id' => 'new-company-subscription-form',
        'action' => [
            'client/create-new-subscription',
            'lang' => Yii::$app->language,
            'id' => $id,
            'tab' => $tab,
        ],
    ]); ?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 select">
                <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                <?php echo $form->field($userServiceActive, 'service_id', [
                    'inputOptions' => [
                        'id' => 'A-C-208',
                        'class' => 'form-control service-selection',
                        'onchange' => 'fillSubscriptionDate()',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-207',
                        'class' => 'control-label'
                    ],
                ])->dropDownList($services, [
                    'prompt' => Yii::t('app', 'NOT_SELECTED'),
                ])->label(Yii::t('element', 'A-C-207')); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 select">
                <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                <?php echo $form->field($userServiceActive, 'user_id', [
                    'inputOptions' => [
                        'id' => 'A-C-210',
                    ],
                    'labelOptions' => [
                        'id' => 'A-C-209',
                        'class' => 'control-label'
                    ],
                ])->dropDownList($companyUsers, [
                    'prompt' => Yii::t('app', 'NOT_SELECTED'),
                ])->label(Yii::t('element', 'A-C-209')); ?>
            </div>
        </div>

        <div class="subscription-end-date-range-picker-wrapper">
            <?php echo $form->field($userServiceActive, 'date_of_purchase', [
                'inputOptions' => [
                    'id' => 'A-C-212',
                ],
                'labelOptions' => [
                    'id' => 'A-C-211',
                    'class' => 'control-label'
                ],
            ])->widget(DateRangePicker::className(), [
                'attributeTo' => 'end_date',
                'form' => $form,
                'language' => Yii::$app->language,
                'labelTo' => Yii::t('element', 'A-C-213'),
                'options' => [
                    'id' => 'A-C-212',
                    'class' => 'form-control date_of_purchase',
                ],
                'optionsTo' => [
                    'id' => 'A-C-214',
                    'class' => 'form-control end_date',
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'startDate' => date('Y-m-d'),
                ],
            ])->label(Yii::t('element', 'A-C-211')); ?>
        </div>

        <div class="text-center">
            <?php echo Html::submitButton(Icon::show('plus', [], Icon::FA) . Yii::t('element', 'A-C-215'), [
                'id' => 'A-C-215',
                'class' => 'primary-button',
                'name' => 'new-company-subscription-button',
            ]); ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
