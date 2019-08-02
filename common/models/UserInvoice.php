<?php

namespace common\models;

use common\models\ServiceType;
use common\components\invoice\InvoiceDirector;
use kartik\mpdf\Pdf;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use \DOMDocument;

/**
 * This is the model class for table "{{%user_invoice}}".
 *
 * @property integer $id
 * @property integer $user_service_id
 * @property integer $type
 * @property string $number
 * @property integer $date
 * @property string $seller_company_name
 * @property string $seller_company_code
 * @property string $seller_vat_code
 * @property string $seller_address
 * @property string $seller_bank_name
 * @property string $seller_bank_code
 * @property string $seller_swift
 * @property string $seller_bank_account
 * @property integer $buyer_id
 * @property string $buyer_title
 * @property string $buyer_code
 * @property string $buyer_vat_code
 * @property string $buyer_address
 * @property integer $buyer_city_id
 * @property string $buyer_phone
 * @property string $buyer_email
 * @property string $product_name
 * @property string $netto_price
 * @property string $discount
 * @property string $vat
 * @property integer $days_to_pay
 * @property string $invoiced_by_position
 * @property string $invoiced_by_name_surname
 * @property string $file_name
 * @property string $file_extension
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property City $buyerCity
 * @property UserService $userService
 * @property Company $buyer
 */
class UserInvoice extends ActiveRecord
{
    /** @const integer Document type is pre-invoice */
    const PRE_INVOICE = 0;

    /** @const integer Document type is invoice */
    const INVOICE = 1;

    /** @const integer Maximum number of characters that type can contain */
    const MAX_TYPE_LENGTH = 1;

    /** @const integer Maximum number of characters that number can contain */
    const MAX_NUMBER_LENGTH = 255;

    /** @const integer Maximum number of characters that seller company name can contain */
    const MAX_SELLER_COMPANY_NAME_LENGTH = 255;

    /** @const integer Maximum number of characters that seller company code can contain */
    const MAX_SELLER_COMPANY_CODE_LENGTH = 255;

    /** @const integer Maximum number of characters that seller company VAT code can contain */
    const MAX_SELLER_COMPANY_VAT_CODE_LENGTH = 255;

    /** @const integer Maximum number of characters that seller address can contain */
    const MAX_SELLER_ADDRESS_LENGTH = 255;

    /** @const integer Maximum number of characters that seller bank name can contain */
    const MAX_SELLER_BANK_NAME_LENGTH = 255;

    /** @const integer Maximum number of characters that seller bank code can contain */
    const MAX_SELLER_BANK_CODE_LENGTH = 255;

    /** @const integer Maximum number of characters that seller SWIFT code can contain */
    const MAX_SELLER_SWIFT_LENGTH = 255;

    /** @const integer Maximum number of characters that seller bank account can contain */
    const MAX_SELLER_BANK_ACCOUNT_LENGTH = 255;

    /** @const integer Maximum number of characters that buyer title can contain */
    const MAX_BUYER_TITLE_LENGTH = 255;

    /** @const null Default buyer code value */
    const DEFAULT_BUYER_CODE = null;

    /** @const integer Maximum number of characters that buyer code can contain */
    const MAX_BUYER_CODE_LENGTH = 255;

    /** @const null Default buyer VAT code value */
    const DEFAULT_BUYER_VAT_CODE = null;

    /** @const integer Maximum number of characters that buyer VAT code can contain */
    const MAX_BUYER_VAT_CODE_LENGTH = 255;

    /** @const integer Maximum number of characters that buyer address can contain */
    const MAX_BUYER_ADDRESS_LENGTH = 255;

    /** @const integer Maximum number of characters that buyer phone can contain */
    const MAX_BUYER_PHONE_LENGTH = 255;

    /** @const integer Maximum number of characters that buyer email can contain */
    const MAX_BUYER_EMAIL_LENGTH = 255;

    /** @const integer Maximum number of characters that product name can contain */
    const MAX_PRODUCT_NAME_LENGTH = 255;

    /** @const integer Number of characters that netto price can contain before comma or dot */
    const NETTO_PRICE_PRECISION = 10;

    /** @const integer Number of characters that netto price can contain after comma or dot */
    const NETTO_PRICE_SCALE = 2;

    /** @const integer Number of characters that discount can contain before comma or dot */
    const DISCOUNT_PRECISION = 10;

    /** @const integer Number of characters that discount can contain after comma or dot */
    const DISCOUNT_SCALE = 2;

    /** @const null Default discount value */
    const DEFAULT_DISCOUNT = null;

    /** @const integer Number of characters that VAT can contain before comma or dot */
    const VAT_PRECISION = 5;

    /** @const integer Number of characters that VAT can contain after comma or dot */
    const VAT_SCALE = 2;

    /** @const null Default VAT value */
    const DEFAULT_VAT = null;

    /** @const null Default days to pay value */
    const DEFAULT_DAYS_TO_PAY = null;

    /** @const integer Minimum number of days within a user has to pay */
    const MIN_DAYS_TO_PAY = 0;

    /** @const integer Maximum number of characters that invoiced by position can contain */
    const MAX_INVOICED_BY_POSITION_LENGTH = 255;

    /** @const integer Maximum number of characters that invoiced by name surname can contain */
    const MAX_INVOICED_BY_NAME_SURNAME_LENGTH = 255;

    /** @const integer Maximum number of characters that file name can contain */
    const MAX_FILE_NAME_LENGTH = 255;

    /** @const integer Maximum number of characters that file extension can contain */
    const MAX_FILE_EXTENSION_LENGTH = 255;

    /** @const integer Maximum number of characters that company name can contain */
    const COMPANY_NAME_MAX_LENGTH = 255;

    /** @const integer Period type is today */
    const TODAY = 1;

    /** @const integer Period type is yesterday */
    const YESTERDAY = 2;

    /** @const integer Period type is the beginning of this month */
    const THIS_MONTH_BEGINNING = 3;

    /** @const integer Period type is the beginning of last month */
    const LAST_MONTH_BEGINNING = 4;

    /** @const integer Period type is last month */
    const LAST_MONTH = 5;

    /** @const integer Period type is this quarter */
    const THIS_QUARTER = 6;

    /** @const integer Period type is last quarter */
    const LAST_QUARTER = 7;

    /** @const integer Period type is the beginning of this year */
    const YEAR_BEGINNING = 8;

    /** @const integer First page size option */
    const FIRST_PAGE_SIZE = 10;

    /** @const integer Second page size option */
    const SECOND_PAGE_SIZE = 20;

    /** @const integer Third page size option */
    const THIRD_PAGE_SIZE = 50;

    /** @const string MIME type for invoice document */
    const DOCUMENT_MIME_TYPE = 'application/pdf';

    /** @const string Model scenario when user buys service */
    const SCENARIO_USER_BUYS_SERVICE = 'user-buys-service';

    /** @const string Model scenario when user buys service */
    const SCENARIO_USER_BUYS_CREDITCODE = 'user-buys-creditcode';

    /** @const string Model scenario when administrator filters user invoices */
    const SCENARIO_ADMIN_FILTERS_USER_INVOICES = 'admin-filters-user-invoices';
    
    /** @const string Model scenario when administrator exports user invoices xml */
    const SCENARIO_ADMIN_EXPORTS_USER_INVOICES_XML = 'admin-exports-user-invoices-xml';

    /** @const string Model scenario when administrator regenerates user invoice document */
    const SCENARIO_ADMIN_REGENERATES_USER_INVOICE = 'admin-regenerates_user_invoice';

    /** @const string Model scenario when administrator creates invoice */
    const SCENARIO_ADMIN_CREATES_INVOICE = 'admin-creates-invoice';

    /** @const string Model scenario when administrator filters planned income */
    const SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES = 'admin-filters-planned-income';

    /** @var null|string User company name */
    public $companyName;

    /** @var null|integer Period of time for user invoice filtration */
    public $period;

    /** @var null|string|integer User invoice filtration date range from attribute */
    public $dateFrom;

