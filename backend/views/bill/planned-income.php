<?php

use backend\controllers\ClientController;
use common\models\Company;
use common\models\UserInvoice;
use common\models\UserService;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use nterms\pagesize\PageSize;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var UserService $userService */
/** @var Company $company */
/** @var UserInvoice $userInvoice */
/** @var ActiveDataProvider $dataProvider */
/** @var float $plannedIncome */
/** @var array $dateRanges */

$this->title = Yii::t('seo', 'TITLE_BILL_PLANNED_INCOME');
$this->params['breadcrumbs'][] = $this->title;

// TODO: čia turi būti nustatoma default_timezone

$dataColumns = [
    [
        'attribute' => 'companyName',
        'label' => Yii::t('element', 'A-C-468'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-468',
            'class' => 'service-buyer-company-name',
        ],
        'contentOptions' => [
            'class' => 'service-buyer-company-name-content A-C-471 text-left',
            'data-title' => Yii::t('element', 'A-C-468'),
        ],
        'value' => function (UserService $userService) {
            $company = Company::findUserCompany($userService->user->id);
            return 'ID: ' . $company->id . ' (' . Html::a($company->getTitleByType(), [
                'client/company',
                'lang' => Yii::$app->language,
                'id' => $company->id,
                'tab' => ClientController::TAB_COMPANY_INFO,
            ]) . ')';
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
    ],
    [
        'attribute' => 'end_date',
        'label' => Yii::t('element', 'A-C-469'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-469',
            'class' => 'service-end-date',
        ],
        'contentOptions' => [
            'class' => 'service-end-date-content A-C-472 A-C-473 text-left',
            'data-title' => Yii::t('element', 'A-C-469'),
        ],
        'value' => function (UserService $userService) {
            list($days, $hours) = $userService->countRemainingDays();
            return Html::tag('div', date('Y-m-d H:i:s', $userService->end_date) . ' ') .
                   Html::tag('div', Yii::t('element', 'A-C-473', compact('days', 'hours')));
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
    ],
    [
        'attribute' => 'lastInvoiceInfo',
        'label' => Yii::t('element', 'A-C-470'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-470',
            'class' => 'last-invoice-info',
        ],
        'contentOptions' => [
            'class' => 'last-invoice-info-content A-C-474 A-C-475 text-left',
            'data-title' => Yii::t('element', 'A-C-470'),
        ],
        'value' => function (UserService $userService) {
            $invoices = $userService->userInvoices;
            usort($invoices, function ($a, $b) {
                return strcmp($b->updated_at, $a->updated_at);
            });
            $invoice = current($invoices); // Last invoice
            if (!$invoice) {
                return null;
            }

            return Html::tag('div', Html::a($invoice->number, [
                    'bill/download',
                    'lang' => Yii::$app->language,
                    'id' => $invoice->id,
                ], [
                    'target' => '_blank',
                ])) . ' ' .
                   Html::tag('div', $invoice->netto_price . ' €');
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
    ],
];

$footerColumns = [
    [
        'headerOptions' => [
            'class' => 'hidden',
        ],
        'contentOptions' => [
            'class' => 'hidden',
        ],
        'value' => function () {
            return '';
        },
        'footer' => Html::tag('div',
            Html::tag('span', Yii::t('element', 'A-C-459'), ['class' => 'total-price-label']) . ' ' .
            Html::tag('span', number_format($plannedIncome, 2), [
                'id' => 'A-C-460',
            ]) . " &euro;", [
                'id' => 'A-C-459',
                'class' => 'planned-income-container',
            ]),
        'footerOptions' => [
            'colspan' => 4, // NOTE: change this if you cahnge the number of columns
            'class' => 'text-right',
        ],
    ],
];
?>

<div class="bill-planned-income">
    <section class="widget">
        <div id="A-C-445" class="widget-heading">
            <?php echo Yii::t('element', 'A-C-445'); ?>
        </div>

        <div id="A-C-446" class="notice">
            <?php echo Yii::t('element', 'A-C-446'); ?>
        </div>

        <div class="widget-content">
            <?php echo Yii::$app->controller->renderPartial('/bill/partial/filtration/planned-income', [
                'userService' => $userService,
                'company' => $company,
                'userInvoice' => $userInvoice,
            ]); ?>

            <div class="export-page-select-row clearfix">
                <div class="export-csv-btn-wrapper pull-left">
                    <?php echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $dataColumns,
                        'emptyText' => Yii::t('element', 'A-C-461a'),
                        'target' => ExportMenu::TARGET_SELF,
                        'showConfirmAlert' => false,
                        'asDropdown' => false,
                        'showColumnSelector' => false,
                        'options' => [
                            'id' => 'A-C-461',
                        ],
                        'exportConfig' => [
                            ExportMenu::FORMAT_HTML => false,
                            ExportMenu::FORMAT_CSV => [
                                'label' => Yii::t('element', 'A-C-461b'),
                                'icon' => false,
                                'alertMsg' => Yii::t('element', 'A-C-461c'),
                                'mime' => 'application/csv',
                                'extension' => 'csv',
                                'writer' => ExportMenu::FORMAT_CSV,
                            ],
                            ExportMenu::FORMAT_TEXT => false,
                            ExportMenu::FORMAT_PDF => false,
                            ExportMenu::FORMAT_EXCEL => false,
                            ExportMenu::FORMAT_EXCEL_X => false,
                        ],
                        'fontAwesome' => true,
                        'filename' => Yii::t('element', 'A-C-461d'),
                    ]); ?>
                </div>

                <div class="pull-right page-size-dropdown select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo PageSize::widget([
                        'label' => Yii::t('element', 'A-C-450'),
                        'defaultPageSize' => UserInvoice::SECOND_PAGE_SIZE,
                        'sizes' => UserInvoice::getPageSizes(),
                        'template' => '{label}{list}',
                        'options' => [
                            'id' => 'A-C-451',
                        ],
                        'labelOptions' => [
                            'id' => 'A-C-450',
                        ],
                    ]); ?>
                </div>
            </div>

            <div class="responsive-table-wrapper">
                <?php echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterSelector' => 'select[name="per-page"]',
                    'options' => [
                        'class' => 'custom-gridview planned-income-gridview',
                    ],
                    'tableOptions' => [
                        'class' => 'responsive-table'
                    ],
                    'resizableColumns' => false,
                    'responsiveWrap' => false,
                    'responsive' => false,
                    'summary' => Html::tag('div',
                        Yii::t('element', 'A-C-462') . ' ' . UserService::convertDateRangesToText($dateRanges),
                        ['id' => 'A-C-462', 'class' => 'summary']),
                    'rowOptions' => function (UserService $userService) {
                        $hasSubscription = count($userService->user->userServiceActives) > 0;
                        return [
                            'class' => $hasSubscription ? 'has-subscription' : 'no-subscription',
                        ];
                    },
                    'columns' => array_merge($dataColumns, $footerColumns),
                    'showFooter' => true,
                    'pager' => [
                        'options' => [
                            'id' => 'A-C-476',
                            'class' => 'pagination',
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </section>
</div>
