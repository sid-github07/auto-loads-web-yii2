<?php

namespace common\components\Payment;

use common\models\UserService;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class PaymentFactory
 *
 * @package common\components\Payment
 */
class PaymentFactory
{
    /**
     * Creates and returns specific payment object by payment method
     *
     * @param null|integer $method Payment method
     * @return PayPal|PaySera
     * @throws NotFoundHttpException If method is invalid
     */
    public static function create($method = null)
    {
        switch ($method) {
            case UserService::PAYSERA:
                return new PaySera();
            case UserService::PAYPAL:
                return new PayPal();
            default:
                throw new NotFoundHttpException(Yii::t('alert', 'PAYMENT_FACTORY_INVALID_METHOD'));
        }
    }
}