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

$this->title = Yii::t('seo', 'Credit Services');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-index clearfix">
    <section class="widget widget-admin-list-edit">
        <div class="widget-heading">
            <?php echo Yii::t('element', 'credit_service_list'); ?>
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
                            'attribute' => 'credit_type_string',
                            'label' => Yii::t('element', 'credit_type'),
                            'format' => 'raw',
                            'value' => function(\common\models\CreditService $creditService) {
                                return Yii::t('element', $creditService->creditTypeString());
                            }
                        ],
                        [
                            'attribute' => 'credit_cost',
                            'label' => Yii::t('element', 'credit_cost'),
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => Yii::t('element', 'credit_services_created_at'),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'label' => Yii::t('element', 'credit_services_updated_at'),
                        ],
                        [
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function (\common\models\CreditService $creditService) {
                                $icon = Html::tag('i', null, ['class' => 'fa fa-pencil']);
                                $link = Html::a($icon, '#', [
                                    'title' => Yii::t('element', 'credit_service_edit'),
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'onclick' => 'edit(event, ' . $creditService->id . ');',
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
    'header' => Yii::t('element', 'credit_service_edit'),
    'size' => 'modal-lg',
]);
    Pjax::begin(['id' => 'edit-pjax']);
    Pjax::end();
Modal::end(); ?>

<?php
$this->registerJs(
    'var actionRenderEditForm = "' . Url::to(['credit-service/render-edit-form', 'lang' => Yii::$app->language]) . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/credit-service/index.js', ['depends' => [JqueryAsset::className()]]);
