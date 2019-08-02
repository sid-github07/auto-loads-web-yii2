<?php

namespace common\components;

use yii\db\ActiveRecord;

/**
 * Class Model
 *
 * @package common\components
 */
class Model
{
    /**
     * Returns model class name
     *
     * @param object $model Model object
     * @return string
     */
    public static function getClassName($model)
    {
        $namespace = new \ReflectionClass($model);
        return $namespace->getShortName();
    }

    /**
     * Returns attributes with their values, that have changed
     *
     * @param ActiveRecord $model Target model
     * @return array
     */
    public static function getAttributeChanges($model)
    {
        return array_diff_assoc($model->getOldAttributes(), $model->getAttributes());
    }

    /**
     * Checks whether model attributes have changes
     *
     * @param ActiveRecord $model Target model
     * @return boolean
     */
    public static function hasChanges($model)
    {
        $changes = self::getAttributeChanges($model);
        return !empty($changes);
    }

    /**
     * Returns years range
     *
     * @param null|integer $from Year range begin year
     * @param null|integer $to Year range end year
     * @return array
     */
    public static function getYearsRange($from = null, $to = null)
    {
        $from = is_null($from) ? date('Y', 0) : $from; // Default: 1970
        $to = is_null($to) ? date('Y') : $to; // Default: current year
        $years = range($from, $to);
        return array_combine($years, $years);
    }
}