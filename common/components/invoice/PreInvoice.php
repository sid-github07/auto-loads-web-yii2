<?php

namespace common\components\invoice;

use common\models\UserInvoice;
use Yii;

/**
 * Class PreInvoice
 *
 * @package common\components\invoice
 */
class PreInvoice extends Invoice implements InvoiceI
{
    /** @var string Pre-invoice unique number */
    private $number = '';

    /** @var string Path to pre-invoice file folder */
    private $path = '';

    /** @var string Pre-invoice file name */
    private $fileName = '';

    /** @var string Pre-invoice file extension */
    private $fileExtension = '';

    /** @var null|integer Invoice type */
    private $type = null;

    /** @var string Pre-invoice file title */
    private $title = '';

    /**
     * PreInvoice constructor
     *
     * @param null|integer $id User service ID
     * @param string $name Service name
     * @param double $price Service price
     */
    public function __construct($id = null, $name = '', $price = 0.00)
    {
        $this->setNumber();
        $this->setPath();
        $this->setFileName();
        $this->setFileExtension();
        $this->setType();
        parent::__construct($id, $name, $price);
    }

    /**
     * @inheritdoc
     */
    public function setNumber()
    {
        $this->number = Yii::$app->params['preInvoiceNumber'];
    }

    /**
     * @inheritdoc
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @inheritdoc
     */
    public function setPath()
    {
        $this->path = Yii::$app->params['preInvoicePath'];
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function setFileName()
    {
        $this->fileName = Yii::$app->params['preInvoiceFileName'];
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @inheritdoc
     */
    public function setFileExtension()
    {
        $this->fileExtension = Yii::$app->params['preInvoiceFileExtension'];
    }

    /**
     * @inheritdoc
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @inheritdoc
     */
    public function setType()
    {
        $this->type = UserInvoice::PRE_INVOICE;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($number)
    {
        $this->title = Yii::t('document', 'PRE_INVOICE_HEADING', [
            'number' => $number,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }
}