<?php

namespace common\components\invoice;

use common\models\UserInvoice;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class InvoiceFactory
 *
 * @package common\components\invoice
 */
class InvoiceFactory
{
    /**
     * Creates new Invoice model depending on invoice type
     *
     * @param integer $type Invoice type
     * @param null|integer $id User service ID
     * @param string $name Service name
     * @param double $price Service price
     * @return PreInvoice|PvmInvoice
     * @throws NotFoundHttpException If invoice type is invalid
     */
    public static function create($type = null, $id = null, $name = '', $price = 0.00)
    {
        switch ($type) {
            case UserInvoice::PRE_INVOICE:
                return new PreInvoice($id, $name, $price);
                break;
            case UserInvoice::INVOICE;
                return new PvmInvoice($id, $name, $price);
                break;
            default:
                throw new NotFoundHttpException(Yii::t('alert', 'INVOICE_FACTORY_INVALID_TYPE'));
        }
    }
}