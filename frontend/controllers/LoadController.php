<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Create;
use common\components\audit\Log;
use common\components\audit\Pay;
use common\components\audit\Read;
use common\components\audit\Search;
use common\components\audit\Map;
use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;
use common\components\ElasticSearch\Suggestions;
use common\components\MailLanguage;
use common\components\MainController;
use common\components\RoundTrips;
use common\models\City;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use common\models\LoadPreview;
use common\models\LoadSuggestion;
use common\models\LoadSuggestionCity;
use common\models\User;
use common\models\UserLanguage;
use common\models\CreditService;
use common\models\LoggingActivatedEmailServices;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\Controller;

/**
 * Class LoadController
 *
 * @package frontend\controllers
 */
class LoadController extends MainController
{
    /** @const string Alphanumeric string to identify that only Cron job is accessing deactivation method */
    const DEACTIVATE_TOKEN = 'YkX5Us8peIXstHyj52i8FjOW935YnI2ib6uTuPwREzyGVqwtA7OnEiBR1WFaKuKA';

    /** @const string Alphanumeric string to identify that only Cron job is accessing removing old loads method */
    const REMOVE_TOKEN = 'W40dO61KT7R27j1CgrBwlX6cmKjHj37KHQsD9dDrVSkfnudbp8ecKJJRploPA1kM';

    /** @const string Alphanumeric string to identify that only Cron job is accessing check for new load suggestion method */
    const SUGGESTIONS_TOKEN = 'YK8f1hsALxmHtLuHuB_y5wj9aLeZdozKUAzrOK1xGfasW5jyuk75_XwCHgvasfYV';

