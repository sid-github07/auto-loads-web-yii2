<?php

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Admin */

use common\models\Admin;
use odaialali\yii2toastr\ToastrAsset;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;


ToastrAsset::register($this);
$this->title = Yii::t('app', 'TITLE_LOGIN');
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="site-login wrap-center">
    <div class="login-alert">
        <?php echo ToastrFlash::widget([
            'options' => [
                'closeButton' => true,
                'debug' => false,
                'newestOnTop' => true,
                'progressBar' => false,
                'positionClass' => 'toast-top-center',
                'preventDuplicates' => true,
                'showDuration' => 0, // how long it takes to show the alert in milliseconds
                'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
                'timeOut' => 4500, // how long the alert must be visible to user in milliseconds
                'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
                'onShown' => 'function() { ' .
                    '$(".login-alert").append($("#toast-container"));' .
                '}',
            ]
        ]); ?>
    </div>
    
    <div class="login-container">
        <div class="login-headline">
            <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                 class="site-logo-login"
                 alt="<?php echo Yii::t('element', 'SITE_LOGO'); ?>"
            />
            <div id="HP-C1-1a" class="logo-subtext">
                <?php echo Yii::t('element', 'LOGO_SUBTEXT'); ?>
            </div>
        </div>

        <div class="login-content">
            <?php $form = ActiveForm::begin(['id' => 'login-form']);

                echo $form->field($model, 'email')->textInput(['autofocus' => true])
                    ->label(Yii::t('element', 'LOGIN_EMAIL'));

                echo $form->field($model, 'password',  [
                    'inputOptions' => [
                    'id' => 'LOGIN_PASSWORD',
                ],])->label(Yii::t('element', 'LOGIN_PASSWORD'))->passwordInput();

                echo Html::beginTag('div', ['class' => 'form-group']);
                    
                    echo Html::submitButton('<i class="fa fa-sign-in" aria-hidden="true"></i>' . 
                    Yii::t('element', 'LOGIN_BUTTON_TEXT'), [
                        'class' => 'primary-button login-btn',
                        'name' => 'login-button'
                    ]);
                
                echo Html::endTag('div');

            ActiveForm::end(); ?>
        </div>
    </div>
</div>
