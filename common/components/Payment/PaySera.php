<?php

namespace common\components\Payment;
use common\models\User;
use common\models\UserService;
use WebToPay;
use Yii;
use yii\helpers\Url;

require Yii::$app->params['webToPatPath'];

/**
 * Class PaySera
 *
 * @package common\components\Payment
 */
class PaySera extends Payment  implements PaymentI
{
    /** @var null|array Payment response */
    protected $response = null;

    /** @var null|integer User service ID */
    protected $id = null;

    /** @var null|double Service price */
    protected $price = null;
  
    /** @var null|User User model */
    protected $user = null;

    /** @var integer Payment method */
    protected $method = UserService::PAYSERA;

    /**
     * Sets payment response
     *
     * @param null|array $response Payment response
     */
    private function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Returns payment response
     *
     * @return array|null
     */
    public function getResponse()
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
     * Returns user service ID
     *
     * @return integer|null
     */
    
    protected function getId()
    {
        return $this->id;
    }

    /**
     * Sets service price
     *
     * @param null|double $price Service price
     */
    private function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Returns service price
     *
     * @return double|null
     */
    protected function getPrice()
    {
        return $this->price;
    }
    
    /**
     * Sets user model
     *
     * @param null|User $user User model
     */
    private function setUser($user)
    {
        if (is_null($user)) {
            $user = !Yii::$app->user->isGuest ? User::findById(Yii::$app->user->id) : null;
        }
        $this->user = $user;
    }

    /**
     * Returns user model
     *
     * @return User|null
     */
    private function getUser()
    {
        return $this->user;
    }

    /**
     * Returns payment method
     *
     * @return integer
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultAttributes($id = null, $price = null, $user = null)
    {
        if (is_null($id) && is_null($price)) {
            $id = $this->getResponse()['orderid'];
            $price = UserService::convertToEuros($this->getResponse()['amount']);
        }
        $this->setId($id);
        $this->setPrice($price);
        $this->setUser($user);
    }

    /**
     * @inheritdoc
     */
    public function pay()
    {
        WebToPay::redirectToPayment([
            'projectid' => Yii::$app->params['paySeraProjectId'],
            'orderid' => $this->getId(),
            'accepturl' => Url::to([
                'subscription/service-payment-accept',
                'lang' => Yii::$app->language,
                'order' => $this->getId(),
            ], true),
            'cancelurl' => Url::to([
                'subscription/service-payment-cancel',
                'lang' => Yii::$app->language,
            ], true),
            'callbackurl' => Url::to([
                'subscription/service-payment-callback',
                'lang' => Yii::$app->language,
                'method' => $this->getMethod(),
                'order' => $this->getId(),
            ], true),
            'sign_password' => Yii::$app->params['paySeraSignPassword'],
            'amount' => UserService::convertToCents($this->getPrice()),
            'currency' => Yii::$app->params['defaultCurrency'],
            'p_firstname' => $this->getUser()->name,
            'p_lastname' => $this->getUser()->surname,
            'p_email' => $this->getUser()->email,
            'test' => Yii::$app->params['paySeraTestPayment'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $response = WebToPay::checkResponse(Yii::$app->request->get(), [
            'projectid' => Yii::$app->params['paySeraProjectId'],
            'sign_password' => Yii::$app->params['paySeraSignPassword'],
        ]);

        if (empty($response)) {
            return false;
        }
        $this->setResponse($response);
        return true;
    }
}