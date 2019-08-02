<?php

use common\models\FaqFeedback;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */

$model = new FaqFeedback(['scenario' => FaqFeedback::SCENARIO_CLIENT_SIDE]);
?>

<?php $form = ActiveForm::begin([
    'id' => 'bug-report-form',
    'action' => ['site/bug-report', 'lang' => Yii::$app->language],
]); ?>
    <?php echo $form->field($model, 'email', [
        'inputOptions' => [
            'id' => 'report-bug-email',
            'class' => 'form-control',
        ],
    ]); ?>

    <?php echo $form->field($model, 'comment', [
        'inputOptions' => [
            'id' => 'report-bug-comment',
            'class' => 'form-control',
        ],
    ])->textarea(['rows' => 3]); ?>

    <div class="text-center">
        <?php echo Html::submitButton(Icon::show('paper-plane', [], Icon::FA) . Yii::t('element', 'DUK-AP-3f'), [
            'id' => 'report-bug-submit',
            'class' => 'primary-button',
        ]); ?>
    </div>
<?php ActiveForm::end(); ?>