<?php

namespace common\components;

use Yii;
use yii\base\Component;

/**
 * Class DateTime
 *
 * @package common\components
 */
class DateTime extends Component
{
    /**
     * Converts given timestamp to text or simple date format, depending on difference between timestamp and today
     *
     * @see http://stackoverflow.com/a/4186922/5747867
     * @param integer $timestamp Date in timestamp format, that needs to be converted
     * @param boolean $showTime Attribute, whether time must be shown
     * @return string
     */
    public function convertToText($timestamp, $showTime = true)
    {
        $dateTime = new \DateTime(); // NOTE: will snap to UTC because of the "@timezone" syntax
        $dateTime->setTimestamp($timestamp);
        $date = $dateTime->format('Y-m-d');
        $time = ($showTime ? $dateTime->format('H:i') : '');

        switch ($this->calculateDatesDifference(time(), $timestamp)) {
            case 0: // Today
                return Yii::t('element', 'C-T-114a') . " $time";
            case 1: // Yesterday
                return Yii::t('element', 'C-T-114b') . " $time";
            default: // Any other day
                return "$date $time";
        }
    }

    /**
     * Calculates days difference between two dates
     *
     * @param integer $firstTimestamp First date in timestamp format
     * @param integer $secondTimestamp Second date in timestamp format
     * @return integer Difference in days
     */
    private function calculateDatesDifference($firstTimestamp, $secondTimestamp)
    {
        $firstDate = date_create(date('Y-m-d', $firstTimestamp));
        $secondDate = date_create(date('Y-m-d', $secondTimestamp));
        $difference = date_diff($firstDate,$secondDate);

        return $difference->days;
    }

    /**
     * Converts given date which is in string format to timestamp
     *
     * @param string $date Date in string format that needs to be converted
     * @return false|integer
     */
    public function convertToTimestamp($date)
    {
        return !empty($date) && is_string($date) ? strtotime($date) : 0;
    }
}
