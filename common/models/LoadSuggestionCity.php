<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%load_service}}".
 *
 * @property integer $user_id
 * @property integer $load_id
 * @property string $token
 * @property  $created_at
 * @property  $updated_at
 */

class LoadSuggestionCity extends ActiveRecord
{   
    /** scenario for saving suggestions */
    const SCENARIO_SAVE_SUGGESTION_BY_CITY = 'scenario-save-suggestion_city';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%load_suggestion_city}}';
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_SAVE_SUGGESTION_BY_CITY => [
                'user_id',
                'load_id',
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
            
            // load_id
            ['load_id', 'required', 'message' => Yii::t('app', 'USER_ID_IS_REQUIRED')],
            ['load_id', 'integer', 'message' => Yii::t('app', 'USER_ID_IS_INTEGER')],
            
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
     * Removes seen suggestions from suggestions list
     * 
     * @param array $suggestedLoadsByCity loads from loads elastic search
     * @param integer $user_id user to who will be removed seen suggestions
     * @return array $suggestedLoadsByCity
     */
    public static function removeSuggestionsSeenByUser(&$suggestedLoadsByCity, $user_id)
    {
        $seenSuggestionsByUser = self::find()->where(['user_id' => $user_id])->all();

        $exist = false;
        
        foreach($seenSuggestionsByUser as $indexy => $suggestion) {
            foreach($suggestedLoadsByCity as $index => $loadId) {
                if (ArrayHelper::keyExists($suggestion->load_id, $loadId, false)) {
                    $exist = true;
                    break;
                }
            }
            if ($exist) {
                unset($suggestedLoadsByCity[$index]);
                $exist = false;
            }
        }
        return;
    }
    
    /**
     * Saves suggestions by user login city to database
     * 
     * @param integer $user_id specifik user to whom load suggestions will be saved
     * @param string $token random string token to identify email receiver
     * @param array $loadSuggestion
     */
    public function saveSuggestionsByCity($user_id, $token = '', $loadSuggestion = null) 
    {
        $this->scenario = self::SCENARIO_SAVE_SUGGESTION_BY_CITY;
        $this->user_id = $user_id;
        $this->load_id = key($loadSuggestion);
        $this->token = $token;
        $this->created_at = time();
        $this->updated_at = time();
        $this->save();
    }
}

