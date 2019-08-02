<?php

use common\models\User;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var User $user */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'TITLE_REQUEST_PASSWORD_RESET');
?>
<div class="site-request-password-reset">
    <h1 id="SP-C-1">
        <?php echo Yii::t('element', 'SP-C-1'); ?>
    </h1>

    <p id="SP-C-2">
        <?php echo Yii::t('element', 'SP-C-2'); ?>
    </p>

    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
        <span class="required-fields-text">
            <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
        </span>
        
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo $form->field($user, 'email', [
                    'inputOptions' => [
                        'id' => 'SP-C-4',
                    ],
                ])->label(Yii::t('element', 'SP-C-3')); ?>
                
                <?php echo Html::submitButton(Icon::show('paper-plane', '', Icon::FA) . Yii::t('element', 'SP-C-5'), [
                    'id' => 'SP-C-5',
                    'class' => 'primary-button',
                    'name' => 'request-password-reset-button',
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>