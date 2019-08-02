<?php

namespace common\components\audit;

use common\components\Model;
use common\models\UserLog;
use Yii;

/**
 * Class ActionAbstractFactory
 *
 * @package common\components\audit
 */
class ActionAbstractFactory
{
    /** @var string User action type */
    protected $action;

    /** @var string User action message placeholder */
    protected $placeholder;

    /** @var array User action models */
    protected $models;

    /** @var string User action data */
    protected $data;

    /** @var null|integer User ID, who performs the action */
    protected $userId;

    /** @var null|string System error message that received user */
    protected $message;

    /**
     * ActionAbstractFactory constructor.
     *
     * @param string $action User action type
     * @param string $placeholder User action message placeholder
     * @param array|string $actionData User action data
     * @param null|integer $userId User ID, who performs the action
     */
    protected function __construct($action, $placeholder, $actionData, $userId)
    {
        $this->setAction($action);
        $this->setPlaceholder($placeholder);
        if (is_string($actionData)) {
            $this->setMessage($actionData);
        } else {
            $this->setModels($actionData);
        }
        $this->setUserId($userId);
    }

    /**
     * Sets user action type
     *
     * @param string $action User action type
     */
    private function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Returns user action type
     *
     * @return string
     */
    protected function getAction()
    {
        return $this->action;
    }

    /**
     * Sets user action message placeholder
     *
     * @param string $placeholder User action message placeholder
     */
    private function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Returns user action message placeholder
     *
     * @return string
     */
    protected function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Sets models, that were used in user action
     *
     * @param array $models User action models
     */
    private function setModels($models)
    {
        foreach ($models as $model) {
            $modelName = Model::getClassName($model);
            if (isset($this->models[$modelName])) {
                $this->models[$modelName . $model->id] = $model; // Sometimes model name repeats
            } else {
                $this->models[$modelName] = $model;
            }
        }
    }

    /**
     * Returns models that were used in user action
     *
     * @return array
     */
    protected function getModels()
    {
        return $this->models;
    }

    /**
     * Sets user ID, who performs the action
     *
     * @param null|integer $userId User ID
     */
    private function setUserId($userId)
    {
        $this->userId = Yii::$app->user->isGuest ? $userId : Yii::$app->user->id;
    }

    /**
     * Returns user ID, that performed the action
     *
     * @return integer|null
     */
    protected function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets data, that was used in user action
     *
     * @param string $data User action data
     */
    protected function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Returns data, that was used in user action
     *
     * @return string
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * Sets system error message, that received user
     *
     * @param null|string $message System error message
     */
    protected function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns system error message, that received user
     *
     * @return null|string
     */
    protected function getMessage()
    {
        return $this->message;
    }

    /**
     * Logs user action
     */
    public final function log()
    {
        $log = new UserLog([
            'scenario' => UserLog::SCENARIO_SYSTEM_SAVES_USER_LOG,
            'user_id' => $this->getUserId(),
            'action' => $this->getAction(),
            'data' => $this->getData(),
        ]);

        $log->save();
    }
}