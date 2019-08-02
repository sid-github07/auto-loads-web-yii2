<?php

namespace common\components\audit;

use common\models\UserLog;

/**
 * Class Log
 *
 * @package common\components\audit
 */
class Log
{
    /** @const string Logging user actions */
    const TYPE_USER = 'user';

    /**
     * Logs user action
     *
     * @param string $action User action type
     * @param string $placeholder User action message placeholder
     * @param array|string $actionData User action data
     * @param null|integer $userId User ID, who performs the action
     */
    public static function user($action, $placeholder, $actionData, $userId = null)
    {
        switch ($action) {
            case Create::ACTION:
                $user = new Create($placeholder, $actionData, $userId);
                break;
            case Read::ACTION:
                $user = new Read($placeholder, $actionData);
                break;
            case Update::ACTION:
                $user = new Update($placeholder, $actionData);
                break;
            case Delete::ACTION:
                $user = new Delete($placeholder, $actionData);
                break;
            case Search::ACTION:
                $user = new Search($placeholder, $actionData);
                break;
            case Map::ACTION:
                $user = new Map($placeholder, $actionData);
                break;
            case Login::ACTION:
                $user = new Login($placeholder);
                break;
            case SystemMessage::ACTION:
                $user = new SystemMessage($placeholder, $userId);
                break;
            case FailedAction::ACTION:
                $user = new FailedAction($placeholder, $actionData);
                break;
            case Pay::ACTION:
                $user = new Pay($placeholder, $actionData, $userId);
                break;
            default:
                return;
        }

        $user->action()->log();
    }

    /**
     * Finds corresponding logs depending on log type
     *
     * @param string $type Log type
     * @param null|integer $id Corresponding log type object ID
     * @param null|string $action Corresponding log type action
     * @return array|UserLog[]
     */
    public static function find($type, $id, $action = null)
    {
        switch ($type) {
            case self::TYPE_USER:
                return self::findUserLogs($id, $action);
            default:
                return [];
        }
    }

    /**
     * Finds and returns user logs
     *
     * @param null|integer $id User ID
     * @param string $action User action type
     * @return array|UserLog[]
     */
    private static function findUserLogs($id, $action)
    {
        return UserLog::find()->where(['user_id' => $id])->filterWhere(['action' => $action])->all();
    }
    
    /**
     * Logs user map opened action
     *
     * @param string $placeholder User action message placeholder
     * @param integer $type view type id
     */
    public static function map($placeholder, $type)
    {
        $user = new Map($placeholder, $type);
        $user->action()->log();
    }
}