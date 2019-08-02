<?php

namespace backend\controllers;

use common\models\Service;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ServiceController
 *
 * @package backend\controllers
 */
class ServiceController extends Controller
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
                            'new',
                            'render-edit-form',
                            'edit',
                            'remove',
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
                        'new' => ['GET', 'POST'],
                        'render-edit-form' => ['POST'],
                        'edit' => ['POST'],
                        'remove' => ['POST'],
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
            'dataProvider' => Service::getDataProvider()
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws NotAcceptableHttpException
     */
    public function actionNew()
    {
        $service = new Service(
            [
                'created_at' => (new \DateTime())->getTimestamp(),
                'days' => 0,
                'updated_at' => (new \DateTime())->getTimestamp(),
            ]
        );
        
        if (Yii::$app->request->isGet) {
            return $this->render('new', ['service' => $service]);
        }

        $data = Yii::$app->request->post();
        $service->load($data);

        $label = $data['Service']['label'];
        $name = Service::getTitleByID($data['Service']['service_type_id']);
        if (is_null($name)) {
            $name = strtoupper(str_replace(' ', '', $label) ). $data['Service']['credits'];
        }
        $service->name = mb_substr($name, 0, 255);
        if (!$service->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }
        $service->save();

        Yii::$app->session->setFlash('success',  Yii::t('alert', 'service_create_success'));
        return $this->redirect(['service/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders administrator/moderator information edit form
     *
     * @return string
     */
    public function actionRenderEditForm()
    {
        $id = Yii::$app->request->post('id');
        $service = Service::findOne($id);
        if (is_null($service)) {
            return Yii::t('alert', 'service_not_found');
        }

        $service->scenario = Service::SCENARIO_DEFAULT;
        return $this->renderAjax('/service/partial/edit', compact('service'));
    }

    /**
     * @param null $id
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionEdit($id = null)
    {
        $service = Service::findOne($id);
        if (is_null($service)) {
            throw new NotFoundHttpException(Yii::t('alert', 'service_not_found'));
        }

        $service->scenario = Service::SCENARIO_DEFAULT;
        $data = Yii::$app->request->post();
        $service->load($data);

        $label = $data['Service']['label'];
        $name = strtoupper(str_replace(' ', '', $label) ). $data['Service']['credits'];
        if (is_null($service->name)) {
            $service->name = mb_substr($name, 0, 255);
        }
        if (!$service->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        $service->save(false);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'service_edit_success'));
        return $this->redirect(['service/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     */
    public function actionRemove()
    {
        $id = Yii::$app->request->post('id');
        if (is_array($id)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'invalid_service_id'));
        }

        Service::deleteAll(['id' => $id]);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'service_removed_successfully'));
        return $this->redirect(['service/index', 'lang' => Yii::$app->language]);
    }

}

