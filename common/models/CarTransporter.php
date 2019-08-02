<?php

namespace common\models;

use backend\controllers\ClientController;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use \DateTime;

/**
 * This is the model class for table "{{%car_transporter}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $quantity
 * @property integer $available_from
 * @property integer $date_of_expiry
 * @property integer $visible
 * @property integer $archived
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property CarTransporterCity[] $carTransporterCities
 * @property CarTransporterPreview[] $carTransporterPreviews
 */
class CarTransporter extends ActiveRecord
{
    const EXTENSION_CREDITS = 1; // Number of credits that user needs to spend in order to extend expired car transporter

    const CODE_MAX_LENGTH = 255;
    const CODE_DEFAULT_VALUE = null;
    const CODE_SYMBOL = 'A';

    const QUANTITY_MIN_VALUE = 1;
    const QUANTITY_MAX_VALUE = 11;
    const QUANTITY_DEFAULT_VALUE = null;

    const AVAILABLE_FROM_DEFAULT_VALUE = null;

    const INVISIBLE = 0;
    const VISIBLE = 1;

    const NOT_ARCHIVED = 0;
    const ARCHIVED = 1;

    const CREDITS_FOR_ANNOUNCEMENT = 1;
    const CREDITS_FOR_PREVIEW = 1;

    const DAYS_TO_EXPIRE = 7; // Number of work days

    const PAGE_SIZE_FIRST = 50;
    const PAGE_SIZE_SECOND = 100;
    const PAGE_SIZE_THIRD = 150;

    const RADIUS_FIRST = 50;
    const RADIUS_SECOND = 100;
    const RADIUS_THIRD = 150;
    
    /** @const integer car transporter is inactive */
    const INACTIVE = 0;

    /** @const integer car transporter is active */
    const ACTIVE = 1;
	
	/** @const integer car transporters both acitive and inactive */
    const BOTH = 2;

    /** @const null Default active value */
    const DEFAULT_ACTIVE = null;

    /** @const integer car transporter is not activated */
    const NOT_ACTIVATED = 0;

    /** @const integer car transporter is activated */
    const ACTIVATED = 1;

    /** @const integer First page size option */
    const FIRST_PAGE_SIZE = 50;

    /** @const integer Second page size option */
    const SECOND_PAGE_SIZE = 100;

    /** @const integer Third page size option */
    const THIRD_PAGE_SIZE = 150;
	
	/** @const boolean Value when search returned results */
    const SEARCH_HAS_RESULTS = 1;
    
    /** @const boolean Value when search returned no results */
    const SEARCH_NO_RESULTS = 0;

    const ENTITY_TYPE_TRANSPORTER = 'car_transporter';

    const SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT = 'user-creates-new-announcement';
    const SCENARIO_SYSTEM_SAVES_CODE = 'system-saves-code';
    const SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER = 'user-searches-for-car-transporter';
    const SCENARIO_USER_CHANGES_AVAILABLE_FROM_DATE = 'user-changes-available-from-date';
    const SCENARIO_USER_CHANGES_QUANTITY = 'user-changes-quantity';
    const SCENARIO_USER_CHANGES_VISIBILITY = 'user-changes-visibility';
    const SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS = 'admin-filters-car-transporters';
    const SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER = 'system-migrates-car-transporter';
    const SCENARIO_UPDATE_TRANSPORTER_ADV = 'update-transporter-advert-data';
    const SCENARIO_UPDATE_OPEN_CONTACTS = 'open-contacts';

    public $radius;
    public $dateFrom;
    public $dateTo;
	