    /** @const integer After how many days old loads will be removed */
    const RECYCLE_DAYS = 90;

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
                            'announce',
                            'announce-validation',
                            'search',
                            'search-results',
                            'preview',
                            'round-trips',
                            'round-trips-results',
                            'deactivate',
                            'remove-old',
                            'activate-load'
                            'hide-load-suggestion',
                            'loads',
                            'preview-load-info',
                            'preview-expired-load-info',
                            'check-for-newest-suggestions',
                            'newest-suggestions',
                            'reject-newest-suggestions',
                            'preview-load-link',
                            'render-map',
                            'render-filters',
                            'render-map-contact',
                            'log-open-map',
                            'send-mail-user-log',
                            'change-email-address',
                            'remove-load',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'suggestions',
                            'validate-suggestions-filter',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'suggestions',
                            'validate-suggestions-filter',
                        ],
                        'allow' => false,
                        'denyCallback' => function () {
                            return $this->redirect(['site/login', 'lang' => Yii::$app->language]);
                        }
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'announce' => ['GET', 'POST'],
                    'announce-validation' => ['POST'],
                    'search' => ['GET'],
                    'search-results' => ['GET'],
                    'preview' => ['POST'],
                    'log-open-map' => ['POST'],
                    'round-trips' => ['GET', 'POST'],
                    'round-trips-results' => ['GET'],
                    'deactivate' => ['GET'],
                    'remove-old' => ['GET'],
                    'hide-load-suggestion' => ['GET'],
                    'suggestions' => ['GET', 'POST'],
                    'validate-suggestions-filter' => ['GET'],
                    'loads' => ['GET'],
                    'preview-load-info' => ['POST'],
                    'newest-suggestions' => ['GET'],
                    'reject-newest-suggestions' => ['GET'],
                    'send-mail-user-log' => ['POST'],
                    'change-email-address' => ['POST'],
                    'activate-load' => ['POST'],
                    'remove-load' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders load announce page and saves announced load to database
     *
     * @return string|Response
     */
    public function actionAnnounce()
    {
        $load = new Load(['scenario' => Load::SCENARIO_ANNOUNCE_CLIENT]);
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_ANNOUNCE_CLIENT]);
        $user = Yii::$app->user->identity;

        if (Yii::$app->user->isGuest) {
            $serviceCredits = 0;
            $subscriptionCredits = 0;
            $subscriptionEndTime = '';
        } else {
            $serviceCredits = $user->service_credits;
            $subscriptionCredits = $user->getSubscriptionCredits();
            $subscriptionEndTime = $user->getSubscriptionEndTime();
        }

        $advertDayList = array_merge(
            [0 => Yii::t('element', 'select_adv_day_count')],
            $load->getDaysRanges()
        );
        $openContactsDayList = array_merge(
            [0 => Yii::t('element', 'select_open_contacts_days')],
            $load->getDaysRanges()
        );
        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);

        if (Yii::$app->request->isGet) {
            return $this->render('announce', compact('load', 'loadCity', 'serviceCredits',
                'subscriptionCredits', 'subscriptionEndTime', 'openContactsCost',
                'advertDayList', 'openContactsDayList'));
        }

        if ($load->load(Yii::$app->request->post()) && !$load->canAnnounceLoad(Yii::$app->user->isGuest)) {
            $link = Html::a(Yii::t('alert', 'SUBSCRIPTION'), ['subscription/index', 'lang' => Yii::$app->language], [
                'class' => 'subscription-link',
            ]);
            Yii::$app->session->setFlash('error',
                Yii::t('alert', 'LOAD_ANNOUNCE_NO_SUBSCRIPTION', ['subscription' => $link]));
            return $this->redirect(['load/announce', 'lang' => Yii::$app->language]);
        }

        /** @var User $user */
        Yii::$app->db->beginTransaction();

        if (!Yii::$app->user->isGuest && $user->hasSubscription()) {
            if ($user->canPayForLoadAnnouncement()) {
                $user->useCredits(Load::LOAD_ANNOUNCEMENT_CREDITS);
                $user->setScenario(User::SCENARIO_UPDATE_CURRENT_CREDITS);
                $user->save();

                Log::user(Pay::ACTION, Pay::PAYFOR_LOAD_PROMO_SUBSCR, Load::LOAD_ANNOUNCEMENT_CREDITS);
                Log::user(Pay::ACTION, Pay::PAYFOR_LOAD_PROMO, Load::LOAD_ANNOUNCEMENT_CREDITS);
            } else {
                Yii::$app->db->transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_EXTEND_LOAD_EXPIRY_TIME'));
                return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
            }
        }

        if (!$this->handleCreditServices($load, $openContactsCost, $user)) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $loadCar = new LoadCar(['scenario' => LoadCar::SCENARIO_ANNOUNCE_CLIENT]);
        if ($this->isAnnouncedSuccessfully($load, $loadCity, $loadCar)) {
            $this->updateElasticSearchCities();
            $this->addLoadToElasticSearch($load);
            $this->showSuccessLoadAnnounceAlert($load, $loadCity);
            $this->logUserAnnouncedLoad($load);
            Yii::$app->db->transaction->commit();
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language, 'token' => $load->token]);
        }

        Yii::$app->db->transaction->rollBack();
        Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ANNOUNCE_CANNOT_ANNOUNCE_LOAD'));

        $userCredService = new UserCreditService();
        return $this->render('announce', compact('load', 'loadCity', 'serviceCredits',
            'subscriptionCredits', 'subscriptionEndTime', 'openContactsCost',
            'advertDayList', 'openContactsDayList'));
    }

    /**
     * Checks whether load announced successfully
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load cities model
     * @param LoadCar $loadCar Load cars model
     * @return boolean
     */
    private function isAnnouncedSuccessfully(Load &$load, LoadCity &$loadCity, LoadCar &$loadCar)
    {
        $post = Yii::$app->request->post();
        $selectedCities = isset($post['LoadCity']) ? $post['LoadCity'] : [];
        $selectedCars = isset($post['LoadCar']) ? $post['LoadCar'] : [];

        return $load->load($post) &&
            $load->create() &&
            $load->updateCode() &&
            $loadCity->create($load->id, $selectedCities) &&
            $loadCar->create($load, $selectedCars) &&
            $load->sendMyLoadsLink();
    }

    /**
     * Sets Load model credit service attributes and deducts service credits from user
     *
     * @param Load $load Load model
     * @param integer $openContactsPrice Open contacts cost in credits
     * @param User $user Current user identity
     * @return boolean Whether credit services were handled successfully
     */
    private function handleCreditServices(Load &$load, $openContactsPrice, $user)
    {
        $advertisemenCost = $load->days_adv * $load->car_pos_adv;
        $openContactsCost = $load->open_contacts_days * $openContactsPrice;
        $totalServiceCost = $advertisemenCost + $openContactsCost;

        if ($totalServiceCost == 0 || Yii::$app->user->isGuest || !$user->hasSubscription()) {
            unset($load->days_adv);
            unset($load->car_pos_adv);
            unset($load->open_contacts_days);
            return true;
        }

        if (!$user->hasEnoughCombinedServiceCredits($totalServiceCost)) {
            return false;
        }

        $userServiceCredits = $user->service_credits;
        Log::user(Pay::ACTION, Pay::PAYFOR_LOAD_OPEN_CONTACTS, $totalServiceCost);
        if ($userServiceCredits < $totalServiceCost && $userServiceCredits > 0) {
            // some credits will use from service or custom (subscr) credits
            $restCredits = $totalServiceCost - $userServiceCredits;
            Log::user(Pay::ACTION, Pay::PAYFOR_LOAD_OPEN_CONTACTS_SUBSCR, $restCredits);
        } else {
            Log::user(Pay::ACTION, Pay::PAYFOR_LOAD_OPEN_CONTACTS_SUBSCR, $totalServiceCost);
        }
        $user->useCombinedServiceCredits($totalServiceCost);

        if ($advertisemenCost > 0) {
            $load->setAdvertisementSubmitTime();
        } else {
            unset($load->days_adv);
            unset($load->car_pos_adv);
        }

        if ($openContactsCost > 0) {
            $load->setOpenContactsExpiry();
        } else {
            unset($load->open_contacts_days);
        }

        return true;
    }

    /**
     * Updates elasticsearch cities
     *
     * @param array $post POST data
     */
    private function updateElasticSearchCities($post = [])
    {
        if (empty($post)) {
            $post = Yii::$app->request->post();
        }

        $loadCitiesIds = isset($post['LoadCity']['loadCityId']) ? $post['LoadCity']['loadCityId'] : [];
        $unloadCitiesIds = isset($post['LoadCity']['unloadCityId']) ? $post['LoadCity']['unloadCityId'] : [];

        foreach ($loadCitiesIds as $loadCityId) {
            Cities::updatePopularity($loadCityId, $unloadCitiesIds);
        }

        foreach ($unloadCitiesIds as $unloadCityId) {
            Cities::updatePopularity($unloadCityId);
        }
    }

    /**
     * Adds load to elastic search
     *
     * @param Load $load Load model
     * @param array $post POST data
     */
    private function addLoadToElasticSearch(Load $load, $post = [])
    {
        if (empty($post)) {
            $post = Yii::$app->request->post();
        }

        $loadCitiesIds = isset($post['LoadCity']['loadCityId']) ? $post['LoadCity']['loadCityId'] : [];
        $unloadCitiesIds = isset($post['LoadCity']['unloadCityId']) ? $post['LoadCity']['unloadCityId'] : [];
        $selectedCars = isset($post['LoadCar']) ? $post['LoadCar'] : [];
        $carsQuantity = LoadCar::countCarsQuantities($selectedCars);
        if ($carsQuantity > LoadCar::QUANTITY_MAX_VALUE) {
            return;
        }
        Loads::addLoad($load, $carsQuantity, $loadCitiesIds, $unloadCitiesIds);
    }

    /**
     * Shows success alert when load is announced successfully
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param array $post POST data
     */
    private function showSuccessLoadAnnounceAlert(Load $load, LoadCity $loadCity, $post = [])
    {
        if (empty($post)) {
            $post = Yii::$app->request->post();
        }

        $loadCityIdList = !empty($post['LoadCity']['loadCityId']) ? $post['LoadCity']['loadCityId'] : [];
        $unloadCityIdList = !empty($post['LoadCity']['loadCityId']) ? $post['LoadCity']['unloadCityId'] : [];
        $selectedCars = isset($post['LoadCar']) ? $post['LoadCar'] : [];
        $carsQuantity = LoadCar::countCarsQuantities($selectedCars);
        $cars = '';
        if (empty($carsQuantity)) {
            $loadInfo = LoadCar::getLoadInfo($load);
            $cars = str_replace('</div>', '', str_replace('<div>', ' ', $loadInfo));
        }

        // TODO: atkomentuoti kai Simonas norės, jog visus skelbimus reikėtų aktyvuoti kas 24 val.
        Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_SUCCESSFULLY', [
                'loadCity' => $loadCity->formatCitiesNames($loadCityIdList),
                'unloadCity' => $loadCity->formatCitiesNames($unloadCityIdList),
                'quantity' => (empty($carsQuantity) ? $cars : $carsQuantity . ' auto'),
            ]) . (Yii::$app->user->isGuest ? '<div class="activate-announce-alert">' . Yii::t('alert',
                    'LOAD_ANNOUNCE_CREATED_ACTIVATE_VIA_EMAIL', [
                        'email' => $load->email,
                    ]) . '</div>' : '') /*
            '<div class="activate-announce-alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' .
            Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_REACTIVATE') .
            (Yii::$app->user->isGuest ? Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_REACTIVATE_VIA_EMAIL') : '') . '</div>'*/
        );
    }

    /**
     * Logs user announced load
     *
     * @param Load $load Currently announced load model
     */
    private function logUserAnnouncedLoad(Load $load)
    {
        if (!Yii::$app->user->isGuest) {
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_ANNOUNCED_LOAD, [$load]);
        }
    }

    /**
     * Validates announce load form
     *
     * @return string
     */
    public function actionAnnounceValidation()
    {
        $loadAdapter = new AjaxValidationAdapter(new Load(), Load::SCENARIO_ANNOUNCE_CLIENT);
        return $loadAdapter->validate();
    }

    /**
     * Renders loads search page
     *
     * @deprecated
     * @param null|integer $cityId Load city ID
     * @return string
     */
    public function actionSearch($cityId = null)
    {
        Yii::$app->session->set('loads-filter-is-opened', true);
        return $this->redirect(['load/loads', 'lang' => Yii::$app->language], 301);
    }

    /**
     * Loads and validates load, load car and load city models
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $post POST data
     * @return boolean Whether POST load successfully and models are valid
     */
    private function loadAndValidate(Load &$load, LoadCar &$loadCar, LoadCity &$loadCity, $post = [])
    {
        $load->setAttributes($post);
        $loadCar->setAttributes($post);
        $loadCity->setAttributes($post);
        return $load->validate() & $loadCar->validate() & $loadCity->validate();
    }

    /**
     * Renders load preview
     *
     * @param string $params JSON encoded search params
     * @return string
     */
    public function actionPreview($params = '')
    {
        $loadId = Yii::$app->request->post('loadId');
        /** @var Load $load */
        $load = Load::findByLoadIdWithUser($loadId);
        if (is_null($load)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_PREVIEW_LOAD_NOT_FOUND'));
            return '';
        }

        $code = $load->code;

        if ($load->isOpenContacts()) {
            return $this->renderAjax('/load/preview/open-contacts', compact('load', 'code', 'params'));
        }

        if (Yii::$app->user->isGuest) {
            return $this->renderAjax('/load/preview/guest', compact('code', 'params'));
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (!$user->hasSubscription()) {
            return $this->renderAjax('/load/preview/simple-user', compact('code'));
        }

//        if (!$load->isOwner($user->id)) {
//            $errorMessage = $this->reduceCreditsForLoadPreview();
//            if (is_string($errorMessage)) {
//               return $errorMessage;
//            }
//        }

        $this->captureLoadPreview($loadId);
        Log::user(Read::ACTION, Read::PLACEHOLDER_USER_REVIEWED_LOAD_INFO, [$load]);
        return $this->renderAjax('/load/preview/subscriber', compact('load'));
    }

    /**
     * Captures that user previews information about load
     *
     * @param integer $loadId Load ID that user previews
     */
    private function captureLoadPreview($loadId)
    {
        $ip = Yii::$app->request->userIP;
        $userId = Yii::$app->user->id;
        $preview = new LoadPreview([
            'scenario' => LoadPreview::SCENARIO_USER_PREVIEWS_LOAD,
            'load_id' => $loadId,
            'user_id' => $userId,
            'ip' => $ip,
        ]);
        if ($preview->validate() && !$preview->hasAlreadyExists()) {
            $preview->save();
        }
    }

    /**
     * Renders loads suggestions
     *
     * @return string
     */
    public function actionSuggestions()
    {
        $log_service_details = LoggingActivatedEmailServices::find()->all();
        $load = new Load(['scenario' => Load::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS]);
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS]);
        $load->load(Yii::$app->request->get());
        $loadCity->load(Yii::$app->request->get());
        $getParams = Yii::$app->getRequest()->get();
        $query = Load::findAllActiveByIds($getParams);
        $loads = Load::getLoadsDataProvider($query,$pageSize);

        return $this->render('suggestions', compact('loads','load','loadCity','pageSize','log_service_details'));
    }


    /**
     * Adds current suggestions to seen so no email with current suggestions won't be sent
     *
     * @param array $signUpCityLoads
     * @return type
     */
    private function addToSeen($signUpCityLoads)
    {
        Suggestions::markAsSeen(Yii::$app->user->id);
        LoadSuggestionCity::removeSuggestionsSeenByUser($signUpCityLoads['direct'], Yii::$app->user->id);
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($signUpCityLoads['direct'] as $loads) {
            $loadSuggestionsByCity = new LoadSuggestionCity();
            $token = 'seen';
            $loadSuggestionsByCity->saveSuggestionsByCity(Yii::$app->user->id, $token, $loads);
        }
        $transaction->commit();
        return;
    }

    /**
     * Validates suggestions filter form data
     *
     * @return string
     */
    public function actionValidateSuggestionsFilter()
    {
        $loadAdapter = new AjaxValidationAdapter(new Load(), Load::SCENARIO_CLIENT_FILTERS_LOADS_SUGGESTIONS);
        return $loadAdapter->validate();
    }

    /**
     * Returns day searches suggestions
     *
     * @param Load $loadFilter Load model
     * @param LoadCity $loadCityFilter Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    private function getDaySuggestions(Load $loadFilter, LoadCity $loadCityFilter, $loadKeys)
    {
        $daySearches = Suggestions::getDaySearches();
        $directLoads = [];
        $additionalLoads = [];
        $fullUnloadLoads = [];
        foreach ($daySearches as $daySearch) {
            $source = $daySearch['_source'];
            list($load, $loadCar, $loadCity) = $this->loadSearchData($source, $loadFilter, $loadCityFilter);
            $this->getDirectLoads($load, $loadCar, $loadCity, $directLoads, $loadKeys);
            $this->getAdditionalLoads($load, $loadCar, $loadCity, $additionalLoads, $loadKeys);
            $this->getFullUnloadLoads($load, $loadCar, $loadCity, $fullUnloadLoads, $loadKeys);
        }

        $result = [
            'direct' => $directLoads,
            'additional' => $additionalLoads,
            'fullUnload' => $fullUnloadLoads,
        ];

        Load::removeHidden($result);
        return $result;
    }

    /**
     * Returns direct loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $directLoads List of direct loads
     * @param array $loadKeys loads id array
     */
    private function getDirectLoads(Load $load, LoadCar $loadCar, LoadCity $loadCity, &$directLoads = [], $loadKeys)
    {
        $loads = Loads::getDirectLoads($load, $loadCar, $loadCity, $loadKeys);

        foreach ($loads as $load) {
            if (!in_array($load, $directLoads)) {
                array_push($directLoads, $load);
            }
        }
    }

    /**
     * Returns additional loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $additionalLoads List of additional loads
     * @param array $loadKeys loads id array
     */
    private function getAdditionalLoads(
        Load $load,
        LoadCar $loadCar,
        LoadCity $loadCity,
        &$additionalLoads = [],
        $loadKeys
    ) {
        $loads = Loads::getAdditionalLoads($load, $loadCar, $loadCity, $loadKeys);
        foreach ($loads as $additionalLoad) {
            if (empty($additionalLoads)) {
                array_push($additionalLoads, $additionalLoad);
                continue;
            }

            if (!$this->isGroupExists($additionalLoads, $additionalLoad)) {
                array_push($additionalLoads, $additionalLoad);
            }
        }
    }

    /**
     * Checks whether load group exists
     *
     * @param array $loads List of load groups
     * @param array $load Load group
     * @return boolean
     */
    private function isGroupExists($loads, $load)
    {
        $exists = false;
        foreach ($loads as $group) {
            if ($group == $load) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    /**
     * Returns full unload loads
     *
     * @param Load $load Load model
     * @param LoadCar $loadCar Load car model
     * @param LoadCity $loadCity Load city model
     * @param array $fullUnloadLoads List of full unload loads
     * @param array $loadKeys loads id array
     */
    private function getFullUnloadLoads(
        Load $load,
        LoadCar $loadCar,
        LoadCity $loadCity,
        &$fullUnloadLoads = [],
        $loadKeys
    ) {
        $loads = Loads::getFullUnloadLoads($load, $loadCar, $loadCity, $loadKeys);

        $this->removeFullUnloadsWithSameLoads($loads);

        foreach ($loads as $fullUnloadLoad) {
            if (empty($fullUnloadLoads)) {
                array_push($fullUnloadLoads, $fullUnloadLoad);
                continue;
            }

            if (!$this->isGroupExists($fullUnloadLoads, $fullUnloadLoad)) {
                array_push($fullUnloadLoads, $fullUnloadLoad);
            }
        }
    }

    /**
     * Removes loads with same Id after forming full unloads array
     *
     * @param array $loads
     */
    private function removeFullUnloadsWithSameLoads(&$loads)
    {
        foreach ($loads as $index => $loadId) {
            if ($loadId[0] == $loadId[1]) {
                unset($loads[$index]);
            }
        }
    }

    /**
     * Returns previous searches suggestions
     *
     * @param Load $loadFilter Load model
     * @param LoadCity $loadCityFilter Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    private function getPreviousSuggestions(Load $loadFilter, LoadCity $loadCityFilter, $loadKeys)
    {
        $previousSearches = Suggestions::getPreviousSearches();
        $directLoads = [];
        $additionalLoads = [];
        $fullUnloadLoads = [];
        foreach ($previousSearches as $previousSearch) {
            $source = $previousSearch['_source'];
            list($load, $loadCar, $loadCity) = $this->loadSearchData($source, $loadFilter, $loadCityFilter);
            $this->getDirectLoads($load, $loadCar, $loadCity, $directLoads, $loadKeys);
            $this->getAdditionalLoads($load, $loadCar, $loadCity, $additionalLoads, $loadKeys);
            $this->getFullUnloadLoads($load, $loadCar, $loadCity, $fullUnloadLoads, $loadKeys);
        }

        $result = [
            'direct' => $directLoads,
            'additional' => $additionalLoads,
            'fullUnload' => $fullUnloadLoads,
        ];

        Load::removeHidden($result);
        return $result;
    }

    /**
     * Loads search data to Load, LoadCar and LoadCity models
     *
     * @param array $source Search data
     * @param Load $loadFilter Load model
     * @param LoadCity $loadCityFilter Load city model
     * @return array
     */
    private static function loadSearchData($source = [], Load $loadFilter, LoadCity $loadCityFilter)
    {
        $load = new Load([
            'searchRadius' => $source['search_radius'],
            'date' => $source['date'],
        ]);
        $loadCar = new LoadCar([
            'quantity' => $source['quantity'],
        ]);
        $loadCity = new LoadCity([
            'loadCityId' => $source['load'],
            'unloadCityId' => $source['unload'],
        ]);

        if (!empty($loadFilter->date) && $loadFilter->validate(['date'])) {
            $load->date = $loadFilter->date;
            $load->filterDate = true;
        }

        if (!empty($loadCityFilter->loadCityId) && $loadCityFilter->validate(['loadCityId'])) {
            $loadCity->loadCityId = $loadCityFilter->loadCityId;
        }

        if (!empty($loadCityFilter->unloadCityId) && $loadCityFilter->validate(['unloadCityId'])) {
            $loadCity->unloadCityId = $loadCityFilter->unloadCityId;
        }

        return [$load, $loadCar, $loadCity];
    }

    /**
     * Returns current user sign up city loads suggestions
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param array $loadKeys loads id array
     * @return array
     */
    private function getSuggestionsByUserSignUpCity(Load $load, LoadCity $loadCity, $loadKeys)
    {
        if (!is_null($load->date) && $load->validate(['date'])) {
            $load->filterDate = true;
        }

        $result = [
            'direct' => Suggestions::getUserSignUpCitySearches($load, $loadCity, $loadKeys),
            'additional' => [],
            'fullUnload' => [],
        ];

        Load::removeHidden($result);
        return $result;
    }

    /**
     * Resets suggestions filters values to default
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     */
    private function resetSuggestionsFilters(Load &$load, LoadCity &$loadCity)
    {
        $load->date = null;
        $loadCity->loadCityId = null;
        $loadCity->unloadCityId = null;
    }

    /**
     * Renders round trips form
     *
     * @return string|Response
     */
    public function actionRoundTrips()
    {
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_CLIENT_SEARCHES_ROUND_TRIPS]);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($loadCity->load($post) && $loadCity->validate(['city_id'])) {
                return $this->redirect([
                    'load/round-trips-results',
                    'lang' => Yii::$app->language,
                    'cityId' => $loadCity->city_id,
                ]);
            }
        }

        return $this->render('round-trips/index', compact('loadCity'));
    }

    /**
     * Renders round trips results
     *
     * @param integer $cityId Round trips home city ID
     * @return string
     * @throws NotFoundHttpException If round trips home city is not valid
     */
    public function actionRoundTripsResults($cityId = 0)
    {
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_CLIENT_SEARCHES_ROUND_TRIPS]);
        $loadCity->city_id = $cityId;
        if (!$loadCity->validate(['city_id'])) {
            throw new NotFoundHttpException(Yii::t('alert', 'ROUND_TRIPS_INVALID_CITY_ID'));
        }

        $roundTrips = RoundTrips::getRoundTrips($loadCity->city_id);
        $uniqueLoadsIds = RoundTrips::getUniqueLoadsIds($roundTrips);
        $loads = Load::getRoundTripsModels($uniqueLoadsIds);
        $roundTrips = Load::removeRoundtripsWithInactiveCompanies($loads, $roundTrips);

        $loadsTypeQuantity = Load::getLoadsTypesQuantity($roundTrips);
        $pages = [
            'roundtrips' => $this->getSearchResultsPagination($loadsTypeQuantity['directLoadsTypeCount'],
                'directPages'),
        ];
        $directLoads = array_slice($roundTrips, $pages['roundtrips']->offset, $pages['roundtrips']->limit, true);

        return $this->render('round-trips/results', compact('roundTrips', 'loads', 'pages'));
    }

    /**
     * Deactivates loads that are active and activated
     *
     * This method is accessible only for Cron job, because only Cron job has token.
     * Token is required to prevent ordinary users from accidentally accessing this method.
     *
     * @param string $token Special string made of alphanumeric to identify that only Cron job is accessing this method
     * @throws NotAcceptableHttpException If token is invalid or not indicated
     */
    public function actionDeactivate($token = '')
    {
        if ($token !== self::DEACTIVATE_TOKEN) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_LOAD_DEACTIVATION_TOKEN'));
        }

        Loads::deactivateAllActivated();
        Load::deactivateAllActivated();
    }

    /**
     * Removes old loads
     *
     * @param string $token Special string made of alphanumeric to identify that only Cron job is accessing this method
     * @throws NotAcceptableHttpException If token is invalid or not indicated
     */
    public function actionRemoveOld($token = '')
    {
        if ($token !== self::REMOVE_TOKEN) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_LOAD_REMOVING_TOKEN'));
        }

        Load::removeOld(self::RECYCLE_DAYS);
    }

    /**
     * Hide suggestion for current session
     *
     * @param string $ids json encoded id array
     */
    public function actionHideLoadSuggestion($ids)
    {
        Load::hideSuggestion(json_decode($ids));

        if (!is_null(Yii::$app->request->referrer)) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(['load/suggestions']);
        }
    }

    /**
     * Forms pagination for certain entries
     *
     * @param integer $totalEntries Count of entries array
     * @param string $pageParam Pagination name to prevent conflict between multiple paginations
     * @return Pagination
     */
    private function getSearchResultsPagination($totalEntries, $pageParam)
    {
        return $pagination = new Pagination([
            'pageSize' => 10,
            'totalCount' => $totalEntries,
            'pageParam' => $pageParam,
        ]);
    }

    private function getMarkers()
    {
        $query = LoadCity::getLoadsQuery();
        $loadCities = $query->all();
        list($partialCities, $fullCities) = LoadCity::divideCities($loadCities);

        $partialMarkers = $this->getLoadMarkers($partialCities, 'partial');
        $fullMarkers = $this->getLoadMarkers($fullCities, 'full');
        return array_merge($partialMarkers, $fullMarkers);
    }

    /**
     * @param int $pageSize
     * @return string
     * @throws \Exception
     */
    public function actionLoads($pageSize = Load::FIRST_PAGE_SIZE)
    {
        $request = Yii::$app->getRequest();
        $getParams = Yii::$app->getRequest()->get();
        $query = LoadCity::getLoadsQueryExtended($getParams);
        $loadCities = $query->all();
        $dataProvider = Load::getLoadsDataProvider($query, $pageSize);
        list($partialCities, $fullCities) = LoadCity::divideCities($loadCities);

        $partialMarkers = $this->getLoadMarkers($partialCities, 'partial');
        $fullMarkers = $this->getLoadMarkers($fullCities, 'full');
        $markers = array_merge($partialMarkers, $fullMarkers);
        $countries = City::getCountries();

        // We have loadCityId/loadCountryId & unloadCityid/unloadCountryId
        if (!$request->get('loadCityId') && $request->get('loadCountryId')) {
            $getParams['loadCityId'] = $request->get('loadCountryId');
        }
        if (!$request->get('unloadCityId') && $request->get('unloadCountryId')) {
            $getParams['unloadCityId'] = $request->get('unloadCountryId');
        }

        // Log
        if (!Yii::$app->getUser()->getIsGuest() && Yii::$app->getRequest()->get('isNewSearch')) {
            $load = new Load([
                'scenario' => Load::SCENARIO_SEARCH_CLIENT,
                'searchRadius' => Yii::$app->getRequest()->get('searchRadius'),
                'type' => Yii::$app->getRequest()->get('type')
            ]);
            $loadCar = new LoadCar([
                'scenario' => LoadCar::SCENARIO_SEARCH_CLIENT,
            ]);
            $loadCity = new LoadCity([
                'scenario' => LoadCity::SCENARIO_SEARCH_CLIENT,
            ]);
            if (empty($loadCities)) {
                $load->haveResults = Load::SEARCH_NO_RESULTS;
            }
            if ($this->loadAndValidate($load, $loadCar, $loadCity, $getParams)) {
                // We may save suggestion only when data is fully validated to avoid broke full existing elasticsearch logic
                Suggestions::saveSearchInfo($load, $loadCar, $loadCity);
            }
            Log::user(Search::ACTION, Search::PLACEHOLDER_USER_SEARCHED_FOR_LOAD, [$load, $loadCar, $loadCity]);
        }

        // New feature: expired loads if no one active was found
        if (!$dataProvider->getTotalCount()) {
            $expiredLoadsDataProvider = new ArrayDataProvider([
                'allModels' => LoadCity::getExpiredLoadsQueryExtended($getParams),
                'pagination' => [
                    'pageSize' => $pageSize,
                ],
                'sort' => [
                    'attributes' => ['times_listed' => SORT_DESC],
                ],
            ]);
        } else {
            $expiredLoadsDataProvider = [];
        }

        return $this->render('/load/loads/index', [
            'dataProvider' => $dataProvider,
            'expiredLoadsDataProvider' => $expiredLoadsDataProvider,
            'markers' => $markers,
            'pageSize' => $pageSize,
            'countries' => $countries,
            'loadCity' => isset($getParams['loadCityId']) ? City::findOne($getParams['loadCityId']) : null,
            'unloadCity' => isset($getParams['unloadCityId']) ? City::findOne($getParams['unloadCityId']) : null,
            'openExpiredPrice' => CreditService::findOne(CreditService::CREDIT_TYPE_OPEN_EXPIRED_OFFERS)->credit_cost
        ]);
    }

    /**
     * Returns partial or full loads markers
     *
     * @param array $cities List of load cities
     * @param string $type Marker type. Could be "partial" or "full"
     * @return array
     */
    private function getLoadMarkers($cities, $type)
    {
        $markers = [];

        foreach ($cities as $coordinates => $loads) {
            $container = [];
            foreach ($loads as $load) {
                $this->collectCitiesNames($load, LoadCity::LOADING, $container);
                $this->collectCitiesNames($load, LoadCity::UNLOADING, $container);
            }

            $position = $this->convertCoordinatesToPosition($coordinates);
            $mapPopoverContent = $this->renderPartial('/load/loads/map-popover-content', compact('container'));
            // NOTE: Google Maps widget requires that marker popover content do not have new lines in string
            $content = preg_replace('~[\r\n]+~', '', $mapPopoverContent);
            $options = ['icon' => "'" . Yii::getAlias('@web') . "/images/marker-" . $type . ".png'"];
            array_push($markers, compact('position', 'content', 'options'));
        }

        return $markers;
    }

    /**
     * Collects cities names
     *
     * @param Load $load Load model
     * @param integer $type Load city type
     * @param array $container Container to collect cities names
     */
    private function collectCitiesNames(Load $load, $type, &$container)
    {
        $loadCities = $load->getLoadCities()->where(compact('type'))->all();

        /** @var LoadCity $loadCity */
        foreach ($loadCities as $loadCity) {
            $countryCode = $loadCity->city->country_code;
            $name = $loadCity->city->name;

            if (isset($container[$load->id][$type][$countryCode])) {
                array_push($container[$load->id][$type][$countryCode], $name);
            } else {
                $container[$load->id][$type][$countryCode] = [$name];
            }
        }

        return;
    }

    /**
     * Converts city coordinates, which is in string format, to latitude and longitude for position
     *
     * @param string $coordinates City coordinates in string format, separated by comma and space
     * @return array
     */
    private function convertCoordinatesToPosition($coordinates)
    {
        $position = explode(', ', $coordinates);
        return [$position[0], $position[1]];
    }

    /**
     * Logs open map action
     *
     * @returns json string
     */
    public function actionLogOpenMap()
    {
        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
            return json_encode(['message' => Yii::t('alert', 'INVALID_REQUEST')]);
        }

        Log::map(Map::PLACEHOLDER_USER_OPENED_MAP, Map::TYPE_LOADS);
        return json_encode(['message' => 'success']);
    }

    /**
     * Renders load information preview
     *
     * @return string
     */
    public function actionPreviewLoadInfo()
    {
        $id = Yii::$app->request->post('id');
        $showLoadInfo = Yii::$app->request->post('showLoadInfo');
        $load = Load::findOne(['id' => $id, 'status' => Load::ACTIVE, 'active' => Load::ACTIVATED]);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }

        if ($load->isOpenContacts()) {
            $this->captureLoadPreview($id);
            Log::user(Read::ACTION, Read::PLACEHOLDER_USER_REVIEWED_LOAD_INFO, [$load]);
            return $this->renderAjax('/load/preview/subscriber', compact('load'));
        }

        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription() || $load->isOwner(Yii::$app->user->id)) {
            return $this->renderAjax('/load/loads/load-info-preview', compact('load', 'showLoadInfo'));
        }

        $this->captureLoadPreview($id);
        Log::user(Read::ACTION, Read::PLACEHOLDER_USER_REVIEWED_LOAD_INFO, [$load]);
        return $this->renderAjax('/load/loads/load-info-preview', compact('load', 'showLoadInfo'));
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function actionPreviewExpiredLoadInfo()
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $showLoadInfo = Yii::$app->request->post('showLoadInfo');
        $load = Load::findOne(['id' => $id]);
        if (is_null($load)) {
            return ['content' => Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID')];
        }

        if (Yii::$app->user->isGuest) {
            return [
                'content' => $this->renderAjax('/load/loads/load-info-preview', compact('load', 'showLoadInfo'))
            ];
        }

        /** @var User $user */
        $user = Yii::$app->getUser()->getIdentity();
        // Check if was already opened
        if (!LoadPreview::find()->where(['load_id' => $load->id, 'user_id' => $user->id])->count() && !$load->isOwner($user->id)) {
            $price = CreditService::findOne(CreditService::CREDIT_TYPE_OPEN_EXPIRED_OFFERS)->credit_cost;
            if (!$user->hasEnoughServiceCredits($price)) {
                if ($user->hasSubscription() && $user->getSubscriptionCredits() >= $price) {
                    $user->useSubscriptionCredits($price);
                    Log::user(Pay::ACTION, Pay::PAYFOR_WHO_OFFERS_SUBSCR, $price);
                    Log::user(Pay::ACTION, Pay::PAYFOR_WHO_OFFERS, $price);
                } else {
                    return ['content' => Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS')];
                }
            } else {
                $user->useServiceCredits($price);
                if (!$user->updateServiceCredits()) {
                    return ['content' => Yii::t('element', 'An unknown error occured. Please, contact with technical support')];
                } else {
                    Log::user(Pay::ACTION, Pay::PAYFOR_WHO_OFFERS, $price);
                }
            }
        }
        $this->captureLoadPreview($id);
        Log::user(Read::ACTION, Read::PLACEHOLDER_USER_REVIEWED_LOAD_INFO, [$load]);
        return [
            'content' => $this->renderAjax('/load/loads/load-info-expired-preview', ['load' => $load, 'showLoadInfo' => $showLoadInfo]),
            'credits' => $user->service_credits,
            'subscription_end_date' => $user->getSubscriptionEndTime(),
            'subscription_credits' => $user->getSubscriptionCredits(),
        ];
    }

    /**
     * Reduces current user credits for previewing load supplier contact information
     *
     * @return boolean|string
     */
    private function reduceCreditsForLoadPreview()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (!$user->hasEnoughCreditsToPreviewLoadInfo()) {
            return Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_PREVIEW_LOAD_INFO');
        }

        $user->useCredits(Load::LOAD_PREVIEW_CREDITS);
        $user->scenario = User::SCENARIO_UPDATE_CURRENT_CREDITS;
        return $user->save();
    }

    /**
     * Cron job action which finds newest suggestions for users
     *
     * @param string $token comes from cron job for access idetification
     * @throws NotAcceptableHttpException
     */
    public function actionCheckForNewestSuggestions($token = '')
    {
        if ($token !== self::SUGGESTIONS_TOKEN) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_SEND_SUGGESTION_TOKEN'));
        }

        $load = new Load();
        $loadCity = new LoadCity();

        $dummy = [
            'direct' => [],
            'additional' => [],
            'fullUnload' => [],
        ];

        $dummy1 = $dummy;

        $users = User::findUsers();

        foreach ($users as $user) {
            $suggestedLoads = Suggestions::findNotSeenUserSuggestions($user->id);
            $suggestedLoadsCity = Suggestions::getUserSignUpCitySusggestions($load, $loadCity, $user->id);


            $loads = Load::getSuggestionsLoads($suggestedLoadsCity, $dummy, $dummy1);
            $suggestedLoadsCity = Load::removeInactive($loads, $suggestedLoadsCity);


            LoadSuggestionCity::removeSuggestionsSeenByUser($suggestedLoadsCity['direct'], $user->id);
            if (!empty($suggestedLoads) || !empty($suggestedLoadsCity['direct'])) {
                $token = Yii::$app->security->generateRandomString();
                $transaction = Yii::$app->db->beginTransaction();
                foreach ($suggestedLoadsCity['direct'] as $loadsId) {
                    $loadSuggestionsByCity = new LoadSuggestionCity();
                    $loadSuggestionsByCity->saveSuggestionsByCity($user->id, $token, $loadsId);
                }
                foreach ($suggestedLoads as $suggestedLoad) {
                    $loadSuggestions = new LoadSuggestion();
                    $loadSuggestions->saveSuggestions($token, $suggestedLoad);
                    Suggestions::markAsSeen($user->id);
                }
                $transaction->commit();
                $userLanguageIds = UserLanguage::getUserLanguages($user->id);
                MailLanguage::setMailLanguage($userLanguageIds);
                $user->sendSuggestionsLink($token, $user->suggestions_token, $user->email);
            }
        }
    }

    /**
     * Gets newest suggestions from loads suggestion table
     *
     * @param Load $loadFilter dymmy model
     * @param LoadCity $loadCityFilter dummy model
     * @param string $token random string token to identify email receiver suggestions
     * @return array
     */
    private function getNewestSuggestions(Load $loadFilter, LoadCity $loadCityFilter, $token = '')
    {
        $suggestions = LoadSuggestion::find()
            ->where([LoadSuggestion::tableName() . '.token' => $token])
            ->asArray()->all();
        $directLoads = [];
        $additionalLoads = [];
        $fullUnloadLoads = [];
        foreach ($suggestions as $suggestion) {
            $source = $suggestion;
            list($load, $loadCar, $loadCity) = $this->loadSearchData($source, $loadFilter, $loadCityFilter);
            $this->getDirectLoads($load, $loadCar, $loadCity, $directLoads);
            $this->getAdditionalLoads($load, $loadCar, $loadCity, $additionalLoads);
            $this->getFullUnloadLoads($load, $loadCar, $loadCity, $fullUnloadLoads);
        }
        $result = [
            'direct' => $directLoads,
            'additional' => $additionalLoads,
            'fullUnload' => $fullUnloadLoads,
        ];

        return $result;
    }

    /**
     * Renders newest suggestions view
     *
     * @return string
     * @throws NotAcceptableHttpException
     */
    public function actionNewestSuggestions()
    {
        $get = Yii::$app->request->get();

        if (!isset($get['token'])) {
            return;
        }

        $loadFilter = new Load();
        $loadCityFilter = new LoadCity();

        $suggestions = $this->getNewestSuggestions($loadFilter, $loadCityFilter, $get['token']);


        $dataProvider = $this->getNewestSuggestionsByCityDataProvider($get['token']);

        $dummy = [
            'direct' => [],
            'additional' => [],
            'fullUnload' => [],
        ];

        $dummy1 = $dummy;

        $loads = Load::getSuggestionsLoads($suggestions, $dummy, $dummy1);


        $suggestions = Load::removeInactive($loads, $suggestions);

        if (empty($suggestions['direct']) && empty($suggestions['additional']) && empty($suggestions['fullUnload']) && !$dataProvider->getTotalCount()) {
            return $this->render('load-suggestions-via-email/newest-suggestions',
                compact('suggestions', 'loads', 'dataProvider'));
        }

        if (!Yii::$app->user->identity) {
            $session = Yii::$app->session;
            $session->open();
            $session['token'] = $get['token'];
        }

        return $this->render('load-suggestions-via-email/newest-suggestions',
            compact('suggestions', 'loads', 'dataProvider'));
    }

    /**
     * Gets newest suggestions by email receiver
     *
     * @param string $token random string to identify email receiver loads
     * @return ActiveDataProvider
     */
    private function getNewestSuggestionsByCityDataProvider($token)
    {
        $suggestionsByCitiesIds = LoadSuggestionCity::find()
            ->select(LoadSuggestionCity::tableName() . '.load_id')
            ->where([LoadSuggestionCity::tableName() . '.token' => $token])
            ->andWhere(['not', [LoadSuggestionCity::tableName() . '.token' => 'seen']])
            ->column();

        $suggestionsByCities = LoadCity::getLoadsSuggestions($suggestionsByCitiesIds);

        return new ActiveDataProvider([
            'query' => $suggestionsByCities,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);
    }

    public function actionRejectNewestSuggestions($email, $token)
    {
        if (empty($email)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_EMAIL'));
        }
        if (empty($token)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_TOKEN'));
        }

        $user = User::find()
            ->where([
                'suggestions_token' => $token,
                'email' => $email,
            ])
            ->one();
        if (empty($user)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'NO_USER_WAS_FOUND'));
        }

        if ($user->suggestions == User::DO_NOT_SEND_SUGGESTIONS) {
            $message = Yii::t('alert', 'LOAD_SUGGESTIONS_REJECT_ALREADY');
            Yii::$app->session->setFlash('error', $message);
            return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
        }

        $user->scenario = User::SCENARIO_REJECT_SUGGESTIONS;
        $user->suggestions = User::DO_NOT_SEND_SUGGESTIONS;
        if ($user->save()) {
            $message = Yii::t('alert', 'LOAD_SUGGESTIONS_REJECT_SUCCESS');
            Yii::$app->session->setFlash('success', $message);
        };
        if (!$user->save()) {
            $message = Yii::t('alert', 'LOAD_SUGGESTIONS_REJECT_FAIL');
            Yii::$app->session->setFlash('error', $message);
        };

        return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders load link preview
     *
     * @return string
     */
    public function actionPreviewLoadLink()
    {
        /** @var integer|null $id Load ID that link needs to be generated */
        $id = Yii::$app->request->post('id');
        $url = [
            'load/loads',
            'lang' => Yii::$app->language,
            'loadId' => $id,
        ];
        $loadLink = Url::to($url, true);

        return $this->renderAjax('loads/load-link-preview', compact('loadLink'));
    }

    /**
     * @return string
     */
    public function actionRenderMap()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription()) {
            return $this->renderAjax('../partial/_closed_gmap');
        }

        $query = LoadCity::getLoadsQueryExtended(Yii::$app->request->get());
        $loadCities = $query->all();
        list($partialCities, $fullCities) = LoadCity::divideCities($loadCities);

        $partialMarkers = $this->getLoadMarkers($partialCities, 'partial');
        $fullMarkers = $this->getLoadMarkers($fullCities, 'full');
        $markers = array_merge($partialMarkers, $fullMarkers);

        // it's for getting city coordinates
        $cityForCoordinates = City::findOne(Yii::$app->request->get('loadCityId'));
        if (!$cityForCoordinates instanceof City) {
            $cityForCoordinates = City::findOne(Yii::$app->request->get('unloadCityId'));
            if (!$cityForCoordinates instanceof City) {
                $cityForCoordinates = City::findOne(Yii::$app->request->get('unloadCityId'));
                if (!$cityForCoordinates instanceof City) {
                    $cityForCoordinates = City::findOne(Yii::$app->request->get('unloadCountryId'));
                }
            }
        }

        return $this->renderAjax('../partial/_gmap', [
            'markers' => $markers,
            'loadCity' => $cityForCoordinates,
            'mapType' => 'load'
        ]);
    }

    /**
     * @return string
     */
    public function actionRenderFilters()
    {
        $loadCity = City::findOne(Yii::$app->getRequest()->get('loadCityId'));
        $unloadCity = City::findOne(Yii::$app->getRequest()->get('unloadCityId'));
        $type = Yii::$app->getRequest()->get('type');
        $countries = City::getCountries();
        return $this->renderAjax('../partial/_filters-load', compact('loadCity', 'unloadCity', 'countries', 'type'));
    }

    /**
     * @return string
     */
    public function actionRenderMapContact()
    {
        $post = Yii::$app->request->post();
        $load = Load::findOne($post['load']);
        return $this->renderAjax('loads/_preview_map', ['load' => $load]);
    }

    public function actionSendMailUserLog() {
        $post = Yii::$app->request->post();
        $email = $post['email'];
        $isSent = Yii::$app->mailer->compose('user/user-log-service-mail', [
            'companyName' => Yii::$app->params['companyName'],
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($email)
            ->setSubject(Yii::t('mail', 'USER_LOG_MAIL', []
        ))
        ->send();
        echo $isSent;die;
    }

    public function actionChangeEmailAddress() {
        $post = Yii::$app->request->post();
        $email = $post['email'];
        $user_id = $post['user_id'];
        if (!empty($email) && !empty($user_id)) {
            $models = User::find()->where(['id' => $user_id])->all();
            if (!empty($models)) {
                foreach ($models as $model) {
                    $model->email = $email;
                    return $model->update(false);
                }
            }
        }
        return 0;
    }

    public function actionRemoveLoad() {
        $post = Yii::$app->request->post();
        $load_id = $post['load_id'];
        $load = Load::findOne($load_id);
        $load->status = Load::INACTIVE;
        return $load->update(false);
    }

    public function actionActivateLoad() {
        $post = Yii::$app->request->post();
        $load_id = $post['load_id'];
        $user_id = $post['user_id'];
        $check_user_log = LoggingActivatedEmailServices::find()
        ->where(['user_id' => $user_id,'load_id' => $load_id])
        ->all();

        if (!empty($check_user_log)) {
            foreach ($check_user_log as $user_log) {
                $user_log->log_activated = 1;
                return $user_log->update(false);
            }
        } else {
            $add_log = new LoggingActivatedEmailServices();
            $add_log->load_id = $load_id;
            $add_log->user_id = $user_id;
            $add_log->log_activated = 1;
            return $add_log->save();die;
        }
        return 0;
    }
}