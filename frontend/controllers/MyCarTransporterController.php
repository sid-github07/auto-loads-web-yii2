<?php

namespace frontend\controllers;

use common\components\audit\Create;
use common\components\audit\Delete;
use common\components\audit\Log;
use common\components\audit\Pay;
use common\components\audit\Read;
use common\components\audit\Update;
use common\components\Credits;
use common\components\ElasticSearch\CarTransporters;
use common\components\MainController;
use common\models\CarTransporter;
use common\models\CarTransporterPreview;
use common\models\CreditService;
use common\models\User;
use Yii;
use yii\helpers\Url;
use common\models\Load;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class MyCarTransporterController
 *
 * This controller is responsible for action with my car transporters
 *
 * @package frontend\controllers
 */
class MyCarTransporterController extends MainController
{
    /** @var null|integer|array List of car transporters IDs or specific car transporter ID */
    private $carTransporterId;

    /** @var null|array List of cities to filter my car transporters */
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
                            'change-available-from-date',
                            'change-quantity',
                            'change-visibility',
                            'remove',
                            'filtration',
                            'change-car-transporter-table-activity',
                            'transporter-adv-form',
                            'adv-transporter',
                            'open-contacts-form',
                            'open-contacts',
                            'transporter-preview-form',
                            'transporter-preview-buy',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'change-page-size' => ['POST'],
                    'change-available-from-date' => ['POST'],
                    'change-quantity' => ['POST'],
                    'change-visibility' => ['POST'],
                    'remove' => ['POST'],
                    'filtration' => ['POST'],
                    'change-car-transporter-table-activity' => ['POST'],
                    'transporter-adv-form' => ['POST'],
                    'adv-transporter' => ['POST'],
                    'open-contacts-form' => ['POST'],
                    'open-contacts' => ['POST'],
                    'transporter-preview-buy' => ['POST'],
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
        $this->setCities(Yii::$app->request->get('carTransporterCities'));

