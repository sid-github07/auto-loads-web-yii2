<?php

use common\components\Location;
use common\components\NumberToText;
use common\models\UserInvoice;

/** @var UserInvoice $userInvoice */

$priceWithDiscount = (float)$userInvoice->netto_price + (float)$userInvoice->discount;
$vatPrice = number_format((($priceWithDiscount * $userInvoice->vat) / 100), 2, '.', '');
$bruttoPrice = number_format(((float)$priceWithDiscount + (float)$vatPrice), 2, '.', '');
$priceInWords = NumberToText::getPrice($bruttoPrice);
// TODO: čia turi būti pakeičiama default_timezone
?>

<div class="pdf-logo">
    <img src="<?php echo Yii::getAlias('@web') . '/images/logo150.png'; ?>"
         class="logo-image"
         alt="Auto-loads logotipas"
    />
    <div class="logo-text">
        <?php echo Yii::t('document', 'INVOICE_UNDER_LOGO_TEXT'); ?>
    </div>
</div>

<h3 class="invoice-heading">
    <?php echo Yii::t('document', ($userInvoice->type == UserInvoice::PRE_INVOICE ? 'PRE_' : '') . 'INVOICE_HEADING', [
        'number' => $userInvoice->number,
    ]); ?>
</h3>

<?php if ($userInvoice->type != UserInvoice::PRE_INVOICE): ?>
    <div class="invoice-date">
        <?php echo Yii::t('document', 'INVOICE_ORDER_NUMBER', [
            'number' => $userInvoice->user_service_id,
        ]); ?>
    </div>
<?php endif; ?>

<div class="invoice-date">
    <?php echo date('Y-m-d', $userInvoice->date); ?>
</div>

<hr />

<table class="imprint-table">
    <tbody>
    <tr>
        <td class="seller">
            <?php echo Yii::t('document', 'INVOICE_SELLER'); ?>
        </td>

        <td class="buyer text-right">
            <?php echo Yii::t('document', 'INVOICE_BUYER'); ?>
        </td>
    </tr>

    <tr>
        <td class="seller-company-name">
            <?php echo $userInvoice->seller_company_name; ?>
        </td>

        <td class="buyer-title text-right">
            <?php echo $userInvoice->buyer_title; ?>
        </td>
    </tr>

    <?php if (!is_null($userInvoice->buyer_code) && !is_null($userInvoice->buyer_vat_code)): ?>

        <tr>
            <td class="seller-company-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_COMPANY_CODE', [
                    'code' => $userInvoice->seller_company_code,
                ]); ?>
            </td>

            <td class="buyer-code text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_CODE', [
                    'code' => $userInvoice->buyer_code,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-vat-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_VAT_CODE', [
                    'code' => $userInvoice->seller_vat_code,
                ]); ?>
            </td>

            <td class="buyer-vat-code text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_VAT_CODE', [
                    'code' => $userInvoice->buyer_vat_code,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-address">
                <?php echo Yii::t('document', 'INVOICE_SELLER_ADDRESS', [
                    'address' => $userInvoice->seller_address,
                ]); ?>
            </td>

            <td class="buyer-address text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_ADDRESS', [
                    'address' => $userInvoice->buyer_address,
                    'city' => $userInvoice->buyerCity->name,
                    'country' => $userInvoice->buyerCity->country_code,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-bank-name">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_NAME', [
                    'name' => $userInvoice->seller_bank_name,
                ]); ?>
            </td>

            <td class="buyer-phone text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_PHONE', [
                    'number' => $userInvoice->buyer_phone,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-bank-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_CODE', [
                    'code' => $userInvoice->seller_bank_code,
                ]); ?>
            </td>

            <?php if (!empty($userInvoice->buyer_email)): ?>
                <td class="buyer-email text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_EMAIL', [
                        'email' => $userInvoice->buyer_email,
                    ]); ?>
                </td>
            <?php endif; ?>
        </tr>

        <tr>
            <td class="seller-swift">
                <?php echo Yii::t('document', 'INVOICE_SELLER_SWIFT', [
                    'code' => $userInvoice->seller_swift,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-bank-account">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_ACCOUNT', [
                    'number' => $userInvoice->seller_bank_account,
                ]); ?>
            </td>
        </tr>

    <?php else: ?>

        <tr>
            <td class="seller-company-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_COMPANY_CODE', [
                    'code' => $userInvoice->seller_company_code,
                ]); ?>
            </td>

            <td class="buyer-address text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_ADDRESS', [
                    'address' => $userInvoice->buyer_address,
                    'city' => $userInvoice->buyerCity->name,
                    'country' => $userInvoice->buyerCity->country_code,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-vat-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_VAT_CODE', [
                    'code' => $userInvoice->seller_vat_code,
                ]); ?>
            </td>
			
			<?php if (!is_null($userInvoice->buyer_vat_code) && !empty($userInvoice->buyer_vat_code)) : ?>
                <td class="buyer-vat-code text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_VAT_CODE', [
                        'code' => $userInvoice->buyer_vat_code,
                    ]); ?>
                </td>
            <?php elseif (!is_null($userInvoice->buyer_phone) && !empty($userInvoice->buyer_phone)) : ?>
                <td class="buyer-vat-code text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_PHONE', [
                        'number' => $userInvoice->buyer_phone,
                    ]); ?>
                </td>
            <?php endif; ?>
        </tr>

        <tr>
            <td class="seller-address">
                <?php echo Yii::t('document', 'INVOICE_SELLER_ADDRESS', [
                    'address' => $userInvoice->seller_address,
                ]); ?>
            </td>

            <?php if (!empty($userInvoice->buyer_email)): ?>
                <td class="buyer-email text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_EMAIL', [
                        'email' => $userInvoice->buyer_email,
                    ]); ?>
                </td>
            <?php endif; ?>
        </tr>

        <tr>
            <td class="seller-bank-name">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_NAME', [
                    'name' => $userInvoice->seller_bank_name,
                ]); ?>
            </td>
			
			<?php if (!is_null($userInvoice->buyer_vat_code) && !empty($userInvoice->buyer_vat_code)) : ?>
                <td class="buyer-phone text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_PHONE', [
                        'number' => $userInvoice->buyer_phone,
                    ]); ?>
                </td>
            <?php endif; ?>
        </tr>

        <tr>
            <td class="seller-bank-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_CODE', [
                    'code' => $userInvoice->seller_bank_code,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-swift">
                <?php echo Yii::t('document', 'INVOICE_SELLER_SWIFT', [
                    'code' => $userInvoice->seller_swift,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td class="seller-bank-account">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_ACCOUNT', [
                    'number' => $userInvoice->seller_bank_account,
                ]); ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>


