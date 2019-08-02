<?php

namespace common\components;

use Yii;

/**
 * Class ControllerTrait
 *
 * @package common\components
 */
trait ControllerTrait
{
    /**
     * Checks whether menu item is active
     *
     * @param string $controllerId Controller ID
     * @param string $actionId Controller action ID
     * @return boolean
     */
    public static function isActiveMenuItem($controllerId = '', $actionId = '')
    {
        return Yii::$app->controller->id === $controllerId && Yii::$app->controller->action->id === $actionId;
    }
}