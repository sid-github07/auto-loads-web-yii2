<?php

namespace common\components\audit;
use yii\helpers\Json;

/**
 * Class Login
 *
 * @package common\components\audit
 */
class Login extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'LOGIN';

    /** @const string Log message placeholder when user logs-in to the system */
    const PLACEHOLDER_USER_LOGGED_IN = 'USER_LOGGED_IN';

    /**
     * Login constructor.
     *
     * @param string $placeholder User action message placeholder
     */
    public function __construct($placeholder)
    {
        parent::__construct(self::ACTION, $placeholder, [], null);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_LOGGED_IN:
                parent::setData($this->getLoginData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user logs-in to the system
     *
     * @return string
     */
    private function getLoginData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        return Json::encode($data);
    }
}