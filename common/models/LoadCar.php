<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%load_car}}".
 *
 * @property integer $id
 * @property integer $load_id
 * @property integer $quantity
 * @property string $model
 * @property string $price
 * @property integer $state
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Load $load
 */
class LoadCar extends ActiveRecord
{
    /** @const null Default quantity value */
    const DEFAULT_QUANTITY = null;

    /** @const integer Minimum value that quantity can contain */
    const QUANTITY_MIN_VALUE = 1;

    /** @const integer Maximum value that quantity can contain */
    const QUANTITY_MAX_VALUE = 11;

    /** @const null Default load car model value */
    const DEFAULT_MODEL = null;

    /** @const integer Maximum number of characters that load car model can contain */
    const MODEL_MAX_LENGTH = 15;

    /** @const integer Maximum number of digits that load car model can contain */
    const MODEL_MAX_DIGITS = 5;

    /** @const null Default price value */
    const DEFAULT_PRICE = null;

    /** @const integer Number of characters that price can contain before comma or dot */
    const PRICE_PRECISION = 10;

    /** @const integer Number of characters that price can contain after comma or dot */
    const PRICE_SCALE = 2;

    /** @const null Default state value */
    const DEFAULT_STATE = null;

    /** @const integer Load car state is not driving */
    const NOT_DRIVING_CAR = 0;

    /** @const integer Load car state is used car */
    const USED_CAR = 1;

    /** @const integer Load car state is new car */
    const NEW_CAR = 2;

    /** @const string Model scenario when user creates new load announce */
    const SCENARIO_ANNOUNCE_CLIENT = 'announce-client';

    /** @const string Model scenario when user announced load cars must be saved to database */
    const SCENARIO_ANNOUNCE_SERVER = 'announce-server';

    /** @const string Model scenario when user creates new load announce */
    const SCENARIO_EDIT_CAR_INFO = 'edit-car-info';
    
    /** @const string Model scenario when user creates new load announce */
    const SCENARIO_CREATE_CAR_INFO = 'create-car-info';

    /** @const string Model scenario when client searches loads */
    const SCENARIO_SEARCH_CLIENT = 'search-client';

