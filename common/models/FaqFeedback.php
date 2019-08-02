<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%faq_feedback}}".
 *
 * @property integer $id
 * @property string $question
 * @property string $email
 * @property string $comment
 * @property integer $solved
 * @property integer $created_at
 * @property integer $updated_at
 */
class FaqFeedback extends ActiveRecord
{
    /** @const integer Maximum length of client email */
    const EMAIL_MAX_LENGTH = 255;

    /** @const integer Minimum length of client comment/question */
    const COMMENT_MIX_LENGTH = 2;

    /** @const integer Maximum length of client comment/question */
    const COMMENT_MAX_LENGTH = 1000;

    /** @const boolean Default attribute value whether problem was solved / question was answered */
    const DEFAULT_SOLVED_VALUE = false;

    /** @const boolean FAQ feedback question is solved */
    const SOLVED = true;

    /** @const boolean FAQ feedback question is not resolved */
    const NOT_RESOLVED = false;

    /** @const string Client side scenario */
    const SCENARIO_CLIENT_SIDE = 'client-side';

    /** @const string Server side scenario */
    const SCENARIO_SERVER_SIDE = 'server-side';

    /** @const string Model scenario when system migrates FAQ feedbacks data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_FAQ_FEEDBACKS_DATA = 'system-migrates-faq-feedbacks-data';

    const SCENARIO_SYSTEM_MIGRATES_FAQ_FEEDBACK = 'system-migrates-faq-feedback';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%faq_feedback}}';
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
    public function rules()
    {
        return [
            // Question
            ['question', 'required', 'message' => Yii::t('app', 'FAQ_FEEDBACK_QUESTION_IS_REQUIRED')],
            ['question', 'in', 'range' => self::getFaqQuestionsPlaceholders()],

            // Email
            ['email', 'required', 'message' => Yii::t('app', 'FAQ_FEEDBACK_EMAIL_IS_REQUIRED')],
            ['email', 'email', 'message' => Yii::t('app', 'FAQ_FEEDBACK_EMAIL_IS_EMAIL')],
            ['email', 'string', 'max' => self::EMAIL_MAX_LENGTH,
                            'tooLong' => Yii::t('app', 'FAQ_FEEDBACK_EMAIL_IS_TOO_LONG', [
                                'length' => self::EMAIL_MAX_LENGTH,
                            ])],
            ['email', 'filter', 'filter' => 'trim'],

            // Comment
            ['comment', 'required', 'message' => Yii::t('app', 'FAQ_FEEDBACK_COMMENT_IS_REQUIRED')],
            ['comment', 'string', 'min' => self::COMMENT_MIX_LENGTH, 
                             'tooShort' => Yii::t('app', 'FAQ_FEEDBACK_COMMENT_IS_TOO_SHORT', [
                                 'length' => self::COMMENT_MIX_LENGTH,
                             ]), 
                                  'max' => self::COMMENT_MAX_LENGTH,
                              'tooLong' => Yii::t('app', 'FAQ_FEEDBACK_COMMENT_IS_TOO_LONG', [
                                  'length' => self::COMMENT_MAX_LENGTH,
                              ])],
            
            // Solved
            ['solved', 'required', 'message' => Yii::t('app', 'FAQ_FEEDBACK_SOLVED_IS_REQUIRED')],
            ['solved', 'boolean', 'message' => Yii::t('app', 'FAQ_FEEDBACK_SOLVED_IS_BOOLEAN')],
            ['solved', 'default', 'value' => self::DEFAULT_SOLVED_VALUE],
            
            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'FAQ_FEEDBACK_CREATED_AT_IS_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'FAQ_FEEDBACK_UPDATED_AT_IS_INTEGER')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CLIENT_SIDE => ['email', 'comment'],
            self::SCENARIO_SERVER_SIDE => ['question', 'email', 'comment', 'solved', 'created_at', 'updated_at'],
            self::SCENARIO_SYSTEM_MIGRATES_FAQ_FEEDBACKS_DATA => [
                'id',
                'question',
                'email',
                'comment',
                'solved',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_FAQ_FEEDBACK => [
                'id',
                'question',
                'email',
                'comment',
                'solved',
                'created_at',
                'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'FAQ_FEEDBACK_LABEL_ID'),
            'question' => Yii::t('app', 'FAQ_FEEDBACK_LABEL_QUESTION'),
            'email' => Yii::t('element', 'DUK-AP-3b'),
            'comment' => Yii::t('element', 'DUK-AP-3d'),
            'solved' => Yii::t('app', 'FAQ_FEEDBACK_LABEL_SOLVED'),
            'created_at' => Yii::t('app', 'FAQ_FEEDBACK_LABEL_CREATED_AT'),
            'updated_at' => Yii::t('app', 'FAQ_FEEDBACK_LABEL_UPDATED_AT'),
        ];
    }

    /**
     * Returns FAQ questions placeholders
     *
     * @note All these placeholders MUST be described in translated files.
     * If any of these placeholders is removed from translates files, it also MUST be removed from this array
     * @return array
     */
    public static function getFaqQuestionsPlaceholders()
    {
        return [
            'WANT_TRANSPORT_CAR',
            'CANT_FIND_TRANSPORTER',
            'TRANSPORTATION_COST',
            'SERVICES_COST',
            'WHERE_PAY_FOR_SERVICES',
            'WHEN_USE_SERVICES',
            'CANT_USE_SERVICE_AFTER_PAY',
            'MEMBERSHIP_EXTENSION',
            'CHANGE_COMPANY_DATA',
            'CHANGE_MY_DATA',
            'DELETE_MY_DATA',
            'FORGOT_PASSWORD',
            'FORGOT_LOGIN_NAME',
            'CANNOT_RECEIVE_TEMPORARY_PASSWORD',
            'CANNOT_CONNECT_NO_SUCH_USER',
            'NOT_WORKING_AUTO-LOADS',
            'CANNOT_GET_CONTACTS_VIA_SMS',
            'HOW_ANNOUNCE_LOAD',
            'HOW_ANNOUNCE_TRANSPORTER',
            'HOW_EDIT_AD',
            'HOW_DELETE_AD',
            'HOW_REGISTER_TO_AUTO-LOADS',
        ];
    }

