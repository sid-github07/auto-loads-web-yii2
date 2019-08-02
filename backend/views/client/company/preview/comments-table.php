<?php

use common\models\Company;
use common\models\CompanyComment;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var CompanyComment[] $comments */
/** @var Company $company */
/** @var integer $toIndex */
?>
<div class="company-comments-preview responsive-table-wrapper custom-gridview">
    <?php if (!empty($comments)): ?>
        <table class="table table-striped table-bordered responsive-table">
            <thead>
                <tr class="comments-title-row">
                    <th><?php echo Yii::t('element', 'A-C-360a'); ?></th>
                    <th><?php echo Yii::t('element', 'A-C-360b'); ?></th>
                    <th><?php echo Yii::t('element', 'A-C-360c'); ?></th>
                    <th></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td width="16%"
                            data-title="<?php echo Yii::t('element', 'A-C-360a'); ?>"
                        >
                            <div><?php echo date('Y-m-d', $comment->created_at); ?></div>
                            <div><?php echo date('H:i:s', $comment->created_at); ?></div>
                        </td>
                        
                        <td data-title="<?php echo Yii::t('element', 'A-C-360b'); ?>">
                            <?php echo $comment->admin->getNameAndSurname(); ?>
                        </td>
                        
                        <td data-title="<?php echo Yii::t('element', 'A-C-360c'); ?>">
                            <?php echo Html::encode($comment->comment); ?>
                        </td>
                        
                        <td class="delete-comment-column-content"
                            data-title="<?php echo Yii::t('element', 'A-C-360d'); ?>"
                        >
                            <?php
                            $icon = Html::tag('i', '', ['class' => 'fa fa-trash-o']);
                            $link = Html::a($icon, '#', [
                                'title' => Yii::t('element', 'A-C-360d'),
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'onclick' => 'removeComment(event, "' .
                                    Url::to([
                                        'client/remove-company-comment',
                                        'lang' => Yii::$app->language,
                                        'commentId' => $comment->id,
                                        'companyId' => $company->id,
                                        'toIndex' => $toIndex,
                                    ]) .
                                    '")',
                            ]);
                            echo $link; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php Modal::begin([
    'id' => 'remove-comment-modal',
    'header' => Yii::t('element', 'A-C-360da'),
]); ?>
    <h4><?php echo Yii::t('element', 'A-C-360db'); ?></h4>

    <div class="modal-form-footer-center">
        <?php echo Html::a(Yii::t('element', 'A-C-360dc'), '#', [
            'id' => 'remove-comment-yes',
            'class' => 'primary-button remove-comment-yes-btn',
        ]); ?>
        
        <?php echo Html::button(Yii::t('element', 'A-C-360dd'), [
            'class' => 'secondary-button no-btn',
            'data-dismiss' => 'modal',
        ]); ?>
    </div>
<?php Modal::end(); ?>
    
<?php
$this->registerJsFile(Url::base() . '/dist/js/client/company/comment.js', ['depends' => [JqueryAsset::className()]]);