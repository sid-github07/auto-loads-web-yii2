<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Admin;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/** @var View $this */
/** @var Admin $admin */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'TITLE_ADMIN_PROFILE_EDIT');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-edit-account-info clearfix">

    <section class="widget widget-admin-list-edit">

        <div class="widget-heading">
            <?php echo Yii::t('text', 'ADMIN_EDIT_ACCOUNT_INFO_PROFILE_INFORMATION'); ?>
        </div>

        <div class="widget-content">
            <p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

            <?php $form = ActiveForm::begin(['id' => 'edit-my-profile-form']); ?>

                <div class="row">

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'name')->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_NAME_LABEL')); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'surname')->label(Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_SURNAME_LABEL')); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
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

                </div>

                <div class="text-center">
                    <button type="submit" class="primary-button">
                        <i class="fa fa-floppy-o"></i>
                        <?php echo Yii::t('element', 'ADMIN_EDIT_ACCOUNT_INFO_SAVE'); ?>
                    </button>
                </div>

            <?php ActiveForm::end(); ?>

        </div>

    </section>

</div>