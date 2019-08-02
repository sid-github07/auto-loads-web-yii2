<?php

use common\models\CompanyInvitation;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/** @var View $this */
/** @var string $title */
/** @var CompanyInvitation $invitation */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'TITLE_INVITATION');
?>
<div class="settings-invitation">
    <h1 id="V-C-64">
        <?php echo Yii::t('element', 'V-C-64'); ?>
    </h1>

    <p id="V-C-65">
        <?php echo Yii::t('element', 'V-C-65'); ?>
    </p>

    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'settings-invitation-form',
        'action' => ['settings/send-invitation', 'lang' => Yii::$app->language],
        'validationUrl' => ['settings/invitation-validation', 'lang' => Yii::$app->language],
    ]); ?>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php echo $form->field($invitation, 'email', [
                'enableAjaxValidation' => true,
                'inputOptions' => [
                    'id' => 'V-C-66',
                ],
            ])->label(Yii::t('element', 'V-C-66')); ?>
        </div>
    </div>

        <?php echo Html::submitButton(Icon::show('paper-plane', '', Icon::FA) . Yii::t('element', 'V-C-67'), [
            'id' => 'V-C-67',
            'class' => 'primary-button send-invitation-to-sign-up',
        ]); ?>
    <?php ActiveForm::end(); ?>
</div>