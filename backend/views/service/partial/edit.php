<?php

use common\models\Admin;
use yii\bootstrap\ActiveForm;

/** @var Admin $admin */
/** @var ActiveForm $form */
?>

<p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

<?php $form = ActiveForm::begin([
    'id' => 'edit-form',
    'action' => [
        'service/edit',
        'lang' => Yii::$app->language,
        'id' => $service->id,
    ],
]); ?>

    <div class="row">
        <div class="col-xs-offset-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($service, 'label')->label(Yii::t('element', 'service_label')); ?>
            <?php echo $form->field($service, 'credits')->label(Yii::t('element', 'service_credits')); ?>
            <?php echo $form->field($service, 'price')->label(Yii::t('element', 'service_price')); ?>
        </div>
    </div>
    <div class="modal-form-footer-center">
        <button type="submit" class="primary-button">
            <i class="fa fa-floppy-o"></i>
            <?php echo Yii::t('element', 'service_edit_button'); ?>
        </button>
    </div>

<?php ActiveForm::end(); ?>
