<?php

use common\models\Admin;
use yii\bootstrap\ActiveForm;

/** @var Admin $admin */
/** @var ActiveForm $form */
?>

<p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

<?php $form = ActiveForm::begin([
    'id' => 'change-password-form',
    'action' => [
        'admin/change-password',
        'lang' => Yii::$app->language,
        'id' => $admin->id,
    ],
]); ?>

    <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($admin, 'password')
                ->passwordInput()
                ->label(Yii::t('element', 'ADMIN_USER_PASSWORD_EDIT_NEW_PASSWORD')); ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($admin, 'repeatPassword')
                ->passwordInput()
                ->label(Yii::t('element', 'ADMIN_USER_PASSWORD_EDIT_REPEAT_NEW_PASSWORD')); ?>
        </div>

    </div>

    <div class="modal-form-footer-center">

        <button type="submit" class="primary-button">
            <i class="fa fa-floppy-o"></i>
            <?php echo Yii::t('element', 'ADMIN_USER_PASSWORD_SAVE'); ?>
        </button>

    </div>

<?php ActiveForm::end(); ?>