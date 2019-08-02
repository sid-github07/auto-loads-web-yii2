<?php

use backend\controllers\ClientController;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var string $tab */
/** @var null|integer $id Company ID */
/** @var null|integer $year */
/** @var ActiveDataProvider $invoiceDataProvider */
/** @var ActiveDataProvider $preInvoiceDataProvider */
?>
<div class="company-invoices">
    <?php Pjax::begin(['id' => 'company-bill-ajax']); ?>
        <?php echo Tabs::widget([
            'navType' => 'nav-tabs nav-justified tabs-navigation',
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => Html::tag('span', Yii::t('element', 'A-C-216'), ['class' => 'tab-label-text']),
                    'content' => Yii::$app->controller->renderPartial('/client/company/preview/invoices', [
                        'companyId' => $id,
                        'year' => $year,
                        'invoiceDataProvider' => $invoiceDataProvider,
                    ]),
                    'active' => $tab === ClientController::TAB_COMPANY_INVOICES,
                    'linkOptions' => [
                        'id' => 'A-C-216',
                        'title' => Yii::t('element', 'A-C-216'),
                        'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_INVOICES . '");',
                    ],
                ],
                [
                    'label' => Html::tag('span', Yii::t('element', 'A-C-236'), ['class' => 'tab-label-text']),
                    'content' => Yii::$app->controller->renderPartial('/client/company/preview/pre-invoices', [
                        'companyId' => $id,
                        'year' => $year,
                        'preInvoiceDataProvider' => $preInvoiceDataProvider,
                    ]),
                    'active' => $tab === ClientController::TAB_COMPANY_PRE_INVOICES,
                    'linkOptions' => [
                        'id' => 'A-C-236',
                        'title' => Yii::t('element', 'A-C-236'),
                        'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_PRE_INVOICES . '");',
                    ],
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
$this->registerJs(
    'var actionCompanyInvoices = "' . Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $id,
        'tab' => ClientController::TAB_COMPANY_INVOICES,
    ]) . '"; ' .
    'var actionCompanyPreInvoices = "' . Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $id,
        'tab' => ClientController::TAB_COMPANY_PRE_INVOICES,
    ]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/bill.js', ['depends' => [JqueryAsset::className()]]);