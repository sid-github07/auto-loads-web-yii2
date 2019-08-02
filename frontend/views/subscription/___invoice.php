<?php

use common\components\NumberToText;
use common\models\UserInvoice;

/** @var string $userServiceId */
/** @var integer $type */
/** @var string $number */
/** @var string $date */
/** @var string $sellerCompanyName */
/** @var string $sellerCompanyCode */
/** @var string $sellerVatCode */
/** @var string $sellerAddress */
/** @var string $sellerBankName */
/** @var string $sellerBankCode */
/** @var string $sellerSwift */
/** @var string $sellerBankAccount */
/** @var string $buyerTitle */
/** @var string $buyerCode */
/** @var string $buyerVatCode */
/** @var string $buyerAddress */
/** @var string $buyerCity */
/** @var string $buyerCountry */
/** @var string $buyerPhone */
/** @var string $buyerEmail */
/** @var string $productName */
/** @var string $nettoPrice */
/** @var string $discount */
/** @var string $vat */
/** @var string $daysToPay */
/** @var string $invoicedByPosition */
/** @var string $invoicedByNameSurname */
/** @var float $priceWithDiscount */
/** @var float $vatPrice */
/** @var float $bruttoPrice */
/** @var false|string $priceInWords */

$priceWithDiscount = (float)$nettoPrice + (float)$discount;
$vatPrice = number_format(($priceWithDiscount * $vat) / 100, 2, '.', '');
$bruttoPrice = number_format((float)$priceWithDiscount + (float)$vatPrice, 2, '.', '');
$priceInWords = NumberToText::getPrice($bruttoPrice);
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
    <?php echo Yii::t('document', ($type == UserInvoice::PRE_INVOICE ? 'PRE_INVOICE_HEADING' : 'INVOICE_HEADING'), [
        'number' => $number,
    ]); ?>
</h3>

<?php if ($type != UserInvoice::PRE_INVOICE): ?>
    <div class="invoice-date"> <!-- TODO: pakeisti klasės pavadinimą -->
        <?php echo Yii::t('document', 'INVOICE_ORDER_NUMBER', [
            'number' => $userServiceId,
        ]); ?>
    </div>
<?php endif; ?>

<div class="invoice-date">
    <?php echo $date; ?>
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
                <?php echo $sellerCompanyName; ?>
            </td>
            
            <td class="buyer-title text-right">
                <?php echo $buyerTitle; ?>
            </td>
        </tr>
        
        <?php if (!is_null($buyerCode) && !is_null($buyerVatCode)): ?>
        
        <tr>
            <td class="seller-company-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_COMPANY_CODE', [
                    'code' => $sellerCompanyCode,
                ]); ?>
            </td>
            
            <td class="buyer-code text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_CODE', [
                    'code' => $buyerCode,
                ]); ?>
            </td>
        </tr>
        
        <tr>
            <td class="seller-vat-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_VAT_CODE', [
                    'code' => $sellerVatCode,
                ]); ?>
            </td>
            
            <td class="buyer-vat-code text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_VAT_CODE', [
                    'code' => $buyerVatCode,
                ]); ?>
            </td>
        </tr>
                    
        <tr>
            <td class="seller-address">
                <?php echo Yii::t('document', 'INVOICE_SELLER_ADDRESS', [
                    'address' => $sellerAddress,
                ]); ?>
            </td>
            
            <td class="buyer-address text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_ADDRESS', [
                    'address' => $buyerAddress,
                    'city' => $buyerCity,
                    'country' => $buyerCountry,
                ]); ?>
            </td>
        </tr>
                
        <tr>
            <td class="seller-bank-name">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_NAME', [
                    'name' => $sellerBankName,
                ]); ?>
            </td>
            
            <td class="buyer-phone text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_PHONE', [
                    'number' => $buyerPhone,
                ]); ?>
            </td>            
        </tr>
        
        <tr>
            <td class="seller-bank-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_CODE', [
                    'code' => $sellerBankCode,
                ]); ?>
            </td>
            
            <?php if (!empty($buyerEmail)): ?>
                <td class="buyer-email text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_EMAIL', [
                        'email' => $buyerEmail,
                    ]); ?>
                </td>
            <?php endif; ?>          
        </tr>        

        <tr>
            <td class="seller-swift">
                <?php echo Yii::t('document', 'INVOICE_SELLER_SWIFT', [
                    'code' => $sellerSwift,
                ]); ?>
            </td>
        </tr>        

        <tr>
            <td class="seller-bank-account">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_ACCOUNT', [
                    'number' => $sellerBankAccount,
                ]); ?>
            </td>
        </tr>
        
        <?php else: ?>
        
        <tr>
            <td class="seller-company-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_COMPANY_CODE', [
                    'code' => $sellerCompanyCode,
                ]); ?>
            </td>            
            
            <td class="buyer-address text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_ADDRESS', [
                    'address' => $buyerAddress,
                    'city' => $buyerCity,
                    'country' => $buyerCountry,
                ]); ?>
            </td>
        </tr>
        
        <tr>
            <td class="seller-vat-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_VAT_CODE', [
                    'code' => $sellerVatCode,
                ]); ?>
            </td>
            
            <td class="buyer-phone text-right">
                <?php echo Yii::t('document', 'INVOICE_BUYER_PHONE', [
                    'number' => $buyerPhone,
                ]); ?>
            </td> 
        </tr>
                    
        <tr>
            <td class="seller-address">
                <?php echo Yii::t('document', 'INVOICE_SELLER_ADDRESS', [
                    'address' => $sellerAddress,
                ]); ?>
            </td>
            
            <?php if (!empty($buyerEmail)): ?>
                <td class="buyer-email text-right">
                    <?php echo Yii::t('document', 'INVOICE_BUYER_EMAIL', [
                        'email' => $buyerEmail,
                    ]); ?>
                </td>
            <?php endif; ?>
        </tr>
                
        <tr>
            <td class="seller-bank-name">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_NAME', [
                    'name' => $sellerBankName,
                ]); ?>
            </td>           
        </tr>
        
        <tr>
            <td class="seller-bank-code">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_CODE', [
                    'code' => $sellerBankCode,
                ]); ?>
            </td>            
        </tr>        

        <tr>
            <td class="seller-swift">
                <?php echo Yii::t('document', 'INVOICE_SELLER_SWIFT', [
                    'code' => $sellerSwift,
                ]); ?>
            </td>
        </tr>        

        <tr>
            <td class="seller-bank-account">
                <?php echo Yii::t('document', 'INVOICE_SELLER_BANK_ACCOUNT', [
                    'number' => $sellerBankAccount,
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
                <?php echo $productName; ?>
            </td>
            
            <td class="product-price">
                <?php echo $nettoPrice; ?>
            </td>
            
            <td class="product-discount">
                <?php echo $discount; ?>
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
                    'price' => $nettoPrice,
                ]); ?>
            </td>
        </tr>

        <tr>
            <td colspan="4" class="vat-heading">
                <?php echo Yii::t('document', 'INVOICE_PRODUCT_VAT_HEADING', [
                    'vat' => round($vat, 2),
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

<div class="payment-deadline">
    <?php echo Yii::t('document', 'INVOICE_PAYMENT_DEADLINE', [
        'days' => $daysToPay,
        'date' => date('Y-m-d', strtotime($date . '+ ' . $daysToPay . 'days')),
    ]); ?>
    
</div>

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
                    'position' => $invoicedByPosition,
                    'fullname' => $invoicedByNameSurname,
                ]); ?>
            </td>
            <td class="underline"></td>
        </tr>
    </tbody>
</table>
