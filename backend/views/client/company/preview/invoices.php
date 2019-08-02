<?php

use backend\controllers\ClientController;
use common\models\UserService;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use common\components\Model;
use yii\helpers\Url;

/** @var null|integer $companyId */
/** @var null|integer $year */
/** @var ActiveDataProvider $invoiceDataProvider */
?>

<div class="clearfix">
    <div class="select year-select-wrapper">
        <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
        <?php echo Html::dropDownList('invoice-year', $year, Model::getYearsRange(), [
            'id' => 'A-C-217',
            'class' => 'invoice-year year-select',
            'onchange' => 'changeInvoiceYear();',
        ]); ?>
    </div>
</div>

<div class="responsive-table-wrapper custom-gridview">
    <?php echo GridView::widget([
        'dataProvider' => $invoiceDataProvider,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered responsive-table'
        ],
        'columns' => [
            [
                'attribute' => 'number',
                'label' => Yii::t('element', 'A-C-218'),
                'headerOptions' => [
                    'id' => 'A-C-218',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-218'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $userInvoice = $userService->getInvoice();
                    if (is_null($userInvoice)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-224']);
                    }

                    return Html::tag('span', $userInvoice->number, ['class' => 'A-C-224']);
                }
            ],
            [
                'attribute' => 'user_invoice_updated_at',
                'label' => Yii::t('element', 'A-C-219'),
                'headerOptions' => [
                    'id' => 'A-C-219',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-219'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $userInvoice = $userService->getInvoice();
                    if (is_null($userInvoice)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-225']);
                    }

                    $date = date('Y-m-d H:i:s', $userInvoice->updated_at);
                    $span = Html::tag('span', $date, ['class' => 'A-C-225']);
                    return $span;
                }
            ],
            [
                'attribute' => 'id',
                'label' => Yii::t('element', 'A-C-220'),
                'headerOptions' => [
                    'id' => 'A-C-220',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-220'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    return Html::tag('span', $userService->id, ['class' => 'A-C-226']);
                }
            ],
            [
                'attribute' => 'user',
                'label' => Yii::t('element', 'A-C-221'),
                'headerOptions' => [
                    'id' => 'A-C-221',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-221'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $name = Html::tag('span', $userService->user->getNameAndSurname(), ['class' => 'A-C-227']);
                    $id = Html::tag('span', $userService->user_id, ['class' => 'A-C-229']);
                    $text = Html::tag('span', "$name (ID: $id)", ['class' => 'A-C-228']);
                    return $text;
                }
            ],
            [
                'attribute' => 'generated_by',
                'label' => Yii::t('element', 'A-C-222'),
                'headerOptions' => [
                    'id' => 'A-C-222',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-222'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    return Html::tag('span', $userService->getWhoGenerated(), ['class' => 'A-C-230']);
                }
            ],
            [
                'attribute' => 'service_updated_at',
                'label' => Yii::t('element', 'A-C-223'),
                'headerOptions' => [
                    'id' => 'A-C-223',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-223'),
                ],
                'format' => 'raw',
                'value' => function (UserService $userService) {
                    $date = date('Y-m-d', $userService->service->updated_at);
                    $span = Html::tag('span', $date, ['class' => 'A-C-231']);
                    return $span;
                }
            ],
            [
                'attribute' => 'preview',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                ],
                'contentOptions' => [
                    'data-title' => Yii::t('element', 'A-C-233'),
                ],
                'value' => function (UserService $userService) {
                    $invoiceId = $userService->getInvoiceId();
                    if (is_null($invoiceId)) {
                        return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'A-C-233']);
                    }

                    $icon = Html::tag('i', '', ['class' => 'fa fa-file-pdf-o A-C-232']);
                    $link = Html::a($icon, Url::to([
                        'bill/download',
                        'lang' => Yii::$app->language,
                        'id' => $invoiceId,
                        'preview' => true,
                    ]), [
                        'class' => 'A-C-233 danger radius-btn',
                        'title' => Yii::t('element', 'A-C-233'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'target' => '_blank',
                        'data-pjax' => 0,
                    ]);

                    return $link;
                },
            ],
            [
                'attribute' => 'regenerate',
                'label' => '',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'action-column text-center',
                    'data-title' => Yii::t('element', 'A-C-235'),
                ],
                'value' => function (UserService $userService) use ($companyId) {
                    $invoiceId = $userService->getInvoiceId();
                    if (is_null($invoiceId)) {
                        return Yii::t('yii', '(not set)');
                    }
                    
                    $icon = Html::tag('i', '', ['class' => 'fa fa-refresh A-C-234']);
                    $link = Html::a($icon, Url::to([
                        'bill/regenerate',
                        'lang' => Yii::$app->language,
                        'id' => $invoiceId,
                        'companyId' => $companyId,
                        'tab' => ClientController::TAB_COMPANY_INVOICES,
                    ]), [
                        'class' => 'A-C-235 info radius-btn',
                        'title' => Yii::t('element', 'A-C-235'),
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