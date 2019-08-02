<?php

namespace backend\controllers;

use common\components\ElasticSearch;
use common\components\ElasticSearch\Cities;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class CityController
 *
 * @package backend\controllers
 */
class CityController extends Controller
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
                            'simple-search',
                        ],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'simple-search' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Searches for city with simple method
     *
     * @param string $phrase City name
     * @param string $code Country code
     * @return string
     */
    public function actionSimpleSearch($phrase = '', $code = '')
    {
        if (strlen($phrase) < ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH) {
            return json_encode(['items' => []]);
        }

        return json_encode(['items' => Cities::getSimpleSearchCities($phrase, $code)]);
    }
}