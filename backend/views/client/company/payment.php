<?php

use backend\controllers\ClientController;
use common\components\Model;
use common\models\UserService;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var null|integer $id Company ID */
/** @var null|string $year */
/** @var ActiveDataProvider $paymentDataProvider */
?>
<div class="company-payment">
    <?php Pjax::begin(['id' => 'company-payment-pjax']); ?>
        <div class="select year-select-wrapper">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo Html::dropDownList('year', $year, Model::getYearsRange(), [
                'id' => 'A-C-272',
                'onchange' => 'changePaymentYear();',
                'class' => 'payment-year year-select',
            ]); ?>
        </div>

        <div class="reponsive-table-wrapper custom-gridview">
            <?php echo GridView::widget([
                'dataProvider' => $paymentDataProvider,
                'summary' => false,
                'rowOptions' => function (UserService $userService) {
                    return [
                        'class' => $userService->isPaid() ? 'paid' : 'not-paid',
                    ];
                },
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered responsive-table'
                ],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('element', 'A-C-273'),
                        'headerOptions' => [
                            'id' => 'A-C-273',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-273'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $span = Html::tag('span', $userService->id, ['class' => 'A-C-278']);
                            return $span;
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('element', 'A-C-274'),
                        'headerOptions' => [
                            'id' => 'A-C-274',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-274'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $date = date('Y-m-d H:i', $userService->created_at);
                            $span = Html::tag('span', $date, ['class' => 'A-C-279']);
                            return $span;
                        }
                    ],
                    [
                        'attribute' => 'content',
                        'label' => Yii::t('element', 'A-C-275'),
                        'headerOptions' => [
                            'id' => 'A-C-275',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-275'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $serviceTitle = $userService->service->getTitle();
                            $span = Html::tag('span', $serviceTitle, ['class' => 'A-C-280']);
                            return $span;
                        }
                    ],
                    [
                        'attribute' => 'paid_by',
                        'label' => Yii::t('element', 'A-C-276'),
                        'headerOptions' => [
                            'id' => 'A-C-276',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-276'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $methods = UserService::getTranslatedPaidByMethods();
                            if (!array_key_exists($userService->paid_by, $methods)) {
                                return Yii::t('yii', '(not set)');
                            }

                            $method = $methods[$userService->paid_by];
                            $span = Html::tag('span', $method, ['class' => 'A-C-281']);
                            return $span;
                        }
                    ],
                    [
                        'attribute' => 'paid',
                        'label' => Yii::t('element', 'A-C-277'),
                        'headerOptions' => [
                            'id' => 'A-C-277',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-277'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $methods = UserService::getTranslatedPaidMethods();
                            if (!array_key_exists($userService->paid, $methods)) {
                                return Yii::t('yii', '(not set)');
                            }

                            $method = $methods[$userService->paid];
                            $span = Html::tag('span', $method, ['class' => 'A-C-282']);
                            return $span;
                        }
                    ],
                ],
                'pager' => [
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                ],            
            ]); ?>
        </div>
    <?php Pjax::end(); ?>
</div>
<?php
$this->registerJs(
    'var actionCompanyPayments = "' . Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $id,
        'tab' => ClientController::TAB_COMPANY_PAYMENTS,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/payment.js', ['depends' => [JqueryAsset::className()]]);