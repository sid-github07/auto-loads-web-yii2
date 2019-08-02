<?php

use common\models\CompanyComment;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var null|integer $id Company ID */
/** @var CompanyComment $companyComment */
/** @var integer $toIndex */
?>
<div class="add-company-comment">
    <?php $form = ActiveForm::begin([
        'id' => 'add-company-comment-form',
        'action' => [
            'client/add-company-comment',
            'lang' => Yii::$app->language,
            'id' => $id,
            'toIndex' => $toIndex,
        ],
    ]); ?>

        <?php echo $form->field($companyComment, 'comment', [
            'inputOptions' => [
                'id' => 'A-C-359',
                'class' => 'form-control',
                'onkeyup' => 'showCommentLength(this, ' . $id . ')',
            ],
        ])->textarea([
            'rows' => 3,
        ])->label(false); ?>

        <div class="comment-length-container comment-length-container-<?php echo $id; ?>">
            <span class="comment-length-<?php echo $id; ?>">0</span>/<?php echo CompanyComment::COMMENT_MAX_LENGTH; ?>
        </div>
        
        <div class="text-center">
            <?php echo Html::submitButton(Icon::show('bullhorn', [], Icon::FA) . Yii::t('element', 'A-C-360'), [
                'id' => 'A-C-360',
                'class' => 'primary-button',
                'name' => 'add-company-comment-button',
            ]); ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs('var COMMENT_MAX_LENGTH = ' . CompanyComment::COMMENT_MAX_LENGTH . ';', View::POS_BEGIN);