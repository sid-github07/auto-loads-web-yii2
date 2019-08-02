<?php

namespace common\components;

use common\models\Language;
use Yii;

class MailLanguage
{
    public static function setMailLanguage($languageIds) 
    {
        $languageToBySet = 'en';
        $languages = Language::find()->all();
        foreach($languageIds as $id) {
            if (Languages::validateLanguage(strtolower($languages[$id-1]->country_code))) {
                $languageToBySet = strtolower($languages[$id-1]->country_code);
                break;
            }
        }
        Yii::$app->language = $languageToBySet;
    }
}

