<?php

namespace backend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\document\DocumentFactory;
use common\components\ElasticSearch;
use common\components\ElasticSearch\Cities;
use common\components\Model;
use common\components\PotentialHaulier;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\Company;
use common\models\CompanyComment;
use common\models\CompanyDocument;
use common\models\CompanyUser;
use common\models\Country;
use common\models\CountryPhoneCode;
use common\models\Language;
use common\models\Load;
use common\models\LoadCity;
use common\models\Service;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserLanguage;
use common\models\UserLog;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class ClientController
 *
 * @package backend\controllers
 */
class ClientController extends Controller
{
    /** @const string Company info tab */
    const TAB_COMPANY_INFO = 'company-info';

    /** @const string Company documents tab */
    const TAB_COMPANY_DOCUMENTS = 'company-documents';

    /** @const string Company users tab */
    const TAB_COMPANY_USERS = 'company-users';

    /** @const string Company subscriptions tab */
    const TAB_COMPANY_SUBSCRIPTIONS = 'company-subscriptions';

    /** @const string Company bills tab */
    const TAB_COMPANY_BILLS = 'company-bills';

    /** @const string Company invoices tab */
    const TAB_COMPANY_INVOICES = 'company-invoices';

    /** @const string Company pre-invoices tab */
    const TAB_COMPANY_PRE_INVOICES = 'company-pre-invoices';

    /** @const string Company payments tab */
    const TAB_COMPANY_PAYMENTS = 'company-payments';

    /** @const string Company loads tab */
    const TAB_COMPANY_LOADS = 'company-loads';

    /** @const string Company car transporters tab */
    const TAB_COMPANY_CAR_TRANSPORTERS = 'company-car-transporters';

    /** @const string Selected subscription adding to list action */
    const ACTION_ADD_SUBSCRIPTION_TO_LIST = 'add-subscription-to-list';

    /** @const string Selected subscription removing from list action */
    const ACTION_REMOVE_SUBSCRIPTION_FROM_LIST = 'remove-subscription-from-list';

    /** @var null|UserServiceActive */
    private $userServiceActive;

    /** @var null|Service */
    private $service;

