<?php

use common\models\UserInvoice;
use kartik\icons\Icon;
use yii\helpers\Url;

/** @var UserInvoice $userInvoice */
/** @var integer $userServiceId */
/** @var array $steps */

$message = ($userInvoice->type == UserInvoice::PRE_INVOICE ? 'PRE_INVOICE_HEADING' : 'INVOICE_HEADING');
Icon::map($this, Icon::FA);
?>

<div class="wizard-elements-container">
    <?php echo Yii::$app->controller->renderPartial('___service-wizard', [
        'steps' => $steps,
    ]); ?>    
</div>

<div class="payment-reminder">
    <?php echo Yii::t('app', 'SERVICE_PRE_INVOICE_PAYMENT')?>
</div>

<table id="download-preinvoice-payment">
    <thead>
        <tr class="headline">
            <th>
                <?php echo Yii::t('app', 'DOWNLOAD_PRE_INVOICE_PAYMENT_TABLE_NAME'); ?>
            </th>
            <th>
                <?php echo Yii::t('app', 'DOWNLOAD_PRE_INVOICE_PAYMENT_TABLE_REVIEW'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="pre-invoice-document-row">
            <td>
                <?php echo Yii::t('document', $message, [
                    'number' => $userInvoice->number,
                ]); ?>
            </td>
            <td>
                <?php echo $userInvoice->file_name . '.' . $userInvoice->file_extension; ?>
                <a href="<?php echo Url::to([
                    'subscription/download-invoice',
                    'lang' => Yii::$app->language,
                    'id' => $userServiceId,
                    'type' => UserInvoice::PRE_INVOICE,
                ]); ?>"
                   class="download-pre-invoice-document"
                   data-pjax="0"
                   target="_blank"
                >
                    <i class="fa fa-download" aria-hidden="true"></i>
                    <?php echo Yii::t('app', 'DOWNLOAD_PRE_INVOICE_PAYMENT_TABLE_DOCUMENT_DOWNLOAD'); ?>
                </a>
            </td>
        </tr>
    </tbody>
</table>

<p class="not-activated-service">
    <?php echo Yii::t('app', 'SERVICE_NOT_ACTIVATED_YET'); ?>
</p>

<button id="PS-C-18" class="primary-button back-to-purchase" data-user-service-id="<?php echo $userServiceId; ?>">
    <?php echo Yii::t('app', 'DOWNLOAD_PRE_INVOICE_PAYMENT_BACK_TO_PURCHASE'); ?>
</button>