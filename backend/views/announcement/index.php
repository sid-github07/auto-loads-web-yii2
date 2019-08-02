<?php

use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;
use common\components\Languages;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('seo', 'announcements');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-index clearfix">
    <section class="widget widget-admin-list-edit">
        <div class="widget-heading">
            <?php echo Yii::t('element', 'announcement_list'); ?>

            <?php if (Yii::$app->admin->identity->isAdmin()): ?>
                <a href="<?php echo Url::to([
                    'announcement/new',
                    'lang' => Yii::$app->language,
                ]); ?>"
                   class="widget-btn"
                   title="<?php echo Yii::t('element', 'create_new_announcement'); ?>"
                   data-toggle="tooltip"
                   data-placement="top"
                >
                    <i class="fa fa-plus"></i>
                </a>
            <?php endif; ?>
        </div>
        <div class="widget-content">
            <div class="responsive-table-wrapper">

                <?php echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => false,
                    'options' => ['class' => 'custom-gridview'],
                    'tableOptions' => ['class' => 'table table-striped table-bordered responsive-tabe'],
                    'columns' => [
                        [
                            'format' => 'raw',
                            'attribute' => '',
                            'label' => Yii::t('element', 'announcement_language'),
                            'value' => function (\common\models\Announcement $announcement) {
                                if (is_null($announcement->language)) {
                                    return null;
                                }
                                $languages = Languages::getLanguages();
                                $code = strtolower($announcement->language->country_code);
                                if ($code == 'us') {
                                    $code = 'en';
                                }
                                if (isset($languages[$code])) {
                                    return $languages[$code];
                                }
                                return null;
                            },
                        ],
                        [
                            'attribute' => 'topic',
                            'label' => Yii::t('element', 'announcement_topic'),
                        ],
                        [
                            'attribute' => 'body',
                            'label' => Yii::t('element', 'announcement_body'),
                            'format' => 'raw',
                        ],
                        [
                            'format' => 'raw',
                            'attribute' => '',
                            'label' => Yii::t('element', 'announcement_status'),
                            'value' => function (\common\models\Announcement $announcement) {
                                $color = 'color: green;';
                                if ($announcement->status === \common\models\Announcement::STATUS_HIDDEN) {
                                    $color = 'color: red';
                                }
                                return "<p style='${color}'>" . Yii::t('element', $announcement->statusString()) . "</p>";
                            },
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => Yii::t('element', 'announcement_created_at'),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\Announcement $announcement) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-pencil']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'announcement_edit'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'edit(event, ' . $announcement->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\Announcement $announcement) {
                                if ($announcement->status === \common\models\Announcement::STATUS_HIDDEN) {
                                    return null;
                                }
                                $icon = Html::tag('i', null, ['class' => 'fa fa-asterisk']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'announcement_hide'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'showHideModal(event, ' . $announcement->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\Announcement $announcement) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-trash']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'announcement_delete'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'showRemoveModal(event, ' . $announcement->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                    ],
                ]); ?>

            </div>
        </div>

    </section>

</div>

<?php Modal::begin([
    'id' => 'edit-modal',
    'header' => Yii::t('element', 'announcement_edit'),
    'size' => 'modal-lg',
]);
    Pjax::begin(['id' => 'edit-pjax']);
    Pjax::end();
Modal::end(); ?>

<?php Modal::begin([
    'id' => 'remove-modal',
    'header' => Yii::t('element', 'announcement_delete'),
]); ?>

    <h4><?php echo Yii::t('element', 'remove_announcement_confirm'); ?></h4>

    <div class="modal-form-footer-center">
        <button id="delete-announcement-button-yes" class="primary-button yes-btn" onclick="remove();">
            <?php echo Yii::t('element', 'announcement_delete_confirm'); ?>
        </button>

        <button id="delete-announcement-button-no" class="secondary-button no-btn" data-dismiss="modal" aria-label="Close">
            <?php echo Yii::t('element', 'announcement_delete_cancel'); ?>
        </button>
    </div>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'hide-modal',
    'header' => Yii::t('element', 'announcement_hide'),
]); ?>

    <h4><?php echo Yii::t('element', 'hide_announcement_confirm'); ?></h4>

    <div class="modal-form-footer-center">
        <button id="hide-announcement-button-yes" class="primary-button yes-btn" onclick="hide();">
            <?php echo Yii::t('element', 'announcement_hide_confirm'); ?>
        </button>

        <button id="hide-announcement-button-no" class="secondary-button no-btn" data-dismiss="modal" aria-label="Close">
            <?php echo Yii::t('element', 'announcement_hide_cancel'); ?>
        </button>
    </div>

<?php Modal::end(); ?>
<?php
$this->registerJs(
    'var actionRenderEditForm = "' . Url::to(['announcement/render-edit-form', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionHide = "' . Url::to(['announcement/hide', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRemove = "' . Url::to(['announcement/remove', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/announcement/index.js', ['depends' => [JqueryAsset::className()]]);
