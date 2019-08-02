<?php

namespace common\components\invoice;

use common\models\City;
use common\models\Company;
use common\models\Country;
use common\models\UserInvoice;
use kartik\mpdf\Pdf;
use Yii;

/**
 * Class Invoice
 *
 * @package common\components\invoice
 */
abstract class Invoice
{
    /** @var null|UserInvoice User invoice model */
    private $userInvoice = null;

    /**
     * Returns number
     *
     * @return string
     */
    abstract protected function getNumber();

    /**
     * Returns path
     *
     * @return string
     */
    abstract protected function getPath();

    /**
     * Returns file name
     *
     * @return string
     */
    abstract protected function getFileName();

    /**
     * Returns file extension
     *
     * @return string
     */
    abstract protected function getFileExtension();

    /**
     * Returns invoice type
     *
     * @return integer
     */
    abstract protected function getType();

    /**
     * Sets file title
     *
     * @param string $number Invoice number
     */
    abstract public function setTitle($number);

    /**
     * Returns file title
     *
     * @return string
     */
    abstract protected function getTitle();

    /**
     * Invoice constructor
     *
     * @param null|integer $id User service ID
     * @param string $name Service name
     * @param double $price Service price
     */
    public function __construct($id = null, $name = '', $price = 0.00)
    {
        $this->setUserInvoice();
        $this->setUserServiceId($id);
        $this->setProductName($name);
        $this->setNettoPrice($price);
    }

    /**
     * Sets user invoice model
     */
    public function setUserInvoice()
    {
        $this->userInvoice = new UserInvoice();
    }

    /**
     * Returns user invoice model
     *
     * @return UserInvoice|null
     */
    public function getUserInvoice()
    {
        return $this->userInvoice;
    }

    /**
     * Sets user service ID
     *
     * @param null|integer $id User service ID
     */
    public function setUserServiceId($id)
    {
        $this->getUserInvoice()->user_service_id = $id;
    }

    /**
     * Sets product name
     *
     * @param string $name Service name
     */
    public function setProductName($name)
    {
        $this->getUserInvoice()->product_name = $name;
    }

    /**
     * Sets netto price
     *
     * @param double $price Service price
     */
    public function setNettoPrice($price)
    {
        $this->getUserInvoice()->netto_price = $price;
    }

    /**
     * Sets user invoice attributes
     *
     * @param Company $company User company
     * @param null|integer $id User ID
     */
    public function setAttributes(Company $company, $id)
    {
        $this->setTitle($this->getUserInvoice()->number);
        $this->setSellerImprint();
        $this->setBuyerImprint($company);
        $this->getUserInvoice()->type = $this->getType();
        if (is_null($this->getUserInvoice()->userService->old_id)) {
            if ($this->getType() == UserInvoice::PRE_INVOICE) {
                $this->getUserInvoice()->number = $this->getNumber() . $this->getUserInvoice()->user_service_id;
            } else {
                $this->getUserInvoice()->number = $this->getNumber() . UserInvoice::getNextInvoiceNumber();
            }
        } else {
            $this->getUserInvoice()->number = $this->getNumber() . sprintf("%'.04d", $this->getUserInvoice()->userService->old_id);
        }
        $this->getUserInvoice()->date = time();
        $this->getUserInvoice()->discount = Yii::$app->params['serviceDiscount'];
        $this->getUserInvoice()->vat = Country::getUserVatRate($id);
        $this->getUserInvoice()->days_to_pay = Yii::$app->params['daysToPayPreInvoice'];
        $this->getUserInvoice()->invoiced_by_position = Yii::$app->params['invoicedByPosition'];
        $this->getUserInvoice()->invoiced_by_name_surname = Yii::$app->params['invoicedByNameSurname'];
        $this->getUserInvoice()->file_name = $this->getFileName() . $this->getUserInvoice()->user_service_id;
        $this->getUserInvoice()->file_extension = $this->getFileExtension();
    }

    /**
     * Sets seller imprint attributes
     */
    private function setSellerImprint()
    {
        $this->getUserInvoice()->seller_company_name = Yii::$app->params['sellerCompanyName'];
        $this->getUserInvoice()->seller_company_code = Yii::$app->params['sellerCompanyCode'];
        $this->getUserInvoice()->seller_vat_code = Yii::$app->params['sellerVatCode'];
        $this->getUserInvoice()->seller_address = Yii::$app->params['sellerAddress'];
        $this->getUserInvoice()->seller_bank_name = Yii::$app->params['sellerBankName'];
        $this->getUserInvoice()->seller_bank_code = Yii::$app->params['sellerBankCode'];
        $this->getUserInvoice()->seller_swift = Yii::$app->params['sellerSwift'];
        $this->getUserInvoice()->seller_bank_account = Yii::$app->params['sellerBankAccount'];
    }

