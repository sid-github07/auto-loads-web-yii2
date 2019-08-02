<?php

namespace backend\controllers;

use common\models\Announcement;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

/**
 * This class represents ANNOUNCEMENT message
 * Class AnnouncementController
 * @package backend\controllers
 */
class AnnouncementController extends Controller
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
                            'hide',
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
                        'hide' => ['POST'],
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
            'dataProvider' => Announcement::getDataProvider()
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws NotAcceptableHttpException
     */
    public function actionNew()
    {
        $announcement = new Announcement();
        if (Yii::$app->request->isGet) {
            return $this->render('new', ['announcement' => $announcement]);
        }

        $data = Yii::$app->request->post();
        $announcement->load($data);
        if (!$announcement->validate()) {
            $errors = $announcement->errors;
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        if ($data['Announcement']['status'] == Announcement::STATUS_ACTIVE) {
            Announcement::hideAllAnnouncements($data['Announcement']['language_id']);
        }
        $announcement->save();

        Yii::$app->session->setFlash('success',  Yii::t('alert', 'announcement_create_success'));
        return $this->redirect(['announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders administrator/moderator information edit form
     *
     * @return string
     */
    public function actionRenderEditForm()
    {
        $id = Yii::$app->request->post('id');
        $announcement = Announcement::findOne($id);
        if (is_null($announcement)) {
            return Yii::t('alert', 'announcement_not_found');
        }

        $announcement->scenario = Announcement::SCENARIO_DEFAULT;
        return $this->renderAjax('partial/edit', compact('announcement'));
    }

    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionHide()
    {
        $id = Yii::$app->request->post('id');
        $announcement = Announcement::findOne($id);
        if (is_null($announcement)) {
            throw new NotFoundHttpException(Yii::t('alert', 'announcement_not_found'));
        }

        $announcement->scenario = Announcement::SCENARIO_DEFAULT;
        $announcement->status = Announcement::STATUS_HIDDEN;
        $announcement->save(false);

        Yii::$app->session->setFlash('success', Yii::t('alert', 'announcement_hide_success'));
        return $this->redirect(['announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @param null $id
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionEdit($id = null)
    {
        $announcement = Announcement::findOne($id);
        if (is_null($announcement)) {
            throw new NotFoundHttpException(Yii::t('alert', 'announcement_not_found'));
        }

        $announcement->scenario = Announcement::SCENARIO_DEFAULT;
        $data = Yii::$app->request->post();
        if ($announcement->status == Announcement::STATUS_HIDDEN && $data['Announcement']['status'] == Announcement::STATUS_ACTIVE) {
            Announcement::hideAllAnnouncements($data['Announcement']['language_id']);
        }

        $announcement->load($data);
        if (!$announcement->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }


        $announcement->save(false);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'announcement_edit_success'));
        return $this->redirect(['announcement/index', 'lang' => Yii::$app->language]);
    }

    /**
     * @return \yii\web\Response
     * @throws NotAcceptableHttpException
     */
    public function actionRemove()
    {
        $id = Yii::$app->request->post('id');
        if (is_array($id)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'invalid_announcement_id'));
        }
        Announcement::deleteAll(['id' => $id]);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'announcement_removed_successfully'));
        return $this->redirect(['announcement/index', 'lang' => Yii::$app->language]);
    }

}

