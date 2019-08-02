<?php

use common\models\User;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var User $user */
/** @var ActiveForm $form */

$user->scenario = User::SCENARIO_CHANGE_EMAIL;
?>

<?php $form = ActiveForm::begin([
    'id' => 'change-email-modal-form',
    'action' => ['settings/request-email-change', 'lang' => Yii::$app->language],
]); ?>

    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <?php echo $form->field($user, 'changeEmail')
        ->textarea(['rows' => 6])
        ->label(Yii::t('app', 'CHANGE_EMAIL_MODAL_CONTENT_LABEL')); ?>

    <div class="modal-form-footer">
        <?php echo Html::submitButton(Icon::show('paper-plane', '', Icon::FA) . Yii::t('app', 'CHANGE_EMAIL_MODAL_SUBMIT_BUTTON'), [
            'class' => 'primary-button send-btn',
            'name' => 'change-email-modal-button',
        ]); ?>
    </div>
<?php ActiveForm::end();