    /** @var null|string|integer User invoice filtration date range to attribute */
    public $dateTo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_invoice}}';
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
            self::SCENARIO_USER_BUYS_SERVICE => [
                'user_service_id',
                'type',
                'number',
                'date',
                'seller_company_name',
                'seller_company_code',
                'seller_vat_code',
                'seller_address',
                'seller_bank_name',
                'seller_bank_code',
                'seller_swift',
                'seller_bank_account',
                'buyer_id',
                'buyer_title',
                'buyer_code',
                'buyer_vat_code',
                'buyer_address',
                'buyer_city_id',
                'buyer_phone',
                'buyer_email',
                'product_name',
                'netto_price',
                'discount',
                'vat',
                'days_to_pay',
                'invoiced_by_position',
                'invoiced_by_name_surname',
                'file_name',
                'file_extension',
            ],
            self::SCENARIO_ADMIN_FILTERS_USER_INVOICES => [
                'type',
                'number',
                'companyName',
                'created_at',
                'period',
                'dateFrom',
                'dateTo',
            ],
            self::SCENARIO_ADMIN_EXPORTS_USER_INVOICES_XML => [
                'dateFrom',
                'dateTo',
            ],
            self::SCENARIO_ADMIN_REGENERATES_USER_INVOICE => [
                'number',
                'date',
                'seller_company_name',
                'seller_company_code'.
                'seller_vat_code',
                'seller_address',
                'seller_bank_name',
                'seller_bank_code',
                'seller_swift',
                'seller_bank_account',
                'buyer_title',
                'buyer_code',
                'buyer_vat_code',
                'buyer_address',
                'buyer_city_id',
                'buyer_phone',
                'buyer_email',
                'product_name',
                'netto_price',
                'discount',
                'vat',
                'days_to_pay',
                'invoiced_by_position',
                'invoiced_by_name_surname',
                'file_name',
                'file_extension',
            ],
            self::SCENARIO_ADMIN_CREATES_INVOICE => [
                'user_service_id',
                'type',
                'number',
                'date',
                'seller_company_name',
                'seller_company_code',
                'seller_vat_code',
                'seller_address',
                'seller_bank_name',
                'seller_bank_code',
                'seller_swift',
                'seller_bank_account',
                'buyer_id',
                'buyer_title',
                'buyer_code',
                'buyer_vat_code',
                'buyer_address',
                'buyer_city_id',
                'buyer_phone',
                'buyer_email',
                'product_name',
                'netto_price',
                'discount',
                'vat',
                'days_to_pay',
                'invoiced_by_position',
                'invoiced_by_name_surname',
                'file_name',
                'file_extension',
            ],
            self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES => [
                'number',
            ],
            self::SCENARIO_USER_BUYS_CREDITCODE => [
                'user_service_id',
                'type',
                'number',
                'date',
                'seller_company_name',
                'seller_company_code',
                'seller_vat_code',
                'seller_address',
                'seller_bank_name',
                'seller_bank_code',
                'seller_swift',
                'seller_bank_account',
                'buyer_id',
                'buyer_title',
                'buyer_code',
                'buyer_vat_code',
                'buyer_address',
                'buyer_city_id',
                'buyer_email',
                'product_name',
                'netto_price',
                'discount',
                'vat',
                'days_to_pay',
                'invoiced_by_position',
                'invoiced_by_name_surname',
                'file_name',
                'file_extension',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // User service ID
            ['user_service_id', 'required', 'message' => Yii::t('app', 'USER_INVOICE_USER_SERVICE_ID_IS_REQUIRED')],
            ['user_service_id', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_USER_SERVICE_ID_IS_NOT_INTEGER')],
            ['user_service_id', 'exist', 'targetClass' => UserService::className(),
                'targetAttribute' => ['user_service_id' => 'id'],
                        'message' => Yii::t('app', 'USER_INVOICE_USER_SERVICE_ID_IS_NOT_EXIST')],

            // Type
            ['type', 'required', 'message' => Yii::t('app', 'USER_INVOICE_TYPE_IS_REQUIRED'),
                'except' => self::SCENARIO_ADMIN_FILTERS_USER_INVOICES],
            ['type', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_TYPE_IS_NOT_INTEGER')],
            ['type', 'in', 'range' => array_keys(self::getTranslatedTypes()),
                'message' => Yii::t('app', 'USER_INVOICE_TYPE_NOT_IN_RANGE')],

            // Number
            ['number', 'required', 'message' => Yii::t('app', 'USER_INVOICE_NUMBER_IS_REQUIRED'),
                'except' => [
                    self::SCENARIO_ADMIN_FILTERS_USER_INVOICES,
                    self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES,
                ]],
            ['number', 'filter', 'filter' => 'trim'],
            ['number', 'string', 'max' => self::MAX_NUMBER_LENGTH,
                             'tooLong' => Yii::t('app', 'USER_INVOICE_NUMBER_IS_TOO_LONG', [
                                 'length' => self::MAX_NUMBER_LENGTH,
                             ]),
                             'message' => Yii::t('app', 'USER_INVOICE_NUMBER_IS_NOT_STRING')],
            ['number', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('app', 'USER_INVOICE_NUMBER_IS_NOT_UNIQUE'),
                 'except' => [
                     self::SCENARIO_ADMIN_FILTERS_USER_INVOICES,
                     self::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES,
                 ]],

            // Date
            ['date', 'required', 'message' => Yii::t('app', 'USER_INVOICE_DATE_IS_REQUIRED')],
            ['date', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_DATE_IS_NOT_INTEGER')],

            // Seller company name
            ['seller_company_name', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_NAME_IS_REQUIRED')],
            ['seller_company_name', 'filter', 'filter' => 'trim'],
            ['seller_company_name', 'string', 'max' => self::MAX_SELLER_COMPANY_NAME_LENGTH,
                                          'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_NAME_IS_TOO_LONG', [
                                              'length' => self::MAX_SELLER_COMPANY_NAME_LENGTH,
                                          ]),
                                          'message' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_NAME_IS_NOT_STRING')],

            // Seller company code
            ['seller_company_code', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_CODE_IS_REQUIRED')],
            ['seller_company_code', 'filter', 'filter' => 'trim'],
            ['seller_company_code', 'string', 'max' => self::MAX_SELLER_COMPANY_CODE_LENGTH,
                                          'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_CODE_IS_TOO_LONG', [
                                              'length' => self::MAX_SELLER_COMPANY_CODE_LENGTH,
                                          ]),
                                          'message' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_CODE_IS_NOT_STRING')],

            // Seller VAT code
            ['seller_vat_code', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_VAT_CODE_IS_REQUIRED')],
            ['seller_vat_code', 'filter', 'filter' => 'trim'],
            ['seller_vat_code', 'string', 'max' => self::MAX_SELLER_COMPANY_VAT_CODE_LENGTH,
                                      'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_VAT_CODE_IS_TOO_LONG', [
                                          'length' => self::MAX_SELLER_COMPANY_VAT_CODE_LENGTH,
                                      ]),
                                      'message' => Yii::t('app', 'USER_INVOICE_SELLER_VAT_CODE_IS_NOT_STRING')],
            // TODO: pridėti tikrinimą iš User modelio, ar PVM kodas yra tikras

            // Seller address
            ['seller_address', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_ADDRESS_IS_REQUIRED')],
            ['seller_address', 'filter', 'filter' => 'trim'],
            ['seller_address', 'string', 'max' => self::MAX_SELLER_ADDRESS_LENGTH,
                                     'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_ADDRESS_IS_TOO_LONG', [
                                         'length' => self::MAX_SELLER_ADDRESS_LENGTH,
                                     ]),
                                     'message' => Yii::t('app', 'USER_INVOICE_SELLER_ADDRESS_IS_NOT_STRING')],

            // Seller bank name
            ['seller_bank_name', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_NAME_IS_REQUIRED')],
            ['seller_bank_name', 'filter', 'filter' => 'trim'],
            ['seller_bank_name', 'string', 'max' => self::MAX_SELLER_BANK_NAME_LENGTH,
                                       'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_NAME_IS_TOO_LONG', [
                                           'length' => self::MAX_SELLER_BANK_NAME_LENGTH,
                                       ]),
                                       'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_NAME_IS_NOT_STRING')],

            // Seller bank code
            ['seller_bank_code', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_CODE_IS_REQUIRED')],
            ['seller_bank_code', 'filter', 'filter' => 'trim'],
            ['seller_bank_code', 'string', 'max' => self::MAX_SELLER_BANK_CODE_LENGTH,
                                       'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_CODE_IS_TOO_LONG', [
                                           'length' => self::MAX_SELLER_BANK_CODE_LENGTH,
                                       ]), 'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_CODE_IS_NOT_STRING')],

            // Seller swift
            ['seller_swift', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_SWIFT_IS_REQUIRED')],
            ['seller_swift', 'filter', 'filter' => 'trim'],
            ['seller_swift', 'string', 'max' => self::MAX_SELLER_SWIFT_LENGTH,
                                   'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_SWIFT_IS_TOO_LONG', [
                                       'length' => self::MAX_SELLER_SWIFT_LENGTH,
                                   ]),
                                   'message' => Yii::t('app', 'USER_INVOICE_SELLER_SWIFT_IS_NOT_STRING')],

            // Seller bank account
            ['seller_bank_account', 'required', 'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_ACCOUNT_IS_REQUIRED')],
            ['seller_bank_account', 'filter', 'filter' => 'trim'],
            ['seller_bank_account', 'string', 'max' => self::MAX_SELLER_BANK_ACCOUNT_LENGTH,
                                          'tooLong' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_ACCOUNT_IS_TOO_LONG', [
                                              'length' => self::MAX_SELLER_BANK_ACCOUNT_LENGTH,
                                          ]),
                                          'message' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_ACCOUNT_IS_NOT_STRING')],

            // Buyer ID
            ['buyer_id', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_ID_IS_REQUIRED')],
            ['buyer_id', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_ID_IS_NOT_INTEGER')],
            ['buyer_id', 'exist', 'targetClass' => Company::className(),
                              'targetAttribute' => ['buyer_id' => 'id'],
                                      'message' => Yii::t('app', 'USER_INVOICE_BUYER_ID_IS_NOT_EXIST')],

            // Buyer title
            ['buyer_title', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_TITLE_IS_REQUIRED')],
            ['buyer_title', 'filter', 'filter' => 'trim'],
            ['buyer_title', 'string', 'max' => self::MAX_BUYER_TITLE_LENGTH,
                                  'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_TITLE_IS_TOO_LONG', [
                                      'length' => self::MAX_BUYER_TITLE_LENGTH,
                                  ]),
                                  'message' => Yii::t('app', 'USER_INVOICE_BUYER_TITLE_IS_NOT_STRING')],

            // Buyer code
            ['buyer_code', 'filter', 'filter' => 'trim'],
            ['buyer_code', 'default', 'value' => self::DEFAULT_BUYER_CODE],
            ['buyer_code', 'string', 'max' => self::MAX_BUYER_CODE_LENGTH,
                                 'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_CODE_IS_TOO_LONG', [
                                     'length' => self::MAX_BUYER_CODE_LENGTH,
                                 ]),
                                 'message' => Yii::t('app', 'USER_INVOICE_BUYER_CODE_IS_NOT_STRING')],

            // Buyer VAT code
            ['buyer_vat_code', 'filter', 'filter' => 'trim'],
            ['buyer_vat_code', 'default', 'value' => self::DEFAULT_BUYER_VAT_CODE],
            ['buyer_vat_code', 'string', 'max' => self::MAX_BUYER_VAT_CODE_LENGTH,
                                     'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_VAT_CODE_IS_TOO_LONG', [
                                         'length' => self::MAX_BUYER_VAT_CODE_LENGTH,
                                     ]),
                                     'message' => Yii::t('app', 'USER_INVOICE_BUYER_VAT_CODE_IS_NOT_STRING')],

            // Buyer address
            ['buyer_address', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_ADDRESS_IS_REQUIRED')],
            ['buyer_address', 'filter', 'filter' => 'trim'],
            ['buyer_address', 'string', 'max' => self::MAX_BUYER_ADDRESS_LENGTH,
                                    'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_ADDRESS_IS_TOO_LONG', [
                                        'length' => self::MAX_BUYER_ADDRESS_LENGTH,
                                    ]),
                                    'message' => Yii::t('app', 'USER_INVOICE_BUYER_ADDRESS_IS_NOT_STRING')],

            // Buyer city ID
            ['buyer_city_id', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_CITY_ID_IS_REQUIRED')],
            ['buyer_city_id', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_CITY_ID_IS_NOT_INTEGER')],
            ['buyer_city_id', 'exist', 'targetClass' => City::className(),
                                   'targetAttribute' => ['buyer_city_id' => 'id'],
                                           'message' => Yii::t('app', 'USER_INVOICE_BUYER_CITY_ID_IS_NOT_EXIST')],

            // Buyer phone
            ['buyer_phone', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_PHONE_IS_REQUIRED')],
            ['buyer_phone', 'filter', 'filter' => 'trim'],
            ['buyer_phone', 'string', 'max' => self::MAX_BUYER_PHONE_LENGTH,
                                  'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_PHONE_IS_TOO_LONG', [
                                      'length' => self::MAX_BUYER_PHONE_LENGTH,
                                  ]),
                                  'message' => Yii::t('app', 'USER_INVOICE_BUYER_PHONE_IS_NOT_STRING')],
            // TODO: pridėti telefono numerio validaciją iš User modelio

            // Buyer email
//            ['buyer_email', 'required', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_EMAIL_IS_REQUIRED')],
            ['buyer_email', 'email', 'message' => Yii::t('app', 'USER_INVOICE_BUYER_EMAIL_IS_NOT_EMAIL'),
                'except' => self::SCENARIO_USER_BUYS_SERVICE],
            ['buyer_email', 'filter', 'filter' => 'trim'],
            ['buyer_email', 'string', 'max' => self::MAX_BUYER_EMAIL_LENGTH,
                                  'tooLong' => Yii::t('app', 'USER_INVOICE_BUYER_EMAIL_IS_TOO_LONG', [
                                      'length' => self::MAX_BUYER_EMAIL_LENGTH,
                                  ]),
                                  'message' => Yii::t('app', 'USER_INVOICE_BUYER_EMAIL_IS_NOT_STRING')],

            // Product name
            ['product_name', 'required', 'message' => Yii::t('app', 'USER_INVOICE_PRODUCT_NAME_IS_REQUIRED')],
            ['product_name', 'filter', 'filter' => 'trim'],
            ['product_name', 'string', 'max' => self::MAX_PRODUCT_NAME_LENGTH,
                                   'tooLong' => Yii::t('app', 'USER_INVOICE_PRODUCT_NAME_IS_TOO_LONG', [
                                       'length' => self::MAX_PRODUCT_NAME_LENGTH,
                                   ]),
                                   'message' => Yii::t('app', 'USER_INVOICE_PRODUCT_NAME_IS_NOT_STRING')],

            // Netto price
            ['netto_price', 'required', 'message' => Yii::t('app', 'USER_INVOICE_NETTO_PRICE_IS_REQUIRED')],
            ['netto_price', 'match', 'pattern' => '/^\d{1,8}(?:(\.|\,)\d{1,2})?$/',
                                     'message' => Yii::t('app', 'USER_INVOICE_NETTO_PRICE_IS_NOT_MATCH')],

            // Discount
            ['discount', 'default', 'value' => self::DEFAULT_DISCOUNT],
            ['discount', 'match', 'pattern' => '/^\d{1,8}(?:(\.|\,)\d{1,2})?$/',
                                  'message' => Yii::t('app', 'USER_INVOICE_DISCOUNT_IS_NOT_MATCH')],

            // VAT
            ['vat', 'default', 'value' => self::DEFAULT_VAT],
            ['vat', 'match', 'pattern' => '/^\d{1,3}(?:(\.|\,)\d{1,2})?$/',
                             'message' => Yii::t('app', 'USER_INVOICE_VAT_IS_NOT_MATCH')],

            // Days to pay
            ['days_to_pay', 'default', 'value' => self::DEFAULT_DAYS_TO_PAY],
            ['days_to_pay', 'integer', 'min' => self::MIN_DAYS_TO_PAY,
                                  'tooSmall' => Yii::t('app', 'USER_INVOICE_DAYS_TO_PAY_IS_TOO_SMALL', [
                                      'min' => self::MIN_DAYS_TO_PAY,
                                  ]),
                                   'message' => Yii::t('app', 'USER_INVOICE_DAYS_TO_PAY_IS_NOT_INTEGER')],

            // Invoiced by position
            ['invoiced_by_position', 'required', 'message' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_POSITION_IS_REQUIRED')],
            ['invoiced_by_position', 'filter', 'filter' => 'trim'],
            ['invoiced_by_position', 'string', 'max' => self::MAX_INVOICED_BY_POSITION_LENGTH,
                                           'tooLong' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_POSITION_IS_TOO_LONG', [
                                               'length' => self::MAX_INVOICED_BY_POSITION_LENGTH,
                                           ]),
                                           'message' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_POSITION_IS_NOT_STRING')],

            // Invoiced by name surname
            ['invoiced_by_name_surname', 'required', 'message' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_NAME_SURNAME_IS_REQUIRED')],
            ['invoiced_by_name_surname', 'filter', 'filter' => 'trim'],
            ['invoiced_by_name_surname', 'string', 'max' => self::MAX_INVOICED_BY_NAME_SURNAME_LENGTH,
                                               'tooLong' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_NAME_SURNAME_IS_TOO_LONG', [
                                                   'length' => self::MAX_INVOICED_BY_NAME_SURNAME_LENGTH,
                                               ]),
                                               'message' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_NAME_SURNAME_IS_NOT_STRING')],

            // File name
            ['file_name', 'required', 'message' => Yii::t('app', 'USER_INVOICE_FILE_NAME_IS_REQUIRED')],
            ['file_name', 'filter', 'filter' => 'trim'],
            ['file_name', 'string', 'max' => self::MAX_FILE_NAME_LENGTH,
                                'tooLong' => Yii::t('app', 'USER_INVOICE_FILE_NAME_IS_TOO_LONG', [
                                    'length' => self::MAX_FILE_NAME_LENGTH,
                                ]),
                                'message' => Yii::t('app', 'USER_INVOICE_FILE_NAME_IS_NOT_STRING')],

            // File extension
            ['file_extension', 'required', 'message' => Yii::t('app', 'USER_INVOICE_FILE_EXTENSION_IS_REQUIRED')],
            ['file_extension', 'filter', 'filter' => 'trim'],
            ['file_extension', 'string', 'max' => self::MAX_FILE_EXTENSION_LENGTH,
                                     'tooLong' => Yii::t('app', 'USER_INVOICE_FILE_EXTENSION_IS_TOO_LONG', [
                                         'length' => self::MAX_FILE_EXTENSION_LENGTH,
                                     ]),
                                     'message' => Yii::t('app', 'USER_INVOICE_FILE_EXTENSION_IS_NOT_STRING')],

            // Created at
            ['created_at', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_CREATED_AT_IS_NOT_INTEGER'),
                'except' => self::SCENARIO_ADMIN_FILTERS_USER_INVOICES],
            ['created_at', 'string', 'message' => Yii::t('app', 'USER_INVOICE_CREATED_AT_IS_NOT_STRING'),
                'on' => self::SCENARIO_ADMIN_FILTERS_USER_INVOICES],
            ['created_at', 'date', 'format' => 'php:Y-m-d',
                'message' => Yii::t('app', 'USER_INVOICE_CREATED_AT_INVALID_FORMAT', [
                    'example' => date('Y-m-d'),
                ]), 'on' => self::SCENARIO_ADMIN_FILTERS_USER_INVOICES],
            
            // Updated at
            ['updated_at', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_UPDATED_AT_IS_NOT_INTEGER')],

            // Company name
            ['companyName', 'filter', 'filter' => 'trim'],
            ['companyName', 'string', 'message' => Yii::t('app', 'USER_INVOICE_COMPANY_NAME_IS_NOT_STRING'),
                                          'max' => self::COMPANY_NAME_MAX_LENGTH,
                                      'tooLong' => Yii::t('app', 'USER_INVOICE_COMPANY_NAME_IS_TOO_LONG', [
                                          'max' => self::COMPANY_NAME_MAX_LENGTH,
                                      ])],

            // Period
            ['period', 'integer', 'message' => Yii::t('app', 'USER_INVOICE_PERIOD_IS_NOT_INTEGER')],
            ['period', 'in', 'range' => array_keys(self::getTranslatedPeriods()),
                'message' => Yii::t('app', 'USER_INVOICE_PERIOD_IS_NOT_IN_RANGE')],

            // Date form
            ['dateFrom', 'required',
                'on' => self::SCENARIO_ADMIN_EXPORTS_USER_INVOICES_XML
            ],
            ['dateFrom', 'string', 'message' => Yii::t('app', 'USER_INVOICE_DATE_FROM_IS_NOT_STRING')],
            ['dateFrom', 'date', 'format' => 'php:Y-m-d',
                'message' => Yii::t('app', 'USER_INVOICE_DATE_FROM_INVALID_FORMAT', [
                    'example' => date('Y-m-d'),
                ])],
            ['dateFrom', 'validateDateRange', 'params' => [
                'emptyMessage' => Yii::t('app', 'USER_INVOICE_DATE_FROM_EMPTY_DATES'),
                'invalidMessage' => Yii::t('app', 'USER_INVOICE_DATE_FROM_IS_INVALID'),
            ]],

            // Date to
            ['dateTo', 'required',
                'on' => self::SCENARIO_ADMIN_EXPORTS_USER_INVOICES_XML
            ],
            ['dateTo', 'string', 'message' => Yii::t('app', 'USER_INVOICE_DATE_TO_IS_NOT_STRING')],
            ['dateTo', 'date', 'format' => 'php:Y-m-d',
                'message' => Yii::t('app', 'USER_INVOICE_DATE_TO_INVALID_FORMAT', [
                    'example' => date('Y-m-d'),
                ])],
            ['dateTo', 'validateDateRange', 'params' => [
                'emptyMessage' => Yii::t('app', 'USER_INVOICE_DATE_TO_EMPTY_DATES'),
                'invalidMessage' => Yii::t('app', 'USER_INVOICE_DATE_TO_IS_INVALID'),
            ]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_service_id' => Yii::t('app', 'USER_INVOICE_USER_SERVICE_ID_LABEL'),
            'type' => Yii::t('app', 'USER_INVOICE_TYPE_LABEL'),
            'number' => Yii::t('app', 'USER_INVOICE_NUMBER_LABEL'),
            'date' => Yii::t('app', 'USER_INVOICE_DATE_LABEL'),
            'seller_company_name' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_NAME_LABEL'),
            'seller_company_code' => Yii::t('app', 'USER_INVOICE_SELLER_COMPANY_CODE_LABEL'),
            'seller_vat_code' => Yii::t('app', 'USER_INVOICE_SELLER_VAT_CODE_LABEL'),
            'seller_address' => Yii::t('app', 'USER_INVOICE_SELLER_ADDRESS_LABEL'),
            'seller_bank_name' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_NAME_LABEL'),
            'seller_bank_code' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_CODE_LABEL'),
            'seller_swift' => Yii::t('app', 'USER_INVOICE_SELLER_SWIFT_LABEL'),
            'seller_bank_account' => Yii::t('app', 'USER_INVOICE_SELLER_BANK_ACCOUNT_LABEL'),
            'buyer_id' => Yii::t('app', 'USER_INVOICE_BUYER_ID_LABEL'),
            'buyer_title' => Yii::t('app', 'USER_INVOICE_BUYER_TITLE_LABEL'),
            'buyer_code' => Yii::t('app', 'USER_INVOICE_BUYER_CODE_LABEL'),
            'buyer_vat_code' => Yii::t('app', 'USER_INVOICE_BUYER_VAT_CODE_LABEL'),
            'buyer_address' => Yii::t('app', 'USER_INVOICE_BUYER_ADDRESS_LABEL'),
            'buyer_city_id' => Yii::t('app', 'USER_INVOICE_BUYER_CITY_ID_LABEL'),
            'buyer_phone' => Yii::t('app', 'USER_INVOICE_BUYER_PHONE_LABEL'),
            'buyer_email' => Yii::t('app', 'USER_INVOICE_BUYER_EMAIL_LABEL'),
            'product_name' => Yii::t('app', 'USER_INVOICE_PRODUCT_NAME_LABEL'),
            'netto_price' => Yii::t('app', 'USER_INVOICE_NETTO_PRICE_LABEL'),
            'discount' => Yii::t('app', 'USER_INVOICE_DISCOUNT_LABEL'),
            'vat' => Yii::t('app', 'USER_INVOICE_VAT_LABEL'),
            'days_to_pay' => Yii::t('app', 'USER_INVOICE_DAYS_TO_PAY_LABEL'),
            'invoiced_by_position' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_POSITION_LABEL'),
            'invoiced_by_name_surname' => Yii::t('app', 'USER_INVOICE_INVOICED_BY_NAME_SURNAME_LABEL'),
            'file_name' => Yii::t('app', 'USER_INVOICE_FILE_NAME_LABEL'),
            'file_extension' => Yii::t('app', 'USER_INVOICE_FILE_EXTENSION_LABEL'),
            'created_at' => Yii::t('app', 'USER_INVOICE_CREATED_AT_LABEL'),
            'updated_at' => Yii::t('app', 'USER_INVOICE_UPDATED_AT_LABEL'),
        ];
    }

    /**
     * Validates date range attribute
     *
     * @param string $attribute Date range attribute name
     * @param array $params The value of the "params" in rule
     * @return boolean
     */
    public function validateDateRange($attribute, $params = [])
    {
        if (empty($this->dateFrom) || empty($this->dateTo)) {
            $this->addError($attribute, isset($params['emptyMessage']) ? $params['emptyMessage'] : '');
            return true;
        }

        if ($this->dateFrom > $this->dateTo) {
            $this->addError($attribute, isset($params['invalidMessage']) ? $params['invalidMessage'] : '');
            return true;
        }

        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getBuyerCity()
    {
        return $this->hasOne(City::className(), ['id' => 'buyer_city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserService()
    {
        return $this->hasOne(UserService::className(), ['id' => 'user_service_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBuyer()
    {
        return $this->hasOne(Company::className(), ['id' => 'buyer_id']);
    }

    /**
     * Finds user invoice by given user service ID
     *
     * @param null|integer $userServiceId User service ID
     * @return array|null|ActiveRecord
     * @throws NotFoundHttpException If user invoice not found
     */
    public static function findByUserServiceId($userServiceId = null)
    {
        $model = self::find()->where(['user_service_id' => $userServiceId])->orderBy('created_at DESC')->one();
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'USER_INVOICE_NOT_FOUND_BY_USER_SERVICE_ID'));
        }
        return $model;
    }

    /**
     * Creates new user invoice entry and generates invoice document file
     *
     * @param null|integer $userServiceId User service ID
     * @param Service $service User service
     * @param integer $type Invoice type
     * @param null|integer $userId User ID, whom invoice is being generated
     * @return boolean Whether entry created successfully
     */
    public static function create($userServiceId = null, Service $service, $type = self::PRE_INVOICE, $userId = null) {
        $invoiceDirector = new InvoiceDirector($type, $userServiceId, $service->name, $service->price);
        $invoiceDirector->makeInvoice($userId);
        $userInvoice = $invoiceDirector->getUserInvoice();
        $userInvoice->scenario = self::SCENARIO_USER_BUYS_SERVICE;
        if ($service->service_type_id == ServiceType::CREDITCODE_TYPE_ID) {
            $userInvoice->scenario = self::SCENARIO_USER_BUYS_CREDITCODE;
        }
        return $userInvoice->save();
    }
    
    /**
     * Finds all user invoices by provided user ID
     * 
     * @param null|integer $userId User ID
     * @return array
     */
    public static function findAllByUser($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::$app->user->id;
        }
        
        return self::find()
                ->innerJoin(UserService::tableName(), UserService::tableName() . '.id = ' . self::tableName() . '.user_service_id')
                ->where([UserService::tableName() . '.user_id' => $userId])
                ->orderBy(['date' => SORT_DESC])
                ->all();
    }
    
    /**
     * Returns all users invoices and pre-invoices
     * 
     * @param null|integer $userId User ID
     * @return array
     */
    public static function getAllUserInvoicesAndPreInvoices($userId = null)
    {
        $allInvoices = self::findAllByUser($userId);
        $invoices = [];
        $preInvoices = [];
        foreach ($allInvoices as $invoice) {
            if ($invoice->type == self::INVOICE) {
                $invoices[$invoice->user_service_id] = $invoice;
            } else {
                $preInvoices[$invoice->user_service_id] = $invoice;
            }
        }
        return [$preInvoices, $invoices];
    }

    /**
     * Returns list of translated periods
     *
     * @return array
     */
    public static function getTranslatedPeriods()
    {
        return [
            self::TODAY => Yii::t('app', 'TODAY'),
            self::YESTERDAY => Yii::t('app', 'YESTERDAY'),
            self::THIS_MONTH_BEGINNING => Yii::t('app', 'THIS_MONTH_BEGINNING'),
            self::LAST_MONTH_BEGINNING => Yii::t('app', 'LAST_MONTH_BEGINNING'),
            self::LAST_MONTH => Yii::t('app', 'LAST_MONTH'),
            self::THIS_QUARTER => Yii::t('app', 'THIS_QUARTER'),
            self::LAST_QUARTER => Yii::t('app', 'LAST_QUARTER'),
            self::YEAR_BEGINNING => Yii::t('app', 'YEAR_BEGINNING'),
        ];
    }

    /**
     * Returns list of translated types
     *
     * @return array
     */
    public static function getTranslatedTypes()
    {
        return [
            self::PRE_INVOICE => Yii::t('app', 'PRE_INVOICE'),
            self::INVOICE => Yii::t('app', 'INVOICE'),
        ];
    }

    /**
     * Returns bill list data provider
     *
     * @param ActiveQuery $query Bill list data provider query
     * @return ActiveDataProvider
     */
    public function getAdminDataProvider($query)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
    }

    /**
     * Returns query for bill list data provider
     *
     * @param array $dates List of filtration date ranges
     * @return ActiveQuery
     */
    public function getBillListDataProviderQuery($dates)
    {
        $grouped = self::find()
                ->select('max(type) as MaxBillType, user_service_id')
                ->groupBy('user_service_id');
        
        $groupJoin = self::find()
                ->innerJoin(['grouped' => $grouped], 'grouped.user_service_id = user_invoice.user_service_id AND grouped.MaxBillType = user_invoice.type');

        return self::find()
            ->from(['grouped' => $groupJoin])
            ->filterWhere(['between', 'grouped.updated_at', $dates['createdAtStart'], $dates['createdAtEnd']])
            ->orFilterWhere(['between', 'grouped.updated_at', $dates['periodStart'], $dates['periodEnd']])
            ->orFilterWhere(['between', 'grouped.updated_at', $dates['dateFrom'], $dates['dateTo']])
            ->andFilterWhere(['grouped.type' => $this->type])
            ->andFilterWhere(['like', 'grouped.number', $this->number])
            ->andFilterWhere(['like', 'grouped.buyer_title', $this->companyName])
            ->orderBy('SUBSTR(grouped.number FROM 1 FOR 2), CAST(SUBSTR(grouped.number FROM 3) AS UNSIGNED) DESC');
    }

    /**
     * Returns all filtration date ranges
     *
     * @return array
     */
    public function getFiltrationDateRanges()
    {
        $periodInterval = self::getPeriodDateRange($this->period);
        list($periodStart, $periodEnd) = self::convertToTimestamp($periodInterval);
        list($createdAtStart, $createdAtEnd) = self::convertToTimestamp([$this->created_at, $this->created_at]);
        list($dateFrom, $dateTo) = self::convertToTimestamp([$this->dateFrom, $this->dateTo]);

        return compact('periodStart', 'periodEnd', 'createdAtStart', 'createdAtEnd', 'dateFrom', 'dateTo');
    }

    /**
     * Returns period date range by provided period type
     *
     * @param null|integer $period Period type
     * @return array
     */
    public static function getPeriodDateRange($period)
    {
        $today = date('Y-m-d', time());

        switch ($period) {
            case self::TODAY:
                return [$today, $today];
            case self::YESTERDAY:
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                return [$yesterday, $yesterday];
            case self::THIS_MONTH_BEGINNING:
                $thisMonth = date('Y-m-d', strtotime('first day of this month'));
                return [$thisMonth, $today];
            case self::LAST_MONTH_BEGINNING:
                $previousMonth = date('Y-m-d', strtotime('first day of previous month'));
                return [$previousMonth, $today];
            case self::LAST_MONTH:
                $previousMonthStart = date('Y-m-d', strtotime('first day of previous month'));
                $previousMonthEnd = date('Y-m-d', strtotime('last day of previous month'));
                return [$previousMonthStart, $previousMonthEnd];
            case self::THIS_QUARTER:
                return self::getQuarterDateRange(self::THIS_QUARTER);
            case self::LAST_QUARTER:
                return self::getQuarterDateRange(self::LAST_QUARTER);
            case self::YEAR_BEGINNING:
                $yearStart = date('Y-01-01');
                return [$yearStart, $today];
            default:
                return [null, null]; // NOTE: period date range must be and array from two elements
        }
    }

    /**
     * Converts date range values to timestamp
     *
     * @param array $dateRange Target date range
     * @return array
     */
    public static function convertToTimestamp($dateRange)
    {
        if (count($dateRange) != 2) { // NOTE: date range MUST consist of array from two elements
            return [null, null]; // NOTE: return value expected to be an array from two elements
        }

        $startDate = current($dateRange);
        $endDate = next($dateRange);

        if (empty($startDate) || empty($endDate)) {
            return [null, null]; // NOTE: return value expected to be an array from two elements
        }

        return [strtotime($startDate . ' 00:00'), strtotime($endDate . ' 23:59')];
    }

    /**
     * Returns date range by quarter type
     *
     * @see http://stackoverflow.com/a/35509890/5747867
     * @param integer $type Quarter type. Could be this quarter or last quarter
     * @return array
     */
    private static function getQuarterDateRange($type)
    {
        $quarter = ceil(date('n') / 3);
        $year = date('Y');
        if ($type === self::LAST_QUARTER) {
            $year = ($quarter === 1) ? $year - 1 : $year;
            $quarter = ($quarter === 1) ? 4 : $quarter - 1; // NOTE: 4 is previous year last quarter
        }

        $startDate = date('Y-m-d', strtotime($year. '-' . ($quarter * 3 - 2) . '-1'));
        $endDate = date('Y-m-t', strtotime($year . '-' . ($quarter * 3) . '-1'));

        return [$startDate, $endDate];
    }

    /**
     * Returns file full name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->file_name . '.' . $this->file_extension;
    }

    /**
     * Returns path to file depending on invoice type
     *
     * @return mixed
     */
    public function getPath()
    {
        if ($this->type == self::INVOICE) {
            return Yii::$app->params['invoicePath'];
        }

        return Yii::$app->params['preInvoicePath'];
    }

    /**
     * Sets invoice data
     *
     * @param Company $company Buyer company model
     * @param null|string $number User invoice number
     */
    public function setInvoiceData(Company $company, $number = null)
    {
        if (is_null($number)) {
			$this->date = time(); // Invoice is being generated at this moment so date should be changed to now
            if ($this->isInvoice()) {
                $this->number = Yii::$app->params['invoiceNumber'] . UserInvoice::getNextInvoiceNumber();
            } else {
                $this->number = Yii::$app->params['preInvoiceNumber'] . $this->user_service_id;
            }
        } else {
            $this->number = $number;
        }
        $this->setSellerAttributes();
        $this->setBuyerAttributes($company);
        $this->product_name = $this->userService->service->name;
        $this->netto_price = $this->userService->price;
        $this->discount = Yii::$app->params['serviceDiscount'];
        $this->vat = Country::getUserVatRate($this->userService->user_id);
        $this->days_to_pay = Yii::$app->params['daysToPayPreInvoice'];
        $this->invoiced_by_position = Yii::$app->params['invoicedByPosition'];
        $this->invoiced_by_name_surname = Yii::$app->params['invoicedByNameSurname'];
        $this->file_name = Yii::$app->params[$this->type == self::INVOICE ? 'invoiceFileName' : 'preInvoiceFileName'];
        $this->file_name .= $this->user_service_id;
        $this->file_extension = Yii::$app->params[$this->type == self::INVOICE ? 'invoiceFileExtension' : 'preInvoiceFileExtension'];
    }

    /**
     * Sets seller attributes
     */
    private function setSellerAttributes()
    {
        $this->seller_company_name = Yii::$app->params['sellerCompanyName'];
        $this->seller_company_code = Yii::$app->params['sellerCompanyCode'];
        $this->seller_vat_code = Yii::$app->params['sellerVatCode'];
        $this->seller_address = Yii::$app->params['sellerAddress'];
        $this->seller_bank_name = Yii::$app->params['sellerBankName'];
        $this->seller_bank_code = Yii::$app->params['sellerBankCode'];
        $this->seller_swift = Yii::$app->params['sellerSwift'];
        $this->seller_bank_account = Yii::$app->params['sellerBankAccount'];
    }

    /**
     * Sets buyer attributes
     *
     * @param Company $company Buyer company model
     */
    private function setBuyerAttributes(Company $company)
    {
        $this->buyer_title = $company->getTitleByType();
        $this->buyer_code = $company->getCodeByType();
        $this->buyer_vat_code = $company->vat_code;
        $this->buyer_address = $company->address;
        $this->buyer_city_id = $company->city_id;
        $this->buyer_phone = $company->phone;
        $this->buyer_email = $company->email;
    }

    /**
     * Generates invoice or pre-invoice document, depending on document type
     */
    public function generateDocument()
    {
        $path = Yii::$app->params[$this->type == self::INVOICE ? 'invoicePath' : 'preInvoicePath'];
        $this->createDirectory($path);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'content' => Yii::$app->controller->renderPartial('partial/invoice', [
                'userInvoice' => $this,
            ]),
            'cssFile' => '@frontend/web/dist/pre-invoice/pre-invoice.css',
            'filename' => $path . $this->getFullName(),
            'destination' => Pdf::DEST_FILE,
            'options' => [
                'title' => Yii::t('document', ($this->type == self::PRE_INVOICE ? 'PRE_' : '') . 'INVOICE_HEADING', [
                    'number' => $this->number,
                ]),
            ],
        ]);
        $pdf->render();
    }

    /**
     * Creates invoice or pre-invoice documents directory depending on document type
     *
     * @param string $path Path to invoice or pre-invoice documents directory
     */
    private function createDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true); // FIXME
        }
    }

    /**
     * Returns list of page sizes
     *
     * @return array
     */
    public static function getPageSizes()
    {
        return [
            self::FIRST_PAGE_SIZE => '1-' . self::FIRST_PAGE_SIZE,
            self::SECOND_PAGE_SIZE => '1-' . self::SECOND_PAGE_SIZE,
            self::THIRD_PAGE_SIZE => '1-' . self::THIRD_PAGE_SIZE,
        ];
    }

    /**
     * Calculates bills amount depending on provided bill type
     *
     * @param array|self[] $bills List of paid bills objects
     * @param integer $type Bill type. Could be pre-invoice or invoice
     * @return float
     */
    public static function calculateBillsAmount($bills, $type)
    {
        $amount = 0.00;
        foreach ($bills as $bill) {
            if ($bill->type != $type) {
                continue;
            }

            $amount += (float) $bill->netto_price;
        }

        return (float) $amount;
    }

    /**
     * Converts date ranges to human readable text
     *
     * @param array $dateRanges List of date ranges
     * @return string
     */
    public static function convertDateRangesToText($dateRanges)
    {
        $createdAtText = self::getDateTextFromDateRange($dateRanges, 'createdAtStart', 'createdAtEnd');
        $periodText = self::getDateTextFromDateRange($dateRanges, 'periodStart', 'periodEnd');
        $dateRangeText = self::getDateTextFromDateRange($dateRanges, 'dateFrom', 'dateTo');

        $text = empty($createdAtText) ? '' : $createdAtText . ' ' . Yii::t('app', 'AND') . ' ';
        $text .= empty($periodText) ? '' : $periodText . ' ' . Yii::t('app', 'AND') . ' ';
        $text .= empty($dateRangeText) ? '' : $dateRangeText . ' ' . Yii::t('app', 'AND') . ' ';

        return rtrim($text, ' ' . Yii::t('app', 'AND') . ' ');
    }

    /**
     * Returns date text from date range
     *
     * @param array $dateRanges List of date ranges
     * @param string $startKey Array key for date range start
     * @param string $endKey Array key for date range end
     * @return string
     */
    private static function getDateTextFromDateRange($dateRanges, $startKey, $endKey)
    {
        if (is_null($dateRanges[$startKey]) || is_null($dateRanges[$endKey])) {
            return '';
        }

        return Yii::t('element', 'A-C-398', [
            'dateFrom' => date('Y-m-d', $dateRanges[$startKey]),
            'dateTo' => date('Y-m-d', $dateRanges[$endKey]),
        ]);
    }

    /**
     * Fixes period conflict with other date filters
     */
    public function fixPeriodDateConflict()
    {
        if (!empty($this->created_at) || !empty($this->dateFrom) || !empty($this->dateTo)) {
            $this->period = null;
        } else {
            $this->period = $this->period ? $this->period : UserInvoice::THIS_MONTH_BEGINNING;
        }
    }

    /**
     * Checks whether user invoice document exists
     *
     * @return boolean
     */
    public function isDocumentExist()
    {
        $fullPath = $this->getPath() . $this->getFullName();
        return file_exists($fullPath);
    }

    /**
     * Checks whether user invoice type is invoice
     *
     * @return boolean
     */
    public function isInvoice()
    {
        return $this->type == self::INVOICE;
    }

    /**
     * Checks whether user invoice type is pre-invoice
     *
     * @return boolean
     */
    public function isPreInvoice()
    {
        return $this->type == self::PRE_INVOICE;
    }

    /**
     * Sends pre-invoice document to users' email
     *
     * @return boolean Whether mail was sent successfully
     */
    public function sendPreInvoiceDocumentToUser()
    {
        return Yii::$app->mailer->compose('user/pre-invoice-document', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->attach($this->getPath() . $this->getFullName())
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($this->userService->user->email)
            ->setSubject(Yii::t('mail', 'PRE_INVOICE_DOCUMENT_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->send();
    }

    /**
     * Checks whether pre-invoice has invoice
     *
     * @return boolean
     */
    public function hasInvoice()
    {
        return self::find()
            ->joinWith('userService.user.userServiceActives')
            ->where([
                'not', [UserServiceActive::tableName() . '.user_id' => null] 
            ])
            ->andWhere([
                'user_service_id' => $this->user_service_id
            ])->exists();
    }

    /**
     * Finds and returns latest invoice number
     *
     * @return integer
     */
    public static function getLatestInvoiceNumber()
    {
        $latestNumber = self::find()
            ->select('number')
            ->where(['type' => self::INVOICE])
            ->orderBy('SUBSTR(number FROM 1 FOR 2), CAST(SUBSTR(number FROM 3) AS UNSIGNED) DESC')
            ->scalar();

        $number = (int) str_replace(Yii::$app->params['invoiceNumber'], '', $latestNumber);

        return $number;
    }

    /**
     * Calculates and returns next invoice number
     *
     * @return integer
     */
    public static function getNextInvoiceNumber()
    {
        $latestNumber = self::getLatestInvoiceNumber();
        $nextNumber = $latestNumber + 1;

        return $nextNumber;
    }
    
    /**
     * Returns invoice due date to pay
     * 
     * @return string
     */
    function getDueDate()
    {
        return date('Y-m-d', strtotime(date('Y-m-d', $this->date) . 
            '+ ' . $this->days_to_pay . 'days'));
    }
    
    /**
     * Streams XML data from specified UserInvoice data provider
     *
     * @param ActiveDataProvider $dataProvider
     * @param array $dateRanges
     */
    public function streamXmlData($dataProvider, $dateRanges)
    {
        $models = $dataProvider->getModels();
        $domTree = $this->createDomTree();
        
        foreach ($models as $model) {
            $model->addToDomTree($domTree, $dateRanges);
        }
        
        $domTree->formatOutput = false;
        echo $domTree->saveXML();
    }
    
    /**
     * Creates and returns DOM tree
     * 
     * @return DOMDocument
     */
    function createDomTree()
    {
        $domTree = new DOMDocument('1.0', 'UTF-8');
        
        $xmlRoot = $domTree->createElementNS('http://www.w3.org/2001/XMLSchema-instance', 'E_Invoice');
        $xmlRoot = $domTree->appendChild($xmlRoot);
        
        $header = $domTree->createElement('Header');
        $header = $xmlRoot->appendChild($header);

        $date = date('Ymd');
        $header->appendChild($domTree->createElement('Date', $date));
        
        $fileId = date('Y-m-d\TH:i:s');
        $header->appendChild($domTree->createElement('FileId', $fileId));

        $appId = 'EINVOICE';
        $header->appendChild($domTree->createElement('AppId', $appId));
        
        $version = 1.1;
        $header->appendChild($domTree->createElement('Version', $version));

        return $domTree;
    }
    
    /**
     * Adds invoice to DOM tree
     * 
     * @param DOMDocument $domTree
     * @param array $dateRanges
     */
    function addToDomTree($domTree, $dateRanges)
    {
        $xmlRoot = $domTree->documentElement;
        
        // Invoice
        $invoice = $xmlRoot->appendChild($domTree->createElement('Invoice'));
        
        // Invoice attributes
        $invoiceId = $this->id;
        $invoice->setAttribute('invoiceId', $invoiceId);
        
        $invoice->setAttribute('presentment', 'Yes');
        
        $invoice->setAttribute('invoiceGlobUniqId', $invoiceId);
        
        $sellerContractId = $this->getXmlString(
            Yii::$app->params['xmlInvoices']['sellerContractId']);
        $invoice->setAttribute('sellerContractId', $sellerContractId);
        
        $sellerRegNumber = $this->getXmlString(
            Yii::$app->params['xmlInvoices']['sellerRegNumber']);
        $invoice->setAttribute('sellerRegNumber', $sellerRegNumber);
        
        // InvoiceParties
        $invoiceParties = $invoice->appendChild(
            $domTree->createElement('InvoiceParties'));
       
        // SellerParty
        $sellerParty = $invoiceParties->appendChild(
            $domTree->createElement('SellerParty'));
        
        // Name
        $sellerName = $this->getXmlString($this->seller_company_name);
        $sellerParty->appendChild($domTree->createElement('Name', $sellerName));
        
        // RegNumber
        $regNumber = $this->getXmlString($this->seller_company_code);
        $sellerParty->appendChild(
            $domTree->createElement('RegNumber', $regNumber));
        
        // VATRegNumber
        $vatRegNumber = $this->getXmlString($this->seller_vat_code);
        $sellerParty->appendChild(
            $domTree->createElement('VATRegNumber', $vatRegNumber));
        
        // ContactData
        $contactData = $sellerParty->appendChild(
            $domTree->createElement('ContactData'));
        
        $sellerEmailAddress = $this->getXmlString(
            Yii::$app->params['xmlInvoices']['sellerEmail']);
        $contactData->appendChild(
            $domTree->createElement('E-mailAddress', $sellerEmailAddress));
        
        // LegalAddress
        $legalAddress = $contactData->appendChild(
            $domTree->createElement('LegalAddress'));
        
        // PostalAddress1
        $postalAddress = $this->getXmlString(
            Yii::$app->params['xmlInvoices']['sellerAddress']);
        $legalAddress->appendChild(
            $domTree->createElement('PostalAddress1', $postalAddress));
        
        // City
        $city = $this->getXmlString(
            Yii::$app->params['xmlInvoices']['sellerCity']);
        $legalAddress->appendChild($domTree->createElement('City', $city));
        
        // BuyerParty
        $buyerParty = $invoiceParties->appendChild(
            $domTree->createElement('BuyerParty'));
        
        // Name
        $buyerName = $this->getXmlString($this->buyer_title);
        $buyerParty->appendChild($domTree->createElement('Name', $buyerName));
        
        // RegNumber
        $regNumber = $this->getXmlString($this->buyer_code);
        $buyerParty->appendChild(
            $domTree->createElement('RegNumber', $regNumber));
        
        // VATRegNumber
        $vatRegNumber = $this->getXmlString($this->buyer_vat_code);
        $buyerParty->appendChild(
            $domTree->createElement('VATRegNumber', $vatRegNumber));
        
        // ContactData
        $contactData = $buyerParty->appendChild(
            $domTree->createElement('ContactData'));
        
        // E-mailAddress
        $buyerEmailAddress = $this->getXmlString($this->buyer_email);
        $contactData->appendChild(
            $domTree->createElement('E-mailAddress', $buyerEmailAddress));
        
        // LegalAddress
        $legalAddress = $domTree->createElement('LegalAddress');
        $legalAddress = $contactData->appendChild($legalAddress);
        
        // PostalAddress1
        $postalAddress = $this->getXmlString($this->buyer_address);
        $legalAddress->appendChild(
            $domTree->createElement('PostalAddress1', $postalAddress));
        
        // City
        $city = $this->getXmlString($this->buyerCity->name);
        $legalAddress->appendChild($domTree->createElement('City', $city));
        
        // Country
        $country = $this->getXmlString($this->buyerCity->country->name);
        $legalAddress->appendChild($domTree->createElement('Country', $country));
        
        // InvoiceInformation
        $invoiceInformation = $invoice->appendChild(
            $domTree->createElement('InvoiceInformation'));
        
        // Type
        $type = $invoiceInformation->appendChild($domTree->createElement('Type'));
        $type->setAttribute('type', 'DEB');
        
        // DocumentName
        $documentName = $this->getXmlString(substr($this->number, 0, 2));
        $invoiceInformation->appendChild(
            $domTree->createElement('DocumentName', $documentName));
        
        // DocumentNumber
        $documentNumber = $this->getXmlString(substr($this->number, 2));
        $invoiceInformation->appendChild(
            $domTree->createElement('DocumentNumber', $documentNumber));
        
        // InvoiceContentText
        $invoiceInformation->appendChild(
            $domTree->createElement('InvoiceContentText', 'PVM SĄSKAITA FAKTŪRA'));
        
        // InvoiceDate
        $invoiceDate = date('Y-m-d', $this->date);
        $invoiceInformation->appendChild(
            $domTree->createElement('InvoiceDate', $invoiceDate));
        
        // DueDate
        $dueDate = $this->dueDate;
        $invoiceInformation->appendChild(
            $domTree->createElement('DueDate', $dueDate));
        
        // Period
        $period = $invoiceInformation->appendChild(
            $domTree->createElement('Period'));
        
        // PeriodName
        $period->appendChild(
            $domTree->createElement('PeriodName', 'Už laikotarpį'));
        
        // StartDate
        $startDate = date('Y-m-d', $dateRanges['dateFrom']);
        $period->appendChild($domTree->createElement('StartDate', $startDate));
        
        // EndDate
        $endDate = date('Y-m-d', $dateRanges['dateTo']);
        $period->appendChild($domTree->createElement('EndDate', $endDate));
        
        // InvoiceSumGroup
        $invoiceSumGroup = $invoiceInformation->appendChild(
            $domTree->createElement('InvoiceSumGroup'));
        
        // InvoiceSum
        $priceWithDiscount = (float)$this->netto_price + (float)$this->discount;
        $vatPrice = ($priceWithDiscount * $this->vat) / 100;
        $bruttoPrice = (float)$priceWithDiscount + (float)$vatPrice;
        
        $discountPercent = 1 - ($priceWithDiscount / (float)$this->netto_price);
        $discountPercent = number_format($discountPercent, 4, '.', '');
        
        $invoiceSum = number_format($bruttoPrice, 2, '.', '');
        $invoiceSumGroup->appendChild($domTree->createElement('InvoiceSum', $invoiceSum));
        
        // Addition
        $addition = $invoiceSumGroup->appendChild(
            $domTree->createElement('Addition'));
        $addition->setAttribute('addCode', 'DSC');
        
        // AddContent
        $addition->appendChild(
            $domTree->createElement('AddContent', 'Pritaikytos nuolaidos'));
        
        // AddRate
        $addRate = $discountPercent > 0 ? $discountPercent * -1 : $discountPercent;
        $addition->appendChild($domTree->createElement('AddRate', $addRate));
        
        // AddSum
        $addSum = number_format($this->discount, 4, '.', '');
        $addSum = $addSum > 0 ? $addSum * -1 : $addSum;
        $addition->appendChild($domTree->createElement('AddSum', $addSum));
        
        //VAT
        $vat = $invoiceSumGroup->appendChild($domTree->createElement('VAT'));
        $vat->setAttribute('vatId', 'TAX');
        
        // VATRate
        $vatRate = round($this->vat);
        $vat->appendChild($domTree->createElement('VATRate', $vatRate));
        
        // VATSum
        $vatSum = number_format($vatPrice, 2, '.', '');
        $vat->appendChild($domTree->createElement('VATSum', $vatSum));
        
        // TotalVATSum
        $totalVATSum = number_format($vatPrice, 2, '.', '');
        $invoiceSumGroup->appendChild(
            $domTree->createElement('TotalVATSum', $totalVATSum));
        
        // TotalSum
        $totalSum = number_format($bruttoPrice, 2, '.', '');
        $invoiceSumGroup->appendChild(
            $domTree->createElement('TotalSum', $totalSum));
        
        // TotalToPay
        $totalToPay = number_format($bruttoPrice, 2, '.', '');
        $invoiceSumGroup->appendChild(
            $domTree->createElement('TotalToPay', $totalToPay));
        
        // Currency
        $invoiceSumGroup->appendChild(
            $domTree->createElement('Currency', 'EUR'));
        
        // InvoiceItem
        $invoiceItem = $invoice->appendChild(
            $domTree->createElement('InvoiceItem'));
        
        // InvoiceItemGroup
        $invoiceItemGroup = $invoiceItem->appendChild(
            $domTree->createElement('InvoiceItemGroup'));
        
        // ItemEntry
        $itemEntry = $invoiceItemGroup->appendChild(
            $domTree->createElement('ItemEntry'));
        
        // RowNo
        $itemEntry->appendChild($domTree->createElement('RowNo', 1));
        
        // SerialNumber
        $service = $this->userService->service;
        
        $serialNumber = $service->id;
        $itemEntry->appendChild(
            $domTree->createElement('SerialNumber', $serialNumber));
        
        // SellerProductId
        $sellerProductId = $this->userService->service->id;
        $itemEntry->appendChild(
            $domTree->createElement('SellerProductId', $sellerProductId));
        
        // Description
        $description = $this->product_name;
        $itemEntry->appendChild(
            $domTree->createElement('Description', $description));
        
        // ItemDetailInfo
        $itemDetailInfo = $invoice->appendChild(
            $domTree->createElement('ItemDetailInfo'));
        
        // ItemUnit
        $itemDetailInfo->appendChild($domTree->createElement('ItemUnit', 'vnt.'));
        
        // ItemAmount
        $itemDetailInfo->appendChild($domTree->createElement('ItemAmount', '1.00'));
        
        // ItemPrice
        $itemPrice = number_format($this->netto_price, 2, '.', '');
        $itemEntry->appendChild($domTree->createElement('ItemPrice', $itemPrice));
        
        // ItemSum
        $itemSum = number_format($this->netto_price, 2, '.', '');
        $itemDetailInfo->appendChild(
            $domTree->createElement('ItemSum', $itemSum));
        
        // Addition
        $addition = $itemDetailInfo->appendChild(
            $domTree->createElement('Addition'));
        $addition->setAttribute('addCode', 'DSC');
        
        // AddContent
        $addContent = 'Nuolaida';
        $addition->appendChild($domTree->createElement('AddContent', $addContent));
        
        // AddRate
        $addRate = $discountPercent > 0 ? $discountPercent * -1 : $discountPercent;
        $addition->appendChild($domTree->createElement('AddRate', $addRate));
        
        // AddSum
        $addSum = number_format($this->discount, 4, '.', '');
        $addSum = $addSum > 0 ? $addSum * -1 : $addSum;
        $addition->appendChild($domTree->createElement('AddSum', $addSum));
        
        // VAT
        $vat = $itemDetailInfo->appendChild($domTree->createElement('VAT'));
        
        // SumBeforeVAT
        $sumBeforeVAT = number_format($priceWithDiscount, 2, '.', '');
        $addition->appendChild(
            $domTree->createElement('SumBeforeVAT', $sumBeforeVAT));
        
        // VATRate
        $vatRate = number_format($this->vat, 2, '.', '');
        $addition->appendChild(
            $domTree->createElement('VATRate', $vatRate));
        
        // VATSum
        $vatSum = number_format($vatPrice, 2, '.', '');
        $addition->appendChild(
            $domTree->createElement('VATSum', $vatSum));
        
        // SumAfterVAT
        $sumAfterVAT = number_format($bruttoPrice, 2, '.', '');
        $addition->appendChild(
            $domTree->createElement('SumAfterVAT', $sumAfterVAT));
        
        // PaymentInfo
        $paymentInfo = $invoice->appendChild(
            $domTree->createElement('PaymentInfo'));
        
        // Currency
        $paymentInfo->appendChild($domTree->createElement('Currency', 'EUR'));
        
        // PaymentDescription
        $paymentDescription = 'Apmokėjimas pagal sąskaitą Nr. ' . $this->number;
        $paymentInfo->appendChild(
            $domTree->createElement('PaymentDescription', $paymentDescription));
        
        // Payable
        $paymentInfo->appendChild($domTree->createElement('Payable', 'Yes'));
        
        // PayDueDate
        $paymentInfo->appendChild(
            $domTree->createElement('PayDueDate', $dueDate));
        
        // PaymentId
        $paymentId = $this->getXmlString($this->number, 2);
        $paymentInfo->appendChild(
            $domTree->createElement('PaymentId', $paymentId));
        
        // PayToAccount
        $payToAccount = str_replace(' ', '', 
            $this->getXmlString($this->seller_bank_account));
        $paymentInfo->appendChild(
            $domTree->createElement('PayToAccount', $payToAccount));
        
        // PayToName
        $payToName = $this->getXmlString($this->seller_company_name);
        $paymentInfo->appendChild(
            $domTree->createElement('PayToName', $payToName));
    }
    
    /**
     * Replaces special characters to get valid XML string
     * 
     * @param string
     * return string
     */
    function getXmlString($str)
    {
        $chars = [
            ['&', '&amp;'],
            ['\\', '&apos;'],
            ['>', '&gt;'],
            ['<', '&lt;'],
            ['"', '&quot;'],
        ];
        foreach ($chars as $item) {
            $str = str_replace($item[0], $item[1], $str);
        }
        
        return $str;
    }
    
    /**
     * Returns special string for empty value
     * 
     * @return string
     */
    function emptyValue($value)
    {
        return !empty($value) ? $value : '&#x20;';
    }
}
