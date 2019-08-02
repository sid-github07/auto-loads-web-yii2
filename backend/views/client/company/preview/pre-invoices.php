<?php

use backend\controllers\ClientController;
use common\components\Model;
use common\models\UserService;
use kartik\icons\Icon;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var null|integer $companyId */
/** @var null|integer $year */
/** @var ActiveDataProvider $preInvoiceDataProvider */
?>
<?php if (!Yii::$app->admin->identity->isModerator()): ?>
<div class="text-right">
    <?php echo Html::button(Icon::show('plus', [], Icon::FA) . Yii::t('element', 'A-C-258'), [
        'id' => 'A-C-258',
        'class' => 'primary-button create-new-pre-invoice-btn',
        'onclick' => 'createPreInvoice();',
    ]); ?>
</div>
<?php endif; ?>

<div class="clearfix">
    <div class="select year-select-wrapper">
        <span class="select-arrow"><i class="fa fa-caret-down"></i></span>

        <?php echo Html::dropDownList('pre-invoice-year', $year, Model::getYearsRange(), [
            'id' => 'A-C-237',
            'class' => 'pre-invoice-year year-select',
            'onchange' => 'changePreInvoiceYear();',
        ]); ?>
    </div>
</div>

<div class="responsive-table-wrapper custom-gridview">
    <?php echo GridView::widget([
        'dataProvider' => $preInvoiceDataProvider,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered responsive-table'
        ],
        'columns' => [
            [
                'attribute' => 'number',
                'label' => Yii::t('element', 'A-C-238'),
                'headerOptions' => [
                    'id' => 'A-C-238',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-238'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $userInvoice = $userService->getPreInvoice();
                    if (is_null($userInvoice)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-243']);
                    }

                    return Html::tag('span', $userInvoice->number, ['class' => 'A-C-243']);
                }
            ],
            [
                'attribute' => 'user_invoice_updated_at',
                'label' => Yii::t('element', 'A-C-239'),
                'headerOptions' => [
                    'id' => 'A-C-239',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-239'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $userInvoice = $userService->getPreInvoice();
                    if (is_null($userInvoice)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-244']);
                    }

                    $date = date('Y-m-d H:i:s', $userInvoice->updated_at);
                    return Html::tag('span', $date, ['class' => 'A-C-244']);
                }
            ],
            [
                'attribute' => 'user',
                'label' => Yii::t('element', 'A-C-240'),
                'headerOptions' => [
                    'id' => 'A-C-240',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-240'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $name = Html::tag('span', $userService->user->getNameAndSurname(), ['class' => 'A-C-245']);
                    $id = Html::tag('span', $userService->user_id, ['class' => 'A-C-247']);
                    $text = Html::tag('span', "$name (ID: $id)", ['class' => 'A-C-246']);
                    return $text;
                }
            ],
            [
                'attribute' => 'generated_by',
                'label' => Yii::t('element', 'A-C-241'),
                'headerOptions' => [
                    'id' => 'A-C-241',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-241'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $whoGenerated = $userService->getWhoGenerated();
                    $span = Html::tag('span', $whoGenerated, ['class' => 'A-C-248']);
                    return $span;
                }
            ],
            [
                'attribute' => 'service_updated_at',
                'label' => Yii::t('element', 'A-C-242'),
                'headerOptions' => [
                    'id' => 'A-C-242',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-242'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $date = date('Y-m-d', $userService->service->updated_at);
                    $span = Html::tag('span', $date, ['class' => 'A-C-249']);
                    return $span;
                }
            ],
            [
                'attribute' => 'download',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                    'data-title' => Yii::t('element', 'A-C-251'),
                ],
                'value' => function (UserService $userService) {
                    $preInvoiceId = $userService->getPreInvoiceId();
                    if (is_null($preInvoiceId)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-251']);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-download A-C-250']);
                    $link = Html::a($icon, Url::to([
                        'bill/download',
                        'lang' => Yii::$app->language,
                        'id' => $preInvoiceId,
                        'preview' => false,
                    ]), [
                        'class' => 'A-C-251 warning radius-btn',
                        'title' => Yii::t('element', 'A-C-251'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-pjax' => 0,
                    ]);

                    return $link;
                }
            ],
            [
                'attribute' => 'refresh',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                    'data-title' => Yii::t('element', 'A-C-253'),
                ],
                'value' => function (UserService $userService) use ($companyId) {
                    $preInvoiceId = $userService->getPreInvoiceId();
                    if (is_null($preInvoiceId)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-253']);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-refresh A-C-252']);
                    $link = Html::a($icon, Url::to([
                        'bill/regenerate',
                        'lang' => Yii::$app->language,
                        'id' => $preInvoiceId,
                        'companyId' => $companyId,
                        'tab' => ClientController::TAB_COMPANY_PRE_INVOICES,
                    ]), [
                        'class' => 'A-C-253 info radius-btn',
                        'title' => Yii::t('element', 'A-C-253'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-pjax' => 0,
                    ]);

                    return $link;
                },
                'visible' => !Yii::$app->admin->identity->isModerator(),
            ],
            [
                'attribute' => 'mark-as-paid',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                    'data-title' => Yii::t('element', 'A-C-255'),
                ],
                'value' => function (UserService $userService) use ($companyId) {
                    $preInvoiceId = $userService->getPreInvoiceId();
                    if (is_null($preInvoiceId)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-254']);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-check A-C-254']);
                    $link = Html::a($icon, Url::to([
                        'bill/mark-as-paid',
                        'lang' => Yii::$app->language,
                        'id' => $preInvoiceId,
                        'companyId' => $companyId,
                        'tab' => ClientController::TAB_COMPANY_PRE_INVOICES,
                    ]), [
                        'class' => 'A-C-255 success radius-btn',
                        'title' => Yii::t('element', 'A-C-255'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-pjax' => 0,
                    ]);

                    return $link;
                },
                'visible' => !Yii::$app->admin->identity->isModerator(),
            ],
            [
                'attribute' => 'send-email',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                    'data-title' => Yii::t('element', 'A-C-257'),
                ],
                'value' => function (UserService $userService) use ($companyId) {
                    $preInvoiceId = $userService->getPreInvoiceId();
                    if (is_null($preInvoiceId)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-257']);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-envelope-o A-C-256']);
                    $link = Html::a($icon, Url::to([
                        'bill/send-pre-invoice-document-to-user',
                        'lang' => Yii::$app->language,
                        'preInvoiceId' => $preInvoiceId,
                        'companyId' => $companyId,
                        'tab' => ClientController::TAB_COMPANY_PRE_INVOICES,
                    ]), [
                        'class' => 'A-C-257 warning radius-btn',
                        'title' => Yii::t('element', 'A-C-257'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-pjax' => 0,
                    ]);

                    return $link;
                },
                'visible' => !Yii::$app->admin->identity->isModerator(),
            ],
        ],
        'pager' => [
            'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
            'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
        ],
    ]); ?>
</div>

<?php Modal::begin([
    'id' => 'pre-invoice-creation-modal',
    'header' => Html::tag('span', Yii::t('element', 'A-C-259'), ['class' => 'A-C-259']),
]); ?>

    <?php Pjax::begin(['id' => 'pre-invoice-creation-pjax']); ?>
    <?php Pjax::end(); ?>

<?php Modal::end(); ?>

<?php
$this->registerJs(
    'var actionRenderPreInvoiceCreationForm = "' . Url::to([
        'client/render-pre-invoice-creation-form',
        'lang' => Yii::$app->language,
        'companyId' => $companyId,
    ]) . '";',
View::POS_BEGIN);