<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\User;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;

/** @var View $this */
/** @var User $user */
/** @var string $email */
/** @var string $token */
/** @var ActiveForm $form */
/** @var array|null $languages */

Icon::map($this, Icon::FA);
$this->title = Yii::t('seo', 'TITLE_SIGN_UP_INVITATION');
?>
<div class="site-sign-up-invitation">
    <h1 id="V-C-66a">
        <?php echo Yii::t('element', 'V-C-66a'); ?>
    </h1>

    <p id="V-C-66b">
        <?php echo Yii::t('element', 'V-C-66b'); ?>
    </p>

    <?php $form = ActiveForm::begin([
        'id' => 'site-sign-up-invitation-form',
        'action' => ['site/sign-up-invitation', 'lang' => Yii::$app->language, 'token' => $token],
        'validationUrl' => ['site/sign-up-invitation-validation', 'lang' => Yii::$app->language],
    ]); ?>
        <div class="form-group">
            <label class="control-label">
                <?php echo Yii::t('element', 'V-C-66c'); ?>
            </label>

            <div class="user-email-text">
                <?php echo $email; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'name', [
                    'enableAjaxValidation' => true,
                    'inputOptions' => [
                        'id' => 'V-C-66d',
                    ],
                ])->label(Yii::t('element', 'V-C-66d')); ?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'surname', [
                    'enableAjaxValidation' => true,
                    'inputOptions' => [
                        'id' => 'V-C-66e',
                    ],
                ])->label(Yii::t('element', 'V-C-66e')); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'password', [
                    'inputOptions' => [
                        'class' => 'V-C-66h form-control',
                    ],
                ])->label(Yii::t('element', 'V-C-66h'))->passwordInput(); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'repeatPassword', [
                    'inputOptions' => [
                        'id' => 'V-C-66i',
                    ],
                ])->label(Yii::t('element', 'V-C-66i'))->passwordInput(); ?>
            </div>
        </div>
    
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'phone')->widget(PhoneInput::className(), [
                    'defaultOptions' => [
                        'id' => 'V-C-66f',
                        'class' => 'form-control',
                    ],
                    'jsOptions' => [
                        // NOTE: there is no such country as 'en' therefore it is changed to 'gb'
                        'initialCountry' => Yii::$app->language == 'en' ? 'gb' : Yii::$app->language,
                        'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) 34567 to +370 612 34567
                    ],
                ])->label(Yii::t('element', 'V-C-66f'))->hint(Yii::t('app', 'USER_SIGN_UP_PHONE_HINT')); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($user, 'language', [
                    'inputOptions' => [
                        'id' => 'V-C-66g',
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
                ])->label(Yii::t('element', 'V-C-66g')); ?>
            </div>
        </div>

        <?php echo $form->field($user, 'rulesAgreement', [
            'options' => [
                'class' => 'custom-checkbox rules-agreement'
            ]
        ])->checkbox([
            'id' => 'V-C-66j',
            'class' => 'checkbox-input',
        ])->label(Yii::t('element', 'V-C-66j', [
            'rules' => Html::a(Yii::t('element', 'V-C-66k'), Url::to([
                'site/guidelines', 'lang' => Yii::$app->language,
            ]), [
                'target' => '_blank',
            ]),
        ])); ?>

        <?php echo Html::submitButton(Icon::show('user-plus', '', Icon::FA) . Yii::t('element', 'V-C-66l'), [
            'id' => 'V-C-66l',
            'class' => 'primary-button sign-up-submit-btn',
        ]); ?>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJsFile(Url::base() . '/dist/js/site/sign-up-invitation.js', ['depends' => [JqueryAsset::className()]]);