<?php

use common\models\Admin;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('seo', 'TITLE_ADMIN_USERS');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-index clearfix">

    <section class="widget widget-admin-list-edit">

        <div class="widget-heading">
            <?php echo Yii::t('text', 'ADMIN_INDEX_USER_LIST'); ?>

            <?php if (Yii::$app->admin->identity->isAdmin()): ?>
                <a href="<?php echo Url::to([
                    'admin/add-new',
                    'lang' => Yii::$app->language,
                ]); ?>"
                   class="widget-btn"
                   title="<?php echo Yii::t('element', 'ADMIN_INDEX_CREATE_NEW_ADMIN'); ?>"
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
                            'attribute' => 'name',
                            'label' => Yii::t('element', 'ADMIN_INDEX_NAME_LABEL'),
                        ],
                        [
                            'attribute' => 'surname',
                            'label' => Yii::t('element', 'ADMIN_INDEX_SURNAME_LABEL'),
                        ],
                        [
                            'attribute' => 'email',
                            'label' => Yii::t('element', 'ADMIN_INDEX_EMAIL_LABEL'),
                        ],
                        [
                            'attribute' => 'phone',
                            'label' => Yii::t('element', 'ADMIN_INDEX_PHONE_LABEL'),
                        ],
                        [
                            'attribute' => 'admin',
                            'label' => Yii::t('element', 'ADMIN_INDEX_ROLE_LABEL'),
                            'value' => function (Admin $admin) {
                                return $admin->getRoleName();
                            }
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (Admin $admin) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-pencil']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'ADMIN_INDEX_EDIT_USER'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'edit(event, ' . $admin->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (Admin $admin) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-unlock-alt']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'ADMIN_INDEX_CHANGE_USER_PASSWORD'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'changePassword(event, ' . $admin->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (Admin $admin) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-trash']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'ADMIN_INDEX_DELETE_USER'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'showRemoveModal(event, ' . $admin->id . ');',
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
    'header' => Yii::t('element', 'ADMIN_INDEX_EDIT_USER'),
    'size' => 'modal-lg',
]);

    Pjax::begin(['id' => 'edit-pjax']);
    Pjax::end();

Modal::end(); ?>

<?php Modal::begin([
    'id' => 'change-password-modal',
    'header' => Yii::t('element', 'ADMIN_INDEX_CHANGE_USER_PASSWORD'),
]);

    Pjax::begin(['id' => 'change-password-pjax']);
    Pjax::end();

Modal::end(); ?>

<?php Modal::begin([
    'id' => 'remove-modal',
    'header' => Yii::t('element', 'ADMIN_INDEX_DELETE_USER'),
]); ?>

    <h4><?php echo Yii::t('element', 'ADMIN_USER_DELETE_USER_COMFIRM_MESSAGE'); ?></h4>

    <div class="modal-form-footer-center">
        <button id="delete-admin-button-yes" class="primary-button yes-btn" onclick="remove();">
            <?php echo Yii::t('element', 'ADMIN_USER_DELETE_CONFIRM'); ?>
        </button>

        <button id="delete-admin-button-no" class="secondary-button no-btn" data-dismiss="modal" aria-label="Close">
            <?php echo Yii::t('element', 'ADMIN_USER_DELETE_CANCEL'); ?>
        </button>
    </div>

<?php Modal::end(); ?>
<?php
$this->registerJs(
    'var actionRenderEditForm = "' . Url::to(['admin/render-edit-form', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRenderChangePasswordForm = "' . Url::to(['admin/render-change-password-form', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRemove = "' . Url::to(['admin/remove', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/admin/index.js', ['depends' => [JqueryAsset::className()]]);
