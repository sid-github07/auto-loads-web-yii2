<?php

namespace common\models;

use common\components\document\DocumentFactory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%company_document}}".
 *
 * @property integer $id
 * @property integer $company_id
 * @property integer $date
 * @property integer $type
 * @property string $extension
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Company $company
 */
class CompanyDocument extends ActiveRecord
{
    /** @const integer Document type is CMR */
    const CMR = 0;

    /** @const integer Document type is EU */
    const EU = 1;

    /** @const integer Document type is IM */
    const IM = 2;
    
    /** @const integer Document state  */
    const ACTIVE = 1;
    
    /** @const integer Document state */
    const NOT_ACTIVE = 0;

    /** @const string List of allowed extensions for document */
    const DOCUMENT_EXTENSIONS = 'pdf';

    /** @const integer Maximum number of documents that can be uploaded at once */
    const DOCUMENT_MAX_FILES = 1;

    /** @const integer Maximum number of bytes that document can contain */
    const DOCUMENT_MAX_SIZE = 10485760; // 10 MB

    /** @const integer Maximum number of kilobytes that document can contain */
    const DOCUMENT_MAX_SIZE_KB = 10000; // kilobytes

    /** @const string List of allowed MIME types for document */
    const DOCUMENT_MIME_TYPES = 'application/pdf';

    /** @const integer Maximum length of document extension */
    const MAX_EXTENSION_LENGTH = 255;
    
    /** @const integer option in extended client search*/
    const HAS_EXPIRED_DOCUMENTS = 0;
    
    /** @const integer option in extended client search */
    const NO_EXPIRED_DOCUMENTS = 1;

    /** @const string Model scenario when company owner uploads CMR document */
    const SCENARIO_CLIENT_CMR = 'client-cmr';

    /** @const string Model scenario when company owner uploads EU document */
    const SCENARIO_CLIENT_EU = 'client-eu';

    /** @const string Model scenario when company owner upload IM document */
    const SCENARIO_CLIENT_IM = 'client-im';

    /** @const string Model scenario when company owners uploaded document must be saved to database */
    const SCENARIO_SERVER = 'server';
    
    /** @const string Model scenario when administrator uses extended search filter to find clients  */
    const SCENARIO_EXTENDED_CLIENT_SEARCH = 'extended-client-search';

    /** @const string Model scenario when system migrates company documents data from one database to another */
    const SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS_DATA = 'system-migrates-company-documents-data';

    const SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS = 'system-migrates-company-documents';

    /** @var object CMR document */
    public $cmr;

    /** @var string CMR document valid date */
    public $dateCMR;

    /** @var object EU document */
    public $eu;

    /** @var string EU document valid date */
    public $dateEU;

    /** @var object IM document */
    public $im;

    /** @var string IM document valid date */
    public $dateIM;
    
