<?php

namespace backend\controllers;

use common\models\Announcement;
use common\models\CreditService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;


class CreditServiceController extends Controller
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
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest;
                        },  
                    ],
                    [
                        'actions' => [
                            'render-edit-form',
                            'edit',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest && Yii::$app->admin->identity->isAdmin();
                        }
                    ],
                ],               
            ], 
            'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'index' => ['GET'],
                        'render-edit-form' => ['POST'],
                        'edit' => ['POST'],
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
     * Renders list of service page
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => CreditService::getDataProvider()
        ]);
    }

    /**
     * Renders administrator/moderator information edit form
     *
     * @return string
     */
    public function actionRenderEditForm()
    {
        $id = Yii::$app->request->post('id');
        $creditService = CreditService::findOne($id);
        if (is_null($creditService)) {
            return Yii::t('alert', 'credit_service_not_found');
        }

        return $this->renderAjax('partial/edit', compact('creditService'));
    }

    /**
     * @param null $id
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionEdit($id = null)
    {
        $CreditService = CreditService::findOne($id);
        if (is_null($CreditService)) {
            throw new NotFoundHttpException(Yii::t('alert', 'credit_service_not_found'));
        }

        $CreditService->scenario = CreditService::SCENARIO_DEFAULT;
        $data = Yii::$app->request->post();

        $CreditService->load($data);
        if (!$CreditService->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        $CreditService->save();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'credit_service_edit_success'));
        return $this->redirect(['credit-service/index', 'lang' => Yii::$app->language]);
    }

}

