<?php

use common\models\Company;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;

/** @var Company $company */
?>
<div class="change-company-name">
    <?php $form = ActiveForm::begin([
        'id' => 'change-company-name-form',
        'action' => [
            'client/change-company-name',
            'lang' => Yii::$app->language,
            'id' => $company->id,
        ],
    ]); ?>

        <?php if ($company->isNatural()): ?>

            <?php echo $form->field($company, 'name', [
                'inputOptions' => [
                    'id' => 'A-M-9c',
                    'class' => 'form-control',
                ],
                'labelOptions' => [
                    'id' => 'A-M-9b',
                    'class' => 'control-label',
                ],
            ])->label(Yii::t('element', 'A-M-9b')); ?>

            <?php echo $form->field($company, 'surname', [
                'inputOptions' => [
                    'id' => 'A-M-9e',
                    'class' => 'form-control',
                ],
                'labelOptions' => [
                    'id' => 'A-M-9d',
                    'class' => 'control-label',
                ],
            ])->label(Yii::t('element', 'A-M-9d')); ?>

        <?php else: ?>

            <?php echo $form->field($company, 'title', [
                'inputOptions' => [
                    'id' => 'A-M-9g',
                    'class' => 'form-control',
                ],
                'labelOptions' => [
                    'id' => 'A-M-9f',
                    'class' => 'control-label',
                ],
            ])->label(Yii::t('element', 'A-M-9f')); ?>

        <?php endif; ?>

        <div class="text-center">
            <button type="submit" id="A-M-9h" class="primary-button">
                <?php echo Icon::show('floppy-o', [], Icon::FA) . Yii::t('element', 'A-M-9h'); ?>
            </button>
        </div>
    <?php ActiveForm::end(); ?>
</div>