        return parent::beforeAction($action);
    }

    /**
     * Sets car transporters IDs or specific car transporter ID
     *
     * @param null|integer|array $id List of car transporters IDs or specific car transporter ID
     */
    public function setId($id)
    {
        $this->carTransporterId = $id;
    }

    /**
     * Returns list of car transporters IDs or specific car transporter ID
     *
     * @return array|integer|null
     */
    public function getId()
    {
        return $this->carTransporterId;
    }

    /**
     * Sets cities for car transporters filtration
     *
     * @param null|string $cities List of cities for car transporters filtration
     * @return array
     */
    public function setCities($cities)
    {
        if (!empty($cities)) {
            $cities = explode(',', $cities);
        }

        return $this->cities = $cities;
    }

    /**
     * Returns list of cities for car transporter filtration
     *
     * @return array|null
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Changes my car transporters table page size
     *
     * @return string
     */
    public function actionChangePageSize()
    {
        return $this->renderMyCarTransportersTable();
    }

    /**
     * Changes specific car transporter available from date
     *
     * @return string
     */
    public function actionChangeAvailableFromDate()
    {
        // Load model
        $carTransporter = CarTransporter::findOne(['id' => $this->getId(), 'user_id' => Yii::$app->user->id]);
        if (is_null($carTransporter)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND'));
            return $this->renderMyCarTransportersTable();
        }

        // Check date
        $availableFromDate = Yii::$app->request->post('availableFromDate');
        if ($carTransporter->hasSameAvailableFromDate($availableFromDate)) {
            return $this->renderMyCarTransportersTable();
        }

        // Set value
        $carTransporter->scenario = CarTransporter::SCENARIO_USER_CHANGES_AVAILABLE_FROM_DATE;
        $carTransporter->available_from = strtotime($availableFromDate);

        // Save to database
        Yii::$app->db->beginTransaction();
        if (!$carTransporter->save()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CAR_TRANSPORTER_AVAILABLE_FROM_DATE_CANNOT_BE_CHANGED'));
            Yii::$app->db->transaction->rollBack();
            return $this->renderMyCarTransportersTable();
        }

        // Log user action
        Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_CAR_TRANSPORTER_INFO, [$carTransporter]);

        // Update ElasticSearch entry
        if (!CarTransporters::updateAvailableFromDate($carTransporter->id, $carTransporter->available_from)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CAR_TRANSPORTER_AVAILABLE_FROM_DATE_CANNOT_BE_CHANGED'));
            Yii::$app->db->transaction->rollBack();
            return $this->renderMyCarTransportersTable();
        }

        // Show success message
        Yii::$app->db->transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'CAR_TRANSPORTER_AVAILABLE_FROM_DATE_CHANGED_SUCCESSFULLY'));
        return $this->renderMyCarTransportersTable();
    }

    /**
     * Changes specific car transporter quantity
     *
     * Method returns void because editable element does not require any response
     */
    public function actionChangeQuantity()
    {
        $carTransporter = CarTransporter::findOne([
            'id' => Yii::$app->request->get('id'),
            'user_id' => Yii::$app->user->id,
            'archived' => CarTransporter::NOT_ARCHIVED,
        ]);

        if (is_null($carTransporter)) {
            return;
        }

        $quantity = Yii::$app->request->post('value');
        /*
         * NOTE: quantity is empty when user selects that his/her car transporter is fully empty.
         * Therefore the value of car transporter quantity must be set as null to show that it is empty
         */
        $carTransporter->quantity = empty($quantity) ? CarTransporter::QUANTITY_DEFAULT_VALUE : $quantity;
        $carTransporter->scenario = CarTransporter::SCENARIO_USER_CHANGES_QUANTITY;
        $carTransporter->save();

        CarTransporters::updateQuantity($carTransporter->id, $carTransporter->quantity);
        return;
    }

    /**
     * Changes selected car transporters or specific car transporter visibility
     *
     * @return string
     */
    public function actionChangeVisibility()
    {
        $visibility = Yii::$app->request->post('visibility', CarTransporter::INVISIBLE);
        if (!in_array($visibility, CarTransporter::getVisibilities())) {
            return $this->renderMyCarTransportersTable();
        }

        /** @var User $user Currently logged-in user */
        $user = Yii::$app->user->identity;
        $carTransporters = CarTransporter::findAll(['id' => $this->getId(), 'user_id' => $user->id]);
        foreach ($carTransporters as $carTransporter) {
            Yii::$app->db->beginTransaction();

            if ($visibility == CarTransporter::VISIBLE && $carTransporter->isExpired()) {
                if ($user->hasEnoughCredits(CarTransporter::EXTENSION_CREDITS)) {
                    $carTransporter->extend();
                    Credits::spend(CarTransporter::EXTENSION_CREDITS);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_CREDITS_TO_EXTEND_CAR_TRANSPORTER'));
                    Yii::$app->db->transaction->rollBack();
                    return $this->renderMyCarTransportersTable();
                }
            }

            $carTransporter->visible = $visibility;
            $carTransporter->scenario = CarTransporter::SCENARIO_USER_CHANGES_VISIBILITY;
            if (!$carTransporter->save()) {
                Yii::$app->db->transaction->rollBack();
                return $this->renderMyCarTransportersTable();
            }

            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_CAR_TRANSPORTER_INFO, [$carTransporter]);
            CarTransporters::updateVisibility($carTransporter->id, $carTransporter->visible);
            CarTransporters::updateDateOfExpiry($carTransporter->id, $carTransporter->date_of_expiry);
            Yii::$app->db->transaction->commit();
        }

        if (count($carTransporters) > 1) {
            $alertMessage = Yii::t('alert', 'MULTIPLE_CAR_TRANSPORTERS_VISIBILITY_CHANGED_SUCCESSFULLY');
        } else {
            $alertMessage = Yii::t('alert', 'CAR_TRANSPORTER_VISIBILITY_CHANGED_SUCCESSFULLY');
        }

        Yii::$app->session->setFlash('success', $alertMessage);
        return $this->renderMyCarTransportersTable();
    }
	
	/**
     * Changes car transporter table view by user selection of which loads he wants to see //TODO possible merge with actionFiltration
     * 
     * @return string new loads table
     */
    public function actionChangeCarTransporterTableActivity()
    {
        return $this->renderMyCarTransportersTable();
    }

    /**
     * Removes selected car transporters or specific car transporter
     *
     * @return string
     */
    public function actionRemove()
    {
        $attributes = ['visible' => CarTransporter::INVISIBLE, 'archived' => CarTransporter::ARCHIVED];
        $condition = ['id' => $this->getId(), 'user_id' => Yii::$app->user->id];
        $numberOfRemovedCarTransporters = CarTransporter::updateAll($attributes, $condition);

        $carTransporters = CarTransporter::findAll($condition);
        foreach ($carTransporters as $carTransporter) {
            CarTransporters::archive($carTransporter->id);
        }

        Log::user(Delete::ACTION, Delete::PLACEHOLDER_USER_REMOVED_MULTIPLE_CAR_TRANSPORTERS, $carTransporters);
        if ($numberOfRemovedCarTransporters > 1) {
            $alertMessage = Yii::t('alert', 'MULTIPLE_CAR_TRANSPORTERS_REMOVED_SUCCESSFULLY');
        } else {
            $alertMessage = Yii::t('alert', 'CAR_TRANSPORTER_REMOVED_SUCCESSFULLY');
        }
        Yii::$app->session->setFlash('success', $alertMessage);
        return $this->renderMyCarTransportersTable();
    }

    /**
     * Returns rendered my car transporters table page
     *
     * @return string
     */
    private function renderMyCarTransportersTable()
    {
        $dataProvider = CarTransporter::getMyCarTransportersDataProvider($this->getCities());
        return $this->renderAjax('/my-announcement/my-car-transporters-table', compact('dataProvider'));
    }

    /**
     * Filters my car transporters table by car transporters cities
     *
     * @return string
     */
    public function actionFiltration()
    {
        $carTransporterCities = Yii::$app->request->post('carTransporterCities', []);
        $dataProvider = CarTransporter::getMyCarTransportersDataProvider($carTransporterCities);
        return $this->renderAjax('/my-announcement/my-car-transporters-table', compact('dataProvider'));
    }

    /**
     * @return string
     */
    public function actionTransporterAdvForm()
    {
        $post = Yii::$app->request->post();
        $transporter = CarTransporter::findOne(['id' => $post['id']]);
        if (is_null($transporter)) {
            return Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND');
        }
        $user = Yii::$app->user->identity;
        $subscriptionCredits = $user->getSubscriptionCredits();
        $subscriptionEndTime = $user->getSubscriptionEndTime();
        $advCredits = User::getServiceCredits();
        
        $load = new Load;
        $advDayList = array_combine($load->getDaysRanges(), $load->getDaysRanges());
        $advPositionList = array_combine($load->getCarPosRanges(), $load->getCarPosRanges());
        
        return $this->renderAjax('/my-announcement/transporter-advertisement-form', [
            'transporter' => $transporter,
            'subscriptionCredits' => $subscriptionCredits,
            'subscriptionEndTime' => $subscriptionEndTime,
            'advCredits' => $advCredits,
            'advDayList' => $advDayList,
            'advPositionList' => $advPositionList,
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionAdvTransporter()
    {
        $post = Yii::$app->request->post();
        $transporter = CarTransporter::findOne(['id' => $post['id']]);
        $transporter->setScenario(CarTransporter::SCENARIO_UPDATE_TRANSPORTER_ADV);
        $user = Yii::$app->user->identity;
        
        if (is_null($transporter)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        if (is_null($user)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_NOT_FOUND_BY_ID'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        if ($transporter->days_adv !== 0) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_ALREADY_ADVERT'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        if (!$transporter->load($post) && !$transporter->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INCORRECT_ADVERT_DATA'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        $advCost = $transporter->days_adv * $transporter->car_pos_adv;
        if (!$user->hasEnoughCombinedServiceCredits($advCost)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_ADVERTS_PTS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        Yii::$app->db->beginTransaction();
        if ($advCost > 0) {
            $user->useCombinedServiceCredits($advCost);
            $transporter->setAdvertisementSubmitTime();
        }

        if ($transporter->save()) {
            Yii::$app->db->transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_ADVERT_SUCCESS'));
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_ADVERTISED_TRANSPORTER, [$transporter]);
        } else {
            Yii::$app->db->transaction->rollback();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_ADVERT_FAILURE'));
        }

        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    public function actionTransporterPreviewForm()
    {
        if (Yii::$app->request->post('transporterId') === null) {
            throw new \ErrorException('Can not determine transporter');
        }

        $carTransporter = CarTransporter::findOne(['id' => Yii::$app->request->post('transporterId')]);
        if (is_null($carTransporter)) {
            throw new \ErrorException('Can not determine transporter');
        }

        $user = Yii::$app->user->identity;
        if ($user->hasBoughtService($carTransporter, CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW) === true) {
            Log::user(Read::ACTION, Read::PLACEHOLDER_USER_PREVIEWED_TRANSPORTER_VIEW_DATA, [$carTransporter]);
        }

        $service = CreditService::find()->where(['credit_type' => CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW, 'status' => CreditService::STATUS_ACTIVE])->one();
        $dataProvider = CarTransporterPreview::getPreviewDataProvider(Yii::$app->request->post('transporterId'));

        return $this->renderAjax('/my-announcement/transporter-preview-data-view-form', compact('dataProvider', 'carTransporter', 'service'));
    }
    /**
     * @return string
     * @throws \ErrorException
     */
    public function actionTransporterPreviewBuy()
    {
        if (Yii::$app->request->get('id') === null) {
            throw new \ErrorException('Can not determine transporter');
        }

        $carTransporter = CarTransporter::findOne(['id' => Yii::$app->request->get('id')]);
        if (is_null($carTransporter)) {
            throw new \ErrorException('Can not determine transporter');
        }

        if ($carTransporter->user_id !== Yii::$app->user->id) {
            throw new \ErrorException('Unknown load');
        }

        $alert = 'preview_buy_success';
        $service = CreditService::buy($carTransporter, CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW, false);
        if ($service) {
            $cost = $service->credit_cost;
            Log::user(Pay::ACTION, Pay::PAYFOR_PREVIEWS, $cost);
        }

        $key = 'success';
        if ($service === false) {
            $alert = 'preview_buy_error';
            $key = 'error';
        } else {
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_BOUGHT_VIEW_TRANSPORTER_PREVIEW_SERVICE, [$carTransporter]);
        }

        Yii::$app->session->setFlash($key, Yii::t('alert', $alert));
        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }
    
    /**
     * Render Ajax open contacts form
     * 
     * @return string
     */
    public function actionOpenContactsForm()
    {
        $post = Yii::$app->request->post();
        $transporter = CarTransporter::findOne(['id' => $post['id']]);
        if (is_null($transporter)) {
            return Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND');
        }
       
        $serviceCredits = User::getServiceCredits();
        
        $user = Yii::$app->user->identity;
        $subscriptionCredits = $user->getSubscriptionCredits();
        $subscriptionEndTime = $user->getSubscriptionEndTime();
        
        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);

        $actionUrl = Url::to([
            'my-car-transporter/open-contacts',
            'lang' => Yii::$app->language,
            'id' => $transporter->id
        ]);

        $load = new Load;
        $dayList = array_merge(
            [0 => Yii::t('element', 'select_open_contacts_days')],
            $load->getDaysRanges()
        );

        return $this->renderAjax('/my-announcement/open-contacts-form', [
            'model' => $transporter, 
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
        $transporter = CarTransporter::findOne(['id' => $post['id']]);
        if (is_null($transporter)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        $transporter->setScenario(Load::SCENARIO_UPDATE_OPEN_CONTACTS);
        if (is_null($transporter)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_NOT_FOUND_BY_ID'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        $user = User::findById(Yii::$app->user->id);
        if (is_null($transporter)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_NOT_FOUND_BY_ID'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        if (!$transporter->load($post) && !$transporter->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INCORRECT_OPEN_CONTACTS_DATA'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        if ($transporter->isOpenContacts()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_ALREADY_HAS_OPEN_CONTACTS', [
                'date' => $transporter->getOpenContactsExpiry()
            ]));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        $totalCost = $openContactsCost * $transporter->open_contacts_days;
        
        if (!$user->hasEnoughCombinedServiceCredits($totalCost)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'NOT_ENOUGH_SERVICE_CREDITS_FOR_OPEN_CONTACTS'));
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }
        
        Yii::$app->db->beginTransaction();
        if ($totalCost > 0) {
            if ($user->service_credits > $totalCost) {
        //TODO  Log::user(Pay::ACTION, Pay::PAYFOR_??, $totalCost);
            } else {
        //TODO  Log::user(Pay::ACTION, Pay::PAYFOR_??_SUBSCR, $totalCost);
            }
            $user->useCombinedServiceCredits($totalCost);
            $transporter->setOpenContactsExpiry();
        }

        if ($transporter->save()) {
            Yii::$app->db->transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_OPEN_CONTACTS_SUCCESS'));
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_SET_TRANSPORTER_OPEN_CONTACTS, [$transporter]);
        } else {
            Yii::$app->db->transaction->rollback();
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_OPEN_CONTACTS_FAILURE'));
        }

        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
    }
}