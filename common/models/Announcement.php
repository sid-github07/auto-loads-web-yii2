<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * Class Announcement
 * @package common\models
 */
class Announcement extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    const STATUS_HIDDEN = 3;
    const STATUS_EXPIRED = 4;

    static $STATUSES = [
        self::STATUS_ACTIVE => 'active',
        self::STATUS_HIDDEN => 'hidden',
        self::STATUS_DELETED => 'deleted',
        self::STATUS_EXPIRED => 'expired',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%announcements}}';
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['body', 'language_id'], 'required'],
            [['language_id'], 'string'],
            [['body'], 'string'],
            [['topic'], 'string'],
            [['status'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public static function statusesDropdown()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'active'),
            self::STATUS_HIDDEN => Yii::t('app', 'hidden'),
        ];
    }

    /**
     * @return mixed|string
     */
    public function statusString()
    {
        if (isset(self::$STATUSES[$this->status])) {
            return self::$STATUSES[$this->status];
        }

        return 'unknown';
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'topic' => Yii::t('app', 'Topic'),
            'body' => Yii::t('app', 'Message'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @param $languageID
     * @return int
     */
    public static function hideAllAnnouncements($languageID)
    {
        return self::updateAll(['status' => Announcement::STATUS_HIDDEN], 'language_id = ' . $languageID);
    }

    /**
     * @return ActiveDataProvider
     */
    public static function getDataProvider()
    {
        $query = self::queryGetAnnouncements();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created_at'=> SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryGetAnnouncements()
    {
        return self::find()->where(self::tableName() . '.status IN (' . self::STATUS_ACTIVE . ',' . self::STATUS_HIDDEN . ')');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function queryGetActiveAnnouncements()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param null $id
     * @return Announcement|null
     * @throws NotFoundHttpException
     */
    public static function findById($id = null)
    {
        $record = self::findOne($id);
        if (is_null($record)) {
            throw new NotFoundHttpException(Yii::t('alert', 'ANNOUNCEMENT_NOT_FOUND_BY_ID'));
        }
        return $record;
    }

}