    /**
     * Sets buyer imprint attributes
     *
     * @param Company $company User company
     */
    private function setBuyerImprint(Company $company)
    {
        $this->getUserInvoice()->buyer_id = $company->id;
        $this->getUserInvoice()->buyer_title = $company->getTitleByType();
        $this->getUserInvoice()->buyer_code = $company->getCodeByType();
        $this->getUserInvoice()->buyer_vat_code = $company->vat_code;
        $this->getUserInvoice()->buyer_address = $company->address;
        $this->getUserInvoice()->buyer_city_id = $company->city_id;
        $this->getUserInvoice()->buyer_phone = $company->phone;
        if (empty($company->email)) {
            $this->getUserInvoice()->buyer_email = trim($company->ownerList->email);
        } else {
            $this->getUserInvoice()->buyer_email = trim($company->email);
        }
    }

    /**
     * Generates invoice document
     */
    public function generateDocument()
    {
        $this->createDirectory();
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'content' => $this->getDocumentBody(),
            'cssFile' => '@frontend/web/dist/pre-invoice/pre-invoice.css',
            'filename' => $this->getPath() . $this->getFileFullName(),
            'destination' => Pdf::DEST_FILE,
            'options' => [
                'title' => $this->getTitle(),
            ],
        ]);
        $pdf->render();
    }

    /**
     * Creates invoice directory
     */
    private function createDirectory()
    {
        if (!is_dir($this->getPath())) {
            mkdir($this->getPath(), 0777, true);
        }
    }

    /**
     * Returns rendered invoice document body
     *
     * @return string
     */
    private function getDocumentBody()
    {
        $city = City::findById($this->getUserInvoice()->buyer_city_id);
        return Yii::$app->controller->renderPartial('___invoice', [
            'userServiceId' => $this->getUserInvoice()->user_service_id,
            'type' => $this->getUserInvoice()->type,
            'number' => $this->getUserInvoice()->number,
            'date' => date('Y-m-d', $this->getUserInvoice()->date),
            'sellerCompanyName' => $this->getUserInvoice()->seller_company_name,
            'sellerCompanyCode' => $this->getUserInvoice()->seller_company_code,
            'sellerVatCode' => $this->getUserInvoice()->seller_vat_code,
            'sellerAddress' => $this->getUserInvoice()->seller_address,
            'sellerBankName' => $this->getUserInvoice()->seller_bank_name,
            'sellerBankCode' => $this->getUserInvoice()->seller_bank_code,
            'sellerSwift' => $this->getUserInvoice()->seller_swift,
            'sellerBankAccount' => $this->getUserInvoice()->seller_bank_account,
            'buyerTitle' => $this->getUserInvoice()->buyer_title,
            'buyerCode' => $this->getUserInvoice()->buyer_code,
            'buyerVatCode' => $this->getUserInvoice()->buyer_vat_code,
            'buyerAddress' => $this->getUserInvoice()->buyer_address,
            'buyerCity' => $city->name,
            'buyerCountry' => Country::getNameByCode($city->country_code),
            'buyerPhone' => $this->getUserInvoice()->buyer_phone,
            'buyerEmail' => $this->getUserInvoice()->buyer_email,
            'productName' => Yii::t('app', $this->getUserInvoice()->userService->service->getTitle($this->getUserInvoice()->user_service_id), [
                'months' => $this->getUserInvoice()->userService->service->getMonthsByDays(),
            ]),
            'nettoPrice' => $this->getUserInvoice()->netto_price,
            'discount' => $this->getUserInvoice()->discount,
            'vat' => $this->getUserInvoice()->vat,
            'daysToPay' => $this->getUserInvoice()->days_to_pay,
            'invoicedByPosition' => $this->getUserInvoice()->invoiced_by_position,
            'invoicedByNameSurname' => $this->getUserInvoice()->invoiced_by_name_surname,
        ]);
    }

    /**
     * Returns invoice file full name
     *
     * @return string
     */
    public function getFileFullName()
    {
        return $this->getUserInvoice()->file_name . '.' . $this->getUserInvoice()->file_extension;
    }

    /**
     * Returns path to invoice directory
     *
     * @return string
     */
    public function getInvoicePath()
    {
        return $this->getPath();
    }
}
