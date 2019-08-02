<?php

namespace common\components\audit;

use yii\helpers\Json;

/**
 * Class FailedAction
 *
 * @package common\components\audit
 */
class FailedAction extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'FAILED_ACTION';

    /** @const string Log message placeholder when user gets error message */
    const PLACEHOLDER_USER_GOT_ERROR_MESSAGE = 'USER_GOT_ERROR_MESSAGE';

    /**
     * FailedAction constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param string $message System error message, that received user
     */
    public function __construct($placeholder, $message)
    {
        parent::__construct(self::ACTION, $placeholder, $message, null);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_GOT_ERROR_MESSAGE:
                parent::setData($this->getFailedActionData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user receives failed action message
     *
     * @return string
     */
    private function getFailedActionData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [
                'message' => $this->getMessage(),
            ],
        ];

        return Json::encode($data);
    }
}