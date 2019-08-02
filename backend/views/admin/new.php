<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Admin;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Admin $admin */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'TITLE_ADMIN_CREATE');
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'TITLE_ADMIN_USERS'), 'url' => ['admin/index'],];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-create clearfix">

    <section class="widget widget-admin-create">

        <div class="widget-heading">
            <?php echo Yii::t('element', 'ADMIN_CREATE_CREATE_NEW'); ?>
        </div>

        <div class="widget-content">

            <?php $form = ActiveForm::begin([
                'id' => 'add-new-admin-form',
                'method' => 'POST',
                'action' => [
                    'admin/add-new',
                    'lang' => Yii::$app->language,
                ],
            ]); ?>

                <div class="row">

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'name')->label(Yii::t('element', 'ADMIN_CREATE_NAME_LABEL')); ?>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'surname')->label(Yii::t('element', 'ADMIN_CREATE_SURNAME_LABEL')); ?>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'email')->label(Yii::t('element', 'ADMIN_CREATE_EMAIL_LABEL')); ?>
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
                        ])->label(Yii::t('element', 'ADMIN_CREATE_PHONE_LABEL')); ?>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 select">
                        <span class="select-arrow"><i class="fa fa-caret-down"></i></span>

                        <?php echo $form->field($admin, 'admin')
                            ->dropDownList(Admin::getTranslatedRoles())
                            ->label(Yii::t('element', 'ADMIN_CREATE_ADMIN_ROLE_LABEL')); ?>
                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'password')
                            ->passwordInput()
                            ->label(Yii::t('element', 'ADMIN_CREATE_PASSWORD_LABEL')); ?>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?php echo $form->field($admin, 'repeatPassword')
                            ->passwordInput()
                            ->label(Yii::t('element', 'ADMIN_CREATE_REPEAT_PASSWORD_LABEL')); ?>
                    </div>

                </div>

                <div class="text-center">
                    <button type="submit" class="primary-button">
                        <i class="fa fa-plus"></i>
                        <?php echo Yii::t('element', 'CREATE_NEW_ADMIN_SUBMIT'); ?>
                    </button>
                </div>

            <?php ActiveForm::end(); ?>

        </div>

    </section>

</div>