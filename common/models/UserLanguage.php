<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotAcceptableHttpException;

/**
 * This is the model class for table "{{%user_language}}".
 *
 * @property integer $user_id
 * @property integer $language_id
 *
 * @property Language $language
 * @property User $user
 */
class UserLanguage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // User ID
            ['user_id', 'required'],
            ['user_id', 'integer'],
            ['user_id', 'exist', 'targetClass' => User::className(),
                             'targetAttribute' => ['user_id' => 'id']],

            // Language ID
            ['language_id', 'required'],
            ['language_id', 'integer'],
            ['language_id', 'exist', 'targetClass' => Language::className(),
                                 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'language_id' => Yii::t('app', 'Language ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Assigns languages to specific user
     *
     * @param null|integer $userId User ID that languages should be assigned
     * @param array $languages Languages that should be assigned to user
     * @return integer Number of rows that were inserted in database
     * @throws NotAcceptableHttpException If user ID is null or empty, or languages is empty or invalid
     */
    public static function create($userId = null, $languages = [])
    {
        if (is_null($userId) || empty($userId) || empty($languages) || !self::isLanguagesValid($languages)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'USER_LANGUAGE_INVALID_USER_ID_OR_LANGUAGE'));
        }

        $rows = self::getRows($userId, $languages);
        return Yii::$app->db->createCommand()
                            ->batchInsert(self::tableName(), ['user_id', 'language_id'], $rows)
                            ->execute();
    }

    /**
     * Checks whether given languages is valid
     *
     * @param array $languages Languages, that needs to be checked if exists
     * @return boolean
     */
    public static function isLanguagesValid($languages)
    {
        $allLanguages = Language::getNames();
        foreach ($languages as $language) {
            if (!array_key_exists($language, $allLanguages)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Forms and returns list of assigned user ID to specific language
     *
     * @param integer $userId User ID that languages should be assigned
     * @param array $languages Languages that should be assigned to user
     * @return array
     */
    public static function getRows($userId, $languages)
    {
        $rows = [];
        foreach ($languages as $language) {
            array_push($rows, [
                'user_id' => $userId,
                'language_id' => $language,
            ]);
        }
        return $rows;
    }

    /**
     * Returns user languages
     *
     * @param null|integer $userId User ID that languages must be returned
     * @return array
     */
    public static function getUserLanguages($userId = null)
    {
        return self::find()->select('language_id')->where(['user_id' => $userId])->column();
    }

    /**
     * Removes user languages by given user ID
     *
     * @param null|integer $userId User ID that languages must be removed
     * @return integer Number of deleted rows
     */
    public static function remove($userId = null)
    {
        return self::deleteAll(['user_id' => $userId]);
    }

    /**
     * Updates user languages
     *
     * @param null|integer $userId User ID that languages should be updated
     * @param array $languages Languages that should be assigned to user
     * @return integer Number of rows that were inserted to database
     */
    public static function updateUserLanguages($userId = null, $languages = [])
    {
        self::remove($userId);
        return self::create($userId, $languages);
    }

    /**
     * Returns user language changes
     *
     * @param array $oldLanguages Old user languages
     * @param array $newLanguages New user languages
     * @return array
     */
    public static function getChanges($oldLanguages, $newLanguages)
    {
        return array_diff_assoc($oldLanguages, $newLanguages);
    }

    /**
     * Checks whether user changed languages that speaks
     *
     * @param array $oldLanguages Old user languages
     * @param array $newLanguages New user languages
     * @return boolean
     */
    public static function hasChanges($oldLanguages, $newLanguages)
    {
        $changes = self::getChanges($oldLanguages, $newLanguages);
        return !empty($changes);
    }
}
