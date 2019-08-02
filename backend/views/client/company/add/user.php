<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\User;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var null|integer $companyId */
/** @var User $user */
/** @var array $languages */
/** @var ActiveForm $form */
?>

<?php $form = ActiveForm::begin([
    'id' => 'add-company-user-form',
    'action' => ['client/add-company-user', 'id' => $companyId],
    'validationUrl' => Url::to(['client/validate-company-user-form']),
]); ?>

    <?php echo $form->field($user, 'email', [
        'enableAjaxValidation' => true,
        'inputOptions' => [
            'id' => 'A-C-167',
            'class' => 'form-control',
        ],
        'labelOptions' => [
            'id' => 'A-C-166',
            'class' => 'control-label'
        ],
    ])->label(Yii::t('element', 'A-C-166')); ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'name', [
                'inputOptions' => [
                    'id' => 'A-C-169a',
                    'class' => 'form-control',
                ],
                'labelOptions' => [
                    'id' => 'A-C-168a',
                    'class' => 'control-label'
                ],
            ])->label(Yii::t('element', 'A-C-168a')); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'surname', [
                'inputOptions' => [
                    'id' => 'A-C-169b',
                    'class' => 'form-control',
                ],
                'labelOptions' => [
                    'id' => 'A-C-168b',
                    'class' => 'control-label'
                ],
            ])->label(Yii::t('element', 'A-C-168b')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'phone')->widget(PhoneInput::className(), [
                'defaultOptions' => [
                    'id' => 'A-C-172',
                    'class' => 'form-control',
                ],
                'jsOptions' => [
                    // NOTE: there is no such country as 'en' therefore it is changed to 'gb'
                    'initialCountry' => Yii::$app->language == 'en' ? 'gb' : Yii::$app->language,
                    'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) to +370 612 3456
                ],
            ])->label(Yii::t('element', 'A-C-170'), [
                'id' => 'A-C-170',
                'class' => 'control-label'
            ]); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'language', [
                'options' => [
                    'id' => 'A-C-174',
                ],
                'labelOptions' => [
                    'id' => 'A-C-173',
                    'class' => 'control-label'
                ],
            ])->widget(Select2::className(), [
                'data' => $languages,
                'options' => [
                    'multiple' => true,
                ],
                'showToggleAll' => false,
                'pluginOptions' => [
                    'escapeMarkup' => new JsExpression('function (m) {return m;}'), // NOTE: escapes HTML encoding
                ],
            ])->label(Yii::t('element', 'A-C-173')); ?>
        </div>
    </div>

    <?php echo $form->field($user, 'password', [
        'inputOptions' => [
            'id' => 'A-C-176',
            'class' => 'form-control',
        ],
        'labelOptions' => [
            'id' => 'A-C-175',
            'class' => 'control-label'
        ],
    ])->label(Yii::t('element', 'A-C-175'))->passwordInput(); ?>

    <div id="A-C-179" class="content-category">
        <?php echo Yii::t('element', 'A-C-179'); ?>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <?php echo $form->field($user, 'active')->checkbox([
                'id' => 'A-C-180',
                'onclick' => 'toggleCheckbox(this);',
            ])->label(Yii::t('element', 'A-C-181'), [
                'id' => 'A-C-181',
                'class' => 'custom-checkbox' . ($user->active ? ' checked' : ''),
            ]); ?>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
            <?php echo $form->field($user, 'sendEmail')->checkbox([
                'id' => 'A-C-182',
                'onclick' => 'toggleCheckbox(this);',
            ])->label(Yii::t('element', 'A-C-183'), [
                'id' => 'A-C-183',
                'class' => 'custom-checkbox' . ($user->sendEmail ? ' checked' : ''),
            ]); ?>
        </div>
    </div>

    <div class="text-center">
        <?php echo Html::submitButton(Icon::show('plus', [], Icon::FA) . Yii::t('element', 'A-C-184'), [
            'id' => 'A-C-184',
            'class' => 'primary-button create-new-user-btn'
        ]); ?>
    </div>

<?php ActiveForm::end();