    /** @const string Model scenario when system migrates load car data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_LOAD_CAR_DATA = 'system-migrates-load-car-data';

    const SCENARIO_SYSTEM_MIGRATES_LOAD_CAR = 'system-migrates-load-car';

    /** @var null|Load Load model */
    public $load = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%load_car}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_ANNOUNCE_CLIENT => [
                'quantity',
                'model',
                'price',
                'state',
            ],
            self::SCENARIO_ANNOUNCE_SERVER => [
                'load_id',
                'quantity',
                'model',
                'price',
                'state',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_EDIT_CAR_INFO => [
                'quantity',
                'model',
                'price',
                'state',
            ],
            self::SCENARIO_SEARCH_CLIENT => [
                'quantity',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR_DATA => [
                'id',
                'load_id',
                'quantity',
                'model',
                'price',
                'state',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR => [
                'id',
                'load_id',
                'quantity',
                'model',
                'price',
                'state',
                'created_at',
                'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Load ID
            ['load_id', 'required', 'message' => Yii::t('app', 'LOAD_CAR_LOAD_ID_IS_REQUIRED')],
            ['load_id', 'integer', 'message' => Yii::t('app', 'LOAD_CAR_LOAD_ID_IS_NOT_INTEGER')],
            ['load_id', 'exist', 'targetClass' => Load::className(),
                             'targetAttribute' => ['load_id' => 'id'],
                                     'message' => Yii::t('app', 'LOAD_CAR_LOAD_ID_IS_NOT_EXIST')],

            // Quantity
            ['quantity', 'required',
                      'when' => function () {
                          if (is_null($this->load) || !$this->load instanceof Load) {
                              return false;
                          }
                          return $this->load->isTypePartial();
                      },
                'whenClient' => 'function () {' .
                                    'return $(".IA-C-2.IA-C-3:checked").val() == "' . Load::TYPE_PARTIAL . '";' .
                                '}',
                'except' => self::SCENARIO_EDIT_CAR_INFO,               
                'message' => Yii::t('app', 'LOAD_CAR_QUANTITY_IS_REQUIRED'),],
            ['quantity', 'required',
                      'when' => function () {
                          if (is_null($this->load) || !$this->load instanceof Load) {
                              return false;
                          }
                          return $this->load->isTypePartial();
                      },
                'whenClient' => 'function () {' .
                                    'return $("#loadType").val() == "' . Load::TYPE_PARTIAL . '";' .
                                '}',                  
                'except' => self::SCENARIO_ANNOUNCE_CLIENT,
                'message' => Yii::t('app', 'LOAD_CAR_QUANTITY_IS_REQUIRED'),],                 
            ['quantity', 'default', 'value' => self::DEFAULT_QUANTITY],
            ['quantity', 'integer', 'min' => self::QUANTITY_MIN_VALUE,
                               'tooSmall' => Yii::t('app', 'LOAD_CAR_QUANTITY_IS_TOO_SMALL', [
                                   'min' => self::QUANTITY_MIN_VALUE,
                               ]),
                                    'max' => self::QUANTITY_MAX_VALUE,
                                'tooBig' => Yii::t('app', 'LOAD_CAR_QUANTITY_IS_TOO_BIG', [
                                    'max' => self::QUANTITY_MAX_VALUE,
                                ]),
                                'message' => Yii::t('app', 'LOAD_CAR_QUANTITY_IS_NOT_INTEGER')],

             //Model
            ['model', 'required',
                'when' => function () {
                    if (is_null($this->load) || !$this->load instanceof Load) {
                        return false;
                    }
                    return $this->load->isTypePartial();
                },
                'whenClient' => 'function () {' .
                                    'return $(".IA-C-2.IA-C-3:checked").val() == "' . Load::TYPE_PARTIAL . '";' .
                                '}',        
                'except' => self::SCENARIO_EDIT_CAR_INFO,         
                'message' => Yii::t('app', 'LOAD_CAR_MODEL_IS_REQUIRED')], 
            ['model', 'required',
                'when' => function () {
                    if (is_null($this->load) || !$this->load instanceof Load) {
                        return false;
                    }
                    return $this->load->isTypePartial();
                },
                'whenClient' => 'function () {' .
                                    'return $("#loadType").val() == "' . Load::TYPE_PARTIAL . '";' .
                                '}',        
                'except' => self::SCENARIO_ANNOUNCE_CLIENT, 
                'message' => Yii::t('app', 'LOAD_CAR_MODEL_IS_REQUIRED')],             
            ['model', 'default', 'value' => self::DEFAULT_MODEL],
            ['model', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['model', 'string', 'max' => self::MODEL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'LOAD_CAR_MODEL_IS_TOO_LONG', [
                                'length' => self::MODEL_MAX_LENGTH,
                            ]),
                            'message' => Yii::t('app', 'LOAD_CAR_MODEL_IS_NOT_STRING'),
                             'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR],
            ['model', 'validateModel', 'params' => [
                'message' => Yii::t('app', 'LOAD_CAR_TOO_MANY_NUMBER_OF_DIGITS_IN_CAR_MODEL')
            ], 'except' => self::SCENARIO_SYSTEM_MIGRATES_LOAD_CAR],

            // Price
            ['price', 'default', 'value' => self::DEFAULT_PRICE],
            ['price', 'match', 'pattern' => '/^\d{1,8}(?:(\.|\,)\d{1,2})?$/',
                               'message' => Yii::t('app', 'LOAD_CAR_PRICE_IS_NOT_MATCH')],

            // State
            ['state', 'default', 'value' => self::DEFAULT_STATE],
            ['state', 'integer', 'message' => Yii::t('app', 'LOAD_CAR_STATE_IS_NOT_INTEGER')],
            ['state', 'in', 'range' => [self::NOT_DRIVING_CAR, self::USED_CAR, self::NEW_CAR],
                          'message' => Yii::t('app', 'LOAD_CAR_STATE_IS_NOT_IN_RANGE')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'LOAD_CAR_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'LOAD_CAR_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }

    /**
     * Validates load car model
     *
     * @param string $attribute Name of attribute that is being validated
     * @param array $params The value of the "params" given in the rule
     */
    public function validateModel($attribute, $params = [])
    {
        if ($this->countDigits() > self::MODEL_MAX_DIGITS) {
            $this->addError($attribute, isset($params['message']) ? $params['message'] : null);
        }
        return;
    }

    /**
     * Counts and returns number of digits from string
     *
     * @see http://stackoverflow.com/a/11023784/5747867
     * @return integer
     */
    private function countDigits()
    {
        return preg_match_all( "/[0-9]/", $this->model);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'load_id' => Yii::t('app', 'LOAD_CAR_LOAD_ID_LABEL'),
            'quantity' => Yii::t('app', 'LOAD_CAR_QUANTITY_LABEL'),
            'model' => Yii::t('app', 'LOAD_CAR_MODEL_LABEL'),
            'price' => Yii::t('app', 'LOAD_CAR_PRICE_LABEL'),
            'state' => Yii::t('app', 'LOAD_CAR_STATE_LABEL'),
            'created_at' => Yii::t('app', 'LOAD_CAR_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'LOAD_CAR_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * Returns load cars sum
     *
     * @return integer
     */
    public static function getLoadsCarsSum()
    {
        $sum = 0;
        $loads = Load::findAllActive();
        foreach ($loads as $load) {
            $cars = $load->countCars();
            $sum += (($load->type == Load::TYPE_FULL) && ($cars == 0))? Load::TYPE_LIMIT : $cars;
        }
        return $sum;
    }
    
    /**
     * Marks transported flag in database table and returns transported load cars sum
     *
     * @return integer
     */
    public static function getTransportedLoadsCarsSum()
    {
        Load::markTransportedNoPreviews();
        Load::markTransported();
        return Load::getTransportedTotal();
    }

    /**
     * Calculates and saves load car totals
     *
     * @return boolean
     */
    public static function recalculateTotals()
    {
        // $loadCarsSum = self::getLoadsCarsSum();
        $transportedLoadCarsSum = self::getTransportedLoadsCarsSum();
        
        $values = PreparedValue::loadModel();
        // $values->total_cars_ready = $loadCarsSum;
        $values->total_cars_transported = $transportedLoadCarsSum;
        return $values->save();
    }
    
    /**
     * @return ActiveQuery
     */
    public function getLoad()
    {
        return $this->hasOne(Load::className(), ['id' => 'load_id']);
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
     * Returns list of car states
     *
     * @return array
     */
    public static function getStates()
    {
        return [
            self::NEW_CAR => Yii::t('app', 'LOAD_CAR_NEW_CAR'),
            self::USED_CAR => Yii::t('app', 'LOAD_CAR_USED_CAR'),
            self::NOT_DRIVING_CAR => Yii::t('app', 'LOAD_CAR_NOT_DRIVING_CAR'),
        ];
    }

    /**
     * Returns formatted information about given load
     *
     * @param Load $load Load model
     * @return string
     */
    public static function getLoadInfo(Load $load)
    {
        $loadInfo = $load->isTypeFull() ? Yii::t('text', 'LOAD_TYPE_FULL') : Yii::t('text', 'LOAD_TYPE_PARTIAL');
        $loadInfo .= self::loadInfoString($load);
        return $loadInfo;
    }

    /**
     * @param Load $load
     * @return string
     */
    public static function getLoadShortInfo(Load $load){
        $string = '';
        foreach ($load->loadCars as $loadCar) {
            $string .= sprintf('(%s)', trim($loadCar->getQuantityAndModelString()));
        }
        return $string;
    }

    /**
     * Forms current load info string
     *
     * @param Load $load Load model
     * @return string
     */
    private static function loadInfoString(Load $load)
    {
        if (count($load->loadCars) >= 4) {
            $quantities = array_reduce($load->loadCars, function ($quantities, $loadCar) {
                return $quantities += $loadCar->quantity;
            });
            $price = array_reduce($load->loadCars, function ($price, $loadCar) {
                return $price += $loadCar->quantity != 0 ? $loadCar->price * $loadCar->quantity : $loadCar->price;
            });
            if ($quantities != 0 && $price != 0) {
                return Html::tag('span', ', ' . $quantities . ' x ' . Yii::t('text', 'MORE_THAN_FOUR_MODELS') . ' (' . $price . ' eur/' . Yii::t('text', 'PAY_FOR_WHOLE_LOAD') . ')');
            }
            if ($quantities != 0) {
                if ($load->isPaymentMethodForAllLoad() && !is_null($load->price)) {
                    return Html::tag('span', ', ' . $quantities . ' x ' . Yii::t('text', 'MORE_THAN_FOUR_MODELS') . ' (' .  $load->price . ' eur/' . Yii::t('text', 'PAY_FOR_WHOLE_LOAD') . ')');
                }
                return Html::tag('span', ', ' . $quantities . ' x ' . Yii::t('text', 'MORE_THAN_FOUR_MODELS'));   
            }
            if($price != 0) {
                return Html::tag('span', ', ' . Yii::t('text', 'MORE_THAN_FOUR_MODELS') . ' (' . $price . ' eur/' . Yii::t('text', 'PAY_FOR_WHOLE_LOAD') . ')');  
            }
            return Html::tag('span', ', ' . Yii::t('text', 'MORE_THAN_FOUR_MODELS'));
        }

        $string = '';
        foreach ($load->loadCars as $loadCar) {
            $string .= Html::tag('span', ', ' . $loadCar->getQuantityAndModelString() . $loadCar->getPriceString());
        }
		if ($load->isPaymentMethodForAllLoad() && !is_null($load->price)) {
            $string .= Html::tag('span', '(' . $load->price . ' eur/' . Yii::t('text', 'PAY_FOR_WHOLE_LOAD') . ')');
        }
        return $string;
    }

    /**
     * Returns formatted load car price
     *
     * @return string
     */
    private function getPriceString()
    {
        return (!is_null($this->price) ? '(' . $this->price . ' eur/auto)' : '');
    }

    /**
     * Returns formatted load car quantity and model string
     *
     * @return string
     */
    private function getQuantityAndModelString()
    {
        return (is_null($this->quantity) ? '' : $this->quantity . ' x ') . 
        (empty($this->model) && !empty($this->quantity) ? 'auto ' : $this->model . ' ');
    }

    /**
     * Inserts selected load cars to database
     *
     * @param Load $load Current load
     * @param array $selectedCars Selected load cars
     * @return boolean|integer Number of rows affected by the execution or false if data is invalid
     */
    public function create(Load $load, $selectedCars = [])
    {
        $this->load = $load;
        $cars = $this->getCarsBatchInsert($selectedCars, $load->id);
        if ($cars) {
            return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $this->attributes(), $cars)->execute();
        }
        if ($load->type == Load::TYPE_FULL) {
            return true;
        }
        if (empty($cars)) {
            return false;
        }
        return true;
    }

    /**
     * Generates and returns load cars for batch insert
     *
     * @param array $selectedCars Selected load cars
     * @param null|integer $loadId Current load ID
     * @return array|boolean Generated load cars or false if data is invalid
     */
    private function getCarsBatchInsert($selectedCars = [], $loadId = null)
    {
        $cars = [];
        $quantityCount = 0;
        $this->scenario = self::SCENARIO_ANNOUNCE_SERVER;
        foreach ($selectedCars as $car) {
            $this->load_id = $loadId;
            $this->quantity = $car['quantity'];
            if ($this->load->isTypeFull() && $this->load->isPaymentMethodForCarModel()) {
                $this->quantity = self::DEFAULT_QUANTITY;
            }
            $this->model = $car['model'];
            $this->price = $this->load->isPaymentMethodForCarModel() ? str_replace(',', '.', $car['price']) : self::DEFAULT_PRICE;
            $this->state = $car['state'];
            $this->created_at = time();
            $this->updated_at = time();
            $quantityCount += $this->quantity;
            if ($this->isEmpty()) {
                continue;
            }
            if (!$this->validate() || $quantityCount > self::QUANTITY_MAX_VALUE) {
                $this->scenario = self::SCENARIO_ANNOUNCE_CLIENT;
                return false;
            }
            array_push($cars, $this->attributes);
        }
        return $cars;
    }

    /**
     * Checks whether load car is empty
     *
     * @return boolean
     */
    private function isEmpty()
    {
        if ($this->load->isTypeFull() && $this->load->isPaymentMethodForCarModel()) {
            return $this->model == '' && $this->price == '' && $this->state == '';
        }
        if ($this->load->isTypeFull() && $this->load->isPaymentMethodForAllLoad()) {
            return $this->quantity == '' && $this->model == '' && $this->state == '';
        }
        if ($this->load->isTypePartial()) {
            return $this->quantity == '' && $this->model == '';
        }
        return true;
    }

    /**
     * Counts how many cars has load
     *
     * @param array $cars List of cars
     * @return integer Number of cars
     */
    public static function countCarsQuantities($cars = [])
    {
        $quantities = 0;
        foreach ($cars as $car) {
            $quantities += $car['quantity'];
        }
        return $quantities;
    }

    /**
     * Checks whether cars were added, updated or deleted
     *
     * @param array $oldCars List of old load cars
     * @param array $newCars List of new load cars
     * @return array
     */
    public static function hasChanges($oldCars, $newCars)
    {
        $isAdded = false;
        $isUpdated = false;
        $isDeleted = false;
        foreach ($oldCars as $oldCar) {
            $oldCarAttributes = $oldCar->attributes;
            foreach ($newCars as $newCar) {
                if ($oldCarAttributes['id'] == $newCar['id'] && self::isCarsUpdated($oldCarAttributes, $newCar)) {
                    $isUpdated = true;
                }
            }
        }

        return compact('isAdded', 'isUpdated', 'isDeleted');
    }

    /**
     * Checks whether cars were updated
     *
     * @param array $oldCar Old information about the car
     * @param array $newCar New information about the car
     * @return boolean
     */
    private static function isCarsUpdated($oldCar, $newCar)
    {
        $attributes = ['quantity', 'model', 'price', 'state'];
        foreach ($attributes as $attribute) {
            if ($oldCar[$attribute] != $newCar[$attribute]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether load cars exceeded quantity
     *
     * @return boolean
     */
    public function exceededQuantity()
    {
        return $this->quantity > self::QUANTITY_MAX_VALUE;
    }
}