	public $haveResults = self::SEARCH_HAS_RESULTS;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%car_transporter}}';
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return self::ENTITY_TYPE_TRANSPORTER;
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

        $scenarios[self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT] = [
            'quantity',
            'available_from',
            'days_adv',
            'car_pos_adv',
            'submit_time_adv',
            'open_contacts_days',
            'open_contacts_expiry',
        ];
        $scenarios[self::SCENARIO_SYSTEM_SAVES_CODE] = [
            'code',
        ];
        $scenarios[self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER] = [
            'radius',
            'quantity',
            'available_from',
        ];
        $scenarios[self::SCENARIO_USER_CHANGES_AVAILABLE_FROM_DATE] = [
            'available_from',
        ];
        $scenarios[self::SCENARIO_USER_CHANGES_QUANTITY] = [
            'quantity',
        ];
        $scenarios[self::SCENARIO_USER_CHANGES_VISIBILITY] = [
            'visible',
            'date_of_expiry',
        ];
        $scenarios[self::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS] = [
            'dateFrom',
            'dateTo',
        ];
        $scenarios[self::SCENARIO_UPDATE_TRANSPORTER_ADV] = [
            'days_adv',
            'car_pos_adv',
            'submit_time_adv',
        ];
        $scenarios[self::SCENARIO_UPDATE_OPEN_CONTACTS] = [
            'open_contacts_days',
            'open_contacts_expiry',
        ];
        $scenarios[self::SCENARIO_SYSTEM_MIGRATES_CAR_TRANSPORTER] = [
            'id',
            'user_id',
            'code',
            'quantity',
            'available_from',
            'date_of_expiry',
            'visible',
            'archived',
            'created_at',
            'updated_at',
            'days_adv',
            'car_pos_adv',
            'submit_time_adv',
            'open_contacts_days',
            'open_contacts_expiry',
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->convertAvailableFromDateToTimestamp();
        $this->setDateOfExpiry();
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (is_null($this->code)) {
            $this->setCode();
            $this->scenario = self::SCENARIO_SYSTEM_SAVES_CODE;
            $this->save(false);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // User ID
            ['user_id', 'integer'],
            ['user_id', 'default', 'value' => Yii::$app->user->id],
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            // Code
            ['code', 'string', 'max' => self::CODE_MAX_LENGTH],
            ['code', 'unique', 'targetClass' => self::tableName()],
            ['code', 'default', 'value' => self::CODE_DEFAULT_VALUE],
            ['code', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],

            // Quantity
            ['quantity', 'integer',
                'min' => self::QUANTITY_MIN_VALUE,
                'tooSmall' => Yii::t('app', 'QUANTITY_IS_TOO_SMALL', [
                    'min' => self::QUANTITY_MIN_VALUE,
                ]),
                'max' => self::QUANTITY_MAX_VALUE,
                'tooBig' => Yii::t('app', 'QUANTITY_IS_TOO_BIG', [
                    'max' => self::QUANTITY_MAX_VALUE,
                ]),
                'skipOnEmpty' => true, // NOTE: quantity can be set as null when car transporter is fully empty
            ],
            ['quantity', 'default', 'value' => self::QUANTITY_DEFAULT_VALUE],

            // Available from
            ['available_from', 'default', 'value' => self::AVAILABLE_FROM_DEFAULT_VALUE],
            ['available_from', 'integer', 'on' => self::SCENARIO_USER_CHANGES_AVAILABLE_FROM_DATE],
            ['available_from', 'date', 'format' => 'php:Y-m-d', 'on' => [
                self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT,
                self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER,
            ]],
            ['available_from', function ($attribute) {
                $today = date('Y-m-d');
                if ($this->available_from < $today) {
                    $this->addError($attribute, Yii::t('app', 'AVAILABLE_FROM_DATE_IS_LESS_THAN_TODAY'));
                }

                return true;
            }, 'on' => self::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT],

            // Date of expiry
            ['date_of_expiry', 'integer'],

            // Visible
            ['visible', 'integer'],
            ['visible', 'default', 'value' => self::VISIBLE],
            ['visible', 'in', 'range' => self::getVisibilities()],

            // Archived
            ['archived', 'integer'],
            ['archived', 'default', 'value' => self::NOT_ARCHIVED],
            ['archived', 'in', 'range' => self::getArchivation()],

            // Created at
            ['created_at', 'integer'],

            // Updated  at
            ['updated_at', 'integer'],

            // Radius
            ['radius', 'required', 'on' => self::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER],
            ['radius', 'in',
                'range' => array_keys(self::getRadius()),
                'message' => Yii::t('app', 'SEARCH_RADIUS_IS_NOT_IN_RANGE')],

            // Date from
            ['dateFrom', 'string'],
            ['dateFrom', 'date', 'format' => 'php:Y-m-d'],
            ['dateFrom', 'validateDateRange', 'params' => ['message' => Yii::t('app', 'INVALID_DATE_RANGE_VALUES')]],

            // Date to
            ['dateTo', 'string'],
            ['dateTo', 'date', 'format' => 'php:Y-m-d'],
            ['dateTo', 'validateDateRange', 'params' => ['message' => Yii::t('app', 'INVALID_DATE_RANGE_VALUES')]],
                    
            // days_adv
            ['days_adv', 'integer', 'message' => Yii::t('app', 'CAR_ADV_DAY_IS_NOT_INTEGER')],
                        
            // car_pos_adv
            ['car_pos_adv', 'integer', 'message' => Yii::t('app', 'CAR_POS_ADV_IS_NOT_INTEGER')],
            ['submit_time_adv', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => Yii::t('app', 'SUBMIT_TIME_ADV_IS_NOT_DATETIME')],
                        
            // open_contacts_days
            ['open_contacts_days', 'required', 'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_days', 'integer', 'except' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_days', 'integer', 'min' => 1, 'max' => 7,
                'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS,
            ],

            // open_contacts_expiry
            ['open_contacts_expiry', 'required', 'on' => self::SCENARIO_UPDATE_OPEN_CONTACTS],
            ['open_contacts_expiry', 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'user_id' => Yii::t('app', 'user_id'),
            'code' => Yii::t('app', 'code'),
            'quantity' => Yii::t('app', 'quantity'),
            'submit_time_adv' => Yii::t('app', 'submit_time_adv'),
            'days_adv' => Yii::t('app', 'days_adv'),
            'car_pos_adv' => Yii::t('app', 'car_pos_adv'),
            'open_contacts_days' => Yii::t('app', 'open_contacts_days'),
            'open_contacts_expiry' => Yii::t('app', 'open_contacts_expiry'),
            'available_from' => Yii::t('app', 'available_from'),
            'date_of_expiry' => Yii::t('app', 'date_of_expiry'),
            'visible' => Yii::t('app', 'visible'),
            'archived' => Yii::t('app', 'archived'),
            'created_at' => Yii::t('app','created_at'),
            'updated_at' => Yii::t('app', 'updated_at'),
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
    public function getCarTransporterCities()
    {
        return $this->hasMany(CarTransporterCity::className(), ['car_transporter_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCarTransporterPreviews()
    {
        return $this->hasMany(CarTransporterPreview::className(), ['car_transporter_id' => 'id']);
    }

    /**
     * Validates date range attribute
     *
     * @param string $attribute Date range attribute name
     * @param array $params The value of the "params" given in the rule
     * @return boolean
     */
    public function validateDateRange($attribute, $params)
    {
        if (empty($this->dateFrom) || empty($this->dateTo)) {
            return true;
        }

        if ($this->dateFrom > $this->dateTo) {
            $this->addError($attribute, $params['message']);
        }

        return true;
    }

    /**
     * Returns list of all available quantities
     *
     * @see http://stackoverflow.com/questions/5360280/php-create-array-where-key-and-value-is-same
     * @return array
     */
    public static function getQuantities()
    {
        $array = range(self::QUANTITY_MIN_VALUE, self::QUANTITY_MAX_VALUE);
        return array_combine($array, $array);
    }

    /**
     * Creates and returns list of available car transporter quantity options specially for editable element
     *
     * Editable element requires special data structure therefore this method forms that structure
     *
     * @return array
     */
    public static function getEditableQuantities()
    {
        $container = [
            [
                'id' => 0, // NOTE: id cannot be set as null because editable does not allow to select it
                'text' => Yii::t('element', 'C-T-29'),
            ],
        ];

        $quantities = self::getQuantities();
        foreach ($quantities as $quantity) {
            array_push($container, [
                'id' => $quantity,
                'text' => $quantity,
            ]);
        }

        return $container;
    }

    /**
     * Checks whether current car transporter is already expired
     *
     * @return boolean
     */
    public function isExpired()
    {
        return $this->date_of_expiry < time();
    }

    /**
     * Extends current car transporter date to fixed weekdays from today
     */
    public function extend()
    {
        $this->date_of_expiry = strtotime("+" . self::DAYS_TO_EXPIRE . " weekdays");
    }

    /**
     * Sets car transporter announcement date of expiry
     */
    public function setDateOfExpiry()
    {
        if (is_null($this->date_of_expiry)) {
            $this->date_of_expiry = strtotime("+" . self::DAYS_TO_EXPIRE . " weekdays");
        }
    }

    /**
     * Converts available from date to timestamp
     */
    public function convertAvailableFromDateToTimestamp()
    {
        if (!$this->isAvailableFromInDateFormat()) {
            return;
        }
        $availableFrom = strtotime($this->available_from);
        $this->available_from = $availableFrom ? $availableFrom : self::AVAILABLE_FROM_DEFAULT_VALUE;
    }

    /**
     * Checks whether car transporter available from date is in date format
     *
     * @return boolean
     */
    public function isAvailableFromInDateFormat()
    {
        return (date('Y-m-d', strtotime($this->available_from)) == $this->available_from);
    }

    /**
     * Sets car transporter announcement code
     */
    public function setCode()
    {
        $this->code = self::CODE_SYMBOL . date('Y') . '-' . date('m') . '-' . sprintf("%'.06d", $this->id);
    }

    /**
     * Returns car transporter visibilities
     *
     * @return array
     */
    public static function getVisibilities()
    {
        return [self::INVISIBLE, self::VISIBLE];
    }

    /**
     * Returns car transporters data provider
     *
     * @param ActiveQuery $query Car transporters query
     * @param integer $pageSize Page size selection value
     * @return ActiveDataProvider
     */
    public static function getDataProvider($query, $pageSize)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => compact('pageSize'),
        ]);
    }

    /**
     * Returns list of page sizes
     *
     * @return array
     */
    public static function getPageSizes()
    {
        return [
            self::PAGE_SIZE_FIRST => '1-' . self::PAGE_SIZE_FIRST,
            self::PAGE_SIZE_SECOND => '1-' . self::PAGE_SIZE_SECOND,
            self::PAGE_SIZE_THIRD => '1-' . self::PAGE_SIZE_THIRD,
        ];
    }

    /**
     * Checks whether current user is car transporter owner
     *
     * @return boolean
     */
    public function isOwner()
    {
        return $this->user_id == Yii::$app->user->id;
    }

    /**
     * Converts available from date from timestamp format to date format
     */
    public function convertAvailableFromDate()
    {
        if (is_null($this->available_from) || $this->isAvailableFromInDateFormat()) {
            return;
        }

        $this->available_from = date('Y-m-d', $this->available_from);
    }

    /**
     * Returns list of car transporter search radius
     *
     * @return array
     */
    public static function getRadius()
    {
        return [
            self::RADIUS_FIRST => self::RADIUS_FIRST . ' km',
            self::RADIUS_SECOND => self::RADIUS_SECOND . ' km',
            self::RADIUS_THIRD => self::RADIUS_THIRD . ' km',
        ];
    }

    /**
     * Returns data provider for my car transporters table
     *
     * @param null|array $ids Load/unload cities IDs
     * @return ActiveDataProvider
     */
    public static function getMyCarTransportersDataProvider($ids)
    {
        return new ActiveDataProvider([
            'query' => self::getMyCarTransportersQuery($ids),
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get('car-transporter-page') - 1),
                'pageParam' => 'car-transporter-page',
				'params' => [
                    'car-transport-activity' => Yii::$app->request->get('car-transporter-activity', self::ACTIVE), 
                    'carTransporterCities' => Yii::$app->request->get('carTransporterCities'),
                ],
                'pageSize' => Yii::$app->request->get('car-transporter-per-page'),
                'pageSizeParam' => 'car-transporter-per-page',
                'defaultPageSize' => self::PAGE_SIZE_FIRST,
                'route' => 'my-announcement/index',
            ],
        ]);
    }

    /**
     * Returns active query for my car transporters data provider
     *
     * @param null|array $ids Load/unload cities IDs
     * @return ActiveQuery
     */
    private static function getMyCarTransportersQuery($ids)
    {
		switch (Yii::$app->request->get('car-transporter-activity', self::ACTIVE)) {
            case self::ACTIVE:
                $condition = ['>', self::tableName() . '.date_of_expiry', time()];
                break;
            case self::NOT_ACTIVATED:
                $condition = ['<', self::tableName() . '.date_of_expiry', time()];
                break;
            case self::BOTH:
                $condition = '';
                break;
            default:
                $condition = ['>', self::tableName() . '.date_of_expiry', time()];
        }
        return self::find()
            ->joinWith('carTransporterCities')
            ->joinWith('carTransporterCities.city')
            ->where([self::tableName() . '.`archived`' => self::NOT_ARCHIVED])
            ->andWhere(['user_id' => Yii::$app->user->id])
			->andWhere($condition)
            ->andFilterWhere(['or',
                [CarTransporterCity::tableName() . '.`city_id`' => $ids],
                [City::tableName() . '.`country_code`' => City::findCountriesCountryCode($ids)],
            ])
            ->groupBy(self::tableName() . '.`id`')
            ->orderBy([self::tableName() . '.`created_at`' => SORT_DESC]);
    }

    /**
     * Checks whether current car transporter is visible to other users
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible == self::VISIBLE;
    }

    /**
     * Checks whether current car transporter has the same available from date as given
     *
     * @param string $date Comparable available from date
     * @return boolean
     */
    public function hasSameAvailableFromDate($date)
    {
        return $this->available_from === strtotime($date);
    }

    /**
     * Returns car transporter archivation
     *
     * @return array
     */
    public static function getArchivation()
    {
        return [self::NOT_ARCHIVED, self::ARCHIVED];
    }

    /**
     * Returns translated car transporter archivation
     *
     * @return array
     */
    public static function getTranslatedArchivation()
    {
        return [
            self::NOT_ARCHIVED => Yii::t('app', 'CAR_TRANSPORTER_IS_NOT_ARCHIVED'),
            self::ARCHIVED => Yii::t('app', 'CAR_TRANSPORTER_IS_ARCHIVED'),
        ];
    }

    /**
     * Returns car transporters data provider for car transporters management in administration panel
     *
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @param null|integer $id Company ID that announced car transporters
     * @return ActiveDataProvider
     */
    public function getAdminDataProvider(CarTransporterCity $carTransporterCity, $id = null)
    {
        $transporterTableName = self::tableName();
        $previewTableName = CarTransporterPreview::tableName();
        $query = self::find()
            ->joinWith('carTransporterCities')
            ->joinWith('carTransporterCities.city city')
            ->joinWith('carTransporterPreviews')
            ->joinWith('user')
            ->joinWith('user.companies ownerCompany')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company userCompany')
            ->addSelect(["COUNT({$previewTableName}.id) as preview_count", "{$transporterTableName}.*"])
            ->andFilterWhere([
                'or',
                ['ownerCompany.id' => $id],
                ['userCompany.id' => $id],
            ])
            ->groupBy([self::tableName() . '.id']);

        $query = $this->filterDate($query);
        $query = $this->filterLocation($carTransporterCity, $query);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'visible' => [
                        'default' => SORT_DESC,
                    ],
                    'preview_count' => [
                        'default' => SORT_DESC,
                    ],
                    'created_at' => [
                        'default' => SORT_DESC,
                    ]
                ],
            ],
        ]);
    }
    
    /**
     * Returns loads data provider for administration car transporters filtration
     *
     * @param CarTransporterCity $carTransporterCity CarTransporter city model
     * @param null|integer $id Company ID
     * @return ActiveDataProvider
     */
    public function companyCarTransportersDataProvider(CarTransporterCity $carTransporterCity, $id = null, $pageSize = self::FIRST_PAGE_SIZE)
    {
        $query = self::find()
            ->joinWith('carTransporterCities')
            ->joinWith('carTransporterCities.city city')
            ->joinWith('carTransporterPreviews')
            ->joinWith('user')
            ->joinWith('user.companies ownerCompany')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company userCompany')
            ->andFilterWhere([
                'or',
                ['ownerCompany.id' => $id],
                ['userCompany.id' => $id],
            ])
            ->groupBy([self::tableName() . '.id'])
            ->orderBy([self::tableName() . '.created_at' => SORT_DESC]);

        $query = $this->filterDate($query);
        $query = $this->filterLocation($carTransporterCity, $query);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get('car-transporter-page') - 1),
                'pageSizeParam' => 'car-transporter-per-page',
                'pageParam' => 'car-transporter-page',
                'params' => ['tab' => ClientController::TAB_COMPANY_CAR_TRANSPORTERS, 'id' => Yii::$app->request->get('id')],
                'pageSize' => Yii::$app->request->get('car-transporter-per-page'),
                'defaultPageSize' => self::FIRST_PAGE_SIZE,
                'route' => 'client/company',
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Filters car transporters by selected date range
     *
     * @param ActiveQuery $query Target query
     * @return ActiveQuery
     */
    private function filterDate(ActiveQuery $query)
    {
        if (!empty($this->dateFrom)) {
            $dateFrom = Yii::$app->datetime->convertToTimestamp($this->dateFrom . ' 00:00:00 Europe/Vilnius');
        } else {
            $dateFrom = Yii::$app->datetime->convertToTimestamp($this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $dateTo = Yii::$app->datetime->convertToTimestamp($this->dateTo . ' 23:59:59 Europe/Vilnius');
        } else {
            $dateTo = Yii::$app->datetime->convertToTimestamp($this->dateTo);
        }

        if (empty($dateFrom) && empty($dateTo)) {
            return $query;
        }

        $query->andFilterWhere(['between', self::tableName() . '.created_at', $dateFrom, $dateTo]);
        return $query;
    }

    /**
     * Filters car transporters by given locations
     *
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @param ActiveQuery $query Target query
     * @return ActiveQuery
     */
    private function filterLocation(CarTransporterCity $carTransporterCity, ActiveQuery $query)
    {
        $loadLocation = City::findOne($carTransporterCity->loadLocation);
        $unloadLocation = City::findOne($carTransporterCity->unloadLocation);

        if (!is_null($loadLocation) && !is_null($unloadLocation)) {
            $carTransporterCityQuery = $this->getCarTransporterCityQuery($loadLocation);
            $on = CarTransporter::tableName() . '.id = ' . CarTransporterCity::tableName() . '.car_transporter_id';
            $query->leftJoin(['carTransporterCity' => $carTransporterCityQuery], $on);
            $query->andWhere('carTransporterCity.car_transporter_id = ' . CarTransporterCity::tableName() . '.car_transporter_id');

            return $this->addLocationFiltrationCondition($query, $unloadLocation, CarTransporterCity::TYPE_UNLOAD);
        }

        if (!is_null($loadLocation)) {
            return $this->addLocationFiltrationCondition($query, $loadLocation, CarTransporterCity::TYPE_LOAD);
        }

        if (!is_null($unloadLocation)) {
            return $this->addLocationFiltrationCondition($query, $unloadLocation, CarTransporterCity::TYPE_UNLOAD);
        }

        return $query;
    }

    /**
     * Creates and returns car transporter city query for load location filtration
     *
     * @param City $location City model
     * @return ActiveQuery
     */
    private function getCarTransporterCityQuery(City $location)
    {
        if ($location->isCountry()) {
            return CarTransporterCity::find()->joinWith('city city')->andWhere([
                'city.country_code' => $location->country_code,
                CarTransporterCity::tableName() . '.type' => CarTransporterCity::TYPE_LOAD,
            ]);
        }

        return CarTransporterCity::find()->where([
            CarTransporterCity::tableName() . '.city_id' => $location->id,
            CarTransporterCity::tableName() . '.type' => CarTransporterCity::TYPE_LOAD,
        ]);
    }

    /**
     * Adds location filtration condition to given query
     *
     * @param ActiveQuery $query Target query
     * @param City $location City model
     * @param integer $type Car transporter city type
     * @return ActiveQuery
     */
    private function addLocationFiltrationCondition(ActiveQuery $query, City $location, $type)
    {
        if ($location->isCountry()) {
            return $this->addCountryFiltrationCondition($query, $location, $type);
        }
        
        return $this->addCityFiltrationCondition($query, $location, $type);
    }

    /**
     * Adds country filtration condition to given query
     *
     * @param ActiveQuery $query Target query
     * @param City $city City model
     * @param integer $type Car transporter city type
     * @return ActiveQuery
     */
    private function addCountryFiltrationCondition(ActiveQuery $query, City $city, $type)
    {
        $query->andWhere([
            'city.country_code' => $city->country_code,
            CarTransporterCity::tableName() . '.type' => $type,
        ]);

        return $query;
    }

    /**
     * Adds city filtration condition to given query
     *
     * @param ActiveQuery $query Target query
     * @param City $city City model
     * @param integer $type Car transporter city type
     * @return ActiveQuery
     */
    private function addCityFiltrationCondition(ActiveQuery $query, City $city, $type)
    {
        $query->andWhere([
            CarTransporterCity::tableName() . '.city_id' => $city->id,
            CarTransporterCity::tableName() . '.type' => $type,
        ]);

        return $query;
    }
    
    /**
     * Deactivates all active loads
     *
     * @return integer Number of deactivated loads
     */
    public static function deactivateAllActivated()
    {
        return self::updateAll(['visible' => self::INVISIBLE], [
            'archived' => self::NOT_ARCHIVED,
            'visible' => self::VISIBLE,
        ]);
    }
    
    /**
     * Removes old loads
     *
     * @param integer $term Number of days that load considered as old
     * @return integer Number of removed old loads
     */
    public static function removeOld($term = 0)
    {
        $limit = strtotime('-' . $term . ' days');
        return self::updateAll([
            'archived' => self::ARCHIVED,
        ], 'archived = :archived AND date_of_expiry <= :limit', [
            ':archived' => self::NOT_ARCHIVED,
            ':limit' => $limit,
        ]);
    }
	
	/**
     * Returns car transporter activities
     *
     * @return array
     */
    public static function getCarTransportersListActivities()
    {
        return [
            self::ACTIVE => Yii::t('element', 'MK-C-22a'), 
            self::NOT_ACTIVATED => Yii::t('element', 'MK-C-22b'), 
            self::BOTH => Yii::t('element', 'MK-C-22c'),
        ];
    }
    
    /**
     * Returns if car transporter list in one week date range
     * 
     * @param integer $currentUserId logged user id
     * return array
     */
    public static function getCarTransportersInDateRange($currentUserId)
    {
        $startDate = strtotime("-1 week"); // User can announce one load per week
        $endDate = time();

        return self::find()
            ->where(['user_id' => $currentUserId])
            ->andWhere(['between', 'created_at', $startDate, $endDate])
            ->all();
    }

    /**
     * @return array
     */
    public function getCarPosRanges()
    {
        $range = range(1, 10);
        return array_combine($range, $range);
    }

    /**
     * @return array
     */
    public function getDaysRanges()
    {
        $range = range(1, 7);
        return array_combine($range, $range);
    }
        
    /**
     * Checks if open contacts service exist by expiry date
     * 
     * @return boolean
     */
    public function isOpenContacts()
    {
        return $this->open_contacts_expiry > time();
    }
    
    /**
     * Returns open contacts expiry date
     * 
     * @return string
     */
    public function getOpenContactsExpiry()
    {
        return date($this->open_contacts_expiry, 'Y-m-d H:i');
    }
    
    /**
     * Returns open contacts expiry datetime
     * 
     * @return string
     */
    public function getOpenContactsExpiryTime()
    {
        return date('Y-m-d H:i:s', $this->open_contacts_expiry);
    }
    
    /**
     * Sets load advertisement submit time
     */
    public function setAdvertisementSubmitTime()
    {
        $date = new DateTime();
        $this->submit_time_adv = $date->format('Y-m-d H:i:s');
    }
    
    /**
     * Sets open contacts days by open_contacts_days attribute value
     */
    public function setOpenContactsExpiry()
    {
        if (empty($this->open_contacts_days)) {
            return;
        }

        if ($this->open_contacts_expiry == 0) {
            $timestamp = time();
        } else {
            $timestamp = $this->open_contacts_expiry;
        }
        
        $days = $this->open_contacts_days;
        $this->open_contacts_expiry = strtotime("+{$days} days", $timestamp);
    }

    /**
     * @param int $type
     * @return string
     */
    public function getCityInfo($type = CarTransporterCity::TYPE_LOAD){
        $cities = [];
        $string = '';
        foreach ($this->carTransporterCities as $cts) {
            if ($type === CarTransporterCity::TYPE_LOAD && $cts->type === $type) {
                $city = City::findOne($cts->city_id);
            } elseif ($type === CarTransporterCity::TYPE_UNLOAD && $cts->type === $type) {
                $city = City::findOne($cts->city_id);
            } else {
                continue;
            }
            $cities[$city->country_code][$city->id] = $city->name;
        }
        foreach ($cities as $countryCode => $list) {
            $string .= $countryCode . ', ';
            foreach ($list as $cityName) {
                $string .= $cityName . ', ';
            }
            $string = rtrim($string, ', ') . ' + ';
        }
        return rtrim($string, ' + ');
    }

    /**
     * @param int $type
     * @return string
     */
    public function getFullCityInfo($type = CarTransporterCity::TYPE_LOAD){
        $cities = [];
        $string = '';
        $countries = City::getOriginalCountriesList();
        foreach ($this->carTransporterCities as $cts) {
            if ($type === CarTransporterCity::TYPE_LOAD && $cts->type === $type) {
                $city = City::findOne($cts->city_id);
            } elseif ($type === CarTransporterCity::TYPE_UNLOAD && $cts->type === $type) {
                $city = City::findOne($cts->city_id);
            } else {
                continue;
            }
            $cities[Yii::t('country', $countries[$city->country_code]['name'])][$city->id] = $city->name;
        }
        foreach ($cities as $countryCode => $list) {
            $string .= $countryCode . ', ';
            foreach ($list as $cityName) {
                $string .= $cityName . ', ';
            }
            $string = rtrim($string, ', ') . ' + ';
        }
        return rtrim($string, ' + ');
    }
}
