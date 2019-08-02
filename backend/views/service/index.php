<?php

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

$this->title = Yii::t('seo', 'services');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-index clearfix">
    <section class="widget widget-admin-list-edit">
        <div class="widget-heading">
            <?php echo Yii::t('text', 'service_list'); ?>

            <?php if (Yii::$app->admin->identity->isAdmin()): ?>
                <a href="<?php echo Url::to([
                    'service/new',
                    'lang' => Yii::$app->language,
                ]); ?>"
                   class="widget-btn"
                   title="<?php echo Yii::t('element', 'create_new_service'); ?>"
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
                            'attribute' => 'service_type_id',
                            'label' => Yii::t('element', 'service_type'),
                            'value' => function (\common\models\Service $service) {
                                if ($service->service_type_id === \common\models\ServiceType::MEMBER_TYPE_ID) {
                                    return Yii::t('text', 'subscription_membership');
                                }
                                if ($service->service_type_id === \common\models\ServiceType::SERVICE_CREDITS_TYPE_ID) {
                                    return Yii::t('text', 'service_credits');
                                }
                                if ($service->service_type_id === \common\models\ServiceType::CREDITS_TYPE_ID) {
                                    return Yii::t('text', 'credits');
                                }
                                if ($service->service_type_id === \common\models\ServiceType::TRIAL_TYPE_ID) {
                                    return Yii::t('text', 'trial');
                                }
                            }
                        ],
                        [
                            'attribute' => 'label',
                            'label' => Yii::t('element', 'service_label'),
                        ],
                        [
                            'attribute' => 'credits',
                            'label' => Yii::t('element', 'service_credits'),
                        ],
                        [
                            'attribute' => 'price',
                            'label' => Yii::t('element', 'service_price'),
                        ],
                        [
                            'attribute' => 'days',
                            'label' => Yii::t('element', 'service_days'),
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => Yii::t('element', 'service_created_at'),
                            'value' => function (\common\models\Service $service) {
                                $time = DateTime::createFromFormat( 'U', $service->created_at);
                                return $time->format( 'Y-m-d H:i:s' );
                            },
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\Service $service) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-pencil']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'service_edit'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'edit(event, ' . $service->id . ');',
                                ]);
                                return $link;
                            },
                            'visible' => Yii::$app->admin->identity->isAdmin(),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\Service $service) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-trash']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'service_delete'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'showRemoveModal(event, ' . $service->id . ');',
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
    'header' => Yii::t('element', 'service_edit'),
    'size' => 'modal-lg',
]);
    Pjax::begin(['id' => 'edit-pjax']);
    Pjax::end();
Modal::end(); ?>

<?php Modal::begin([
    'id' => 'remove-modal',
    'header' => Yii::t('element', 'service_delete'),
]); ?>

    <h4><?php echo Yii::t('element', 'remove_service_confirm'); ?></h4>

    <div class="modal-form-footer-center">
        <button id="delete-service-button-yes" class="primary-button yes-btn" onclick="remove();">
            <?php echo Yii::t('element', 'service_delete_confirm'); ?>
        </button>

        <button id="delete-service-button-no" class="secondary-button no-btn" data-dismiss="modal" aria-label="Close">
            <?php echo Yii::t('element', 'service_delete_cancel'); ?>
        </button>
    </div>

<?php Modal::end(); ?>
<?php
$this->registerJs(
    'var actionRenderEditForm = "' . Url::to(['service/render-edit-form', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRemove = "' . Url::to(['service/remove', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/service/index.js', ['depends' => [JqueryAsset::className()]]);
