<?php

namespace common\components;
use kartik\icons\Icon;
use Yii;
use yii\base\Behavior;

/**
 * Class Languages
 *
 * @package common\components
 */
class Languages extends Behavior
{
    /** const string English language name */
    const ENGLISH_NAME = 'English';

    /** const string English language name in short version */
    const SHORT_ENGLISH_NAME = 'en';

    /** const string Lithuania language name */
    const LITHUANIA_NAME = 'Lietuvių';

    /** const string Lithuania language name in short version */
    const SHORT_LITHUANIA_NAME = 'lt';

    /** const string Polish language name */
    const POLISH_NAME = 'Polski';

    /** const string Polish language name in short version */
    const SHORT_POLISH_NAME = 'pl';

    /** const string Russian language name */
    const RUSSIAN_NAME = 'Русский';

    /** const string Russian language name in short version */
    const SHORT_RUSSIAN_NAME = 'ru';

    /** const string German language name */
    const GERMAN_NAME = 'Deutsch';

    /** const string German language name in short version */
    const SHORT_GERMAN_NAME = 'de';

    /** const string Spanish language name */
    const SPANISH_NAME = 'Español';

    /** const string Spanish language name in short version */
    const SHORT_SPANISH_NAME = 'es';

    const ROMANIAN_NAME = 'România';

    const SHORT_ROMANIAN_NAME = 'ro';

    const MOLDAVIA_NAME = 'Moldavia';

    const SHORT_MOLDAVIAN_NAME = 'md';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [];
    }

    /**
     * Returns list of all website languages.
     *
     * @return array
     */
    public static function getLanguages()
    {
        return [
            self::SHORT_ENGLISH_NAME => Icon::show('gb', [], Icon::FI) . ' ' . self::ENGLISH_NAME,
            self::SHORT_LITHUANIA_NAME => Icon::show('lt', [], Icon::FI) . ' ' . self::LITHUANIA_NAME,
            self::SHORT_POLISH_NAME => Icon::show('pl', [], Icon::FI) . ' ' . self::POLISH_NAME,
            self::SHORT_RUSSIAN_NAME => Icon::show('ru', [], Icon::FI) . ' ' . self::RUSSIAN_NAME,
            self::SHORT_GERMAN_NAME => Icon::show('de', [], Icon::FI) . ' ' . self::GERMAN_NAME,
            self::SHORT_SPANISH_NAME => Icon::show('es', [], Icon::FI) . ' ' . self::SPANISH_NAME,
            self::SHORT_ROMANIAN_NAME => Icon::show('ro', [], Icon::FI) . ' ' . self::ROMANIAN_NAME,
            self::SHORT_MOLDAVIAN_NAME => Icon::show('md', [], Icon::FI) . ' ' . self::MOLDAVIA_NAME,
        ];
    }

    /**
     * Checks whether language is valid
     *
     * @param string $language Language name in short version
     * @return boolean
     */
    public static function validateLanguage($language)
    {
        $languages = self::getLanguages();
        return array_key_exists($language, $languages);
    }

    /**
     * Sets website language
     *
     * @param string $language Language name in short version
     */
    public static function setLanguage($language)
    {
        if (!self::validateLanguage($language)) {
            $language = self::SHORT_LITHUANIA_NAME; // Sets lithuanian as default website language
        }

        Yii::$app->language = $language;
        return;
    }

    /**
     * Returns 2 letters of language name short version
     *
     * @param null|string $language Language name short version
     * @return string
     */
    public static function getCode($language = null)
    {
        if (is_null($language)) {
            $language = Yii::$app->language;
        }

        return strtoupper($language);
    }
}
