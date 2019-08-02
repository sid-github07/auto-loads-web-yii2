<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Log;
use common\components\audit\Search;
use common\components\ElasticSearch\CarTransporters;
use common\components\ElasticSearch\CarTransportersSuggestions;
use common\components\MainController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class CarTransporterSearchController
 *
 * This controller is responsible for car transporter search and search results representing
 *
 * @package frontend\controllers
 */
class CarTransporterSearchController extends MainController
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
                            'search-form',
                            'validate-available-from-date',
                            'search',
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'search-form' => ['GET'],
                    'validate-available-from-date' => ['GET'],
                    'search' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Renders car transporter search form
     *
     * @return string
     */
    public function actionSearchForm()
    {
        Yii::$app->session->set('car-transporter-filter-is-opened', true);
        return $this->redirect(['car-transporter/index', 'lang' => Yii::$app->language], 301);
    }

    /**
     * Collects car transporters IDs from car transporter search results
     *
     * @param array $searchResults Car transporter search results
     * @return array
     */
    private function collectCarTransportersIds($searchResults)
    {
        $ids = [];

        foreach ($searchResults as $searchResult) {
            $id = $searchResult['_source']['id'];
            if (!in_array($id, $ids)) {
                array_push($ids, $id);
            }
        }

        return $ids;
    }

    /**
     * Returns car transporter search criteria
     *
     * @param CarTransporter $carTransporter Car transporter model
     * @param CarTransporterCity $carTransporterCity Car transporter city model
     * @return array
     * @throws NotFoundHttpException If load or unload location was not found
     */
    private function getSearchCriteria(CarTransporter $carTransporter, CarTransporterCity $carTransporterCity)
    {
        $loadLocation = City::findOne($carTransporterCity->loadLocation);
        $unloadLocation = City::findOne($carTransporterCity->unloadLocation);
        if (is_null($loadLocation) || is_null($unloadLocation)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CITY_NOT_FOUND_BY_ID'));
        }

        return [
            'quantity' => $carTransporter->quantity,
            CarTransporterCity::TYPE_LOAD => $loadLocation,
            CarTransporterCity::TYPE_UNLOAD => $unloadLocation,
        ];
    }
}