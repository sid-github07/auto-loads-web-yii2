<?php

namespace frontend\controllers;

use common\components\audit\Create;
use common\components\audit\Delete;
use common\components\audit\Log;
use common\components\audit\Pay;
use common\components\audit\Read;
use common\components\audit\Update;
use common\components\Credits;
use common\components\ElasticSearch\Loads;
use common\components\MainController;
use common\components\Model;
use common\components\PotentialHaulier;
use common\components\Searches24h;
use common\components\Trucks;
use common\models\CreditService;
use common\models\Load;
use common\models\LoadCity;
use common\models\LoadOpenedContacts;
use common\models\LoadPreview;
use common\models\LoadCar;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Class MyLoadController
 *
 * This controller is responsible for actions with my loads
 *
 * @package frontend\controllers
 */
class MyLoadController extends MainController
{
    /** @var null|integer|array List of loads IDs or specific load ID or null if load ID is not set */
    private $loadId;

    /** @var null|integer Currently logged-in user ID or null if user is guest */
    private $userId;

    /** @var null|string Load token to identify which user announced concrete load */
    private $token;

    /** @var null|array List of cities to filter my loads */
    private $cities;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'change-page-size',
                            'change-date',
                            'load-editing-form',
                            'load-editing',
                            'change-visibility',
                            'remove',
                            'filtration',
                            'change-load-table-activity',
                            'load-adv-form',
                            'open-contacts-form',
                            'load-preview-form',
                            'load-preview-buy',
                            'adv-load',
                            'open-contacts',
                            'load-form-potential-hauliers',
                            'load-form-searches-in-24',
                            'load-form-trucks',
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'change-page-size' => ['POST'],
                    'change-date' => ['POST'],
                    'load-editing-form' => ['POST'],
                    'load-adv-form' => ['POST'],
                    'open-contacts-form' => ['POST'],
                    'load-editing' => ['POST'],
                    'change-visibility' => ['POST'],
                    'remove' => ['POST'],
                    'filtration' => ['POST'],
                    'change-load-table-activity' => ['POST'],
                    'adv-load' => ['POST'],
                    'open-contacts' => ['POST'],
                    'load-preview-buy' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->setId(Yii::$app->request->post('id'));
        $this->setUserId(Yii::$app->user->isGuest);
        $this->setToken(Yii::$app->request->get('token'));
        $this->setCities(Yii::$app->request->get('loadCities'));

