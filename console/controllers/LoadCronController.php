<?php
namespace console\controllers;

use common\models\Load;
use console\traits\ConsoleMethods;
use yii\console\Controller;
use yii;

/**
 * This controller is responsible for setting load.days_adv load.submit_time_adv and load.car_pos_adv to NULL / zero values
 * after advertisement has been expired - so the user could advertise the load again and the load ordering would be properly functional
 *
 * Class LoadCronController
 * @package console\controllers
 */
class LoadCronController extends Controller
{
    use ConsoleMethods;

    /**
     *  validates loads every hour
     */
    public function actionHourly()
    {

        $this->validateLoads();
    }

    /**
     *  Updates all load entries that have advertisements expired and resets them
     */
    public function validateLoads()
    {
        Yii::info('LoadCronController is initiated');
        $start = new \DateTime();
        $errorMessage = null;

        try {
            // we add 3 hours because mysql timezone is UTC, e.g. 3 hrs behind?
            // proper fix to this would be to make sure timezones are all the same in every service
            $now = new \DateTime();
            $now->add(new \DateInterval("PT3H"));
            $nowString = $now->format('Y-m-d H:i:s');
            echo 'Current time ' . $nowString . PHP_EOL;

            $sql = 'UPDATE '. Load::tableName() .' SET days_adv=:days, car_pos_adv=:pos, submit_time_adv=:time WHERE submit_time_adv is NOT NULL AND days_adv != 0 AND DATE_FORMAT(DATE_ADD(submit_time_adv, INTERVAL days_adv DAY), \'%Y-%m-%d %H:%i:%s\') <= :now LIMIT 1000;';
            $result = Yii::$app->db->createCommand($sql, [':days' => 0, ':pos' => 0, ':time' => null, ':now' => $nowString,])->execute();

            $elapsed = new \DateTime();
            $elapsedTimeString = $this->elapsedTimeString($start, $elapsed);
            echo 'SQL command executed' . PHP_EOL . $sql . PHP_EOL;
            echo 'Result: ' . $result . PHP_EOL;
            echo "Completed in: " .  $elapsedTimeString. PHP_EOL;

            Yii::info("LoadCronController successfully validated ${result} record(s) in ${elapsedTimeString}");

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Yii::error($errorMessage);
            echo 'The following Error was produced trying to run this cron:' . PHP_EOL;
            echo $errorMessage . PHP_EOL;
        }

        echo PHP_EOL . 'Done!' . PHP_EOL;
    }

}
