<?php

namespace frontend\controllers;

use common\components\MainController;
use common\models\CarTransporter;
use common\models\City;
use common\models\Load;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class MyAnnouncementController
 *
 * This controller is responsible for actions with my announcements
 *
 * @package frontend\controllers
 */
class MyAnnouncementController extends MainController
{
    const TAB_MY_LOADS = 'my-loads';
    const TAB_MY_CAR_TRANSPORTERS = 'car-transporters';

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
                        'actions' => ['index'],
                        'allow' => true,
                        'matchCallback' => function () {
                            // NOTE: guest user must have token in order to access my announcements
                            if (Yii::$app->user->isGuest && is_null(Yii::$app->request->get('token'))) {
                                return $this->redirect(['site/login', 'lt' => Yii::$app->language]);
                            }

                            return true;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Renders my announcements page
     *
     * @return string
     */
    public function actionIndex()
    {
        $defaultTab = isset($_COOKIE['tab']) ? $_COOKIE['tab'] : self::TAB_MY_LOADS;
        $tab = Yii::$app->request->get('tab', $defaultTab);
        $token = Yii::$app->request->get('token');

        $myLoadsInfo = $this->getMyLoadsInfo($token);
        $myCarTransportersInfo = $this->getMyCarTransportersInfo();

        return $this->render('index', array_merge(compact('tab', 'token'), $myLoadsInfo, $myCarTransportersInfo));
    }

    /**
     * Returns information that is used in my loads page
     *
     * @param null|string $token Load token to identify user
     * @return array
     */
    private function getMyLoadsInfo($token)
    {
        $loadCities = Yii::$app->request->get('loadCities');
        if (!empty($loadCities)) {
            $loadCities = explode(',', $loadCities);
        }

        $loadCitiesNames = ArrayHelper::map(City::findAll($loadCities), 'id', function (City $city) {
            return $city->getNameAndCountryCode();
        });
        $loadDataProvider = Load::getMyLoadsDataProvider($token, $loadCities);

        return compact('loadCities', 'loadCitiesNames', 'loadDataProvider');
    }

    /**
     * Returns information that is used in my car transporters page
     *
     * @return array
     */
    private function getMyCarTransportersInfo()
    {
        $carTransporterCities = Yii::$app->request->get('carTransporterCities');
        if (!empty($carTransporterCities)) {
            $carTransporterCities = explode(',', $carTransporterCities);
        }

        $carTransporterCitiesNames = ArrayHelper::map(City::findAll($carTransporterCities), 'id', function (City $city) {
            return $city->getNameAndCountryCode();
        });
        $carTransporterDataProvider = CarTransporter::getMyCarTransportersDataProvider($carTransporterCities);

        return compact('carTransporterCities', 'carTransporterCitiesNames', 'carTransporterDataProvider');
    }
}