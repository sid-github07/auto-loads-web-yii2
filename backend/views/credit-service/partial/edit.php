<?php

use common\models\Admin;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use yii\web\JqueryAsset;
use yii\helpers\Url;

/** @var Admin $admin */
/** @var ActiveForm $form */
?>

<p><?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?></p>

<?php $form = ActiveForm::begin([
    'id' => 'edit-form',
    'action' => [
        'credit-service/edit',
        'lang' => Yii::$app->language,
        'id' => $creditService->id,
    ],
]); ?>
    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($creditService, 'credit_cost')->label(Yii::t('element', 'credit_cost')); ?>
        </div>
    </div>
    <div class="modal-form-footer-center">
        <button type="submit" class="primary-button">
            <i class="fa fa-floppy-o"></i>
            <?php echo Yii::t('element', 'credit_cost_edit_button'); ?>
        </button>
    </div>
<?php ActiveForm::end();
$this->registerJsFile(Url::base() . '/dist/js/credits-cost/ckedit.js', ['depends' => [JqueryAsset::className()]]);