        return parent::beforeAction($action);
    }

    /**
     * Sets loads IDs or specific load ID
     *
     * @param null|integer|array $id List of loads IDs or specific load ID or null if load ID is not specified
     */
    public function setId($id)
    {
        $this->loadId = $id;
    }

    /**
     * Returns list of loads IDs or specific load ID or null if load ID is not specified
     *
     * @return array|integer|null
     */
    public function getId()
    {
        return $this->loadId;
    }

    /**
     * Sets currently logged-in user ID
     *
     * @param boolean $isGuest Attribute whether user is guest
     */
    public function setUserId($isGuest)
    {
        $this->userId = $isGuest ? Load::DEFAULT_USER_ID : Yii::$app->user->id;
    }

    /**
     * Returns currently logged-in user ID
     *
     * @return integer|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets load token
     *
     * @param null|string $token Load token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns load token
     *
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets cities for loads filtration
     *
     * @param null|string $cities List of cities for loads filtration
     */
    public function setCities($cities)
    {
        if (!empty($cities)) {
            $cities = explode(',', $cities);
        }

        $this->cities = $cities;
    }

    /**
     * Returns list of cities for loads filtration
     *
     * @return null|array
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Changes my loads table page size
     *
     * @return string
     */
    public function actionChangePageSize()
    {
        return $this->renderMyLoadsTable();
    }

    /**
     * Changes load date
     *
     * @return string
     */
    public function actionChangeDate()
    {
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        if (is_null($load)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID'));
            return $this->renderMyLoadsTable();
        }

        $date = Yii::$app->request->post('date');
        if ($load->hasSameDate($date)) {
            return $this->renderMyLoadsTable();
        }

        $load->scenario = Load::SCENARIO_EDIT_LOAD_DATE;
        $load->date = strtotime($date);

        Yii::$app->db->beginTransaction();
        if (!$load->save()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_DATE_CANNOT_BE_CHANGED'));
            Yii::$app->db->transaction->rollBack();
            return $this->renderMyLoadsTable();
        }

        if (!Yii::$app->user->isGuest) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_LOAD_INFO, [$load]);
        }

        Loads::updateDate($load->id, $load->date);
        Yii::$app->db->transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_DATE_CHANGED_SUCCESSFULLY'));
        return $this->renderMyLoadsTable();
    }

    /**
     * Renders specific load editing form
     *
     * @return string
     */
    public function actionLoadEditingForm()
    {
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        return $this->renderAjax('/my-announcement/load-editing-form', compact('load'));
    }

    /**
     * Saves edited load information
     *
     * @return string
     */
    public function actionLoadEditing()
    {
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }

        $post = [];
        parse_str(Yii::$app->request->post('data'), $post);

        $load->scenario = Load::SCENARIO_EDIT_LOAD_INFO;
        $load->load($post);
        $load->setPrice();

        Yii::$app->db->beginTransaction();
        if (Model::hasChanges($load)) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_LOAD_INFO, [$load]);
        }

        if (!$load->save()) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('app', 'EDIT_LOAD_CANNOT_SAVE'));
            return $this->renderMyLoadsTable();
        }

        $cars = isset($post['LoadCar']) ? $post['LoadCar'] : [];
        $carChanges = LoadCar::hasChanges($load->loadCars, $cars);
        if ($carChanges['isUpdated']) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_LOAD_CARS, [$load]);
        }

        LoadCar::deleteAll(['load_id' => $load->id]);
        $loadCar = new LoadCar(['scenario' => LoadCar::SCENARIO_EDIT_CAR_INFO]);
        if (!$loadCar->create($load, $cars)) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'EDIT_LOAD_CANNOT_SAVE'));
            return $this->renderMyLoadsTable();
        }

        $carsQuantity = LoadCar::countCarsQuantities($cars);
        Loads::updateQuantity($load->id, $carsQuantity);
        Yii::$app->db->transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'EDIT_LOAD_UPDATED_SUCCESSFULLY'));
        return $this->renderMyLoadsTable();
    }

	/**
     * Changes load table view by user selection of which loads he wants to see //TODO possible merge with actionFiltration
     *
     * @return string new loads table
     */
    public function actionChangeLoadTableActivity()
    {
        return $this->renderMyLoadsTable();
    }

    /**
     * Changes selected loads or specific load visibility
     *
     * @return string
     */
    public function actionChangeVisibility()
    {
        $newStatus = Yii::$app->request->post('newStatus', Load::NOT_ACTIVATED);
        if (!in_array($newStatus, Load::getActivities())) {
            return $this->renderMyLoadsTable();
        }

        $loads = Load::findAll(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        foreach ($loads as $load) {
            Yii::$app->db->beginTransaction();

            if ($newStatus == Load::ACTIVATED && $load->isExpired()) {
                if (Yii::$app->user->identity->hasEnoughCredits(Load::EXPIRED_LOAD_REACTIVATION_CREDITS)) {
                    $load->extendExpiryDate();
                    Credits::spend(Load::EXPIRED_LOAD_REACTIVATION_CREDITS);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_EXTEND_LOAD_EXPIRY_TIME'));
                    Yii::$app->db->transaction->rollBack();
                    return $this->renderMyLoadsTable();
                }
            }

            $load->active = $newStatus;
            $load->scenario = Load::SCENARIO_CHANGE_ACTIVE;
            if (!$load->save()) {
                Yii::$app->db->transaction->rollBack();
                return $this->renderMyLoadsTable();
            }

            if (!Yii::$app->user->isGuest) {
                Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_LOAD_ACTIVE_STATUS, [$load]);
            }

            Loads::updateActivity($load->id, $newStatus, $load->token, $load->user_id);
            Yii::$app->db->transaction->commit();
        }

        $message = count($loads) > 1 ? 'MULTIPLE_LOADS_ACTIVITY_UPDATED_SUCCESSFULLY' : 'LOAD_ACTIVITY_UPDATED_SUCCESSFULLY';
        Yii::$app->session->setFlash('success', Yii::t('alert', $message));
        return $this->renderMyLoadsTable();
    }

    /**
     * Removes selected loads or specific load
     *
     * @return string
     */
    public function actionRemove()
    {
        $condition = ['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()];
        $numberOfRemovedLoads = Load::updateAll(['status' => Load::INACTIVE], $condition);

        $loads = Load::findAll($condition);
        foreach ($loads as $load) {
            Loads::updateStatus($load->id, Load::INACTIVE, $load->token, $load->user_id);
        }

        if (!Yii::$app->user->isGuest) {
            Log::user(Delete::ACTION, Delete::PLACEHOLDER_USER_REMOVED_MULTIPLE_LOADS, $loads);
        }

        $text = $numberOfRemovedLoads > 1 ? 'MULTIPLE_LOADS_REMOVED_SUCCESSFULLY' : 'LOAD_REMOVED_SUCCESSFULLY';
        Yii::$app->session->setFlash('success', Yii::t('alert', $text));

        return $this->renderMyLoadsTable();
    }

    /**
     * Returns rendered my loads table page
     *
     * @return string
     */
    private function renderMyLoadsTable()
    {
        $dataProvider = Load::getMyLoadsDataProvider($this->getToken(), $this->getCities());
        return $this->renderAjax('/my-announcement/my-loads-table', compact('dataProvider'));
    }

    /**
     * Filters my loads table by loads cities
     *
     * @return string
     */
    public function actionFiltration()
    {
        $loadCities = Yii::$app->request->post('loadCities');
        $dataProvider = Load::getMyLoadsDataProvider($this->getToken(), $loadCities);
        return $this->renderAjax('/my-announcement/my-loads-table', compact('dataProvider'));
    }

    /**
     * @return string
     */
    public function actionLoadAdvForm()
    {
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        $user = Yii::$app->user->identity;
        $subscriptionCredits = $user->getSubscriptionCredits();
        $subscriptionEndTime = $user->getSubscriptionEndTime();
        $advCredits = User::getServiceCredits();
        $advDayList = array_combine($load->getDaysRanges(), $load->getDaysRanges());
        $advPositionList = array_combine($load->getCarPosRanges(), $load->getCarPosRanges());
        return $this->renderAjax('/my-announcement/load-advertisement-form', [
            'load' => $load,
            'subscriptionCredits' => $subscriptionCredits,
            'subscriptionEndTime' => $subscriptionEndTime,
            'advCredits' => $advCredits,
            'advDayList' => $advDayList,
            'advPositionList' => $advPositionList,
            'token' => $this->getToken(),
        ]);
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    public function actionLoadPreviewForm()
    {
        if (Yii::$app->request->post('loadId') === null) {
            throw new \ErrorException('Can not determine load');
        }

        $load = Load::findOne(['id' => Yii::$app->request->post('loadId')]);
        if (is_null($load)) {
            throw new \ErrorException('Can not determine load');
        }

        $user = Yii::$app->user->identity;
        if ($user->hasBoughtService($load, CreditService::CREDIT_TYPE_LOAD_PREVIEW_VIEW) === true) {
            Log::user(Read::ACTION, Read::PLACEHOLDER_USER_PREVIEWED_LOAD_VIEW_DATA, [$load]);
        }

        $token = $this->getToken();
        $service = CreditService::find()->where(['credit_type' => CreditService::CREDIT_TYPE_LOAD_PREVIEW_VIEW, 'status' => CreditService::STATUS_ACTIVE])->one();
        $dataProvider = LoadPreview::getPreviewDatProvider(Yii::$app->request->post('loadId'));

        return $this->renderAjax('/my-announcement/load-preview-data-view-form', compact('dataProvider', 'load', 'service', 'token'));
    }

    /**
     * @return array|string|Response
     * @throws \ErrorException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionLoadFormPotentialHauliers()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $serviceType = CreditService::CREDIT_TYPE_OPEN_POTENTIAL_HAULIERS;

        $load = Load::findOne(['id' => Yii::$app->request->post('loadId')]);
        if (!$load instanceof Load) {
            throw new \ErrorException('Load not defined');
        }

        $token = $this->getToken();
        /** @var CreditService $service */
        $service = CreditService::find()->where([
            'credit_type' => $serviceType,
            'status' => CreditService::STATUS_ACTIVE
        ])->one();
        $potentialHauliers = new PotentialHaulier($load);

        // Getting contacts
        if (Yii::$app->request->post('action') === 'get-contacts') {
            $userList = array_column($potentialHauliers->getPotentialHauliersByHistoryOfSearch(), 'user_id');
            $userIdRequested = Yii::$app->getRequest()->post('userId');
            if (in_array($userIdRequested, $userList)) {
                $openedBy = Yii::$app->getUser()->getIdentity();
                Log::user(Pay::ACTION, Pay::PAYFOR_PREVIEWS, $service->credit_cost);
                if ($openedBy->service_credits < $service->credit_cost
                    && $openedBy->hasSubscription()
                    && $openedBy->getSubscriptionCredits() >= $service->credit_cost
                ) {
                    Log::user(Pay::ACTION, Pay::PAYFOR_POTENTIAL_HAULERS_SUBSCR, $service->credit_cost);
                }
                $loadOpener = new LoadOpenedContacts();
                $result = $loadOpener->logOpen($userIdRequested, $load, $service, false);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($result) {
                    return [
                        'alert' => $this->renderAjax('/my-announcement/forms/parts/preview-alert', [
                            'service' => $service,
                            'user' => $user
                        ]),
                        'content' => $this->renderAjax('/my-announcement/forms/parts/show-contacts',
                            ['user' => User::findOne($userIdRequested)]),
                        'params' => [
                            'viewed' => Yii::t('element', 'viewed')
                        ],
                        'error' => null
                    ];
                } else {
                    $errors = array_values($loadOpener->getFirstErrors());
                    if (empty($errors)) {
                        $error = Yii::t('element', 'An unknown error occured. Please, contact with technical support');
                    } else {
                        $error = array_shift($errors);
                    }
                    return [
                        'error' => $error
                    ];
                }
            } else {
                throw new \Exception(sprintf("User not found"));
            }
        }

        return $this->renderAjax('/my-announcement/forms/potential-hauliers-preview', [
            'potentialHauliers' => $potentialHauliers->getPotentialHauliersPreviews(),
            'opened' => LoadOpenedContacts::find()->select('user_id')->indexBy('user_id')->where([
                'service_id' => $service->id,
                'opened_by' => $user->id,
                'load_id' => $load->id
            ])->column(),
            'load' => $load,
            'service' => $service,
            'token' => $token,
            'user' => $user,
            'formName' => 'load-form-potential-hauliers'
        ]);
    }

    /**
     * @return array|string|Response
     * @throws \ErrorException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionLoadFormSearchesIn24()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $serviceType = CreditService::CREDIT_TYPE_OPEN_SEARCHES_IN_24H;

        $load = Load::findOne(['id' => Yii::$app->request->post('loadId')]);
        if (!$load instanceof Load) {
            throw new \ErrorException('Load not defined');
        }
        $loadCities = $unloadCities = [];
        foreach ($load->loadCities as $city) {
            if ($city->type === LoadCity::LOADING) {
                $loadCities[] = $city;
            } else {
                $unloadCities[] = $city;
            }
        }

        $token = $this->getToken();
        /** @var CreditService $service */
        $service = CreditService::find()->where([
            'credit_type' => $serviceType,
            'status' => CreditService::STATUS_ACTIVE
        ])->one();

        $searchesLast24h = (new Searches24h($load))->getSearchesLast24h();

        // Getting contacts
        if (Yii::$app->request->post('action') === 'get-contacts') {
            $userList = array_column($searchesLast24h, 'user_id');
            $userIdRequested = Yii::$app->getRequest()->post('userId');
            if (in_array($userIdRequested, $userList)) {
                $openedBy = Yii::$app->getUser()->getIdentity();
                Log::user(Pay::ACTION, Pay::PAYFOR_SIMILAR_SEARCHES, $service->credit_cost);
                if ($openedBy->service_credits < $service->credit_cost
                    && $openedBy->hasSubscription()
                    && $openedBy->getSubscriptionCredits() >= $service->credit_cost
                ) {
                    Log::user(Pay::ACTION, Pay::PAYFOR_SIMILAR_SEARCHES_SUBSCR, $service->credit_cost);
                }
                $loadOpener = new LoadOpenedContacts();
                $result = $loadOpener->logOpen($userIdRequested, $load, $service, false);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($result) {
                    return [
                        'alert' => $this->renderAjax('/my-announcement/forms/parts/preview-alert', [
                            'service' => $service,
                            'user' => $user
                        ]),
                        'content' => $this->renderAjax('/my-announcement/forms/parts/show-contacts',
                            ['user' => User::findOne($userIdRequested)]),
                        'params' => [
                            'viewed' => Yii::t('element', 'viewed')
                        ],
                        'error' => null
                    ];
                } else {
                    $errors = array_values($loadOpener->getFirstErrors());
                    if (empty($errors)) {
                        $error = Yii::t('element', 'An unknown error occured. Please, contact with technical support');
                    } else {
                        $error = array_shift($errors);
                    }
                    return [
                        'error' => $error
                    ];
                }
            } else {
                throw new \Exception(sprintf("User not found"));
            }
        }

        return $this->renderAjax('/my-announcement/forms/searches-in-24h-preview', [
            'searchesLast24h' => $searchesLast24h,
            'opened' => LoadOpenedContacts::find()->select('user_id')->indexBy('user_id')->where([
                'service_id' => $service->id,
                'opened_by' => $user->id,
                'load_id' => $load->id
            ])->column(),
            'load' => $load,
            'service' => $service,
            'token' => $token,
            'user' => $user,
            'formName' => 'load-form-searches-in-24',
            'loadCities' => $loadCities,
            'unloadCities' => $unloadCities,
        ]);
    }

    /**
     * Free for users with membership
     * @return array|string|Response
     * @throws \ErrorException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionLoadFormTrucks()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $serviceType = CreditService::CREDIT_TYPE_OPEN_TRUCKS;
        $freeForMemberships = true; // Free for memberships

        $load = Load::findOne(['id' => Yii::$app->request->post('loadId')]);
        if (!$load instanceof Load) {
            throw new \ErrorException('Load not defined');
        }
        $loadCities = $unloadCities = [];
        foreach ($load->loadCities as $city) {
            if ($city->type === LoadCity::LOADING) {
                $loadCities[] = $city;
            } else {
                $unloadCities[] = $city;
            }
        }

        $token = $this->getToken();
        /** @var CreditService $service */
        $service = CreditService::find()->where([
            'credit_type' => $serviceType,
            'status' => CreditService::STATUS_ACTIVE
        ])->one();

        $trucks = (new Trucks($load))->getTrucks();

        // Getting contacts
        if (Yii::$app->request->post('action') === 'get-contacts') {
            $userList = array_column($trucks, 'user_id');
            $userIdRequested = Yii::$app->getRequest()->post('userId');
            if (in_array($userIdRequested, $userList)) {
                $openedBy = Yii::$app->getUser()->getIdentity();
                if (!($freeForMemberships && $openedBy->hasSubscription())) {
                    Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_CONTACTS, $service->credit_cost);
                    if ($openedBy->service_credits >= $service->credit_cost
                        && $openedBy->hasSubscription()
                        && $openedBy->getSubscriptionCredits() >= $service->credit_cost
                    ) {
                        Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_CONTACTS_SUBSCR, $service->credit_cost);
                    }
                }
                $loadOpener = new LoadOpenedContacts();
                $result = $loadOpener->logOpen($userIdRequested, $load, $service, $freeForMemberships);
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($result) {
                    return [
                        'alert' => $this->renderAjax('/my-announcement/forms/parts/preview-alert', [
                            'service' => $service,
                            'user' => $user,
                        ]),
                        'content' => $this->renderAjax('/my-announcement/forms/parts/show-contacts',
                            ['user' => User::findOne($userIdRequested)]),
                        'params' => [
                            'viewed' => Yii::t('element', 'viewed')
                        ],
                        'error' => null
                    ];
                } else {
                    $errors = array_values($loadOpener->getFirstErrors());
                    if (empty($errors)) {
                        $error = Yii::t('element', 'An unknown error occured. Please, contact with technical support');
                    } else {
                        $error = array_shift($errors);
                    }
                    return [
                        'error' => $error
                    ];
                }
            } else {
                throw new \Exception(sprintf("User not found"));
            }
        }

        return $this->renderAjax('/my-announcement/forms/trucks-preview', [
            'trucks' => $trucks,
            'opened' => LoadOpenedContacts::find()->select('user_id')->indexBy('user_id')->where([
                'service_id' => $service->id,
                'opened_by' => $user->id,
                'load_id' => $load->id
            ])->column(),
            'load' => $load,
            'service' => $service,
            'token' => $token,
            'user' => $user,
            'formName' => 'load-form-trucks',
            'loadCities' => $loadCities,
            'unloadCities' => $unloadCities,
            'freeForMemberships' => $freeForMemberships
        ]);
    }


    /**
     * @return string
     * @throws \ErrorException
     */
    public function actionLoadPreviewBuy()
    {
        if (Yii::$app->request->get('id') === null) {
            throw new \ErrorException('Can not determine load');
        }

        $load = Load::findOne(['id' => Yii::$app->request->get('id')]);
        if (is_null($load)) {
            throw new \ErrorException('Can not determine load');
        }

        if ($load->user_id !== Yii::$app->user->id) {
            throw new \ErrorException('Unknown load');
        }


        $alert = 'preview_buy_success';
        $service = CreditService::buy($load, CreditService::CREDIT_TYPE_LOAD_PREVIEW_VIEW, false);
        if ($service) {
            $cost = $service->credit_cost;
            Log::user(Pay::ACTION, Pay::PAYFOR_PREVIEWS, $cost);
        }
        
        $key = 'success';
        if ($service === false) {
            $alert = 'preview_buy_error';
            $key = 'error';
        } else {
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_BOUGHT_VIEW_LOAD_PREVIEW_SERVICE, [$load]);
        }

        Yii::$app->session->setFlash($key, Yii::t('alert', $alert));
        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAdvLoad()
    {
        $post = Yii::$app->request->post();
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        $load->scenario = Load::SCENARIO_UPDATE_LOAD_ADV;
        $user = User::findById($this->getUserId());

        if (is_null($load)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        if (is_null($user)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_NOT_FOUND_BY_ID'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        if ($load->days_adv !== 0) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ALREADY_ADVERT'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        if (!$load->load($post) && !$load->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INCORRECT_ADVERT_DATA'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $advCost = $load->days_adv * $load->car_pos_adv;
        if (!$user->hasEnoughCombinedServiceCredits($advCost)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_ADVERTS_PTS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        Yii::$app->db->beginTransaction();
        if ($advCost > 0) {
            $user->useCombinedServiceCredits($advCost);
            $load->setAdvertisementSubmitTime();
        }

        if ($load->save()) {
            Yii::$app->db->transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_ADVERT_SUCCESS'));
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_ADVERTISED_LOAD, [$load]);
        } else {
            Yii::$app->db->transaction->rollback();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ADVERT_FAILURE'));
        }

        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @return string
     */
    public function actionOpenContactsForm()
    {
        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);

        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        $serviceCredits = User::getServiceCredits();

        $user = Yii::$app->user->identity;
        $subscriptionCredits = $user->getSubscriptionCredits();
        $subscriptionEndTime = $user->getSubscriptionEndTime();

        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);

        $actionUrl = Url::to([
            'my-load/open-contacts',
            'lang' => Yii::$app->language,
            'token' => $this->getToken(),
            'id' => $load->id
        ]);

        $dayList = array_merge(
            [0 => Yii::t('element', 'select_open_contacts_days')],
            $load->getDaysRanges()
        );

        return $this->renderAjax('/my-announcement/open-contacts-form', [
            'model' => $load,
            'actionUrl' => $actionUrl,
            'serviceCredits' => $serviceCredits,
            'subscriptionCredits' => $subscriptionCredits,
            'subscriptionEndTime' => $subscriptionEndTime,
            'openContactsCost' => $openContactsCost,
            'dayList' => $dayList,
        ]);
    }

    /**
     * Handles request to set loan open contacts
     *
     * @return string
     */
    public function actionOpenContacts()
    {
        $post = Yii::$app->request->post();

        $load = Load::findOne(['id' => $this->getId(), 'token' => $this->getToken(), 'user_id' => $this->getUserId()]);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        $load->setScenario(Load::SCENARIO_UPDATE_OPEN_CONTACTS);
        $user = User::findById($this->getUserId());

        if (is_null($user)) {
            return Yii::t('alert', 'USER_NOT_FOUND_BY_ID');
        }
        if (!$load->load($post) && !$load->validate()) {
            return Yii::t('alert', 'INCORRECT_OPEN_CONTACTS_DATA');
        }

        if ($load->isOpenContacts()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ALREADY_HAS_OPEN_CONTACTS', [
                'date' => $load->getOpenContactsExpiry()
            ]));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        $totalCost = $openContactsCost * $load->open_contacts_days;

        if (!$user->hasEnoughCombinedServiceCredits($totalCost)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS_FOR_OPEN_CONTACTS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        Yii::$app->db->beginTransaction();
        if ($totalCost > 0) {
            $user->useCombinedServiceCredits($totalCost);
            $load->setOpenContactsExpiry();
        }

        if ($load->save()) {
            Yii::$app->db->transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_OPEN_CONTACTS_SUCCESS'));
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_SET_LOAD_OPEN_CONTACTS, [$load]);
        } else {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_OPEN_CONTACTS_FAILURE'));
        }

        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }
}