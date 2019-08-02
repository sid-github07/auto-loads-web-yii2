<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\User;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var User $user */
/** @var ActiveForm $form */
/** @var array $languages */

$user->scenario = User::SCENARIO_EDIT_MY_DATA_CLIENT;
?>

<?php $form = ActiveForm::begin([
    'id' => 'edit-my-data-form',
    'action' => ['settings/edit-my-data', 'lang' => Yii::$app->language],
    'validationUrl' => Url::to(['settings/edit-my-data-validation', 'lang' => Yii::$app->language]),
]); ?>
    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <div class="contact-admin-wrapper">
        <div class="change-email-wrapper">
            <?php echo $form->field($user, 'email', [
                'inputOptions' => [
                    'id' => 'N-C-2',
                    'class' => 'form-control how-to-change-email-input',
                    'disabled' => true,
                ],
            ])->label(Yii::t('element', 'N-C-1')); ?>

            <?php echo Icon::show('question-circle-o', [
                'id' => 'N-C-3',
                'class' => 'fa-2x how-to-change-icon',
                'title' => Yii::t('element', 'N-C-3a'),
                'data-toggle' => 'popover',
                'data-content' => Yii::t('element', 'N-C-3b'),
                'data-placement' => 'top',
            ], Icon::FA); ?>

        </div>

        <?php echo Html::button(Yii::t('element', 'N-C-4'), [
            'id' => 'N-C-4',
            'class' => 'primary-button contact-admin change-email',
            'name' => 'change-email-button',
        ]); ?>
    </div>    

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'name', [
                'enableAjaxValidation' => true,
                'inputOptions' => [
                    'id' => 'N-C-6a',
                ],
            ])->label(Yii::t('element', 'N-C-5a')); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'surname', [
                'enableAjaxValidation' => true,
                'inputOptions' => [
                    'id' => 'N-C-6b',
                ],
            ])->label(Yii::t('element', 'N-C-5b')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'phone')->widget(PhoneInput::className(), [
                'defaultOptions' => [
                    'id' => 'N-C-8',
                    'class' => 'form-control',
                ],
                'jsOptions' => [
                    // NOTE: there is no such country as 'en' therefore it is changed to 'gb'
                    'initialCountry' => Yii::$app->language == 'en' ? 'gb' : Yii::$app->language,
                    'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) 34567 to +370 612 34567
                ],
            ])->label(Yii::t('element', 'N-C-7'))->hint(Yii::t('app', 'USER_EDIT_MY_DATA_PHONE_HINT')); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($user, 'language', [
                'inputOptions' => [
                    'id' => 'RG-F-10',
                ],
                'options' => [
                    'class' => 'clearfix',
                ],
            ])->widget(Select2::className(), [
                'data' => $languages,
                'options' => [
                    'multiple' => true,
                ],
                'showToggleAll' => false,
                'pluginOptions' => [
                    'escapeMarkup' => new JsExpression('function(m) { return m; }'), // NOTE: escapes HTML encoding
                ],
            ])->label(Yii::t('element', 'N-C-9')); ?>
        </div>
    </div>

    <div class="text-center">
        <?php echo Html::submitButton(Icon::show('floppy-o', '', Icon::FA) . Yii::t('element', 'N-C-11'), [
            'id' => 'N-C-11',
            'class' => 'primary-button settings-save-btn',
            'name' => 'edit-my-data-button',
        ]); ?>
    </div>
<?php ActiveForm::end(); ?>

<?php Modal::begin([
    'id' => 'change-email',
    'header' => Yii::t('app', 'CHANGE_EMAIL_MODAL_HEADER'),
    'size' => 'modal-lg'
]); ?>
    <?php echo Yii::$app->controller->renderPartial('__change-email-modal', [
        'user' => $user,
    ]); ?>
<?php Modal::end();