    /** @var integer valid option */
    public $documentActivity;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CLIENT_CMR => [
                'cmr',
                'dateCMR',
            ],
            self::SCENARIO_CLIENT_EU => [
                'eu',
                'dateEU',
            ],
            self::SCENARIO_CLIENT_IM => [
                'im',
                'dateIM',
            ],
            self::SCENARIO_SERVER => [
                'company_id',
                'date',
                'type',
                'extension',
            ],
            self::SCENARIO_EXTENDED_CLIENT_SEARCH => [
                'documentActivity',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS_DATA => [
                'id',
                'company_id',
                'date',
                'type',
                'extension',
                'created_at',
                'updated_at',
            ],
            self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS => [
                'id',
                'company_id',
                'date',
                'type',
                'extension',
                'created_at',
                'updated_at',
            ],
        ];
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
    public static function tableName()
    {
        return '{{%company_document}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Company ID
            ['company_id', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_COMPANY_ID_IS_REQUIRED')],
            ['company_id', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_COMPANY_IS_INTEGER')],
            ['company_id', 'exist', 'targetClass' => Company::className(),
                                'targetAttribute' => ['company_id' => 'id'],
                                        'message' => Yii::t('app', 'COMPANY_DOCUMENT_COMPANY_ID_NOT_EXIST')],

            // Date
            ['date', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_REQUIRED'),
                                   'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
            ['date', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IS_INTEGER'),
                                   'except' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],

            // Type
            ['type', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_TYPE_IS_REQUIRED')],
            ['type', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_TYPE_IS_INTEGER')],
            ['type', 'in', 'range' => [self::CMR, self::EU, self::IM],
                         'message' => Yii::t('app', 'COMPANY_DOCUMENT_TYPE_IS_NOT_IN_RANGE')],

            // Extension
            ['extension', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_EXTENSION_IS_REQUIRED')],
            ['extension', 'string', 'max' => self::MAX_EXTENSION_LENGTH,
                                'tooLong' => Yii::t('app', 'COMPANY_DOCUMENT_EXTENSION_IS_TOO_LONG', [
                                    'length' => self::MAX_EXTENSION_LENGTH,
                                ])],
            ['extension', 'in', 'range' => [self::DOCUMENT_EXTENSIONS],
                              'message' => Yii::t('app', 'COMPANY_DOCUMENT_EXTENSION_IS_NOT_IN_RANGE'),
                'except' => self::SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_CREATED_AT_IS_INTEGER')],

            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_UPDATED_AT_IS_INTEGER')],

            // CMR
            ['cmr', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_CMR_IS_REQUIRED')],
            ['cmr', 'file', 'extensions' => [self::DOCUMENT_EXTENSIONS],
                        'wrongExtension' => Yii::t('app', 'COMPANY_DOCUMENT_CMR_WRONG_EXTENSION', [
                            'extensions' => self::DOCUMENT_EXTENSIONS,
                        ]),

                              'maxFiles' => self::DOCUMENT_MAX_FILES,
                               'tooMany' => Yii::t('app', 'COMPANY_DOCUMENT_CMR_TOO_MANY', [
                                   'maxFiles' => self::DOCUMENT_MAX_FILES,
                               ]),

                               'maxSize' => self::DOCUMENT_MAX_SIZE,
                                'tooBig' => Yii::t('app', 'COMPANY_DOCUMENT_CMR_TOO_BIG', [
                                    'maxSize' => self::convertFileSize(self::DOCUMENT_MAX_SIZE),
                                ]),

                             'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                         'wrongMimeType' => Yii::t('app', 'COMPANY_DOCUMENT_CMR_WRONG_MIME_TYPE', [
                             'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                         ])],

            // Date CMR
            ['dateCMR', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_CMR_IS_REQUIRED')],
            ['dateCMR', 'match', 'pattern' => '/^([1][9]|[2][0])[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/',
                                 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_CMR_IS_NOT_MATCH')],

            // EU
            ['eu', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_EU_IS_REQUIRED')],
            ['eu', 'file', 'extensions' => [self::DOCUMENT_EXTENSIONS],
                       'wrongExtension' => Yii::t('app', 'COMPANY_DOCUMENT_EU_WRONG_EXTENSION', [
                           'extensions' => self::DOCUMENT_EXTENSIONS,
                       ]),

                             'maxFiles' => self::DOCUMENT_MAX_FILES,
                              'tooMany' => Yii::t('app', 'COMPANY_DOCUMENT_EU_TOO_MANY', [
                                  'maxFiles' => self::DOCUMENT_MAX_FILES,
                              ]),

                              'maxSize' => self::DOCUMENT_MAX_SIZE,
                               'tooBig' => Yii::t('app', 'COMPANY_DOCUMENT_EU_TOO_BIG', [
                                   'maxSize' => self::DOCUMENT_MAX_SIZE,
                               ]),

                            'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                        'wrongMimeType' => Yii::t('app', 'COMPANY_DOCUMENT_EU_WRONG_MIME_TYPE', [
                            'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                        ])],

            // Date EU
            ['dateEU', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_EU_IS_REQUIRED')],
            ['dateEU', 'match', 'pattern' => '/^([1][9]|[2][0])[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/',
                                'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_EU_IS_NOT_MATCH')],

            // IM
            ['im', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_IM_IS_REQUIRED')],
            ['im', 'file', 'extensions' => [self::DOCUMENT_EXTENSIONS],
                       'wrongExtension' => Yii::t('app', 'COMPANY_DOCUMENT_IM_WRONG_EXTENSION', [
                           'extensions' => self::DOCUMENT_EXTENSIONS,
                       ]),

                             'maxFiles' => self::DOCUMENT_MAX_FILES,
                              'tooMany' => Yii::t('app', 'COMPANY_DOCUMENT_IM_TOO_MANY', [
                                  'maxFiles' => self::DOCUMENT_MAX_FILES
                              ]),

                              'maxSize' => self::DOCUMENT_MAX_SIZE,
                               'tooBig' => Yii::t('app', 'COMPANY_DOCUMENT_IM_TOO_BIG', [
                                   'maxSize' => self::DOCUMENT_MAX_SIZE,
                               ]),

                            'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                        'wrongMimeType' => Yii::t('app', 'COMPANY_DOCUMENT_IM_WRONG_MIME_TYPE', [
                            'mimeTypes' => self::DOCUMENT_MIME_TYPES,
                        ])],

            // Date IM
            ['dateIM', 'required', 'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IM_IS_REQUIRED')],
            ['dateIM', 'match', 'pattern' => '/^([1][9]|[2][0])[0-9]{2}[-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1])$/',
                                'message' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_IM_NOT_MATCH')],
            
            // Document Activity
            ['documentActivity', 'in', 'range' => [self::NO_EXPIRED_DOCUMENTS, self::HAS_EXPIRED_DOCUMENTS],
                              'message' => Yii::t('app', 'COMPANY_DOCUMENT_ACTIVITY_IS_NOT_IN_RANGE'),
                              'on' => self::SCENARIO_EXTENDED_CLIENT_SEARCH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => Yii::t('app', 'COMPANY_DOCUMENT_COMPANY_ID_LABEL'),
            'date' => Yii::t('app', 'COMPANY_DOCUMENT_DATE_LABEL'),
            'type' => Yii::t('app', 'COMPANY_DOCUMENT_TYPE_LABEL'),
            'created_at' => Yii::t('app', 'COMPANY_DOCUMENT_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'COMPANY_DOCUMENT_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Finds company documents by company ID
     *
     * @param null|integer $companyId Company ID
     * @return static[]
     */
    public static function findByCompany($companyId = null)
    {
        $subquery = self::find()
                ->select('MAX(created_at) MaxPostDate, company_id, type')
                ->where(['extension' => 'pdf'])
                ->andWhere(['company_id' => $companyId])
                ->groupBy('company_id, type');
                
        return $mainquery = self::find()
                ->from(CompanyDocument::tableName() . ' as p1')
                ->innerJoin(['p2' => $subquery], 'p1.company_id = p2.company_id AND p1.created_at = p2.MaxPostDate AND p1.type = p2.type')
                ->where(['p1.extension' => 'pdf'])
                ->andWhere(['p1.company_id' => $companyId])
                ->orderBy(['p1.created_at' => SORT_DESC])->all();
    }

    /**
     * Finds current user company document entry by document type
     *
     * @param null|string $type Document type
     * @return array|null|ActiveRecord
     * @throws NotFoundHttpException If company document not found
     */
    public static function findCurrentUserByType($type = null)
    {
        $model = self::find()
            ->innerJoin(Company::tableName(), Company::tableName() . '.id = ' . self::tableName() . '.company_id')
            ->innerJoin(User::tableName(), User::tableName() . '.id = ' . Company::tableName() . '.owner_id')
            ->where(User::tableName() . '.id = ' . Yii::$app->getUser()->getId())
            ->andWhere(['type' => self::getTypeConstByTypeString($type)])
            ->andWhere(['extension' => 'pdf'])    
            ->one();
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'NOT_FOUND_COMPANY_DOCUMENT_BY_TYPE'));
        }
        return $model;
    }
    
    /**
     * Finds current user company document entry by document type
     *
     * @param null|string $type Document type
     * @return array|null|ActiveRecord
     * @throws NotFoundHttpException If company document not found
     */
    public static function findCurrentCompanyByType($type = null, $companyId = null)
    {
        $model = self::find()
            ->innerJoin(Company::tableName(), Company::tableName() . '.id = ' . self::tableName() . '.company_id')
            ->innerJoin(User::tableName(), User::tableName() . '.id = ' . Company::tableName() . '.owner_id')
            ->where(Company::tableName() . '.id = ' . $companyId)
            ->andWhere(['type' => self::getTypeConstByTypeString($type)])
            ->andWhere(['extension' => 'pdf'])    
            ->one();
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'NOT_FOUND_COMPANY_DOCUMENT_BY_TYPE'));
        }
        return $model;
    }

    /**
     * Converts bytes to kilobytes/megabytes/gigabytes
     *
     * @param null|integer $bytes Bytes that needs to be converted
     * @return null|string
     */
    public static function convertFileSize($bytes = null)
    {
        if (is_null($bytes) || !is_int($bytes)) {
            return null;
        }

        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' ' . Yii::t('app', 'BYTES');
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' ' . Yii::t('app', 'BYTE');
        } else {
            $bytes = '0 ' . Yii::t('app', 'BYTES');
        }

        return $bytes;
    }

    /**
     * Returns document type constant by given document type in string
     *
     * @param string $type Document type
     * @return integer|null
     */
    public static function getTypeConstByTypeString($type = '')
    {
        switch ($type) {
            case DocumentFactory::CMR:
                return self::CMR;
                break;
            case DocumentFactory::EU:
                return self::EU;
                break;
            case DocumentFactory::IM:
                return self::IM;
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Deletes company documents by provided document type
     *
     * @param null|integer $companyId Company ID
     * @param null|string $type Document type
     * @return integer Number of deleted rows
     */
    public static function deleteByType($companyId = null, $type = null)
    {
        return self::deleteAll([
            'company_id' => $companyId,
            'type' => self::getTypeConstByTypeString($type),
        ]);
    }

    /**
     * Creates new company document entry
     *
     * @param null|integer $companyId Company ID
     * @param string $date Document date of expiry
     * @param null|string $type Document type
     * @param null|string $extension Document extension
     * @return boolean Whether entry was created successfully
     */
    public static function create($companyId = null, $date = '', $type = null, $extension = null)
    {
        $model = new self(['scenario' => self::SCENARIO_SERVER]);
        $model->setAttribute('company_id', $companyId);
        $model->setAttribute('date', strtotime($date));
        $model->setAttribute('type', self::getTypeConstByTypeString($type));
        $model->setAttribute('extension', $extension);
        return $model->save();
    }
    
    /**
     * Returns list of all translated document activity options, extended search
     *
     * @return array
     */
    public function getTranslatedDocumentActivity() 
    {
        return [
            self::NO_EXPIRED_DOCUMENTS => Yii::t('app', 'DOCUMENT_LABEL_ACTIVITY_ACTIVE'),
            self::HAS_EXPIRED_DOCUMENTS => Yii::t('app', 'DOCUMENT_LABEL_ACTIVITY_EXPIRED'),
        ];
    }

    /**
     * Checks whether company document type is CMR
     *
     * @return boolean
     */
    public function isCMR()
    {
        return $this->type == self::CMR;
    }

    /**
     * Converts company document type to text
     *
     * @param integer $type Company document type
     * @return null|string
     */
    public static function convertTypeToString($type)
    {
        switch ($type) {
            case self::CMR:
                return DocumentFactory::CMR;
                break;
            case self::EU:
                return DocumentFactory::EU;
                break;
            case self::IM:
                return DocumentFactory::IM;
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Returns company type name
     *
     * @return null|string
     */
    public function getCompanyTypeName()
    {
        return self::convertTypeToString($this->type);
    }
}