<table>
    <thead>
    <tr class="table-heading">
        <th class="product-number-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_NUMBER_HEADING'); ?>
        </th>

        <th class="product-name-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_NAME_HEADING'); ?>
        </th>

        <th class="product-price-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_PRICE_HEADING'); ?>
        </th>

        <th class="product-discount-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_DISCOUNT_HEADING'); ?>
        </th>

        <th class="product-full-price-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_FULL_PRICE_HEADING'); ?>
        </th>
    </tr>
    </thead>

    <tbody>
    <tr class="product-info-row">
        <td class="product-number">
            1
        </td>

        <td class="product-name">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_NAME', [
                'name' => $userInvoice->product_name,
            ]); ?>
        </td>

        <td class="product-price">
            <?php echo $userInvoice->netto_price; ?>
        </td>

        <td class="product-discount">
            <?php echo $userInvoice->discount; ?>
        </td>

        <td class="product-full-price">
            <?php echo number_format((float)$priceWithDiscount, 2, '.', ''); ?>
        </td>
    </tr>
    </tbody>

    <tfoot>
    <tr>
        <td colspan="4" class="netto-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_NETTO_HEADING'); ?>
        </td>

        <td class="netto-price">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_NETTO_PRICE', [
                'price' => $userInvoice->netto_price,
            ]); ?>
        </td>
    </tr>

    <tr>
        <td colspan="4" class="vat-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_VAT_HEADING', [
                'vat' => round($userInvoice->vat, 2),
            ]); ?>
        </td>
        <td class="vat-price">
            <?php echo $vatPrice; ?>
        </td>
    </tr>

    <tr>
        <td colspan="4" class="brutto-heading">
            <?php echo Yii::t('document', 'INVOICE_PRODUCT_BRUTTO_HEADING'); ?>
        </td>
        <td class="brutto-price">
            <?php echo number_format((float)$bruttoPrice, 2, '.', ''); ?>
        </td>
    </tr>
    </tfoot>
</table>

<div class="price-in-words">
    <?php echo Yii::t('document', 'INVOICE_PRODUCT_AMOUNT_IN_WORDS', [
        'words' => ($priceInWords) ? $priceInWords : '',
    ]); ?>
</div>

<?php if ($userInvoice->type == UserInvoice::PRE_INVOICE): ?>
    <div class="payment-deadline">
        <?php echo Yii::t('document', 'INVOICE_PAYMENT_DEADLINE', [
            'days' => $userInvoice->days_to_pay,
            'date' => date('Y-m-d', strtotime(date('Y-m-d', $userInvoice->date) . '+ ' . $userInvoice->days_to_pay . 'days')),
        ]); ?>
    </div>
<?php endif; ?>

<div class="notice">
    <?php echo Yii::t('document', 'INVOICE_NOTICE'); ?>
</div>

<table>
    <tbody>
    <tr>
        <td class="invoiced-by">
            <?php echo Yii::t('document', 'INVOICE_INVOICED_BY'); ?>
        </td>
        <td class="receiver">
            <?php echo Yii::t('document', 'INVOICE_RECEIVER'); ?>
        </td>
    </tr>
    <tr>
        <td class="invoiced-by-who">
            <?php echo Yii::t('document', 'INVOICE_INVOICED_BY_WHO', [
                'position' => $userInvoice->invoiced_by_position,
                'fullname' => $userInvoice->invoiced_by_name_surname,
            ]); ?>
        </td>
        <td class="underline"></td>
    </tr>
    </tbody>
</table>
