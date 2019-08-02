<?php

namespace common\components\audit;

use yii\helpers\Json;

/**
 * Class SystemMessage
 *
 * @package common\components\audit
 */
class SystemMessage extends ActionAbstractFactory implements ActionInterface
{

    /** @const string Action name */
    const ACTION = 'SYSTEM_MESSAGE';

    /** @const string Log message placeholder when user requests administrator to change VAT code */
    const PLACEHOLDER_USER_REQUESTED_VAT_CODE_CHANGE = 'USER_REQUESTED_VAT_CODE_CHANGE';

    /** @const string Log message placeholder when user receives email for announced load */
    const PLACEHOLDER_USER_RECEIVED_EMAIL_FOR_ANNOUNCING_LOAD = 'USER_RECEIVED_EMAIL_FOR_ANNOUNCING_LOAD';

    /** @const string Log message placeholder when user receives subscription reminder */
    const PLACEHOLDER_USER_RECEIVED_SUBSCRIPTION_REMINDER_EMAIL = 'USER_RECEIVED_SUBSCRIPTION_REMINDER_EMAIL';

    /** @const string Log message placeholder when user receives request for carrier documents */
    const PLACEHOLDER_USER_RECEIVED_CARRIER_DOCUMENTS_REQUEST = 'USER_RECEIVED_CARRIER_DOCUMENTS_REQUEST';

    /** @const string Log message placeholder when user requests administrator to change email */
    const PLACEHOLDER_USER_REQUESTED_EMAIL_CHANGE = 'USER_REQUESTED_EMAIL_CHANGE';

    /** @const string Log message placeholder when user receives email after successfully sign up by admin */
    const PLACEHOLDER_USER_RECEIVED_SIGN_UP_EMAIL = 'USER_RECEIVED_SING_UP_EMAIL';

    /** @const string Log message placeholder when user requests for password reset */
    const PLACEHOLDER_USER_REQUESTED_PASSWORD_RESET = 'USER_REQUESTED_PASSWORD_RESET';

    /** @const string Log message placeholder when user receives sign up confirmation email */
    const PLACEHOLDER_USER_RECEIVED_SIGN_UP_CONFIRMATION = 'USER_RECEIVED_SIGN_UP_CONFIRMATION';

    /** @const string Log message placeholder when user receives successful payment email */
    const PLACEHOLDER_USER_RECEIVED_SUCCESSFUL_PAYMENT_EMAIL = 'USER_RECEIVED_SUCCESSFUL_PAYMENT_EMAIL';

    /** @const string Log message placeholder when user receives expired subscription email */
    const PLACEHOLDER_USER_RECEIVED_EXPIRED_SUBSCRIPTION_EMAIL = 'USER_RECEIVED_EXPIRED_SUBSCRIPTION_EMAIL';

    /**
     * SystemMessage constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param null|integer $userId User ID, who performs the action
     */
    public function __construct($placeholder, $userId)
    {
        parent::__construct(self::ACTION, $placeholder, [], $userId);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_REQUESTED_VAT_CODE_CHANGE:
            case self::PLACEHOLDER_USER_RECEIVED_EMAIL_FOR_ANNOUNCING_LOAD:
            case self::PLACEHOLDER_USER_RECEIVED_SUBSCRIPTION_REMINDER_EMAIL:
            case self::PLACEHOLDER_USER_RECEIVED_CARRIER_DOCUMENTS_REQUEST:
            case self::PLACEHOLDER_USER_REQUESTED_EMAIL_CHANGE:
            case self::PLACEHOLDER_USER_RECEIVED_SIGN_UP_EMAIL:
            case self::PLACEHOLDER_USER_REQUESTED_PASSWORD_RESET:
            case self::PLACEHOLDER_USER_RECEIVED_SIGN_UP_CONFIRMATION:
            case self::PLACEHOLDER_USER_RECEIVED_SUCCESSFUL_PAYMENT_EMAIL:
            case self::PLACEHOLDER_USER_RECEIVED_EXPIRED_SUBSCRIPTION_EMAIL:
                parent::setData($this->getSystemMessageData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user sends or gets system message
     *
     * @return string
     */
    private function getSystemMessageData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        return Json::encode($data);
    }
}