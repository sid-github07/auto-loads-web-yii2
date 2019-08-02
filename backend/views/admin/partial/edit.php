<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Admin;
use yii\bootstrap\ActiveForm;

/** @var Admin $admin */
/** @var ActiveForm $form */
?>

<p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

<?php $form = ActiveForm::begin([
    'id' => 'edit-form',
    'action' => [
        'admin/edit',
        'lang' => Yii::$app->language,
        'id' => $admin->id,
    ],
]); ?>

    <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($admin, 'name')->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_NAME_LABEL')); ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($admin, 'surname')->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_SURNAME_LABEL')); ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($admin, 'phone')->widget(PhoneInput::className(), [
                'defaultOptions' => [
                    'class' => 'form-control',
                ],
                'jsOptions' => [
                    'initialCountry' => 'lt', // Only lithuanian language is in administration panel
                    'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) 34567 to +370 612 34567
                ],
            ])->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_PHONE_LABEL')); ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>

            <?php echo $form->field($admin, 'admin')
                ->dropDownList(Admin::getTranslatedRoles())
                ->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_ROLE_LABEL')); ?>
        </div>

    </div>

    <div class="modal-form-footer-center">

        <button type="submit" class="primary-button">
            <i class="fa fa-floppy-o"></i>
            <?php echo Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_SAVE'); ?>
        </button>

    </div>

<?php ActiveForm::end(); ?>
