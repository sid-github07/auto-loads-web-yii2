<?php

namespace common\components\Payment;

use common\models\Service;
use common\models\UserService;
use Rbs\Payment\Factory;
use Rbs\Payment\Gateway\Paypal as PaypalAPI;
use Rbs\Payment\Http\Client;
use Yii;
use yii\helpers\Url;

require Yii::$app->params['paypalParser'];
require Yii::$app->params['paypalLoaderInterface'];
require Yii::$app->params['paypalFilesystem'];
require Yii::$app->params['paypalFields'];
require Yii::$app->params['paypalAbstractGateway'];
require Yii::$app->params['paypalPaypal'];
require Yii::$app->params['paypalClient'];
require Yii::$app->params['paypalFactory'];

/**
 * Class PayPal
 *
 * @package common\components\Payment
 */
class PayPal extends Payment  implements PaymentI
{
    /** @var null|string Payment response */
    protected $response = null;

    /** @var null|integer User service ID */
    protected $id = null;

    /** @var null|double Service price */
    protected $price = null;

    /** @var integer Payment method */
    protected $method = UserService::PAYPAL;

    /**
     * Sets payment response
     */
    private function setResponse()
    {
        $this->response = Yii::$app->request->post();
    }

    /**
     * @inheritdoc
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets user service ID
     *
     * @param null|integer $id User service ID
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    protected function getId()
    {
        return $this->id;
    }

    /**
     * Sets service price
     *
     * @param null|double $price
     */
    private function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @inheritdoc
     */
    protected function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultAttributes($id = null, $price = null)
    {
        $this->setResponse();
        if (is_null($id) && is_null($price)) {
            $response = $this->getResponse();
            $id = isset($response['item_number']) ? $response['item_number'] : null;
            $price = isset($response['mc_gross']) ? $response['mc_gross'] : null;
        }
        $this->setId($id);
        $this->setPrice($price);
    }

    /**
     * @inheritdoc
     */
    public function pay()
    {
        $paypal = new PaypalAPI();
        $paypal->setCurrency(Yii::$app->params['defaultCurrency']);
        $paypal->setAccountIdentifier(Yii::$app->params['paypalIdentifier']);
        $paypal->setReturnOnSuccessUrl(
            Url::toRoute([
                'subscription/service-payment-accept',
                'lang' => Yii::$app->language,
                'order' => $this->getId(),
            ], true)
        );
        $paypal->setReturnOnFailureUrl(
            Url::toRoute([
                'subscription/service-payment-cancel',
                'lang' => Yii::$app->language,
            ], true)
        );
        $paypal->setNotificationUrl(
            Url::toRoute([
                'subscription/service-payment-callback',
                'lang' => Yii::$app->language,
                'method' => $this->method,
                'order' => $this->getId(),
            ], true)
        );
        $paypal->setSingleItem(Service::getNameByUserServiceId($this->getId()), $this->getPrice(), $this->getId());
        $paypal->setOrderNumber($this->getId());
        $paypal->setTestMode(Yii::$app->params['paypalTestMode']);
        $paypal->proceed();
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $paypal = Factory::factory('Paypal');
        $paypal->setTestMode(Yii::$app->params['paypalTestMode']);
        $paypal->populate(Yii::$app->request->post());
        $paypal->setHttpClient(new Client());
        return $paypal->verify();
    }
}
