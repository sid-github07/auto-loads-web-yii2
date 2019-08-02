<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%user_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $action
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserLog extends ActiveRecord
{
    /** @const integer Maximum number of characters that action can contain */
    const ACTION_MAX_LENGTH = 255;

    /** @const integer Maximum number of characters that data can contain */
    const DATA_MAX_LENGTH = 65535;

    /** @const string Model scenario when system saves user log */
    const SCENARIO_SYSTEM_SAVES_USER_LOG = 'system-saves-user-log';

    /** @const string Log message placeholder for user updated field */
    const PLACEHOLDER_UPDATED_FIELD = 'UPDATED_FIELD';

    /** @const string Log message placeholder for user updated multiple models */
    const PLACEHOLDER_UPDATED_MULTIPLE_FIELDS = 'UPDATED_MULTIPLE_FIELDS';
    
    const SCENARIO_ADMIN_FILTERS_SEARCHES_LOG = 'filter-searches-log';
    const SCENARIO_ADMIN_FILTERS_MAP_SEARCHES_LOG = 'filter-map-searches-log';
    const SCENARIO_ADMIN_FILTERS_CREDITS_LOG = 'filter-credits-log';
    
    /** @const integer for searches which had results */
    const HAVE_RESULTS = 1;
    
    /** @const integer for searches which had no results */
    const NO_RESULTS = 0;
    
    /** @const integer for searches of loads */
    const LOG_LOAD_SEARCH = 1;
    
    /** @const integer for searches of car transporters */
    const LOG_CAR_TRANSPORTER_SEARCH = 2;
    
    /** @const integer for searches of map expand action in loads */
    const LOG_MAP_OPEN_IN_LOADS = 1;
    
    /** @const integer for searches of map expand action in car transporters */
    const LOG_MAP_OPEN_IN_CAR_TRANSPORTERS = 2;
    
    /** attribute to mark if search returned any results */
    public $haveFound;
    
    /** start date attribute to find date interval  */
    public $startDate;
    
    /** end date attribute to find date interval  */
    public $endDate;
    
    /** attribute to mark what was searched for */
    public $searchType;

    public $searchCredit;
    public $sumcredits;
    public $day;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_log}}';
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
        return [
            self::SCENARIO_SYSTEM_SAVES_USER_LOG => [
                'user_id',
                'action',
                'data',
            ],
            self::SCENARIO_ADMIN_FILTERS_SEARCHES_LOG => [
                'startDate',
                'endDate',
                'searchType',
                'haveFound',
            ],
            self::SCENARIO_ADMIN_FILTERS_MAP_SEARCHES_LOG => [
                'startDate',
                'endDate',
                'searchType',
            ],
            self::SCENARIO_ADMIN_FILTERS_CREDITS_LOG => [
                'startDate',
                'endDate',
                'searchCredit',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // User ID
            ['user_id', 'required', 'message' => Yii::t('app', 'USER_LOG_USER_ID_IS_REQUIRED')],
            ['user_id', 'integer', 'message' => Yii::t('app', 'USER_LOG_USER_ID_IS_NOT_INTEGER')],
            ['user_id', 'exist', 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
                'message' => Yii::t('app', 'USER_LOG_USER_ID_NOT_EXIST')],

            // Action
            ['action', 'required', 'message' => Yii::t('app', 'USER_LOG_ACTION_IS_REQUIRED')],
            ['action', 'filter', 'filter' => 'trim'],
            ['action', 'string', 'max' => self::ACTION_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'USER_LOG_ACTION_IS_TOO_LONG', [
                                  'length' => self::ACTION_MAX_LENGTH,
                              ]),
                             'message' => Yii::t('app', 'USER_LOG_ACTION_IS_NOT_STRING')],

            // Data
            ['data', 'required', 'message' => Yii::t('app', 'USER_LOG_DATA_IS_REQUIRED')],
            ['data', 'filter', 'filter' => 'trim'],
            ['data', 'string', 'max' => self::DATA_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'USER_LOG_DATA_IS_TOO_LONG', [
                                'length' => self::DATA_MAX_LENGTH,
                            ]),
                           'message' => Yii::t('app', 'USER_LOG_DATA_IS_NOT_STRING')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'USER_LOG_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'USER_LOG_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'USER_LOG_USER_ID_LABEL'),
            'action' => Yii::t('app', 'USER_LOG_ACTION_LABEL'),
            'data' => Yii::t('app', 'USER_LOG_DATA_LABEL'),
            'created_at' => Yii::t('app', 'USER_LOG_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'USER_LOG_UPDATED_AT_LABEL'),
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
     * Returns translated user log message
     *
     * @return string
     */
    public function getMessage()
    {
        $data = Json::decode($this->data);
        $message = Yii::t($data['t'], $data['message'], $data['params']);
        if ($this->hasFields($data)) {
            $fields = $this->translateModelChanges($data);
            $message .= ' ' . Html::tag('div', Html::tag('ul', $fields));
        }
        if ($this->hasMultiple($data)) {
            $multipleModels = $this->translateMultipleModelChanges($data);
            $message .= ' ' . Html::tag('div', Html::tag('ul', $multipleModels));
        }

        return $message;
    }
    
    /**
     * Checks whether user action model has changes
     *
     * @param array $data User action data
     * @return boolean
     */
    private function hasFields($data)
    {
        return isset($data['fields']) && !empty($data['fields']);
    }

    /**
     * Checks whether user updated multiple models
     *
     * @param array $data User action data
     * @return boolean
     */
    private function hasMultiple($data)
    {
        return isset($data['multiple']) && !empty($data['multiple']);
    }

    /**
     * Returns list of translated changes that were made in model
     *
     * @param array $data User action data
     * @return string
     */
    private function translateModelChanges($data)
    {
        $fields = $data['fields'];

        $message = '';
        foreach ($fields as $attribute => $field) {
            $message .= Yii::t($data['t'], self::PLACEHOLDER_UPDATED_FIELD, $field);
        }

        return $message;
    }

    /**
     * Returns list of translated changes that were made in multiple models
     *
     * @param array $data User action data
     * @return string
     */
    private function translateMultipleModelChanges($data)
    {
        $models = $data['multiple'];

        $message = '';
        foreach ($models as $model) {
            $message .= Yii::t($data['t'], self::PLACEHOLDER_UPDATED_MULTIPLE_FIELDS, $model);
        }

        return $message;
    }

    /**
     * Returns formatted user log entry creation date
     *
     * @return string
     */
    public function getDateTime()
    {
        return Yii::$app->formatter->asDatetime($this->created_at, 'php:Y-m-d H:i:s');
    }
	
    /**
     * Gets query of searches
     * 
     * @param array $get filter parameters
     * @return ActiveRecord
     */
    public function getQuery()
    {
        $conditionHaveFound = '';
        $conditionType = '';
        $conditionDateInterval = [];
        if ($this->haveFound == UserLog::HAVE_RESULTS) {
            $conditionHaveFound = ['like', 'data', '"haveResults":1'];
        }
        if (($this->haveFound == UserLog::NO_RESULTS) && trim($this->haveFound)!=='') {
            $conditionHaveFound = ['like', 'data', '"haveResults":0'];
        }
        
        if ($this->searchType == UserLog::LOG_LOAD_SEARCH) {
            $conditionType = ['like', 'data', '"message":"USER_SEARCHED_FOR_LOAD"'];
        }
        if (($this->searchType == UserLog::LOG_CAR_TRANSPORTER_SEARCH)) {
            $conditionType = ['like', 'data', '"message":"USER_SEARCHED_FOR_CAR_TRANSPORTER"'];
        }
        
        if(!empty($this->startDate) && !empty($this->endDate)) {
            $conditionDateInterval = ['between', 'created_at', strtotime($this->startDate . ' 00:00:00 Europe/Vilnius'), strtotime($this->endDate . ' 23:59:59 Europe/Vilnius')];
        }

        return UserLog::find()
                ->where(['action' => 'SEARCH'])
                ->andWhere($conditionHaveFound)
                ->andFilterWhere($conditionDateInterval)
                ->andWhere($conditionType);
    }
    
    /**
     * Gets query of map expand actions
     * 
     * @param array $get filter parameters
     * @return ActiveRecord
     */
    public function getMapOpenQuery()
    {
        if (!empty($this->searchType)) {
            $conditionType = ['like', 'data', '"type":' . $this->searchType];
        } else {
            $conditionType = '';
        }
        
        if(!empty($this->startDate) && !empty($this->endDate)) {
            $conditionDateInterval = ['between', 'created_at', strtotime($this->startDate . ' 00:00:00 Europe/Vilnius'), strtotime($this->endDate . ' 23:59:59 Europe/Vilnius')];
        } else {
            $conditionDateInterval = [];
        }

        return UserLog::find()
                ->where(['action' => 'MAP'])
                ->andFilterWhere($conditionDateInterval)
                ->andWhere($conditionType);
    }

    /**
     * Gets query of credits (all fields)
     * 
     * @return ActiveQuery
     */
    public function getCreditsQuery()
    {
        if(!empty($this->startDate) && !empty($this->endDate)) {
            $conditionDateInterval = ['between', 'created_at', strtotime($this->startDate . ' 00:00:00 Europe/Vilnius'), strtotime($this->endDate . ' 23:59:59 Europe/Vilnius')];
        } else {
            $conditionDateInterval = [];
        }
        return UserLog::find()
            ->where(['action' => $this->searchCredit])
            ->andFilterWhere($conditionDateInterval);
    }

    /** Time zone shift in seconds for Europe/Vilnius */
    public static $TZ_SHIFT_SEC = +2*60*60;

    /**
     * Gets query of credits sum
     * 
     * @return ActiveQuery
     */
    public function getCreditsSumQuery()
    {
        if(!empty($this->startDate) && !empty($this->endDate)) {
            $conditionDateInterval = ['between', 'created_at', strtotime($this->startDate . ' 00:00:00 Europe/Vilnius'), strtotime($this->endDate . ' 23:59:59 Europe/Vilnius')];
        } else {
            $conditionDateInterval = [];
        }

        $tzshift = static::$TZ_SHIFT_SEC;
        $fieldDay = new \yii\db\Expression("FROM_UNIXTIME(created_at + {$tzshift}, '%Y/%m/%d') AS day");
        $fieldSumCredits = new \yii\db\Expression("SUM(data) AS sumcredits");
        $query = UserLog::find()
            ->select([$fieldDay, 'action', $fieldSumCredits])
            ->groupBy(['day', 'action'])
            ->where(['action' => $this->searchCredit])
            ->andFilterWhere($conditionDateInterval);
        return $query;
    }
    
    /**
     * Gets data provider instance
     * 
     * @param ActiveRecord $userLog user log active record for data provider
     * @return ActiveDataProvider
     */
    public function getDataProvider($userLog)
    {
        return new ActiveDataProvider([
            'query' => $userLog,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                    ]
                ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }
    
    public function getHaveFoundFilter()
    {
        return [
            UserLog::HAVE_RESULTS => Yii::t('text', 'LOG_HAVE_FOUND'), 
            UserLog::NO_RESULTS => Yii::t('text', 'LOG_NO_FOUND')
        ];
    }
    
    public function getSearchTypeFilter()
    {
        return [
            UserLog::LOG_LOAD_SEARCH => Yii::t('text', 'LOG_SEARCH_TYPE_LOAD'), 
            UserLog::LOG_CAR_TRANSPORTER_SEARCH => Yii::t('text', 'LOG_SEARCH_TYPE_CAR_TRANSPORTER'),
        ];
    }
    
    public function getMapOpenSearchTypeFilter()
    {
        return [
            UserLog::LOG_MAP_OPEN_IN_LOADS => Yii::t('text', 'LOG_MAP_OPEN_IN_LOADS'), 
            UserLog::LOG_MAP_OPEN_IN_CAR_TRANSPORTERS => Yii::t('text', 'LOG_MAP_OPEN_IN_CAR_TRANSPORTERS'),
        ];
    }

}
