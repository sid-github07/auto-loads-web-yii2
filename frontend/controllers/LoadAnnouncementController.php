<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Create;
use common\components\audit\Log;
use common\components\ElasticSearch\Cities;
use common\components\ElasticSearch\Loads;
use common\components\MainController;
use common\models\City;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use common\models\User;
use common\models\CreditService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Class LoadAnnouncementController
 *
 * @package frontend\controllers
 */
class LoadAnnouncementController extends MainController
{
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
                            'announcement-form',
                            'announcement-form-validation',
                            'announcement',
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'announcement-form' => ['GET', 'POST'],
                    'announcement-form-validation' => ['POST'],
                    'announcement' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders load announcement form
     *
     * @return string
     */
    public function actionAnnouncementForm()
    {
        $load = new Load(['scenario' => Load::SCENARIO_ANNOUNCE_CLIENT]);
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_ANNOUNCE_CLIENT]);

        if (Yii::$app->user->isGuest) {
            $serviceCredits = 0;
        } else {
            $user = Yii::$app->user->identity;
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
            return $this->render('index', compact('load', 'loadCity', 'serviceCredits'));
        }

        $loadCities = Yii::$app->request->get('loadCities');
        if (!empty($loadCities)) {
            $loadCity->loadCityId = explode(',', $loadCities);
        }

        $citiesNames = ArrayHelper::map(City::findAll($loadCity->loadCityId), 'id', function (City $city) {
            return $city->getNameAndCountryCode();
        });
        
        $serviceCost = CreditService::findById(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        return $this->renderAjax('announcement-form', compact('load', 'loadCity', 
            'citiesNames', 'serviceCredits', 'subscriptionCredits', 
            'subscriptionEndTime', 'openContactsCost', 'advertDayList', 'openContactsDayList'));
    }

    /**
     * Validates load announcement form
     *
     * @return string
     */
    public function actionAnnouncementFormValidation()
    {
        $loadAdapter = new AjaxValidationAdapter(new Load(), Load::SCENARIO_ANNOUNCE_CLIENT);
        return $loadAdapter->validate();
    }

    /**
     * Announces new load
     *
     * @return Response
     */
    public function actionAnnouncement()
    {
        $load = new Load(['scenario' => Load::SCENARIO_ANNOUNCE_CLIENT]);
        $post = Yii::$app->request->post();
        $load->load($post);
        $user = Yii::$app->user->identity;
        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        
        if (!$load->canAnnounceLoad(Yii::$app->user->isGuest)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ANNOUNCE_NO_SUBSCRIPTION', [
                'subscription' => Html::a(Yii::t('alert', 'SUBSCRIPTION'), [
                    'subscription/index',
                    'lang' => Yii::$app->language
                ], ['class' => 'subscription-link'])
            ]));
            return $this->redirect(['load-announcement/announcement-form', 'lang' => Yii::$app->language]);
        }

        Yii::$app->db->beginTransaction();
        
        if (!Yii::$app->user->isGuest && !$user->canPayForLoadAnnouncement()) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_EXTEND_LOAD_EXPIRY_TIME'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        if (!$this->handleCreditServices($load, $openContactsCost, $user)) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
               
        $loadCar = new LoadCar(['scenario' => LoadCar::SCENARIO_ANNOUNCE_CLIENT]);
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_ANNOUNCE_CLIENT]);
        if (!$this->announce($load, $loadCity, $loadCar)) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ANNOUNCE_CANNOT_ANNOUNCE_LOAD'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $loadCity->scenario = LoadCity::SCENARIO_ANNOUNCE_CLIENT;
        $loadCity->load(Yii::$app->request->post());
        $loadCity->loadCityId = empty($loadCity->loadCityId) ? [] : $loadCity->loadCityId;
        $loadCity->unloadCityId = empty($loadCity->unloadCityId) ? [] : $loadCity->unloadCityId;

        $this->updateElasticSearchCities($loadCity);
        $this->addLoadToElasticSearch($load, $loadCity);
        $this->showSuccessfulLoadAnnouncementAlert($load, $loadCity);
        $this->logUserActionAboutAnnouncedLoad($load);

        Yii::$app->db->transaction->commit();
        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Saves load, load cities and load cars to database
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     * @param LoadCar $loadCar Load car model
     * @return boolean Whether load was announced successfully
     */
    private function announce(Load &$load, LoadCity &$loadCity, LoadCar &$loadCar)
    {
        if (!$load->create()) {
            var_dump($load); exit;
            return false;
        }

        if (!$load->updateCode()) {
            return false;
        }

        if (!$loadCity->create($load->id, Yii::$app->request->post('LoadCity', []))) {
            return false;
        }

        if (!$loadCar->create($load, Yii::$app->request->post('LoadCar', []))) {
            return false;
        }

        if (!$load->sendMyLoadsLink()) {
            return false;
        }

        return true;
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
     * Updates ElasticSearch cities
     *
     * @param LoadCity $loadCity Load city model
     */
    private function updateElasticSearchCities(LoadCity $loadCity)
    {
        foreach ($loadCity->loadCityId as $loadCityId) {
            Cities::updatePopularity($loadCityId, $loadCity->unloadCityId);
        }

        foreach ($loadCity->unloadCityId as $unloadCityId) {
            Cities::updatePopularity($unloadCityId);
        }
    }

    /**
     * Adds new load to ElasticSearch
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     */
    private function addLoadToElasticSearch(Load $load, LoadCity $loadCity)
    {
        $cars = Yii::$app->request->post('LoadCar', []);
        $carsQuantity = LoadCar::countCarsQuantities($cars);
        if ($carsQuantity > LoadCar::QUANTITY_MAX_VALUE) {
            return;
        }

        Loads::addLoad($load, $carsQuantity, $loadCity->loadCityId, $loadCity->unloadCityId);
    }

    /**
     * Adds successful new load announcement alert message to session
     *
     * @param Load $load Load model
     * @param LoadCity $loadCity Load city model
     */
    private function showSuccessfulLoadAnnouncementAlert(Load $load, LoadCity $loadCity)
    {
        $carsString = '';
        if (empty($carsQuantity)) {
            $loadInfo = LoadCar::getLoadInfo($load);
            $carsString = str_replace('</div>', '', str_replace('<div>', ' ', $loadInfo));
        }

        // TODO: atkomentuoti kai Simonas norės, jog visus skelbimus reikėtų aktyvuoti kas 24 val.
        Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_SUCCESSFULLY', [
                'loadCity' => $loadCity->formatCitiesNames($loadCity->loadCityId),
                'unloadCity' => $loadCity->formatCitiesNames($loadCity->unloadCityId),
                'quantity' => (empty($carsQuantity) ? $carsString : $carsQuantity . ' auto'),
            ]) /*. (Yii::$app->user->isGuest ? '<div class="activate-announce-alert">' . Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_ACTIVATE_VIA_EMAIL', [
                    'email' => $load->email,
                ]) . '</div>' : '') .
            '<div class="activate-announce-alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' .
            Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_REACTIVATE') .
            (Yii::$app->user->isGuest ? Yii::t('alert', 'LOAD_ANNOUNCE_CREATED_REACTIVATE_VIA_EMAIL') : '') . '</div>'*/
        );
    }

    /**
     * Logs that user announced new load
     *
     * @param Load $load Load model
     */
    private function logUserActionAboutAnnouncedLoad(Load $load)
    {
        if (!Yii::$app->user->isGuest) {
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_ANNOUNCED_LOAD, [$load]);
        }
    }
}