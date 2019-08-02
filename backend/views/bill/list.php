<?php

use backend\controllers\ClientController;
use common\models\Company;
use common\models\UserInvoice;
use kartik\export\ExportMenu;
use nterms\pagesize\PageSize;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;

/** @var View $this */
/** @var UserInvoice $userInvoice */
/** @var ActiveDataProvider $dataProvider */
/** @var float $paidBillsAmount */
/** @var float $unpaidBillsAmount */
/** @var array $dateRanges */
/** @var boolean $returnFromXmlExport */

$this->title = Yii::t('seo', 'TITLE_BILL_LIST');
$this->params['breadcrumbs'][] = $this->title;

$dataColumns = [
    [
        'attribute' => 'number',
        'label' => Yii::t('element', 'A-C-401'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-401',
            'class' => 'bill-number-column',
        ],
        'contentOptions' => [
            'class' => 'bill-number-column-content A-C-404 text-left',
            'data-title' => Yii::t('element', 'A-C-401'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            return Html::a($userInvoice->number, [
                'bill/download',
                'lang' => Yii::$app->language,
                'id' => $userInvoice->id,
                'preview' => 0,
            ]);
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
    ],
    [
        'attribute' => 'buyer_title',
        'label' => Yii::t('element', 'A-C-402'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-402',
            'class' => 'buyer-title-column',
        ],
        'contentOptions' => [
            'class' => 'buyer-title-column-content A-C-405 text-left',
            'data-title' => Yii::t('element', 'A-C-402'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            $company = Company::findByCompanyId($userInvoice->buyer_id);
            return 'ID: ' . $company->id . ' (' . Html::a(Html::encode($company->getTitleByType()), [
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
        'attribute' => 'updated_at',
        'label' => Yii::t('element', 'A-C-403'),
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-403',
            'class' => 'bill-generation-date-column',
        ],
        'contentOptions' => [
            'class' => 'bill-generation-date-column-content A-C-406 text-left',
            'data-title' => Yii::t('element', 'A-C-403'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            return date('Y-m-d H:i', $userInvoice->updated_at);
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
    ],
];

$actionColumns = [
    [
        'attribute' => 'regenerate',
        'label' => '',
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-408',
            'class' => 'regenerate-column',
        ],
        'contentOptions' => [
            'class' => 'action-column regenerate-column-content A-C-408 text-center',
            'data-title' => Yii::t('element', 'A-C-408'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            return Html::a(Html::tag('i', null, ['class' => 'fa fa-refresh A-C-407']), [
                'bill/regenerate',
                'lang' => Yii::$app->language,
                'id' => $userInvoice->id,
            ], [
                'data-placement' => 'top',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('element', 'A-C-408'),
                'class' => 'info radius-btn'
            ]);
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
        'visible' => !Yii::$app->admin->identity->isModerator(),
    ],
    [
        'attribute' => 'mark-as-paid',
        'label' => '',
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-442',
            'class' => 'mark-as-paid-column',
        ],
        'contentOptions' => [
            'class' => 'action-column mark-as-paid-column-content A-C-442 text-center clearfix',
            'data-title' => Yii::t('element', 'A-C-442'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            if ($userInvoice->type == UserInvoice::INVOICE) {
                return '';
            }

            return Html::a(Html::tag('i', null, ['class' => 'fa fa-check A-C-441']), [
                'bill/mark-as-paid',
                'lang' => Yii::$app->language,
                'id' => $userInvoice->id,
            ], [
                'data-placement' => 'top',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('element', 'A-C-442'),
                'class' => 'success radius-btn'
            ]);
        },
        'footerOptions' => [
            'class' => 'hidden',
        ],
        'visible' => !Yii::$app->admin->identity->isModerator(),
    ],
    [
        'attribute' => 'preview',
        'label' => '',
        'format' => 'raw',
        'headerOptions' => [
            'id' => 'A-C-409',
            'class' => 'preview-document-column',
        ],
        'contentOptions' => [
            'class' => 'action-column preview-document-column-content A-C-409 text-center',
            'data-title' => Yii::t('element', 'A-C-409'),
        ],
        'value' => function (UserInvoice $userInvoice) {
            $caretIcon = Html::tag('i', null, ['class' => 'fa fa-caret-down', 'aria-hidden' => true]);
            return Html::a($caretIcon, [
                'bill/download',
                'lang' => Yii::$app->language,
                'id' => $userInvoice->id,
            ], [
                'target' => '_blank',
                'class' => 'preview-icon',
                'data-placement' => 'top',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('element', 'A-C-409'),
            ]);
        },
        'footer' => Html::tag('div',
                Html::tag('span', Yii::t('element', 'A-C-393'), ['class' => 'total-price-label']) . ' ' .
                Html::tag('span', number_format($paidBillsAmount, 2), [
                    'id' => 'A-C-393',
                ]) . " &euro;", [
                    'id' => 'A-C-392',
                    'class' => 'paid-bills-container',
                ]) . Html::tag('div',
                Html::tag('span', Yii::t('element', 'A-C-424'), ['class' => 'total-price-label']) . ' ' .
                Html::tag('span', number_format($unpaidBillsAmount, 2), [
                    'id' => 'A-C-425',
                ]) . " &euro;", [
                    'id' => 'A-C-424',
                    'class' => 'unpaid-bills-container',
                ]),
        'footerOptions' => [
            'colspan' => 6, // NOTE: change this if you change the number of columns
            'class' => 'text-right',
        ],
    ],
];
?>

<div class="bill-list">
    <section class="widget widget-bill-list">
        <div id="A-C-379" class="widget-heading">
            <?php echo Yii::t('element', 'A-C-379'); ?>
        </div>

        <div class="widget-content">
            <?php echo Yii::$app->controller->renderPartial('/bill/partial/filtration/list', [
                'userInvoice' => $userInvoice,
                'returnFromXmlExport' => $returnFromXmlExport,
            ]); ?>
            
            <?php $disabledClass = $dataProvider->getTotalCount() == 0 ?
                ' export-full-csv-disabled' : ''; ?>
            
            <div class="export-page-select-row clearfix">
                <div class="export-csv-btn-wrapper pull-left">
                    <?php echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $dataColumns,
                        'emptyText' => Yii::t('element', 'A-C-394a'),
                        'target' => ExportMenu::TARGET_SELF,
                        'showConfirmAlert' => false,
                        'asDropdown' => false,
                        'showColumnSelector' => false,
                        'options' => [
                            'id' => 'A-C-394',
                        ],
                        'exportConfig' => [
                            ExportMenu::FORMAT_HTML => false,
                            ExportMenu::FORMAT_CSV => [
                                'label' => Yii::t('element', 'A-C-394b'),
                                'icon' => false,
                                'alertMsg' => Yii::t('element', 'A-C-394c'),
                                'options' => [
                                    'class' => 'export-list-inline',
                                ],
                                'linkOptions' => [
                                    'class' => 'export-full-csv' . $disabledClass,
                                ],
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
                        'filename' => Yii::t('element', 'A-C-394d'),
                    ]); 
                    
                    echo Html::button(Yii::t('element', 'export_xml'), [
                        'id' => 'export-xml',
                        'class' => 'export-full-csv' . $disabledClass,
                        'title' => Yii::t('element', 'export_xml_title'),
                        'data-url' => Url::to('/bill/export-invoices-xml'),
                        'disabled' => $dataProvider->getTotalCount() == 0,
                    ]); ?>
                </div>
                
                <div class="pull-right page-size-dropdown select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo PageSize::widget([
                        'label' => Yii::t('element', 'A-C-383'),
                        'defaultPageSize' => UserInvoice::SECOND_PAGE_SIZE,
                        'sizes' => UserInvoice::getPageSizes(),
                        'template' => '{label}{list}',
                        'options' => [
                            'id' => 'A-C-384',
                        ],
                        'labelOptions' => [
                            'id' => 'A-C-383',
                        ],
                    ]); ?>
                </div>
            </div>
            
            <div class="responsive-table-wrapper">
                <?php echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterSelector' => 'select[name="per-page"]',
                    'options' => [
                        'class' => 'custom-gridview bill-list-gridview',
                    ],
                    'tableOptions' => [
                        'class' => 'responsive-table'
                    ],
                    'resizableColumns' => false,
                    'responsiveWrap' => false,
                    'responsive' => false,
                    'summary' => Html::tag('div', Yii::t('element', 'A-C-396') . ' ' .
                        UserInvoice::convertDateRangesToText($dateRanges), ['class' => 'summary']),
                    'rowOptions' => function (UserInvoice $userInvoice) {
                        return [
                            'class' => $userInvoice->type == UserInvoice::INVOICE ? 'invoice' : 'pre-invoice',
                        ];
                    },
                    'columns' => array_merge($dataColumns, $actionColumns),
                    'showFooter' => true,
                ]); ?>
            </div>
        </div>
    </section>
</div>