    /**
     * @inheritdoc
     */
    public function behaviors() 
    {
        $denyCallback = function () {
            throw new ForbiddenHttpException(Yii::t(
                'alert', 'ERROR_ACTION_NEEDS_RIGHTS'));
        };
        
        return [
            'access' => [ 
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'company',
                            'edit-company-info-validation',
                            'change-company-archivation',
                            'city-list',
                            'company-info-by-vat-code',
                            'add-document',
                            'download-document',
                            'remove-document',
                            'company-user-edit-form',
                            'edit-company-user',
                            'company-user-add-form',
                            'validate-company-user-form',
                            'add-company-user',
                            'company-user-activity-preview',
                            'change-subscription-end-date',
                            'render-new-subscription-form',
                            'get-subscription-range',
                            'create-new-subscription',
                            'render-company-comment-form',
                            'add-company-comment',
                            'show-company-comments',
                            'remove-company-comment',
                            'render-pre-invoice-creation-form',
                            'create-pre-invoice',
                            'validate-vat-code',
                            'render-company-name-change-form',
                            'change-company-name',
                            'review-account',
                            'change-subscription-end-date',
                            'change-subscription-activity',
							'save-potentiality',
                            'load-user-routes'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest &&
                                Yii::$app->admin->identity->isAdmin();
                        }
                    ],
                    [
                        'actions' => ['company'],
                        'allow' => false,
                        'matchCallback' => function () {
                            if (Yii::$app->admin->isGuest) {
                                return false;
                            }

                            $action = Yii::$app->request->post('action');
                            return Yii::$app->admin->identity->isModerator() && !empty($action);
                        },
                        'denyCallback' => $denyCallback,
                    ],
                    [
                        'actions' => [
                            'index',
                            'company',
                            'city-list',
                            'company-info-by-vat-code',
                            'download-document',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest &&
                                Yii::$app->admin->identity->isModerator();
                        },
                    ],
                    [
                        'allow' => false,
                        'denyCallback' => $denyCallback,
                    ],
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'change-subscription-end-date' => ['POST'],
                    'change-company-archivation' => ['POST'],
                ]
            ]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * renders client index page
     */
    public function actionIndex() 
    {
        $user = new User(['scenario' => User::SCENARIO_EXTENDED_CLIENT_SEARCH]);
        $company = new Company(null, Company::SCENARIO_EXTENDED_CLIENT_SEARCH);
        $userServiceActive = new UserServiceActive(['scenario' => UserServiceActive::SCENARIO_EXTENDED_CLIENT_SEARCH]);
        $userService = new UserService(['scenario' => User::SCENARIO_EXTENDED_CLIENT_SEARCH]);
        $companyDocument = new CompanyDocument(['scenario' => CompanyDocument::SCENARIO_EXTENDED_CLIENT_SEARCH]);
        $get = Yii::$app->request->get();
        $query = [];
        
        if (Yii::$app->request->referrer && !in_array(Company::COMPANY_FILTER_INPUT_NAME, $get) && !empty($get[Company::COMPANY_FILTER_INPUT_NAME])) {
            $companies = Company::getCompanyUserByEmail($get[Company::COMPANY_FILTER_INPUT_NAME]);
            $searchText = Yii::$app->request->get(Company::COMPANY_FILTER_INPUT_NAME);
        } else {
            $companies = Company::getAllCompaniesForClientIndex();
            $query = Company::getAllCompaniesForClientIndex();
            $searchText = '';
        }
        
        if ($this->loadAndValidate($user, $company, $userServiceActive, $userService, $companyDocument, $get)) {
            $companies = Company::getAllFilteredCompaniesForClientIndex(Yii::$app->request->get());
            $searchText = Yii::$app->request->get(Company::COMPANY_FILTER_INPUT_NAME);
            $query = Company::getAllFilteredCompaniesForClientIndex($get);
        }
        $dataProvider = Company::getCompaniesDataProvider($query);

        $pages = new Pagination([
            'pageSize' => 10,
            'totalCount' => $companies->count(),
        ]);

        $companiesResult = $companies->limit($pages->limit)->offset($pages->offset)->all();

        return $this->render('index', [
            'title' => Yii::t('seo', 'TITLE_ADMIN_CLIENTS'),
            'companies' => $companiesResult,
            'searchText' => $searchText,
            'companiesCount' => $pages->totalCount,
            'user' => $user,
            'company' => $company,
            'userServiceActive' => $userServiceActive,
            'userService' => $userService,
            'companyDocument' => $companyDocument,
            'comments' => $this->getCompaniesComments($companiesResult),
            'pages' => $pages,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionLoadUserRoutes(){
        $user = User::findOne(Yii::$app->getRequest()->post('user_id'));
        if (!$user instanceof User) {
            return Yii::t('element', 'User not found');
        }
        $loadList = Load::find()->where(['user_id' => $user, 'status' => Load::ACTIVE, 'active' => Load::ACTIVATED])->all();
        $mainRoutes = [];
        /** @var Load $load */
        foreach ($loadList as $load) {
            $views = array_sum(array_column((new PotentialHaulier($load))->getPotentialHauliersByHistoryOfSearch(), 'counter'));
            if ($views) {
                $mainRoutes[$load->id] = $views;
            }
            if (count($mainRoutes) >= 15) {
                break;
            }
        }
        arsort($mainRoutes);
        return $this->renderAjax('forms/main_routes', ['mainRoutes' => $mainRoutes]);
    }

    /**
     * Returns associated list of companies comments
     *
     * All comments are grouped by company ID. In each company ID, comments are separated by arrays,
     * where array key is comment ID and value is comment object
     *
     * @param Company[] $companies List of companies objects
     * @return array
     */
    private function getCompaniesComments($companies = [])
    {
        $companiesIds = $this->getCompaniesIds($companies);
        $companies = CompanyComment::find()
            ->where([
                'company_id' => $companiesIds,
                'archived' => CompanyComment::NOT_ARCHIVED,
            ])
            ->all();

        return ArrayHelper::map($companies, 'id', function (CompanyComment $companyComment) {
            return $companyComment;
        }, function (CompanyComment $companyComment) {
            return $companyComment->company_id;
        });
    }

    /**
     * Returns all companies IDs from list of array of companies objects
     *
     * @param Company[] $companies List of companies objects
     * @return array
     */
    private function getCompaniesIds($companies = [])
    {
        return array_map(create_function('$company', 'return $company->id;'), $companies);
    }
    
    /**
     * Loads post data into models and validates it
     * 
     * @param User $user model object
     * @param Company $company model object
     * @param UserServiceActive $userServiceActive model object
     * @param UserService $userService model object
     * @param CompanyDocument $companyDocument model object
     * @param array $post posted data
     * @return boolean
     */
    public function loadAndValidate(User &$user,
                                    Company &$company, 
                                    UserServiceActive &$userServiceActive, 
                                    UserService &$userService, 
                                    CompanyDocument &$companyDocument,
                                    $post = null)
    {
        return $user->load($post) &&
               $company->load($post) &&
               $userServiceActive->load($post) &&
               $userService->load($post) &&
               $companyDocument->load($post) &&
               $user->validate() &&
               $company->validate() &&
               $userServiceActive->validate() &&
//               $userService->validate() && // FIX ME add scenarios to user service
               $companyDocument->validate();
    }

    /**
     * Renders company management panel
     *
     * @param null|integer $id Company ID
     * @param string $tab Current tab
     * @return string
     * @throws NotFoundHttpException If company not found
     */
    public function actionCompany($id = null, $tab = self::TAB_COMPANY_INFO)
    {
        $company = Company::findOne($id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CLIENT_COMPANY_NOT_FOUND'));
        }

        $load = new Load(['scenario' => Load::SCENARIO_ADMIN_FILTERS_LOADS]);
        $load->load(Yii::$app->request->get());
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_ADMIN_FILTERS_LOADS]);
        $loadCity->load(Yii::$app->request->get());
        
        $carTransporter = new CarTransporter(['scenario' => CarTransporter::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS]);
        $carTransporter->load(Yii::$app->request->get());
        $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS]);
        $carTransporterCity->load(Yii::$app->request->get());

        if (Yii::$app->admin->identity->isModerator() && $tab == self::TAB_COMPANY_INFO) {
            $tab = self::TAB_COMPANY_INVOICES;
        }
        
        switch ($tab) {
            case self::TAB_COMPANY_INFO:
                $this->companyInfo($company);
                break; 
        }

        $year = Yii::$app->request->post('year', date('Y'));
        return $this->render('/client/company/index', [
            'activeVatRateLegalCountryCode' => Country::getValidVatRateCountryCode($company->vat_code),
            'vatRateCountries' => Country::getVatRateCountries(),
            'cityLegal' => City::getNameById($company->city_id),
            'phoneNumbers' => CountryPhoneCode::getPhoneNumbers(),
            'activePhoneNumber' => $company->getActivePhoneNumber(),
            'company' => $company,
            'companyDocument' => new CompanyDocument(),
            'subscriptionDataProvider' => $this->companySubscriptions($id, $year),
			'subscriptionHistoryDataProvider' => $this->companyHistorySubscriptions($id, $year),
            'invoiceDataProvider' => $this->companyInvoices($id, $year, UserService::PAID),
            'preInvoiceDataProvider' => $this->companyInvoices($id, $year, UserService::NOT_PAID),
            'paymentDataProvider' => $this->companyPayments($id, $year),
            'load' => $load,
            'loadCity' => $loadCity,
            'countries' => Country::getAssociativeNames(),
            'loadDataProvider' => $load->companyLoadsDataProvider($loadCity, $company->id),
            'carTransporter' => $carTransporter,
            'carTransporterCity' => $carTransporterCity,
            'carTransporterDataProvider' => $carTransporter->companyCarTransportersDataProvider($carTransporterCity, $company->id),
            'year' => $year,
            'id' => $id,
            'tab' => $tab,
        ]);
    }

    /**
     * Edits company info
     *
     * @note logika iškelta į šią funkciją, tačiau pati logika ir funkcijų vidus nesutvarkyti
     * @param Company $company Target company
     */
    private function companyInfo(Company &$company)
    {
        if (Yii::$app->request->isPost) {
            $company->scenario = Company::SCENARIO_EDIT_COMPANY_DATA_CLIENT;
            $company->ownerList->scenario = User::SCENARIO_CHANGE_COMPANY_CLASS;
            foreach ($company->companyUsers as $companyUser) {
                $companyUser->user->scenario = User::SCENARIO_EDIT_COMPANY_DATA_CLIENT;
            }

            $this->saveEditedCompanyInfo(Yii::$app->request->post(), $company);
        }
    }

    /**
     * Saves edited company info
     * 
     * @param array $post
     * @param Company $company Company onject
     */
    private function saveEditedCompanyInfo($post = [], $company = null) 
    {
        $transaction = Yii::$app->db->beginTransaction();
        $oldOwnerIdAttribute = $company->owner_id;
        if ($this->loadAndValidateCompanyInfo($company, $post)) {
            if ($this->findChangedCompanyUser($company, $oldOwnerIdAttribute) && $company->ownerList->changeClass($post['User']['class'], $post['Company']['owner_id'])
                    && $company->changeCompanyInfo($post)) {
                if (!$post['Company']['visible']) {
                    User::inactives($company->getAllUsersIds());  
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('alert', 'COMPANY_INFO_CHANGE_SUCCESSFUL'));
            } else {
                $transaction->rollback();
                Yii::$app->session->setFlash('error', Yii::t('alert', 'COMPANY_INFO_CHANGE_ERROR'));
            }
        } else {
            $transaction->rollback();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'COMPANY_INFO_CHANGE_ERROR'));
        }
    }

    /**
     * Returns company subscriptions active data provider
     *
     * @param null|integer $companyId Company ID
     * @param null|integer $year Currently selected company subscriptions year
     * @return null|ActiveDataProvider
     */
    private function companySubscriptions($companyId, $year)
    {
        $year = $this->validateYear($year) ? $year : date('Y');
        return UserServiceActive::getCompanySubscriptionsDataProvider($companyId, $year);
    }
	
	/**
     * Returns company history subscriptions active data provider
     *
     * @param null|integer $companyId Company ID
     * @param null|integer $year Currently selected company subscriptions year
     * @return null|ActiveDataProvider
     */
    private function companyHistorySubscriptions($companyId, $year)
    {
        $year = $this->validateYear($year) ? $year : date('Y');
        return UserService::getCompanySubscriptionsHistoryDataProvider($companyId, $year);
    }

    /**
     * Checks whether selected year is valid
     *
     * @param null|integer $year Selected year
     * @return boolean
     */
    private function validateYear($year)
    {
        $years = Model::getYearsRange();
        return in_array($year, $years);
    }

    /**
     * Administrator changes user subscription end date
     */
    public function actionChangeSubscriptionEndDate()
    {
        $scenario = UserServiceActive::SCENARIO_ADMIN_CHANGES_END_DATE;
        $end_date = strtotime(Yii::$app->request->post('date') . '  23:59:59');
        $userServiceActive = UserServiceActive::find()
                ->where(['id' => Yii::$app->request->post('id')])->one();
        $userServiceActive->scenario = $scenario;
        $userServiceActive->end_date = $end_date;
        $userService = UserService::find()
                ->where([
                    'user_id' => $userServiceActive->user_id,
                    'start_date' =>  $userServiceActive->date_of_purchase,
                    ])
                ->one();
        if (!$userServiceActive->validate(['end_date'])) {
            return Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_SUBSCRIPTION_END_DATE'));
        };
        $userService->end_date = $end_date;
        $userService->scenario = UserService::SCENARIO_ADMIN_CHANGE_SUBSCRIPTION_END_DATE;
        $updatedRows = UserServiceActive::updateAll(compact('end_date'), ['id' => Yii::$app->request->post('id')]);
        if ($updatedRows && $userService->validate()) {
            $userService->save();
            return Yii::$app->session->setFlash('success', Yii::t('alert', 'SUBSCRIPTION_END_DATE_CHANGED_SUCCESSFULLY'));
        }

        return Yii::$app->session->setFlash('error', Yii::t('alert', 'SUBSCRIPTION_END_DATE_WAS_NOT_CHANGED'));
    }

    /**
     * Administrator changes user subscription activity
     */
    public function actionChangeSubscriptionActivity()
    {
        $scenario = UserServiceActive::SCENARIO_ADMIN_CHANGES_STATUS;
        $newStatus = Yii::$app->request->post('status');
        $subscriptionType = UserServiceActive::findByIid(Yii::$app->request->post('id'))->service->service_type_id;
        
        $status = $newStatus === 'false' ? UserServiceActive::NOT_ACTIVE : UserServiceActive::ACTIVE;
        if ((UserServiceActive::findActivatedSubscriptions(Yii::$app->request->post('userId')) && $status) && ($subscriptionType == ServiceType::MEMBER_TYPE_ID)) {
            return Yii::$app->session->setFlash('error', Yii::t('alert', 'SUBSCRIPTION_ALREADY_EXIST'));
        }
        $userActiveService = new UserServiceActive(compact('scenario', 'status'));

        if (!$userActiveService->validate(['status'])) {
            return Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_SUBSCRIPTION_STATUS'));
        }

        $updatedRows = UserServiceActive::updateAll(compact('status'), ['id' => Yii::$app->request->post('id')]);
        if ($updatedRows) {
            return Yii::$app->session->setFlash('success', Yii::t('alert', 'SUBSCRIPTION_STATUS_CHANGED_SUCCESSFULLY'));
        }

        return Yii::$app->session->setFlash('error', Yii::t('alert', 'SUBSCRIPTION_STATUS_WAS_NOT_CHANGED'));
    }
    
    /**
     * Finds and returns all company invoices
     *
     * @param null|integer $id Company ID
     * @param null|integer $year Selected company invoices year
     * @param boolean $isPaid Attribute, whether user service is paid or not
     * @return ActiveDataProvider
     */
    private function companyInvoices($id, $year, $isPaid)
    {
        $year = $this->validateYear($year) ? $year : date('Y');
        $beginningOfTheYear = strtotime($year . '-01-01');
        $endOfTheYear = strtotime($year . '-12-31');
        $tab = $isPaid ? ClientController::TAB_COMPANY_INVOICES : ClientController::TAB_COMPANY_PRE_INVOICES;
        $page = $isPaid ? 'invoice-page' : 'preinvoice-page';
        $query = UserService::find()
            ->distinct()
            ->joinWith('userInvoices')
            ->joinWith('service')
            ->joinWith('user')
            ->joinWith('user.companyUser')
            ->joinWith('user.companyUser.company AS userCompany')
            ->joinWith('user.companies AS ownerCompany')
            ->where([UserService::tableName() . '.paid' => $isPaid])
            ->andWhere([
                'or',
                ['ownerCompany.id' => $id],
                ['userCompany.id' => $id],
            ])
            ->andWhere(['between', UserService::tableName() . '.created_at', $beginningOfTheYear, $endOfTheYear]);

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'page' => (Yii::$app->request->get($page) - 1),
                'pageParam' => $page,
                'params' => ['tab' => $tab, 'id' => Yii::$app->request->get('id')],
            ]
        ]);
    }

    /**
     * Returns company payments data provider
     *
     * @param null|integer $id Company ID
     * @param null|integer $year Currently selected company payments year
     * @return ActiveDataProvider
     */
    private function companyPayments($id, $year)
    {
        $year = $this->validateYear($year) ? $year : date('Y');
        return UserService::getCompanyPaymentsDataProvider($id, $year);
    }

    /**
     * Renders new company subscription creation form
     *
     * @param null|integer $id Company ID
     * @param string $tab Current tab
     * @return string
     */
    public function actionRenderNewSubscriptionForm($id = null, $tab = self::TAB_COMPANY_SUBSCRIPTIONS)
    {
        $userServiceActive = new UserServiceActive(['scenario' => UserServiceActive::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION]);
        $userServiceActive->date_of_purchase = date('Y-m-d');
        $userServiceActive->end_date = date('Y-m-d');
        return $this->renderAjax('/client/company/add/subscription', [
            'id' => $id,
            'tab' => $tab,
            'services' => ArrayHelper::map(Service::find()->all(), 'id', function (Service $service) {
                return Yii::t('element', "{$service->name} {0}", $service->credits);
            }),
            'companyUsers' => ArrayHelper::map(User::findAllCompanyUsers($id), 'id', function (User $user) {
                return $user->getNameAndSurname();
            }),
            'userServiceActive' => $userServiceActive,
        ]);
    }

    /**
     * Returns new company subscription date range
     *
     * @return null|string
     */
    public function actionGetSubscriptionRange()
    {
        $serviceId = Yii::$app->request->post('serviceId');
        $service = Service::findOne($serviceId);
        if (is_null($service)) {
            return null;
        }

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+" . $service->days . " days"));

        return json_encode([$startDate, $endDate]);
    }

    /**
     * Creates new company subscription
     *
     * @param null|integer $id Company ID
     * @param string $tab Current tab
     * @return Response
     */
    public function actionCreateNewSubscription($id = null, $tab = self::TAB_COMPANY_SUBSCRIPTIONS)
    {
        Yii::$app->db->beginTransaction();
        if (!$this->createUserServiceActive() || !$this->createUserService() || !$this->updateUserCredits()) {
            Yii::$app->db->transaction->rollBack();
        } else {
            Yii::$app->db->transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('alert', 'CREATE_NEW_SUBSCRIPTION_SAVED_SUCCESSFULLY'));
        }

        return $this->redirect([
            'client/company',
            'lang' => Yii::$app->language,
            'id' => $id,
            'tab' => $tab,
        ]);
    }

    /**
     * Creates new active user service
     *
     * @return boolean Whether active user service was created successfully
     */
    private function createUserServiceActive()
    {
        $userId = Yii::$app->request->post('UserServiceActive')['user_id'];
        $serviceId = Yii::$app->request->post('UserServiceActive')['service_id'];
        $service = Service::find()->where(['id' => $serviceId])->one();
        $userServiceActive = new UserServiceActive([
            'scenario' => UserServiceActive::SCENARIO_ADMIN_CREATES_NEW_SUBSCRIPTION,
            'status' => UserServiceActive::ACTIVE,
            'reminder' => UserServiceActive::DEFAULT_REMINDER,
            'credits' => !is_null($service) ? $service->credits : null,
        ]);
        $userServiceActive->load(Yii::$app->request->post());
        if (strtotime($userServiceActive->date_of_purchase) <= time() && strtotime($userServiceActive->end_date) >= time())
        {
            if ($service->service_type_id == ServiceType::MEMBER_TYPE_ID) {
                $userServiceActiveToDelete = UserServiceActive::findAllActiveServicesToDelete($userId); 
            }
            $service = Service::findOne($userServiceActive->service_id);
            if (is_null($service)) {
                Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_USER_SERVICE_ACTIVE_SERVICE_NOT_FOUND'));
                return false;
            }

            $userServiceActive->convertDateOfPurchaseToTimestamp();
            $userServiceActive->convertEndDateToTimestamp();
            $userServiceActive->setScenario(UserServiceActive::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION);
            if (!$userServiceActive->validate()) {
                Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_USER_SERVICE_ACTIVE_INVALID_USER_SERVICE_ACTIVE_DATA'));
                return false;
            }

            if (!$userServiceActive->save(false)) {
                Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_USER_SERVICE_ACTIVE_CANNOT_SAVE_USER_SERVICE_ACTIVE_DATA'));
                return false;
            }
        }
        $this->service = $service;
        $this->userServiceActive = $userServiceActive;
        return true;
    }

    /**
     * Creates new user service
     *
     * @return boolean
     */
    private function createUserService()
    {
        $userService = new UserService([
            'scenario' => UserService::SCENARIO_SYSTEM_SAVES_NEW_SUBSCRIPTION,
            'user_id' => $this->userServiceActive->user_id,
            'service_id' => $this->service->id,
            'paid' => UserService::PAID,
            'paid_by' => UserService::ADMIN,
            'admin_id' => Yii::$app->admin->id,
            'start_date' => $this->userServiceActive->date_of_purchase,
            'end_date' => $this->userServiceActive->end_date,
            'price' => $this->service->price,
            'response' => UserService::DEFAULT_RESPONSE,
        ]);

        if (!$userService->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_USER_SERVICE_INVALID_USER_SERVICE_DATA'));
            return false;
        }

        if (!$userService->save(false)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_USER_SERVICE_CANNOT_SAVE_USER_SERVICE_DATA'));
            return false;
        }

        return true;
    }

    /**
     * Updates current user credits
     *
     * @return boolean Whether credits were updated successfully
     */
    private function updateUserCredits()
    {
        $user = User::findOne($this->userServiceActive->user_id);
        if (is_null($user)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'UPDATE_USER_CREDITS_USER_NOT_FOUND'));
            return false;
        }

        $credits = $this->service->credits;
        $userActiveServices = $this->userServiceActive->user->userServiceActives;
        if ($this->service->service_type_id !== ServiceType::SERVICE_CREDITS_TYPE_ID) {
            foreach ($userActiveServices as $userActiveService) {
                if ($userActiveService->service->isMemberType()) {
                    $credits += $this->userServiceActive->user->current_credits;
                    break;
                }
            }
        }

        if ($this->service->service_type_id === ServiceType::SERVICE_CREDITS_TYPE_ID) {
            $user->setScenario(User::SCENARIO_UPDATE_SERVICE_CREDITS);
            $user->setServiceCredits($credits);
        } else {
            $user->setScenario(User::SCENARIO_UPDATE_CURRENT_CREDITS);
            $user->setCurrentCredits($credits);
        }

        if (!$user->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'UPDATE_USER_CREDITS_INVALID_USER_DATA'));
            return false;
        }

        if (!$user->save(false)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'UPDATE_USER_CREDITS_CANNOT_SAVE_USER_DATA'));
            return false;
        }

        return true;
    }

    /**
     * Loads post data into object and validates it
     * 
     * @param Company $company
     * @param array $post
     * @return boolean
     */
    public function loadAndValidateCompanyInfo(Company &$company, $post = null) {
        return  $company->load($post) && $company->validate();
    }
    
    /**
     * Finds invited company user who, has been made to owner and swaps it with with company old owner id
     * 
     * @param Company $company
     * @param type $oldOwnerIdAttribute
     * @return boolean
     */
    public function findChangedCompanyUser($company, $oldOwnerIdAttribute) 
    {
        foreach ($company->companyUsers as $companyUser) {
            if ($companyUser->user_id == $company->getAttribute('owner_id')) {
                return $companyUser->changeOwnerToSimpleUser($oldOwnerIdAttribute);
            } 
        }
        return true;
    }
    
    /**
     * Validates create admin form
     *
     * @return string
     */
    public function actionEditCompanyInfoValidation()
    {
        $companyAdapter = new AjaxValidationAdapter(new Company(), Company::SCENARIO_EDIT_COMPANY_DATA_CLIENT);
        return $companyAdapter->validate();
    }
    
    /**
     * Changes company archivation status
     *
     * @param null|integer $id Company ID that archivation status needs to be changed
     * @return Response
     * @throws NotAcceptableHttpException If archive status is invalid
     * @throws NotFoundHttpException If company not found
     */
    public function actionChangeCompanyArchivation($id = null)
    {
        $company = Company::findOne($id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'COMPANY_NOT_FOUND_BY_ID'));
        }

        $archive = Yii::$app->request->post('archive');
        switch ($archive) {
            case Company::NOT_ARCHIVED:
                Company::unarchive($id);
                break;
            case Company::ARCHIVED:
                Company::archive($id);
                if ($company->ownerList->isCarrier()) {
                    $company->ownerList->makeSupplier();
                }
                User::archive($company->getAllUsersIds());
                break;
            default:
                throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_ARCHIVE_STATUS'));
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'COMPANY_ARCHIVE_STATUS_CHANGED_SUCCESSFULLY'));
        return $this->redirect(['client/company', 'lang' => Yii::$app->language, 'id' => $company->id]);
    }
    
    /**
     * Searches for city/cities by user entered phrase
     *
     * @param string $searchableCity Phrase that user entered to city search input
     * @param boolean $load Attribute whether searchable city is for load
     * @param boolean $unload Attribute whether searchable city is for unload city
     * @param boolean $filter Attribute whether searchable city is for my loads filter
     * @param null|string $token Unique string to identify announced load
     * @return string List of user searchable cities in JSON format. NOTE: return structure must be exactly the same
     * as provided. If 'items' is changed, then also it must be changed in view file.
     * @throws NotAcceptableHttpException If searchable city is shorter than required
     */
    public function actionCityList($searchableCity = '', $load = false, $unload = false, $filter = false, $token = null)
    {
        if (strlen($searchableCity) < ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'ELASTIC_SEARCH_TEXT_TOO_SHORT', [
                'length' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
            ]));
        }

        if ($filter) {
            if (Yii::$app->user->isGuest && is_null($token)) {
                return json_encode(['items' => []]);
            }
            return json_encode(['items' => ElasticSearch::filterMyLoads($searchableCity, $token)]);
        }

        if ($load || $unload) {
            return json_encode(['items' => Cities::getLoadCitiesSuggestions($searchableCity, $unload)]);
        }

        return json_encode(['items' => Cities::getSimpleSearchCities($searchableCity)]);
    }
    
    /**
     * Returns information about user/company by VAT code from POST
     *
     * @return string
     * @throws BadRequestHttpException If request is not AJAX or not POST or POST has invalid parameters
     */
    public function actionCompanyInfoByVatCode()
    {
        $company = new Company(null, Company::SCENARIO_EDIT_COMPANY_DATA_CLIENT);
        $post = Yii::$app->request->post();
        $company->vat_code = isset($post['vatCode']) ? $post['vatCode'] : '';
        if (!Company::isVatCodeLengthValid($company->vat_code) || !$company->validate(['vat_code'])) { // TODO 
            return json_encode(['address' => '', 'companyName' => '', 'valid' => false]);
        }
        list($code, $number) = Company::splitVatCode($company->vat_code, Company::VAT_CODE_MIN_LENGTH);
        $response = Company::getInfoFromECByVatCode($code, $number);
        return json_encode([
            'address' => $response['address'],
            'companyName' => $response['name'],
            'valid' => $response['valid'],
        ]);
    }
    
    /**
     * Adds company documents
     *
     * @param null|string $type Document type
     * @param string $date Document date of expiry
     * @return string
     */
    public function actionAddDocument($type = null, $date = '', $companyId = null)
    {
        $document = DocumentFactory::create($type, $companyId);
        if (is_null($document)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_INVALID_TYPE'));
            exit;
        }

        /** @var CompanyDocument|string $result */
        $result = $document->upload($date);
        if (is_string($result)) {
            Yii::$app->getSession()->setFlash('error', $result); // NOTE: document could not be saved to catalog
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_CANNOT_CREATE'));
            return $this->redirect([
                '/client/company',
                'lang' => Yii::$app->language,
                'id' => $companyId,
                'tab' => self::TAB_COMPANY_DOCUMENTS,
            ]);
        }

        if ($result->hasErrors()) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_CANNOT_CREATE'));
            return $this->redirect([
                '/client/company',
                'lang' => Yii::$app->language,
                'id' => $companyId,
                'tab' => self::TAB_COMPANY_DOCUMENTS,
            ]);
        }

        $document->addWatermark($result);
        CompanyDocument::deleteByType($document->getCompany()->id, $type);
        if (CompanyDocument::create($document->getCompany()->id, $date, $type, $document->getExtension($result))) {
            $document->setCompany($companyId); // NOTE: refreshes company and company documents
            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'ADD_DOCUMENT_CREATED_SUCCESSFULLY'));
        } else {
            $document->remove();
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_CANNOT_CREATE'));
        }
        return $this->redirect([
            '/client/company',
            'lang' => Yii::$app->language,
            'id' => $companyId,
            'tab' => self::TAB_COMPANY_DOCUMENTS,
        ]);
    }

    /**
     * Downloads company document by given document type
     *
     * @param null|string $type Document type
     * @return string
     */
    public function actionDownloadDocument($type = null, $companyId = null)
    {
        $document = DocumentFactory::create($type, $companyId);
        if (is_null($document)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'DOWNLOAD_DOCUMENT_INVALID_TYPE'));
            exit;
        }

        /** @var CompanyDocument $companyDocument */
        $companyDocument = CompanyDocument::findCurrentCompanyByType($type, $document->getCompany()->id);
        $fullPath = $document->getFullPath($companyDocument);
        if (!file_exists($fullPath)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'DOWNLOAD_DOCUMENT_FILE_NOT_EXISTS'));
            return $this->redirect([
                '/client/company',
                'lang' => Yii::$app->language,
                'id' => $document->getCompany()->id,
                'tab' => self::TAB_COMPANY_DOCUMENTS,
            ]);
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: ' . CompanyDocument::DOCUMENT_MIME_TYPES);
        header('Content-Disposition: inline; filename="' . $document->getName() . '.' . $companyDocument->extension . '"');
        readfile($fullPath);
        exit;
    }

    /**
     * Removes company document
     *
     * @param null|string $type Document type
     * @return string
     */
