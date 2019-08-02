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
        'announcement/edit',
        'lang' => Yii::$app->language,
        'id' => $announcement->id,
    ],
]); ?>

    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($announcement, 'language_id')
                ->dropDownList(\common\models\Language::getNames())
                ->label(Yii::t('element', 'language'));
            ?>
            <?php echo $form->field($announcement, 'topic')->label(Yii::t('element', 'announcement_topic')); ?>
            <?php echo
                $form->field($announcement, 'body')->widget(CKEditor::className(), [
                    'options' => ['rows' => 6],
                    'preset' => 'basic'
                ])->label(Yii::t('element', 'announcement_body'));
            ?>
            <?php echo $form->field($announcement, 'status')
                ->dropDownList(\common\models\Announcement::statusesDropdown())
                ->label(Yii::t('element', 'announcement_status'));
            ?>
        </div>
    </div>
    <div class="modal-form-footer-center">
        <button type="submit" class="primary-button">
            <i class="fa fa-floppy-o"></i>
            <?php echo Yii::t('element', 'announcement_edit_button'); ?>
        </button>
    </div>

<?php ActiveForm::end();
$this->registerJsFile(Url::base() . '/dist/js/announcement/ckedit.js', ['depends' => [JqueryAsset::className()]]);
