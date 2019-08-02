<?php

namespace backend\controllers;

use common\models\Admin;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AdminController
 *
 * @package backend\controllers
 */
class AdminController extends Controller
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
                            'edit-my-profile',
                            'change-my-password',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest;
                        },
                    ],
                    [
                        'actions' => [
                            'add-new',
                            'render-edit-form',
                            'edit',
                            'render-change-password-form',
                            'change-password',
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
                    'edit-my-profile' => ['GET', 'POST'],
                    'change-my-password' => ['GET', 'POST'],
                    'add-new' => ['GET', 'POST'],
                    'render-edit-form' => ['POST'],
                    'edit' => ['POST'],
                    'render-change-password-form' => ['POST'],
                    'change-password' => ['POST'],
                    'remove' => ['POST'],
                ],
            ],
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
        $dataProvider = Admin::getDataProvider();
        return $this->render('index', compact('dataProvider'));
    }

    /**
     * Adds new administrator/moderator
     *
     * @return string|Response
     * @throws NotAcceptableHttpException If administrator/moderator data is invalid
     */
    public function actionAddNew()
    {
        $admin = new Admin([
            'scenario' => Admin::SCENARIO_ADMIN_ADDS_NEW_ADMIN,
            'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
            'archived' => Admin::NOT_ARCHIVED,
        ]);

        if (Yii::$app->request->isGet) {
            return $this->render('/admin/new', compact('admin'));
        }

        $admin->load(Yii::$app->request->post());
        $admin->scenario = Admin::SCENARIO_SYSTEM_SAVES_NEW_ADMIN;
        $admin->generateAuthKey();
        $admin->setPassword($admin->password);
        if (!$admin->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        $admin->save();
        if ($admin->isAdmin()) {
            $message = Yii::t('alert', 'NEW_ADMINISTRATOR_ADDED_SUCCESSFULLY');
        } else {
            $message = Yii::t('alert', 'NEW_MODERATOR_ADDED_SUCCESSFULLY');
        }

        Yii::$app->session->setFlash('success', $message);
        return $this->redirect(['admin/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders administrator/moderator information edit form
     *
     * @return string
     */
    public function actionRenderEditForm()
    {
        $id = Yii::$app->request->post('id');
        $admin = Admin::findOne($id);
        if (is_null($admin)) {
            return Yii::t('alert', 'ADMIN_NOT_FOUND');
        }

        $admin->scenario = Admin::SCENARIO_ADMIN_EDITS_INFO;
        return $this->renderAjax('/admin/partial/edit', compact('admin'));
    }

    /**
     * Edits administrator/moderator information
     *
     * @param null|integer $id Admin ID
     * @return string|Response
     * @throws NotAcceptableHttpException If administrator/moderator data is invalid
     * @throws NotFoundHttpException If administrator/moderator not found
     */
    public function actionEdit($id = null)
    {
        $admin = Admin::findOne($id);
        if (is_null($admin)) {
            throw new NotFoundHttpException(Yii::t('alert', 'ADMIN_NOT_FOUND'));
        }

        $admin->scenario = Admin::SCENARIO_ADMIN_EDITS_INFO;
        $admin->load(Yii::$app->request->post());
        if (!$admin->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        $admin->save(false);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'ADMIN_DATA_SAVED_SUCCESSFULLY'));
        return $this->redirect(['admin/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders administrator/moderator password change form
     *
     * @return string
     */
    public function actionRenderChangePasswordForm()
    {
        $id = Yii::$app->request->post('id');
        $admin = Admin::findOne($id);
        if (is_null($admin)) {
            return Yii::t('alert', 'ADMIN_NOT_FOUND');
        }

        $admin->scenario = Admin::SCENARIO_ADMIN_CHANGES_PASSWORD;
        return $this->renderAjax('/admin/partial/change-password', compact('admin'));
    }

    /**
     * Changes administrator/moderator password
     *
     * @param null|integer $id Admin ID
     * @return Response
     * @throws NotAcceptableHttpException If administrator/moderator data is invalid
     * @throws NotFoundHttpException If administrator/moderator not found
     */
    public function actionChangePassword($id = null)
    {
        $admin = Admin::findOne($id);
        if (is_null($admin)) {
            throw new NotFoundHttpException(Yii::t('alert', 'ADMIN_NOT_FOUND'));
        }

        $admin->scenario = Admin::SCENARIO_ADMIN_CHANGES_PASSWORD;
        $admin->load(Yii::$app->request->post());
        $admin->setPassword($admin->password);
        $admin->scenario = Admin::SCENARIO_SYSTEM_SAVES_PASSWORD;
        if (!$admin->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_DATA'));
        }

        $admin->save();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'PASSWORD_CHANGED_SUCCESSFULLY'));
        return $this->redirect(['admin/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Removes administrator/moderator
     *
     * @return Response
     * @throws NotAcceptableHttpException If admin ID is invalid or admin ID is currently logged-in admin ID
     */
    public function actionRemove()
    {
        $id = Yii::$app->request->post('id');
        if (is_array($id)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_ADMIN_ID'));
        }

        if ($id == Yii::$app->admin->identity->id) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CANNOT_REMOVE_ITSELF_ACCOUNT'));
        }

        Admin::deleteAll(['id' => $id]);
        Yii::$app->session->setFlash('success', Yii::t('alert', 'ADMINISTRATOR_REMOVED_SUCCESSFULLY'));
        return $this->redirect(['admin/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Administrator/moderator edits its profile
     *
     * @return string|Response
     */
    public function actionEditMyProfile()
    {
        /** @var Admin $admin */
        $admin = Yii::$app->admin->identity;
        $admin->scenario = Admin::SCENARIO_ADMIN_CHANGES_SELF_PROFILE;
        if (Yii::$app->request->isGet) {
            return $this->render('/admin/edit/profile', compact('admin'));
        }

        $admin->load(Yii::$app->request->post());
        if (!$admin->save()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_DATA'));
            return $this->redirect(['admin/edit-my-profile', 'lang' => Yii::$app->language]);
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'ADMIN_DATA_SAVED_SUCCESSFULLY'));
        return $this->redirect(['admin/edit-my-profile', 'lang' => Yii::$app->language]);
    }

    /**
     * Administrator/moderator changes its password
     *
     * @return string|Response
     */
    public function actionChangeMyPassword()
    {
        /** @var Admin $admin */
        $admin = Yii::$app->admin->identity;
        $admin->scenario = Admin::SCENARIO_ADMIN_CHANGES_SELF_PASSWORD;
        if (Yii::$app->request->isGet) {
            return $this->render('/admin/edit/password', compact('admin'));
        }

        $admin->load(Yii::$app->request->post());
        if (!$admin->validatePassword($admin->oldPassword)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_ADMIN_CURRENT_PASSWORD'));
            return $this->redirect(['admin/change-my-password', 'lang' => Yii::$app->language]);
        }

        $admin->setPassword($admin->newPassword);
        $admin->scenario = Admin::SCENARIO_SYSTEM_SAVES_PASSWORD;
        if (!$admin->save()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_DATA'));
            return $this->redirect(['admin/change-my-password', 'lang' => Yii::$app->language]);
        }

        Yii::$app->session->setFlash('success', Yii::t('alert', 'PASSWORD_CHANGED_SUCCESSFULLY'));
        return $this->redirect(['admin/change-my-password', 'lang' => Yii::$app->language]);
    }
}