//    public function actionRemoveDocument($type = null, $companyId = null, $selectedTab = '')
//    {
//        $document = DocumentFactory::create($type, $companyId);
//        if (is_null($document)) {
//            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'REMOVE_DOCUMENT_INVALID_TYPE'));
//            exit;
//        }
//
//        if ($document->remove()) {
//            CompanyDocument::deleteByType($document->getCompany()->id, $type);
//            $document->setCompany($companyId); // NOTE: refreshes company and company documents
//            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'REMOVE_DOCUMENT_REMOVED_SUCCESSFULLY'));
//        } else {
//            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'REMOVE_DOCUMENT_CANNOT_REMOVE'));
//        }
//
//        return $this->redirect([
//            '/client/company',
//            'lang' => Yii::$app->language,
//            'id' => $companyId,
//            'tab' => self::TAB_COMPANY_DOCUMENTS,
//        ]);
//    }

    /**
     * Renders company user edit form on PJAX call
     *
     * @return string
     * @throws NotFoundHttpException If user not found
     */
    public function actionCompanyUserEditForm()
    {
        $id = Yii::$app->request->post('id');
        $user = User::findOne($id);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CLIENT_COMPANY_USER_EDIT_FORM_USER_NOT_FOUND'));
        }

        $user->scenario = User::SCENARIO_ADMIN_EDITS_COMPANY_USER;
        $user->language = $user->getLanguagesIds();
        $user->blocked_until = is_null($user->blocked_until) ? null : date('Y-m-d', $user->blocked_until);
        $company = Company::findUserCompany($user->id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CLIENT_COMPANY_USER_EDIT_FORM_COMPANY_NOT_FOUND'));
        }

        return $this->renderAjax('/client/company/edit/user', [
            'id' => $company->id,
            'user' => $user,
            'languages' => Language::getIconicNames(),
            'lastActiveService' => UserServiceActive::findLastEndDate($user->id),
            'comments' => CompanyComment::find()->where(['company_id' => $company->id])
                                                ->andWhere(['archived' => CompanyComment::NOT_ARCHIVED])
                                                ->count(),
        ]);
    }

    /**
     * Edits company user information
     *
     * @param null|integer $id User ID
     * @return Response
     * @throws NotAcceptableHttpException If user model cannot be loaded, or not valid, or user languages cannot be updated
     * @throws NotFoundHttpException If user model not found
     * @throws ServerErrorHttpException If user model changes cannot be saved
     */
    public function actionEditCompanyUser($id = null)
    {
        $user = User::findOne($id);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_USER_NOT_FOUND'));
        }

        $user->scenario = User::SCENARIO_ADMIN_EDITS_COMPANY_USER;
        if (!$user->load(Yii::$app->request->post())) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_CANNOT_LOAD_USER_DATA'));
        }

        if (!empty($user->password)) {
            $user->setPassword($user->password);
        }

        $user->active = $user->isArchived() ? User::INACTIVE : User::ACTIVE;
        $user->visible = $user->isArchived() ? User::INVISIBLE : User::VISIBLE;

        if (!empty($user->blocked_until)) {
            $user->convertBlockedUntilToTimestamp();
            $user->allow = User::FORBIDDEN;
        } else {
            $user->allow = User::ALLOWED;
        }

        $user->scenario = User::SCENARIO_SYSTEM_UPDATES_COMPANY_USER;
        if (!$user->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_INVALID_USER_DATA'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        if (!$user->save(false)) {
            $transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_CANNOT_SAVE_USER_DATA'));
        }

        if (!UserLanguage::updateUserLanguages($user->id, $user->language)) {
            $transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_CANNOT_UPDATE_USER_LANGUAGES'));
        }

        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'CLIENT_EDIT_COMPANY_USER_SAVED_SUCCESSFULLY'));
        $company = Company::findUserCompany($user->id);
        return $this->redirect([
            'client/company',
            'id' => is_null($company) ? null : $company->id,
            'tab' => self::TAB_COMPANY_USERS,
        ]);
    }

    /**
     * Renders company user adding form on PJAX call
     *
     * @return string
     */
    public function actionCompanyUserAddForm()
    {
        /** @var null|integer $id Company ID */
        $id = Yii::$app->request->post('id');

        return $this->renderAjax('/client/company/add/user', [
            'companyId' => $id,
            'user' => new User([
                'scenario' => User::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER,
                'active' => User::ACTIVE,
                'sendEmail' => User::SEND_EMAIL,
            ]),
            'languages' => Language::getIconicNames(),
        ]);
    }

    /**
     * Validates company user form
     *
     * @return string
     */
    public function actionValidateCompanyUserForm()
    {
        $userAdapter = new AjaxValidationAdapter(new User(), User::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER);
        return $userAdapter->validate();
    }

    /**
     * Creates new company user account
     *
     * @param null|integer $id Company ID
     * @return Response
     * @throws NotAcceptableHttpException If user data cannot be loaded or not valid or user languages cannot be saved
     * or user cannot be assigned to company or email cannot be sent
     * @throws ServerErrorHttpException If user data cannot be saved
     */
    public function actionAddCompanyUser($id = null)
    {
        $user = new User([
            'scenario' => User::SCENARIO_ADMIN_ADDS_NEW_COMPANY_USER,
            'allow' => User::ALLOWED,
            'visible' => User::VISIBLE,
        ]);
        if (!$user->load(Yii::$app->request->post())) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CANNOT_LOAD_USER_DATA'));
        }

        $user->setPasswordExpiration();
        $user->generateAuthKey();
        $user->setPassword($user->password);

        $user->scenario = User::SCENARIO_SYSTEM_SAVES_NEW_COMPANY_USER;
        if (!$user->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_INVALID_USER_DATA'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        if (!$user->save(false)) {
            $transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CANNOT_SAVE_USER_DATA'));
        }

        if (!UserLanguage::updateUserLanguages($user->id, $user->language)) {
            $transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CANNOT_SAVE_USER_LANGUAGES'));
        }

        if (!CompanyUser::assign($id, $user->id)) {
            $transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CANNOT_ADD_USER_TO_COMPANY'));
        }

        if ($user->sendEmail && !$user->informAboutNewAccount()) {
            $transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CANNOT_SEND_EMAIL'));
        }

        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'CLIENT_ADD_COMPANY_USER_CREATED_SUCCESSFULLY'));
        return $this->redirect([
            'client/company',
            'id' => $id,
            'tab' => self::TAB_COMPANY_USERS,
        ]);
    }

    /**
     * Renders company user activity preview on PJAX call
     *
     * @return string
     */
    public function actionCompanyUserActivityPreview()
    {
        /** @var null|integer $id User ID */
        $id = Yii::$app->request->post('id');

        return $this->renderAjax('/client/company/preview/activity', [
            'logs' => UserLog::find()->where(['user_id' => $id])->orderBy(['created_at' => SORT_DESC])->all(),
        ]);
    }

    /**
     * Renders company comment form
     *
     * @return string
     */
    public function actionRenderCompanyCommentForm()
    {
        /** @var null|integer $id Company ID */
        $id = Yii::$app->request->post('id');
        return $this->renderAjax('/client/company/add/comment', [
            'id' => $id,
            'companyComment' => new CompanyComment(['scenario' => CompanyComment::SCENARIO_ADMIN_ADDS_COMPANY_COMMENT]),
            'toIndex' => Yii::$app->request->post('toIndex', 1),
        ]);
    }

    /**
     * Adds new comment to company
     *
     * @param null|integer $id Company ID
     * @param integer $toIndex Attribute, whether admin must be redirected to index after form submit
     * @return Response
     */
    public function actionAddCompanyComment($id = null, $toIndex = 1)
    {
        $companyComment = new CompanyComment([
            'scenario' => CompanyComment::SCENARIO_ADMIN_ADDS_COMPANY_COMMENT,
            'company_id' => $id,
            'admin_id' => Yii::$app->admin->id,
            'archived' => CompanyComment::NOT_ARCHIVED,
        ]);
        $companyComment->load(Yii::$app->request->post());
        $companyComment->setScenario(CompanyComment::SCENARIO_SYSTEM_SAVES_COMPANY_COMMENT);
        if (!$companyComment->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'ADD_COMPANY_COMMENT_INVALID_DATA'));
            return $this->redirect(['client/index', 'lang' => Yii::$app->language]);
        }

        if (!$companyComment->save(false)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'ADD_COMPANY_COMMENT_CANNOT_SAVE'));
            return $this->redirect(['client/index', 'lang' => Yii::$app->language]);
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'ADD_COMPANY_COMMENT_SAVED_SUCCESSFULLY'));
        if ($toIndex) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->redirect([
            'client/company',
            'lang' => Yii::$app->language,
            'id' => $id,
            'tab' => self::TAB_COMPANY_USERS,
        ]);
    }

    /**
     * Renders company comments preview page
     *
     * @param null|integer $id Company ID
     * @return string
     * @throws NotFoundHttpException If company not found by provided company ID
     */
    public function actionShowCompanyComments($id = null)
    {
        $comments = CompanyComment::find()
            ->where([
                'company_id' => $id,
                CompanyComment::tableName() . '.archived' => CompanyComment::NOT_ARCHIVED,
            ])
            ->orderBy([CompanyComment::tableName() . '.created_at' => SORT_DESC])
            ->all();

        $company = Company::findOne($id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SHOW_COMPANY_COMMENTS_COMPANY_NOT_FOUND'));
        }

        return $this->render('/client/company/preview/comments', compact('comments', 'company'));
    }

    /**
     * Archives specific company comment
     *
     * @param null|integer $commentId Comment ID, that needs to be archived
     * @param null|integer $companyId Company ID
     * @param integer $toIndex Attribute, whether admin must be redirected to index after form submit
     * @return Response
     * @throws NotFoundHttpException If none comment was archived
     */
    public function actionRemoveCompanyComment($commentId = null, $companyId = null, $toIndex = 1)
    {
        $updatedComments = CompanyComment::updateAll(['archived' => CompanyComment::ARCHIVED], ['id' => $commentId]);
        if (!$updatedComments) {
            throw new NotFoundHttpException(Yii::t('alert', 'REMOVE_COMPANY_COMMENT_NONE_COMMENT_REMOVED'));
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'REMOVE_COMPANY_COMMENT_REMOVED_SUCCESSFULLY'));
        if ($toIndex) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->redirect([
            'client/show-company-comments',
            'lang' => Yii::$app->language,
            'id' => $companyId,
        ]);
    }

    /**
     * Renders pre-invoice creation form
     *
     * @param null|integer $companyId Company ID
     * @return string
     */
    public function actionRenderPreInvoiceCreationForm($companyId = null)
    {
        $serviceTypeId = Yii::$app->request->post('serviceTypeId');
        $subscriptions = Yii::$app->request->post('subscriptions', []);
        if (is_string($subscriptions)) {
            $subscriptions = json_decode($subscriptions, true);
        }
        $services = Service::find()->joinWith('serviceType')->where(['service_type_id' => $serviceTypeId])->all();
        $durations = ArrayHelper::map($services, 'id', function (Service $service) {
            return $service->getMonthsByDays() . ' ' . Yii::t('app', 'SHORT_MONTH');
        });
        $subscription = Yii::$app->request->post('subscription');
        $this->manageSelectedSubscriptions($durations, $services, $subscription, $subscriptions);

        return $this->renderAjax('/client/company/add/pre-invoice', [
            'types' => ServiceType::getTranslatedNames(),
            'serviceTypeId' => $serviceTypeId,
            'subscription' => $subscription,
            'subscriptions' => $subscriptions,
            'durations' => $durations,
            'companyId' => $companyId,
            'companyUsers' => ArrayHelper::map(User::findAllCompanyUsers($companyId), 'id', function (User $user) {
                $id = $user->id;
                $name = $user->getNameAndSurname();
                return "$name (ID: $id)";
            }),
            'userService' => new UserService([
                'scenario' => UserService::SCENARIO_ADMIN_CREATES_PRE_INVOICE,
                'user_id' => Yii::$app->request->post('user'),
                'start_date' => Yii::$app->request->post('startDate'),
            ]),
        ]);
    }

    /**
     * Adds or removes administrator selected user subscription to or from subscriptions list
     *
     * @param array $durations List of subscription available months
     * @param array|Service[] $services List of all available services
     * @param null|integer $subscription Subscription ID that needs to be added to subscriptions list
     * @param array $subscriptions List of administrator selected subscriptions
     * @return null
     */
    private function manageSelectedSubscriptions($durations, $services, $subscription, &$subscriptions)
    {
        $action = Yii::$app->request->post('action');
        switch ($action) {
            case self::ACTION_ADD_SUBSCRIPTION_TO_LIST:
                return $this->addSubscriptionToList($durations, $services, $subscription, $subscriptions);
            case self::ACTION_REMOVE_SUBSCRIPTION_FROM_LIST:
                return $this->removeSubscriptionFromList($subscriptions);
            default:
                return null;
        }
    }

    /**
     * Adds administrator selected subscription to list of subscriptions
     *
     * @param array $durations List of subscription available months
     * @param array|Service[] $services List of all available services
     * @param null|integer $subscription Subscription ID that needs to be added to subscription list
     * @param array $subscriptions List of administrator selected subscriptions
     * @return null
     */
    private function addSubscriptionToList($durations, $services, $subscription, &$subscriptions)
    {
        if (is_null($subscription)) {
            return null;
        }

        /** @var Service $service */
        foreach ($services as $service) {
            if ($service->id != $subscription) {
                continue;
            }

            $name = $service->serviceType->name;
            $text = Yii::t('app', 'SUBSCRIPTION');
            $duration = $durations[$subscription];
            $subscriptions[$subscription] = "$name $text $duration";
        }

        return null;
    }

    /**
     * Removes administrator selected subscription from subscriptions list
     *
     * @param array $subscriptions List of administrator selected subscriptions
     * @return null
     */
    private function removeSubscriptionFromList(&$subscriptions)
    {
        /** @var null|integer $id Service ID that needs to be removed from list */
        $id = Yii::$app->request->post('serviceToRemove');
        if (is_null($id)) {
            return null;
        }

        unset($subscriptions[$id]);
        return null;
    }

    /**
     * Creates user company pre-invoice
     *
     * @todo refactor
     * @param null|integer $companyId Company ID
     * @param string $tab Company tab
     * @return Response Redirects administrator to user company
     */
    public function actionCreatePreInvoice($companyId = null, $tab = self::TAB_COMPANY_PRE_INVOICES)
    {
        $post = Yii::$app->request->post();
        if (!isset($post['UserService']) || empty($post['UserService'])) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_PRE_INVOICE_INVALID_DATA'));
            return $this->redirect(['client/company', 'lang' => Yii::$app->language,'id' => $companyId, 'tab' => $tab]);
        }

        Yii::$app->db->beginTransaction();
        foreach ($post['UserService'] as $element) {
            if (!is_array($element)) {
                continue;
            }

            foreach ($element as $attribute => $serviceId) {
                $userService = new UserService([
                    'scenario' => UserService::SCENARIO_ADMIN_CREATES_PRE_INVOICE,
                    'service_id' => $serviceId,
                    'paid' => UserService::NOT_PAID,
                    'paid_by' => UserService::DEFAULT_PAID_BY,
                    'admin_id' => UserService::DEFAULT_ADMIN_ID,
                    'generated_by' => Yii::$app->admin->id,
                    'response' => UserService::DEFAULT_RESPONSE,
                ]);
                $userService->load(Yii::$app->request->post());
                $userService->calculateEndDateFromService();
                $userService->convertStartDateToTimestamp();
                $userService->setPriceFromService();
                $userService->setScenario(UserService::SCENARIO_SYSTEM_CREATES_PRE_INVOICE);
                if (!$userService->validate()) {
                    Yii::$app->db->transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_PRE_INVOICE_INVALID_USER_SERVICE_DATA'));
                    return $this->redirect(['client/company', 'lang' => Yii::$app->language,'id' => $companyId, 'tab' => $tab]);
                }
                if (!$userService->save(false)) {
                    Yii::$app->db->transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_PRE_INVOICE_CANNOT_SAVE_USER_SERVICE'));
                    return $this->redirect(['client/company', 'lang' => Yii::$app->language,'id' => $companyId, 'tab' => $tab]);
                }

                if (!UserInvoice::create($userService->id, $userService->service, UserInvoice::PRE_INVOICE, $userService->user_id)) {
                    Yii::$app->db->transaction->rollBack();
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'CREATE_PRE_INVOICE_CANNOT_SAVE_USER_INVOICE'));
                    return $this->redirect(['client/company', 'lang' => Yii::$app->language,'id' => $companyId, 'tab' => $tab]);
                }
            }
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'CREATE_PRE_INVOICE_CREATED_SUCCESSFULLY'));
        Yii::$app->db->transaction->commit();
        return $this->redirect(['client/company', 'lang' => Yii::$app->language,'id' => $companyId, 'tab' => $tab]);
    }

    /**
     * Validates whether provided VAT code is valid
     *
     * @return string
     */
    public function actionValidateVatCode()
    {
        $code = Yii::$app->request->post('code');
        list($code, $number) = User::splitVatCode($code, User::VAT_CODE_MIN_LENGTH);
        $response = User::getInfoFromECByVatCode($code, $number);
        return $response['valid'] ? Yii::t('element', 'N-C-15a') : Yii::t('element', 'N-C-15b');
    }

    /**
     * Renders company name change form
     *
     * @param null|integer $id Company ID
     *
     * @return string
     * @throws NotFoundHttpException If company not found
     */
    public function actionRenderCompanyNameChangeForm($id = null)
    {
        $company = Company::findOne($id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'COMPANY_NOT_FOUND'));
        }

        $titleScenario = Company::SCENARIO_ADMIN_CHANGES_COMPANY_TITLE;
        $nameAndSurnameScenario = Company::SCENARIO_ADMIN_CHANGES_COMPANY_NAME_SURNAME;
        $company->scenario = $company->isNatural() ? $nameAndSurnameScenario : $titleScenario;
        return $this->renderAjax('/client/company/edit/company-name', compact('company'));
    }

    /**
     * Changes company name
     *
     * @param null|integer $id Company ID
     *
     * @return Response
     * @throws NotAcceptableHttpException If company data is not valid
     * @throws NotFoundHttpException If company not found
     */
    public function actionChangeCompanyName($id = null)
    {
        $company = Company::findOne($id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'COMPANY_NOT_FOUND'));
        }

        $titleScenario = Company::SCENARIO_ADMIN_CHANGES_COMPANY_TITLE;
        $nameAndSurnameScenario = Company::SCENARIO_ADMIN_CHANGES_COMPANY_NAME_SURNAME;
        $company->scenario = $company->isNatural() ? $nameAndSurnameScenario : $titleScenario;
        $company->load(Yii::$app->request->post());
        if (!$company->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_COMPANY_DATA'));
        }

        $company->save(false);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'COMPANY_NAME_CHANGED_SUCCESSFULLY'));
        return $this->redirect([
            'client/company',
            'lang' => Yii::$app->language,
            'id' => $id,
            'tab' => self::TAB_COMPANY_INFO,
        ]);
    }
	
	/**
     * Saves company potentiality change
     * 
     * @return string
     */
	public function actionSavePotentiality()
    {
        $post = Yii::$app->request->post();

        $company = Company::findByCompanyId($post['companyId']);
        
        $company->saveCompanyPotentiality($post['status']);
        
        return $this->redirect(Yii::$app->request->referrer);
    }
}

