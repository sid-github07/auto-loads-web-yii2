<?php

use common\models\Admin;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var Admin $admin */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'TITLE_ADMIN_PASSWORD_EDIT');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-edit-account-password clearfix">

    <section class="widget widget-account-password-edit">

        <div class="widget-heading">
            <?php echo Yii::t('text', 'ADMIN_EDIT_ACCOUNT_PASSWORD_CHANGE'); ?>
        </div>

        <div class="widget-content">
            <p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

            <?php $form = ActiveForm::begin(['id' => 'change-my-password-form']); ?>

                <div class="row">

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'oldPassword')
                            ->passwordInput()
                            ->label(Yii::t('element', 'ADMIN_USER_PASSWORD_EDIT_CURRENT_PASSWORD')); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'newPassword')
                            ->passwordInput()
                            ->label(Yii::t('element', 'ADMIN_USER_PASSWORD_EDIT_NEW_PASSWORD')); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'repeatNewPassword')
                            ->passwordInput()
                            ->label(Yii::t('element', 'ADMIN_USER_PASSWORD_EDIT_REPEAT_NEW_PASSWORD')); ?>
                    </div>

                </div>

                <div class="text-center">
                    <button type="submit" class="primary-button">
                        <?php echo Yii::t('element', 'ADMIN_USER_PASSWORD_SAVE'); ?>
                    </button>
                </div>

            <?php ActiveForm::end(); ?>
        </div>

    </section>

</div>