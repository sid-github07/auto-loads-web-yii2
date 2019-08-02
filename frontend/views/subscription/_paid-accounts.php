<?php

use yii\helpers\Url;

/** @var array $preInvoices */
/** @var array $invoices */
?>

<div class="custom-table paid-accounts-table table-responsive">
    <table id="paid-accounts" class="table table-striped">
        <thead>
            <tr class="headline">
                <th>
                    <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_NUMBER'); ?>
                </th>
                <th>
                    <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_BUY_DATE'); ?>
                </th>
                <th>
                    <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_STATUS'); ?>
                </th>
                <th>
                    <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_REVIEW_DOWNLOAD'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            
            <?php // foreach ($preInvoices as $key => $preInvoice): ?>
                <?php // if (!array_key_exists($key, $invoices)): ?>
<!--                    <tr class="paid-accounts-row">
                        <td>-->
                            <?php // echo $preInvoice->number ?>
<!--                        </td>
                        <td>-->
                            <?php // echo date('Y-m-d', $preInvoice->date) ?>
<!--                        </td>
                        <td>-->
                            <?php // echo Yii::t('app', 'PAID_ACCOUNT_TABLE_UNPAID_ACCOUNT'); ?>
<!--                        </td>
                        <td>-->
<!--                            <a href="<?php // echo Url::to([
//                                    'subscription/download-invoice',
//                                    'lang' => Yii::$app->language,
//                                    'id' => $preInvoice->user_service_id,
//                                    'type' => $preInvoice->type,
//                               ]); ?>" 
                               class="download-document"
                               data-pjax="0"
                               target="_blank"
                            >-->
<!--                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>-->
                                <?php // echo $preInvoice->file_name . '.' . $preInvoice->file_extension; ?>
<!--                            </a>
                        </td>
                    </tr>-->
                <?php // endif; ?>
            <?php // endforeach; ?>
                    
            <?php foreach ($invoices as $invoice): ?>
                <tr class="paid-accounts-row">
                    <td>
                        <?php echo $invoice->number ?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d H:i', $invoice->date); ?>
                    </td>
                    <td>
                        <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_PAID_ACCOUNT'); ?>
                    </td>
                    <td>
                        <a href="<?php echo Url::to([
                                'subscription/download-invoice',
                                'lang' => Yii::$app->language,
                                'id' => $invoice->user_service_id,
                                'type' => $invoice->type,
                           ]); ?>" 
                           class="download-document"
                           data-pjax="0"
                           target="_blank"
                        >
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                            <?php echo $invoice->file_name . '.' . $invoice->file_extension; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
                    
            <?php if (empty($invoices)): ?>
                <tr class="paid-accounts-row">
                    <td colspan="4">
                        <?php echo Yii::t('app', 'PAID_ACCOUNT_TABLE_NO_ACCOUNTS_YET'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>