<?php

use common\models\Admin;
use yii\web\View;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use yii\web\JqueryAsset;
use yii\helpers\Url;

/** @var View $this */
/** @var Admin $admin */
/** @var ActiveForm $form */

$this->title = Yii::t('seo', 'title_announcement_create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'announcements'), 'url' => ['announcement/index'],];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-create clearfix">
    <section class="widget widget-admin-create">
        <div class="widget-heading">
            <?php echo Yii::t('element', 'announcement_new'); ?>
        </div>
        <div class="widget-content">
            <?php $form = ActiveForm::begin([
                'id' => 'add-new-announcement-form',
                'method' => 'POST',
                'action' => [
                    'announcement/new',
                    'lang' => Yii::$app->language,
                ],
            ]); ?>
                <div class="row">
                    <div class="col-xs-offset-3 col-lg-6 col-md-6 col-sm-12 col-xs-12">
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
                <div class="text-center">
                    <button type="submit" class="primary-button">
                        <i class="fa fa-plus"></i>
                        <?php echo Yii::t('element', 'create_new_announcement_submit'); ?>
                    </button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </section>
</div>
<?php
$this->registerJsFile(Url::base() . '/dist/js/announcement/ckedit.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/announcement/index.js', ['depends' => [JqueryAsset::className()]]);
