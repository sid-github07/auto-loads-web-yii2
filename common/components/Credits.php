<?php

namespace common\components;

use common\models\User;
use Yii;

/**
 * Class Credits
 *
 * This component is responsible for actions with user credits
 *
 * @package common\components
 */
class Credits
{
    /**
     * Subtracts given number of credits from user
     *
     * @param integer $credits Number of credits to subtract from user
     * @return boolean Whether credits were successfully subtracted from user
     */
    public static function spend($credits = 0)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        $user->useCredits($credits);
        $user->scenario = User::SCENARIO_UPDATE_CURRENT_CREDITS;
        return $user->save();
    }
}