    /**
     * Returns translated "Yes"/"No" values by given solve value
     *
     * @param boolean $solveValue Attribute, whether problem/question is solved/answered
     * @return string
     */
    public static function getTranslatedSolveValues($solveValue)
    {
        return ($solveValue) ? Yii::t('app', 'FAQ_FEEDBACK_IS_SOLVED') : Yii::t('app', 'FAQ_FEEDBACK_IS_NOT_SOLVED');
    }

    /**
     * Creates new FAQ feedback entry
     *
     * @param string $question Question placeholder, that client is commenting
     * @return boolean Whether entry saved successfully
     */
    public function create($question)
    {
        $this->question = $question;
        $this->solved = static::DEFAULT_SOLVED_VALUE;
        $this->scenario = self::SCENARIO_SERVER_SIDE;
        return $this->save();
    }

    /**
     * Sends client filled FAQ feedback form question/problem to admins email
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendEmailToAdmin()
    {
        return Yii::$app->mailer->compose('faq-feedback/admin', [
                                    'serialNumber' => $this->id,
                                    'question' => Yii::t('duk', $this->question),
                                    'clientEmail' => $this->email,
                                    'isSolved' => static::getTranslatedSolveValues($this->solved),
                                    'comment' => $this->comment,
                                ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo(Yii::$app->params['adminEmail'])
                                ->setSubject(Yii::t('mail', 'FAQ_FEEDBACK_ADMIN_SUBJECT', [
                                    'serialNumber' => $this->id,
                                    'question' => Yii::t('duk', $this->question),
                                ]))
                                ->send();
    }

    /**
     * Informs client that FAQ feedback form was successfully registered
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendEmailToClient()
    {
        return Yii::$app->mailer->compose('faq-feedback/client', [
                                    'serialNumber' => $this->id,
                                    'question' => Yii::t('duk', $this->question),
                                    'companyName' => Yii::$app->params['companyName'],
                                    'comment' => $this->comment,
                                ])
                                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
                                ->setTo($this->email)
                                ->setSubject(Yii::t('mail', 'FAQ_FEEDBACK_CLIENT_SUBJECT', [
                                    'serialNumber' => $this->id,
                                    'question' => Yii::t('duk', $this->question),
                                ]))
                                ->send();
    }
    
    /**
     * Informs admin about bug on the website
     * 
     * @return boolean Whether mail was sent successfully
     */
    public function sendToAdmin()
    {
        return Yii::$app->mailer->compose('faq-feedback/bug', [
            'comment' => $this->comment,
        ])
            ->setFrom($this->email)
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Klaida svetainėje')
            ->send();
    }
}
