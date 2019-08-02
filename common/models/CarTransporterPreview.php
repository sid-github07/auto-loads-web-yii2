<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;


/**
 * This is the model class for table "{{%car_transporter_preview}}".
 *
 * @property integer $id
 * @property integer $car_transporter_id
 * @property integer $user_id
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property CarTransporter $carTransporter
 */
class CarTransporterPreview extends ActiveRecord
{
    /** @const integer Minimum number of characters that user IP address can contain */
    const IP_MIN_LENGTH = 7;
    
    /** @const integer Maximum number of characters that user IP address can contain */
    const IP_MAX_LENGTH = 255;

    /** @const string Model scenario when user previews car transporter information */
    const SCENARIO_USER_PREVIEWS_CAR_TRANSPORTER = 'user-previews-car-transporter';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%car_transporter_preview}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_USER_PREVIEWS_CAR_TRANSPORTER] = [
            'car_transporter_id',
            'user_id',
            'ip'
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Car transporter ID
            ['car_transporter_id', 'required'],
            ['car_transporter_id', 'integer'],
            ['car_transporter_id', 'exist',
                'targetClass' => CarTransporter::className(),
                'targetAttribute' => ['car_transporter_id' => 'id']],

            // User ID
            ['user_id', 'required'],
            ['user_id', 'integer'],
            ['user_id', 'exist',
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']],

            // IP
            ['ip', 'required'],
            ['ip', 'string', 'message' => Yii::t('app', 'LOAD_PREVIEW_IP_IS_NOT_STRING'),
                                 'min' => self::IP_MIN_LENGTH,
                            'tooShort' => Yii::t('app', 'LOAD_PREVIEW_IP_IS_TOO_SHORT', [
                                'min' => self::IP_MIN_LENGTH,
                            ]),
                                 'max' => self::IP_MAX_LENGTH,
                             'tooLong' => Yii::t('app', 'LOAD_PREVIEW_IP_IS_TOO_LONG', [
                                 'max' => self::IP_MAX_LENGTH,
                             ])],
            ['ip', 'ip'],

            // Created at
            ['created_at', 'integer'],

            // Updated at
            ['updated_at', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_transporter_id' => 'Car Transporter ID',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCarTransporter()
    {
        return $this->hasOne(CarTransporter::className(), ['id' => 'car_transporter_id']);
    }

    /**
     * Checks whether current user has already previewed this car transporter
     *
     * @return boolean
     */
    public function hasAlreadyPreviewed()
    {
        return self::find()
            ->where([
                'car_transporter_id' => $this->car_transporter_id,
                'user_id' => $this->user_id,
                'ip' => $this->ip,
            ])
            ->exists();
    }

    /**
     * @param $transporterId
     * @return ActiveDataProvider
     */
    public static function getPreviewDataProvider($transporterId)
    {
        $query = self::find()->where(['car_transporter_id' => $transporterId])
            ->with('user')->orderBy('created_at DESC');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> false,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        return $dataProvider;
    }
}
