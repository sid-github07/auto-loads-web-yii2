<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Admin;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var Admin $admin */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'title_service_create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'services'), 'url' => ['service/index'],];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-create clearfix">
    <section class="widget widget-admin-create">
        <div class="widget-heading">
            <?php echo Yii::t('element', 'service_new'); ?>
        </div>
        <div class="widget-content">
            <?php $form = ActiveForm::begin([
                'id' => 'add-new-service-form',
                'method' => 'POST',
                'action' => [
                    'service/new',
                    'lang' => Yii::$app->language,
                ],
            ]); ?>
                <div class="row">
                    <div class="col-xs-offset-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <?php echo $form->field($service, 'service_type_id')
                            ->dropDownList(\common\models\ServiceType::getServiceTypes())
                            ->label(Yii::t('element', 'service_create_type_id'));
                        ?>
                        <?php echo $form->field($service, 'label')->label(Yii::t('element', 'service_create_label')); ?>
                        <?php echo $form->field($service, 'price')->label(Yii::t('element', 'service_create_price')); ?>
                        <?php echo $form->field($service, 'credits')->label(Yii::t('element', 'service_create_credits')); ?>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="primary-button">
                        <i class="fa fa-plus"></i>
                        <?php echo Yii::t('element', 'create_new_service_submit'); ?>
                    </button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </section>
</div>