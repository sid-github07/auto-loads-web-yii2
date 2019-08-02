<?php

use common\models\User;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var User $user */
/** @var ActiveForm $form */

$user->scenario = User::SCENARIO_CHANGE_PASSWORD_CLIENT;
?>
<?php $form = ActiveForm::begin([
    'id' => 'change-password-form',
    'action' => ['settings/change-password', 'lang' => Yii::$app->language],
]); ?>

<div class="required-fields-text">
    <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <?php echo $form->field($user, 'currentPassword', [
            'inputOptions' => [
                'class' => 'N-C-56 form-control',
            ],
        ])->label(Yii::t('element', 'N-C-55'))->passwordInput(); ?>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <?php echo $form->field($user, 'newPassword', [
            'inputOptions' => [
                'class' => 'N-C-58 form-control',
            ],
        ])->label(Yii::t('element', 'N-C-57'))->passwordInput(); ?>
    </div>
    
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <?php echo $form->field($user, 'repeatNewPassword', [
            'inputOptions' => [
                'class' => 'N-C-60 form-control',
            ],
        ])->label(Yii::t('element', 'N-C-59'))->passwordInput(); ?>
    </div>
</div>

<div class="text-center">
    <?php echo Html::submitButton(Icon::show('floppy-o', '', Icon::FA) . Yii::t('element', 'N-C-61'), [
        'id' => 'N-C-61',
        'class' => 'primary-button settings-save-btn',
        'name' => 'change-password-button',
    ]); ?>
</div>    
<?php ActiveForm::end();