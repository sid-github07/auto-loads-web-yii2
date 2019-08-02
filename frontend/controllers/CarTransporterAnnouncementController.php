<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Create;
use common\components\audit\Log;
use common\components\audit\Pay;
use common\components\Credits;
use common\components\ElasticSearch\CarTransporters;
use common\components\MainController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\CreditService;
use common\models\Load;
use kartik\icons\Icon;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Class CarTransporterAnnouncementController
 *
 * This controller is responsible for actions with new car transporter announcement
 *
 * @package frontend\controllers
 */
class CarTransporterAnnouncementController extends MainController
{
    /**
     * @var CarTransporter Newly announced car transporter model
     */
    private $carTransporter;

    /**
     * @var array List of newly announced car transporter load locations
     */
    private $loadLocations;

    /**
     * @var array List of newly announced car transporter unload locations
     */
    private $unloadLocations;

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
                            'validate-available-from-date',
                            'announcement',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            if (Yii::$app->user->isGuest) {
                                
                                return false;
                            }
                            return true;
                        }
                    ],
                    [
                        'actions' => [
                            'announcement-form',
                            'validate-available-from-date',
                            'announcement',
                        ],
                        'allow' => false,
                        'denyCallback' => function () {
                            $message = Yii::t('alert', 'ONLY_SIGNED_UP_USER_CAN_ANNOUNCE_CAR_TRANSPORTER');
                            Yii::$app->session->setFlash('error', $message);
                            return $this->redirect(['site/login', 'lang' => Yii::$app->language]);
                        }
                    ]        
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'announcement-form' => ['GET', 'POST'],
                    'validate-available-from-date' => ['POST'],
                    'announcement' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders car transporter announcement form
     *
     * @return string
     */
    public function actionAnnouncementForm()
    {
        $carTransporter = new CarTransporter(['scenario' => CarTransporter::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT]);
        $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT]);
        $user = Yii::$app->user->identity;
        
        $serviceCredits = $user->service_credits;
        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        
        $subscriptionCredits = $user->getSubscriptionCredits();
        $subscriptionEndTime = $user->getSubscriptionEndTime();
        
        $load = new Load;
        $advertDayList = array_merge(
            [0 => Yii::t('element', 'select_adv_day_count')],
            $load->getDaysRanges()
        );
        $openContactsDayList = array_merge(
            [0 => Yii::t('element', 'select_open_contacts_days')],
            $load->getDaysRanges()
        );
        
        $load = new Load(['scenario' => Load::SCENARIO_ANNOUNCE_CLIENT]);

        if (Yii::$app->session->has('post')) {
            $carTransporter->load(Yii::$app->session->get('post'));
            $carTransporterCity->load(Yii::$app->session->get('post'));
            Yii::$app->session->remove('post');

            $carTransporterCity->associateLocationIdWithName('loadLocations');
            $carTransporterCity->associateLocationIdWithName('unloadLocations');
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('announcement-form', compact('carTransporter', 'carTransporterCity', 'serviceCredits', 
                'subscriptionCredits', 'subscriptionEndTime', 'openContactsCost', 
                'advertDayList', 'openContactsDayList', 'load'));
        }

        return $this->render('index', compact('carTransporter', 'carTransporterCity', 'serviceCredits', 
            'subscriptionCredits', 'subscriptionEndTime', 'openContactsCost', 
            'advertDayList', 'openContactsDayList', 'load'));
    }

    /**
     * Validates car transporter available from date
     *
     * @return string
     */
    public function actionValidateAvailableFromDate()
    {
        $scenario = CarTransporter::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT;
        $carTransporterAdapter = new AjaxValidationAdapter(new CarTransporter(), $scenario);

        return $carTransporterAdapter->validate();
    }

    /**
     * Announces car transporter announcement
     *
     * @return Response
     */
    public function actionAnnouncement()
    {
        if (!Yii::$app->user->identity->canAnnounceCarTransporter(CarTransporter::CREDITS_FOR_ANNOUNCEMENT)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_ANNOUNCE_NO_SUBSCRIPTION', [
                'subscription' => Html::a(Yii::t('alert', 'SUBSCRIPTION'), [
                    'subscription/index',
                    'lang' => Yii::$app->language
                ], ['class' => 'subscription-link'])
            ]));
            return $this->redirect(['car-transporter-announcement/announcement-form', 'lang' => Yii::$app->language]);
        }

        Yii::$app->db->beginTransaction();

        if (!$this->saveCarTransporter()) {
            return $this->showAnnouncementError(Yii::t('alert', 'CANNOT_SAVE_CAR_TRANSPORTER'));
        }
        
        if (!$this->saveCarTransporterCities()) {
            return $this->showAnnouncementError(Yii::t('alert', 'CANNOT_SAVE_CAR_TRANSPORTER'));
        }

        $creditsSpent = CarTransporter::CREDITS_FOR_ANNOUNCEMENT;
        Credits::spend($creditsSpent);
        Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_PROMO, $creditsSpent);
        if ($creditsSpent > 0) {
            Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_PROMO_SUBSCR, $creditsSpent);
        }

        CarTransporters::add($this->carTransporter, $this->loadLocations, $this->unloadLocations);
        Log::user(Create::ACTION, Create::PLACEHOLDER_USER_ANNOUNCED_CAR_TRANSPORTER, [$this->carTransporter]);
        Yii::$app->db->transaction->commit();
        $this->setSuccessfulAnnouncementMessage();

        $tab = MyAnnouncementController::TAB_MY_CAR_TRANSPORTERS;
        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language, 'tab' => $tab]);
    }

    /**
     * Saves newly announced car transporter
     * @return bool
     * @throws \ErrorException
     */
    private function saveCarTransporter()
    {
        $post = Yii::$app->request->post();
        $scenario = CarTransporter::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT;
        $this->carTransporter = new CarTransporter(compact('scenario'));
        $user = Yii::$app->user->identity;
        
        if (!$user->canPayForLoadAnnouncement()) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_EXTEND_LOAD_EXPIRY_TIME'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $this->carTransporter->load($post);
        
        if (!$this->handleCreditServices()) {
            Yii::$app->db->transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        $this->carTransporter->scenario = CarTransporter::SCENARIO_DEFAULT;
        return $this->carTransporter->save();
    }
    
    /**
     * Sets CarTransporter model credit service attributes and deducts service credits from user
     *
     * @return boolean Whether credit services were handled successfully
     */
    private function handleCreditServices()
    {
        $advertisemenCost = $this->carTransporter->days_adv * $this->carTransporter->car_pos_adv;
        $openContactsPrice = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        $openContactsCost = $this->carTransporter->open_contacts_days * $openContactsPrice;
        $totalServiceCost = $advertisemenCost + $openContactsCost;
        $user = Yii::$app->user->identity;
        
        if ($totalServiceCost == 0 || Yii::$app->user->isGuest || !$user->hasSubscription()) {
            unset($this->carTransporter->days_adv);
            unset($this->carTransporter->car_pos_adv);
            unset($this->carTransporter->open_contacts_days);
            return true;
        }
        
        if (!$user->hasEnoughCombinedServiceCredits($totalServiceCost)) {
            return false;
        }
        
        Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_OPEN_CONTACTS, $totalServiceCost);
        $userServiceCredits = $user->service_credits;
        if ($userServiceCredits < $totalServiceCost && $userServiceCredits > 0) {
            // some credits will use from service or custom (subscr) credits
            $restCredits = $totalServiceCost - $userServiceCredits;
            Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_OPEN_CONTACTS_SUBSCR, $restCredits);
        } else {
            Log::user(Pay::ACTION, Pay::PAYFOR_TRUCK_OPEN_CONTACTS_SUBSCR, $totalServiceCost);
        }
        $user->useCombinedServiceCredits($totalServiceCost);

        if ($advertisemenCost > 0) {
            $this->carTransporter->setAdvertisementSubmitTime();
        } else {
            unset($this->carTransporter->days_adv);
            unset($this->carTransporter->car_pos_adv);
        }
        
        if ($openContactsCost > 0) {
            $this->carTransporter->setOpenContactsExpiry();
        } else {
            unset($this->carTransporter->open_contacts_days);
        }
        
        return true;
    }
    
    /**
     * Saves newly announced car transporter cities
     *
     * @return boolean Whether all car transporter cities were saved successfully
     */
    private function saveCarTransporterCities()
    {
        $scenario = CarTransporterCity::SCENARIO_USER_CREATES_NEW_ANNOUNCEMENT;
        $carTransporterCity = new CarTransporterCity(compact('scenario'));
        $carTransporterCity->load(Yii::$app->request->post());
        $carTransporterId = $this->carTransporter->id;

        $this->loadLocations = City::findAll($carTransporterCity->loadLocations);
        $type = CarTransporterCity::TYPE_LOAD;
        if (!$this->saveCarTransporterLocations($this->loadLocations, $carTransporterId, $type, $carTransporterCity)) {
            return false;
        }

        $this->unloadLocations = City::findAll($carTransporterCity->unloadLocations);
        $type = CarTransporterCity::TYPE_UNLOAD;
        if (!$this->saveCarTransporterLocations($this->unloadLocations, $carTransporterId, $type, $carTransporterCity)) {
            return false;
        }

        return true;
    }

    /**
     * Saves car transporter locations
     *
     * @param array $cities List of user selected locations
     * @param integer $car_transporter_id Car transporter ID
     * @param integer $type Car transporter location type (load or unload)
     * @return boolean Whether all car transporters locations were saved successfully
     */
    private function saveCarTransporterLocations($cities, $car_transporter_id, $type, $carTransporterCity)
    {
        $locations = ArrayHelper::map($cities, 'id', 'country_code');
        foreach ($cities as $city) {
            $country_code = $locations[$city->id];
            $city_id = $city->id;
            $load_postal_code = $carTransporterCity->load_postal_code;
            $unload_postal_code = $carTransporterCity->unload_postal_code;
            $attributes = compact('car_transporter_id', 'city_id', 'country_code', 'type', 'load_postal_code', 'unload_postal_code');
            $carTransporterCity = new CarTransporterCity($attributes);
            if (!$carTransporterCity->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets alert message that user successfully announced new car transporter
     */
    private function setSuccessfulAnnouncementMessage()
    {
        $quantity = Yii::t('element', 'C-T-29');
        if (!is_null($this->carTransporter->quantity)) {
            $quantity = $this->carTransporter->quantity;
        }

        $baseMessage = Yii::t('alert', 'CAR_TRANSPORTER_ANNOUNCEMENT_ANNOUNCED_SUCCESSFULLY', [
            'loadLocations' => City::combineNamesToString($this->loadLocations),
            'unloadLocations' => City::combineNamesToString($this->unloadLocations),
            'quantity' => $quantity,
        ]);

        $icon = Icon::show('exclamation-triangle', [], Icon::FA);
        $reactivationMessage = Yii::t('alert', 'CAR_TRANSPORTER_ANNOUNCEMENT_REACTIVATE');
        $additionalMessage = Yii::t('alert', Html::tag('div', $icon . $reactivationMessage, [
            'class' => 'activate-announce-alert',
        ]));

        // TODO: atkomentuoti kai Simonas norės, jog visus skelbimus reikėtų aktyvuoti kas 24 val.
        Yii::$app->session->setFlash('success', $baseMessage /*. $additionalMessage*/);
    }

    /**
     * Sets car transporter announcement error message and redirects user to another page
     *
     * @param string $errorMessage Error message why car transporter cannot be announced
     * @return Response
     */
    private function showAnnouncementError($errorMessage)
    {
        if (isset(Yii::$app->db->transaction)) {
            Yii::$app->db->transaction->rollBack(); 
        }
        Yii::$app->session->setFlash('error', $errorMessage);

        if (Yii::$app->request->isAjax) {
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        Yii::$app->session->set('post', Yii::$app->request->post());
        return $this->redirect(['car-transporter-announcement/announcement-form', 'lang' => Yii::$app->language]);
    }
}