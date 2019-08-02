<?php

use common\models\Company;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var Company $company */
/** @var ActiveForm $form */

$company->scenario = Company::SCENARIO_CHANGE_VAT_CODE;
?>
<?php $form = ActiveForm::begin([
    'id' => 'change-vat-code-modal-form',
    'action' => ['settings/request-vat-code-change', 'lang' => Yii::$app->language],
]); ?>
    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <?php echo $form->field($company, 'changeVatCode')
        ->textarea(['rows' => 6])
        ->label(Yii::t('app', 'CHANGE_VAT_CODE_MODAL_CONTENT_LABEL')); ?>

    <div class="modal-form-footer">
        <?php echo Html::submitButton(Icon::show('paper-plane', '', Icon::FA) . 
        Yii::t('app', 'CHANGE_VAT_CODE_MODAL_SUBMIT_BUTTON'), [
            'class' => 'primary-button send-btn',
            'name' => 'change-vat-code-modal-button',
        ]); ?>
    </div>
<?php ActiveForm::end();