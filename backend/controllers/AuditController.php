<?php

namespace backend\controllers;

use common\models\UserLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class AdminController
 *
 * @package backend\controllers
 */
class AuditController extends Controller
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
                            'index',
                            'map',
                            'credits',
                            'credits-users',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest;
                        },  
                    ],
                ],               
            ], 
            'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'index' => ['GET'],
                        'map' => ['GET'],
                    ],
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
     * Renders list of admins page
     *
     * @return string
     */
    public function actionIndex()
    {
        $userLog = new UserLog(['scenario' => UserLog::SCENARIO_ADMIN_FILTERS_SEARCHES_LOG]);
        $userLog->load(Yii::$app->request->get());
        $query = $userLog->getQuery();
        $dataProvider = $userLog->getDataProvider($query);
        return $this->render('index', compact('dataProvider', 'userLog'));
    }
    
    /**
     * Renders open map log list view
     *
     * @return string
     */
    public function actionMap()
    {
        $userLog = new UserLog(['scenario' => UserLog::SCENARIO_ADMIN_FILTERS_MAP_SEARCHES_LOG]);
        $userLog->load(Yii::$app->request->get());
        $query = $userLog->getMapOpenQuery();
        $dataProvider = $userLog->getDataProvider($query);
        return $this->render('map', compact('dataProvider', 'userLog'));
    }

    /**
     * Renders credits log list view - sum of every action for period
     */
    public function actionCredits()
    {
        $userLog = new UserLog(['scenario' => UserLog::SCENARIO_ADMIN_FILTERS_CREDITS_LOG]);
        $userLog->load(Yii::$app->request->get());

        $query = $userLog->getCreditsSumQuery();
        $dataProvider = $userLog->getDataProvider($query);
        return $this->render('credits', compact('dataProvider', 'userLog'));
    }

    /**
     * Renders credits log list view - by users
     */
    public function actionCreditsUsers()
    {
        $userLog = new UserLog(['scenario' => UserLog::SCENARIO_ADMIN_FILTERS_CREDITS_LOG]);
        $userLog->load(Yii::$app->request->get());

        $query = $userLog->getCreditsQuery();
        $dataProvider = $userLog->getDataProvider($query);
        return $this->render('credits-by-users', compact('dataProvider', 'userLog'));
    }

}

