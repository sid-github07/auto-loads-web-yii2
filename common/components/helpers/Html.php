<?php

namespace common\components\helpers;

use common\models\City;

/**
 * Class Html
 * @package common\components\helpers
 */
class Html extends \yii\helpers\Html
{
    /**
     * @param string $text
     * @param int $count
     * @param bool $badgeBefore show before text or after?
     * @param bool $showEmpty show 0?
     * @param array $options [color, emptyColor, style]
     * @return string
     */
    public static function getBadge($text, $count, $badgeBefore = true, $showEmpty = false, $options = [])
    {
        if (!$count && !$showEmpty) {
            return $text;
        }
        foreach ([
                     'color' => 'red',
                     'emptyColor' => 'silver',
                     'style' => ''
                 ] as $k => $v) {
            if (!isset($options[$k])) {
                $options[$k] = $v;
            }
        }
        if (!$count) {
            $options['color'] = $options['emptyColor'];
        }
        $badge = Html::tag('span', $count, [
            "class" => "badge",
            "style" => "background-color: {$options['color']} !important; position: relative; {$options['style']}",
        ]);
        return trim((!$badgeBefore ? '' : $badge) . ' ' . $text . ' ' . ($badgeBefore ? '' : $badge));
    }

    /**
     * @param string $countryCode
     * @param string $cityName
     * @param bool $fullCountry
     * @return string
     */
    public static function getFlagIcon($countryCode, $cityName, $fullCountry = false)
    {
        $countryName = $countryCode;
        if ($fullCountry) {
            $countryName = \Yii::t('country', City::find()->where(['modification_date' => null, 'country_code' => $countryCode])->select('name')->scalar());
        }
        return sprintf('%s %s, %s',
            self::tag('i', '', ['class' => 'flag-icon flag-icon-' . strtolower($countryCode)]),
            $countryName,
            $cityName
        );
    }
}