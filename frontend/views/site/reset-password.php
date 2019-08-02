<?php

use common\models\User;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var User $user */
/** @var ActiveForm $form */

Icon::map($this, Icon::FA);
$this->title = Yii::t('seo', 'TITLE_RESET_PASSWORD');
?>
<div class="site-reset-password">
    <h1 id="SP-C-9" class="reset-password-title">
        <?php echo Yii::t('element', 'SP-C-9'); ?>
    </h1>
    
    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <div class="required-fields-text">
            <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
        </div>

        <?php echo $form->field($user, 'password')
            ->passwordInput()
            ->label(Yii::t('element', 'SP-C-11'));
        ?>

        <?php echo $form->field($user, 'repeatPassword')
            ->passwordInput()
            ->label(Yii::t('element', 'SP-C-13'));
        ?>

        <?php echo Html::submitButton(Icon::show('floppy-o', '', Icon::FA) . Yii::t('element', 'SP-C-14'), [
            'id' => 'SP-C-14',
            'class' => 'primary-button reset-password-save-btn',
            'name' => 'reset-password-button',
        ]); ?>

    <?php ActiveForm::end(); ?>
</div>