<?php

namespace common\components\invoice;

use common\models\Company;
use common\models\UserInvoice;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class InvoiceDirector
 *
 * @package common\components\invoice
 */
class InvoiceDirector
{
    /** @var null|Invoice Invoice object */
    private $invoice = null;

    /**
     * InvoiceDirector constructor
     *
     * @param integer $type Invoice type
     * @param null|integer $id User service ID
     * @param string $name Service name
     * @param double $price Service price
     */
    public function __construct($type, $id, $name, $price)
    {
        $invoice = InvoiceFactory::create($type, $id, $name, $price);
        $this->setInvoice($invoice);
    }

    /**
     * Sets invoice
     *
     * @param Invoice $invoice Invoice object
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Returns invoice object
     *
     * @return Invoice|null
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Sets invoice model attributes and generates new invoice document
     *
     * @param null|integer $userId User ID whom invoice is being generated
     * @param boolean $generateDocument Attribute, whether invoice document must be generated
     * @throws NotFoundHttpException If company or invoice document not found
     */
    public function makeInvoice($userId = null, $generateDocument = true)
    {
        $userId = is_null($userId) ? Yii::$app->user->id : $userId;
        $company = Company::findUserCompany($userId);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'INVOICE_CREATE_BY_ADMIN_COMPANY_NOT_FOUND'));
        }

        $this->getInvoice()->setAttributes($company, $userId);
        if ($generateDocument) {
            $this->getInvoice()->generateDocument();
            if (!file_exists($this->getInvoice()->getInvoicePath() . $this->getInvoice()->getFileFullName())) {
                throw new NotFoundHttpException(Yii::t('alert', 'INVOICE_DOCUMENT_NOT_FOUND'));
            }
        }
    }

    /**
     * Returns UserInvoice model
     *
     * @return UserInvoice|null
     */
    public function getUserInvoice()
    {
        return $this->getInvoice()->getUserInvoice();
    }
}