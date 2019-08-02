<?php

namespace common\components\Payment;

use common\components\audit\Create;
use common\components\audit\Log;
use common\models\Service;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;
/**
 * Class Payment
 *
 * @package common\components\Payment
 */
abstract class Payment
{
    /** @var null|UserService User service model */
    private $userService = null;

    /** @var null|Service Service model */
    private $service = null;

    /** @var null|UserServiceActive User service active model */
    private $activeService = null;

    /**
     * Returns payment response
     *
     * @return null|string
     */
    abstract protected function getResponse();

    /**
     * Returns user service ID
     *
     * @return null|integer
     */
    abstract protected function getId();

    /**
     * Returns service price
     *
     * @return null|double
     */
    abstract protected function getPrice();

    /**
     * Returns payment method
     *
     * @return integer
     */
    abstract protected function getMethod();

    /**
     * Marks user service as paid
     *
     * @return boolean Whether marked successfully
     * @throws \Exception $e
     */
    public function markAsPaid()
    {
        $userService = UserService::findById($this->getId());
        Yii::info('Payment.php::markAsPaid() is called', 'debug');

        if ($userService->getBruttoPrice() != $this->getPrice()) {
            Yii::info('Payment.php::markAsPaid() getBruttoPrice() != getPrice() returned true', 'debug');
            return false;
        }
        
        $this->setActiveServiceIfExists($userService->user_id);

        if ($userService->markAsPaid(json_encode($this->getResponse()), $this->getMethod(), $this->activeService)) {
            Yii::info('Payment.php::markAsPaid() $userService->markAsPaid returned true ', 'debug');
            $this->userService = $userService;
            return true;
        }
        Yii::info('Payment.php::markAsPaid() $userService->markAsPaid returned false', 'debug');

        return false;
    }

    /**
     * Activates user service
     *
     * @return boolean|UserServiceActive|null
     */
    public function activateUserService()
    {
        Yii::info('Payment.php::activateUserService()', 'debug');

        $this->service = Service::findByUserServiceId($this->userService->id);
        if ($this->service->service_type_id === ServiceType::SERVICE_TYPE_SERVICE_CREDITS) {
            Yii::info('Payment.php::activateUserService() service credits - returning true', 'debug');
            return true;
        }
        if (is_null($this->service)) {
            return false;
        }
        if(!is_null($this->activeService)) {
            return true;
        }
        
        $this->activeService = UserServiceActive::create($this->userService, $this->service, $this->userService->user_id);
        Log::user(Create::ACTION, Create::PLACEHOLDER_USER_BOUGHT_SUBSCRIPTION, [$this->service], $this->userService->user_id);
        return $this->activeService;
    }

    /**
     * Updates user current credits
     *
     * @return boolean Whether current credits updated successfully
     * @throws \yii\web\NotFoundHttpException
     */
    public function updateCurrentCredits()
    {
        if (is_null($this->activeService) && $this->service->service_type_id !== ServiceType::SERVICE_CREDITS_TYPE_ID) {
            return false;
        }

        Yii::info($this->service, 'debug');
        $user = User::findById($this->userService->user_id);
        if (is_null($this->service) === false && $this->service->service_type_id === ServiceType::SERVICE_CREDITS_TYPE_ID) {
            return $user->updateServiceCredits($this->service->credits);
        }
        return $user->updateCurrentCredits($this->activeService->credits);
    }

    /**
     * Updates user adv credits
     * @return boolean Whether current credits updated successfully
     * @throws \yii\web\NotFoundHttpException
     */
    public function updateAdvertisementCredits()
    {
        if (is_null($this->activeService)) {
            return false;
        }


        $user = User::findById($this->userService->user_id);
        return $user->updateServiceCredits($this->activeService->credits);
    }

    /**
     * Generates user service invoice
     *
     * @return boolean Whether invoice generated successfully
     */
    public function generateInvoice()
    {
        return UserInvoice::create($this->getId(), $this->service, UserInvoice::INVOICE, $this->userService->user_id);
    }

    /**
     * Sends email to user informing that payment was successful
     */
    public function sendSuccessfulPayment()
    {
        $this->userService->user->sendSuccessfulPayment();
    }

    /**
     * Sets user service end date
     *
     * @return boolean
     */
    public function setEndDate()
    {
        $this->userService->end_date = $this->activeService->end_date;
        $this->userService->scenario = UserService::SCENARIO_SYSTEM_SETS_END_DATE;
        return $this->userService->save();
    }
    
    /**
     * Checks and sets if where are any active services by given user id
     * 
     * @param integer $userId user id
     */
    public function setActiveServiceIfExists($userId)
    {
        $this->activeService = UserServiceActive::findActivatedSubscriptions($userId);
    }
}