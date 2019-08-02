<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%load_service}}".
 *
 * @property integer $user_id
 * @property integer $search_radius
 * @property string $token
 * @property integer $date
 * @property integer $quantity
 * @property integer $load
 * @property integer $unload
 * @property  $created_at
 * @property  $updated_at
 */

class LoadSuggestion extends ActiveRecord
{   
    /** scenario for saving suggestions */
    const SCENARIO_SAVE_SUGGESTION = 'scenario-save-suggestion';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%load_suggestion}}';
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_SAVE_SUGGESTION => [
                'user_id',
                'search_radius',
                'date',
                'quantity',
                'load',
                'unload',
                'token',
                'created_at',
                'updated_at',
            ],
        ];
    }
    
    public function rules()
    {
        return [
            // user_id
            ['user_id', 'required', 'message' => Yii::t('app', 'USER_ID_IS_REQUIRED')],
            ['user_id', 'integer', 'message' => Yii::t('app', 'USER_ID_IS_INTEGER')],
            
            // search_radius
            ['search_radius', 'required', 'message' => Yii::t('app', 'LOAD_ID_IS_REQUIRED')],
            ['search_radius', 'integer', 'message' => Yii::t('app', 'LOAD_ID_IS_INTEGER')],
            
            // date
            ['date', 'required', 'message' => Yii::t('app', 'LOAD_ID_IS_REQUIRED')],
            ['date', 'integer', 'message' => Yii::t('app', 'LOAD_ID_IS_INTEGER')],
            
            // quantity
            ['quantity', 'required', 'message' => Yii::t('app', 'LOAD_ID_IS_REQUIRED')],
            ['quantity', 'integer', 'message' => Yii::t('app', 'LOAD_ID_IS_INTEGER')],
            
            // load
            ['load', 'required', 'message' => Yii::t('app', 'LOAD_ID_IS_REQUIRED')],
            ['load', 'integer', 'message' => Yii::t('app', 'LOAD_ID_IS_INTEGER')],
            
            // unload
            ['unload', 'required', 'message' => Yii::t('app', 'LOAD_ID_IS_REQUIRED')],
            ['unload', 'integer', 'message' => Yii::t('app', 'LOAD_ID_IS_INTEGER')],
            
            // token
            ['token', 'required', 'message' => Yii::t('app', 'TOKEN_IS_REQUIRED')],
            ['token', 'string', 'message' => Yii::t('app', 'TOKEN_MUST_BE_STRING')],
            
            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'SUGGESTION_CREATED_AT_IS_NOT_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'SUGGESTION_UPDATED_AT_IS_NOT_INTEGER')],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'USER_LABEL_CODE'),
            'search_radius' => Yii::t('app', 'SEARCH_RADIUS_LABEL_CODE'),
            'date' => Yii::t('app', 'DATE_LABEL_CODE'),
            'quantity' => Yii::t('app', 'QUANTITY_LABEL_CODE'),
            'load' => Yii::t('app', 'LOAD_LABEL_CODE'),
            'unload' => Yii::t('app', 'UNLOAD_LABEL_CODE'),
            'token' => Yii::t('app', 'TOKEN_LABEL_NAME'),
            'created_at' => Yii::t('app', 'SUGGESTION_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'SUGGESTION_UPDATED_AT_LABEL'),
        ];
    }
    
    /**
     * Saves not seen suggestions to database
     * 
     * @param string $token random string to identify for whom email is sent
     * @param array $loadSuggestion info about suggestion taken from elastic search
     */
    public function saveSuggestions($token = '', $loadSuggestion = null) 
    {
        $this->scenario = self::SCENARIO_SAVE_SUGGESTION;
        $this->user_id = $loadSuggestion['user_id'];
        $this->search_radius = $loadSuggestion['search_radius'];
        $this->date = $loadSuggestion['date'];
        $this->quantity = $loadSuggestion['quantity'];
        $this->load = $loadSuggestion['load'];
        $this->unload = $loadSuggestion['unload'];
        $this->token = $token;
        $this->created_at = time();
        $this->updated_at = time();
        $this->save();
    }
}