<?php

namespace frontend\controllers;

use common\components\audit\Log;
use common\components\audit\Read;
use common\components\audit\Map;
use common\components\audit\Search;
use common\components\ElasticSearch\CarTransporters;
use common\components\ElasticSearch\CarTransportersSuggestions;
use common\components\MainController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\CarTransporterPreview;
use common\models\City;
use common\models\Company;
use common\models\CreditCode;
use common\models\CreditService;
use common\models\Language;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;

/** Dare Arnoldas Sveikauskas Hackinti cia */

/**
 * Class CarTransporterController
 *
 * @package frontend\controllers
 */
class CarTransporterController extends MainController
{
    /** @const string Alphanumeric string to identify that only Cron job is accessing deactivation method */
    const DEACTIVATE_TOKEN = 'YkX5Us8peIXstHyj52i8FjOW935YnI2ib6uTuPwREzyGVqwtA7OnEiBR1WFaKuKA';

    /** @const string Alphanumeric string to identify that only Cron job is accessing removing old loads method */
    const REMOVE_TOKEN = 'W40dO61KT7R27j1CgrBwlX6cmKjHj37KHQsD9dDrVSkfnudbp8ecKJJRploPA1kM';

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
                            'index',
                            'preview',
                            'deactivate',
                            'remove-old',
                            'preview-link',
                            'render-map',
                            'render-filters',
                            'render-map-contact',
                            'log-open-map',
                            'get-msgs-creditcode-state'
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'preview' => ['POST'],
                    'deactivate' => ['GET'],
                    'remove-old' => ['GET'],
                    'log-open-map' => ['POST'],
                    'get-msgs-creditcode-state' => ['GET'],
                ],
            ],
        ];
    }
    
    /**
     * Renders car transporters page
     *
     * @param integer $pageSize Page size selection value
     * @return string
     */
    public function actionIndex($pageSize = CarTransporter::PAGE_SIZE_FIRST)
    {
        $query = CarTransporterCity::getExtendedQuery(Yii::$app->getRequest()->get());
        $dataProvider = CarTransporter::getDataProvider($query, $pageSize);
        $carTransporters = $this->groupCarTransportersByCoordinates($query->all());
        $markers = $this->getMarkers($carTransporters);
        $countries = City::getCountries();

        // Log
        if (!Yii::$app->getUser()->getIsGuest() && Yii::$app->getRequest()->get('isNewSearch')) {
            $loadLocation = City::findOne(Yii::$app->request->get('loadCityId'));
            if (!$loadLocation instanceof City) {
                $loadLocation = City::findOne(Yii::$app->request->get('loadCountryId'));
            }
            $unloadLocation = City::findOne(Yii::$app->request->get('unloadCityId'));
            if (!$unloadLocation instanceof City) {
                $unloadLocation = City::findOne(Yii::$app->request->get('unloadCountryId'));
            }

            $carTransporter = new CarTransporter(['scenario' => CarTransporter::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER]);
            $carTransporter->radius = Yii::$app->request->get('searchRadius');
            $carTransporterCity = new CarTransporterCity(['scenario' => CarTransporterCity::SCENARIO_USER_SEARCHES_FOR_CAR_TRANSPORTER]);
            $carTransporterCity->loadLocation = ($loadLocation instanceof City) ? $loadLocation->id : null;
            $carTransporterCity->unloadLocation = ($unloadLocation instanceof City) ? $unloadLocation->id : null;
            if (!$query->count()) {
                $carTransporter->haveResults = CarTransporter::SEARCH_NO_RESULTS;
            }

            if ($carTransporter->validate() && $carTransporterCity->validate()) {
                // We may save suggestion only when data is fully validated to avoid broke full existing elasticsearch logic
                CarTransportersSuggestions::save($carTransporter, $carTransporterCity);
            }
            Log::user(Search::ACTION, Search::PLACEHOLDER_USER_SEARCHED_FOR_CAR_TRANSPORTER, [$carTransporter, $carTransporterCity]);
        }

        return $this->render('index', compact( 'dataProvider', 'markers', 'pageSize', 'countries'));
    }

    /**
     * Groups car transporters models by coordinates
     *
     * @param CarTransporterCity[] $carTransporterCities List of car transporter cities
     * @return array
     */
    private function groupCarTransportersByCoordinates($carTransporterCities)
    {
        $container = [];

        /** @var CarTransporterCity $carTransporterCity */
        foreach ($carTransporterCities as $carTransporterCity) {
            $coordinates = $carTransporterCity->city->latitude . ', ' . $carTransporterCity->city->longitude;
            if (array_key_exists($coordinates, $container)) {
                array_push($container[$coordinates], $carTransporterCity->carTransporter);
            } else {
                $container[$coordinates] = [$carTransporterCity->carTransporter];
            }
        }

        return $container;
    }

    /**
     * Returns car transporters markers
     *
     * @param array $groupedCarTransporters Car transporters grouped by coordinates
     * @return array
     */
    private function getMarkers($groupedCarTransporters)
    {
        $markers = [];

        foreach ($groupedCarTransporters as $coordinates => $carTransporters) {
            $citiesContainer = $this->collectCitiesNames($carTransporters);
            $mapPopoverContent = $this->renderPartial('map-popover-content', compact('citiesContainer'));
            // NOTE: Google Maps widget requires that marker popover content do not have new lines in string
            $content = preg_replace('~[\r\n]+~', '', $mapPopoverContent);
            $position = $this->convertCoordinatesToArray($coordinates);
            $options = ['icon' => "'" . Yii::getAlias('@web') . "/images/marker-truck.png'"];
            array_push($markers, compact('content', 'position', 'options'));
        }

        return $markers;
    }

    /**
     * Collects car transporters cities names from list of car transporters
     *
     * @param CarTransporter[] $carTransporters List of car transporters
     * @return array
     */
    private function collectCitiesNames($carTransporters)
    {
        $citiesContainer = [];

        /** @var CarTransporter $carTransporter */
        foreach ($carTransporters as $carTransporter) {
            $this->addCityNameToCitiesContainer($carTransporter, $citiesContainer, CarTransporterCity::TYPE_LOAD);
            $this->addCityNameToCitiesContainer($carTransporter, $citiesContainer, CarTransporterCity::TYPE_UNLOAD);
        }

        return $citiesContainer;
    }

    /**
     * Adds city name to cities container
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param array $citiesContainer Cities container
     * @param integer $type Car transporter city type
     */
    private function addCityNameToCitiesContainer(CarTransporter $carTransporter, &$citiesContainer, $type)
    {
        $carTransporterCities = $carTransporter->getCarTransporterCities()->where(compact('type'))->all();

        /** @var CarTransporterCity $carTransporterCity */
        foreach ($carTransporterCities as $carTransporterCity) {
            $countryCode = $carTransporterCity->city->country_code;
            $name = $carTransporterCity->city->name;

            if (isset($citiesContainer[$carTransporter->id][$type][$countryCode])) {
                array_push($citiesContainer[$carTransporter->id][$type][$countryCode], $name);
            } else {
                $citiesContainer[$carTransporter->id][$type][$countryCode] = [$name];
            }
        }
    }

    /**
     * Converts car transporter coordinates from string format to array format
     *
     * @param string $coordinates Car transporter coordinates in string format
     * @return array
     */
    private function convertCoordinatesToArray($coordinates)
    {
        $position = explode(', ', $coordinates);
        return [$position[0], $position[1]];
    }

    /**
     * Renders car transporter preview
     *
     * @return string
     */
    public function actionPreview()
    {
        $id = Yii::$app->request->post('id');
        $showInfo = Yii::$app->request->post('showInfo');
        $sCreditCode = Yii::$app->request->post('creditCode', '');

        /** @var CarTransporter $carTransporter */
        $carTransporter = CarTransporter::find()
            ->where([
                'id' => $id,
                'visible' => CarTransporter::VISIBLE,
                'archived' => CarTransporter::NOT_ARCHIVED,
            ])
            ->andWhere(['>=', 'date_of_expiry', time()])
            ->one();

        if (is_null($carTransporter)) {
            return Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND');
        }

        if ($carTransporter->isOpenContacts()) {
            $company = Company::findUserCompany($carTransporter->user_id);
            $languages = Language::getUserSelectedLanguages($carTransporter->user_id);
            $showInfo = false;
            $boughtByCreditCode = false;
            $this->renderAjax('/car-transporter/preview', compact('carTransporter', 'showInfo', 'company', 'languages', 'boughtByCreditCode'));
        }

        $service = CreditService::findOne(['credit_type' => CreditService::CREDIT_TYPE_CAR_TRANSPORTER_DETAILS_VIEW]);
        $creditsCost = $service->credit_cost;

        if (Yii::$app->user->isGuest && !$carTransporter->isOpenContacts()) {
            $hasError = false;
            // If credit code is entered check the creditcode
            if (!empty($sCreditCode) && !is_null($service) && Yii::$app->user->isGuest) {
                $creditCode = CreditCode::find()->where(['creditcode' => $sCreditCode])->one();
                if (!$creditCode || !$creditCode->isPaid()) {
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_CREDITCODE_ENTERED'));
                    $hasError = true;
                } elseif(($creditCode->isPaid() && $creditCode->canBuy($creditsCost)) || $creditCode->hasBought($carTransporter, $service)) {
                    if (!$creditCode->hasBought($carTransporter, $service) && $creditCode->canBuy($creditsCost)) {
                        $creditCode->buy($carTransporter, $service);
                    }
                    Yii::$app->session->setFlash('success', Yii::t('alert', 'CREDITCODE_APPLIED_TO_PREVIEW'));
                    return $this->renderPreview($carTransporter, $showInfo, true);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('alert', 'CREDITCODE_INSUFFICIENT', [
                        'creditsCost' => $creditsCost,
                        'creditsLeft' => $creditCode->creditsleft,
                    ]));
                    $hasError = true;
                }
            }
            return $this->renderAjax('guest-preview', compact('carTransporter', 'showInfo', 'creditsCost', 'sCreditCode', 'hasError'));
        }

        if ($carTransporter->isOwner()) {
            return $this->renderPreview($carTransporter, $showInfo);
        }
        
        $hasServiceCreditsOrPaidAlready = false;
        $user = Yii::$app->user->identity;
        if (!is_null($service) && !Yii::$app->user->isGuest) {
            $hasServiceCreditsOrPaidAlready = $user->hasBoughtService($carTransporter) === true ||
                $user->hasEnoughServiceCredits($creditsCost);
        }

        if ((!is_null($user) && $user->hasSubscription()) ||
            $hasServiceCreditsOrPaidAlready === true ||
            $carTransporter->isOpenContacts()
        ) {
            return $this->renderPreview($carTransporter, $showInfo);
        }

        return $this->renderAjax('non-subscriber-preview', compact('carTransporter', 'showInfo', 'creditsCost'));
    }

    /**
     * Renders car transporter preview file
     *
     * @param CarTransporter $carTransporter Car transporter model that user previews
     * @param boolean $showInfo Attribute, whether car transporter info must be shown
     * @param boolean $boughtByCreditCode User bought preview through CreditCode.
     * @return string
     */
    private function renderPreview(CarTransporter $carTransporter, $showInfo, $boughtByCreditCode = false)
    {
        $service = CreditService::buy($carTransporter);
        if ($service) {
            $creditsCost = $service->credit_cost;
        } else {
            $creditsCost = 0;
        }
        Log::user(Read::ACTION, Read::PLACEHOLDER_USER_REVIEWED_CAR_TRANSPORTER_INFO, [$carTransporter]);
        $this->registerCarTransporterPreview($carTransporter->id);
        $company = Company::findUserCompany($carTransporter->user_id);
        $languages = Language::getUserSelectedLanguages($carTransporter->user_id);
        if (!empty($creditsCost)) {
            return $this->renderAjax('preview', compact('carTransporter', 'showInfo', 'company', 'languages', 'creditsCost', 'boughtByCreditCode'));
        } else {
            return $this->renderAjax('preview', compact('carTransporter', 'showInfo', 'company', 'languages', 'boughtByCreditCode'));
        }
    }

    /**
     * Returns flash message information when a user enters creditcode to get
     * contact information.
     *
     * @return string Json flashmessage information
     */
    public function actionGetMsgsCreditcodeState()
    {
        $aryMessage = ['type' => '', 'message' => ''];
        if (Yii::$app->request->isAjax) {
            $session = Yii::$app->session;
            if ($session->hasFlash('success')) {
                $aryMessage['type'] = 'success';
                $aryMessage['message'] = $session->getFlash('success');
            } elseif ($session->hasFlash('error')) {
                $aryMessage['type'] = 'error';
                $aryMessage['message'] = $session->getFlash('error');
            }
        }
        return json_encode($aryMessage);
    }

    /**
     * Registers that the user has previewed car transporter owner information
     *
     * @param integer $id Car transporter ID
     */
    private function registerCarTransporterPreview($id)
    {
        $carTransporterPreview = new CarTransporterPreview([
            'scenario' => CarTransporterPreview::SCENARIO_USER_PREVIEWS_CAR_TRANSPORTER,
            'car_transporter_id' => $id,
            'user_id' => Yii::$app->user->id,
            'ip' => Yii::$app->request->userIP,
        ]);

        if ($carTransporterPreview->validate() && !$carTransporterPreview->hasAlreadyPreviewed()) {
            $carTransporterPreview->save(false);
        }
    }
    
    /**
     * Deactivates car transporter that are active and activated
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

        CarTransporters::deactivateAllActivated();
        CarTransporter::deactivateAllActivated();
    }

    /**
     * Removes old car transporter
     *
     * @param string $token Special string made of alphanumeric to identify that only Cron job is accessing this method
     * @throws NotAcceptableHttpException If token is invalid or not indicated
     */
    public function actionRemoveOld($token = '')
    {
        if ($token !== self::REMOVE_TOKEN) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_LOAD_REMOVING_TOKEN'));
        }

        CarTransporter::removeOld(self::RECYCLE_DAYS);
    }

    /**
     * Renders car transporter link preview
     *
     * @return string
     */
    public function actionPreviewLink()
    {
        $id = Yii::$app->request->post('id');
        $url = [
            'car-transporter/index',
            'lang' => Yii::$app->language,
            'carTransporterId' => $id,
        ];
        $carTransporterLink = Url::to($url, true);

        return $this->renderAjax('car-transporter-link-preview', compact('carTransporterLink'));
    }

    /**
     * @return string
     */
    public function actionRenderMap()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->hasSubscription()) {
            return $this->renderAjax('../partial/_closed_gmap');
        }

        // it's for getting city coordinates
        $cityForCoordinates = City::findOne(Yii::$app->request->get('loadCityId'));
        if (!$cityForCoordinates instanceof City) {
            $cityForCoordinates = City::findOne(Yii::$app->request->get('loadCountryId'));
            if (!$cityForCoordinates instanceof City) {
                $cityForCoordinates = City::findOne(Yii::$app->request->get('unloadCityId'));
                if (!$cityForCoordinates instanceof City) {
                    $cityForCoordinates = City::findOne(Yii::$app->request->get('unloadCountryId'));
                }
            }
        }

        $query = CarTransporterCity::getExtendedQuery(Yii::$app->getRequest()->get());
        $carTransporters = $this->groupCarTransportersByCoordinates($query->all());
        $markers = $this->getMarkers($carTransporters);

        return $this->renderAjax('../partial/_gmap', [
            'markers' => $markers,
            'loadCity' => $cityForCoordinates,
            'mapType' => 'transporter'
        ]);
    }

    /**
     * @return string
     */
    public function actionRenderFilters()
    {
        $loadCity = City::findOne(Yii::$app->request->get('loadCityId'));
        $unloadCity = City::findOne(Yii::$app->request->get('unloadCityId'));
        $countries = City::getCountries();

        return $this->renderAjax('../partial/_filters-transporter', compact('loadCity', 'countries', 'unloadCity'));
    }
    /**
     * @return string
     */
    public function actionRenderMapContact()
    {
        $post = Yii::$app->request->post();
        $carTransporter = CarTransporter::findOne($post['transporter']);
        return  $this->renderAjax('_preview_map', ['carTransporter' => $carTransporter]);
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
        
        Log::map(Map::PLACEHOLDER_USER_OPENED_MAP, Map::TYPE_TRANSPORTERS);
        return json_encode(['message' => 'success']);
    }
}
