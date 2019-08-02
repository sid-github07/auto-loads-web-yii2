<?php

use common\models\User;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var string $title */
/** @var User $user */
/** @var ActiveForm $form */

?>
<div class="site-login">
    <h1 id="PR-C-1" class="login-title">
        <?php echo Yii::t('element', 'PR-C-1'); ?>
    </h1>

    <div class="login-form-container">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <span class="required-fields-text">
                <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
            </span>

            <?php echo $form->field($user, 'email', [
                'inputOptions' => [
                    'id' => 'PR-C-3',
                ],
            ])->label(Yii::t('element', 'PR-C-2')); ?>

            <?php echo $form->field($user, 'password', [
                'inputOptions' => [
                    'id' => 'PR-C-5',
                ],
            ])->label(Yii::t('element', 'PR-C-4'))->passwordInput(); ?>
        
            <?php echo Html::a(Yii::t('element', 'PR-C-7'), [
                'site/request-password-reset',
                'lang' => Yii::$app->language,
            ], [
                'id' => 'PR-C-7',
                'class' => 'remind-password-link'
            ]); ?>
                                        
            <?php echo Html::submitButton(Icon::show('sign-in', '', Icon::FA) . Yii::t('element', 'PR-C-6'), [
                'id' => 'PR-C-6',
                'class' => 'primary-button login-button',
                'name' => 'login-button',
            ]); ?>

        <?php ActiveForm::end(); ?>     
        
        <div class="link-to-register">
            <span id="PR-C-8a">
                <?php echo Yii::t('element', 'PR-C-8a'); ?>
            </span>
        
            <?php echo Html::a(Yii::t('element', 'PR-C-8'), [
                'site/sign-up',
                'lang' => Yii::$app->language
            ], [
                'id' => 'PR-C-8',
            ]); ?>
        </div>       
    </div>
</div>