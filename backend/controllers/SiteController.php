<?php
namespace backend\controllers;

use common\components\ElasticSearch;
use common\components\ElasticSearch\Cities;
use common\models\Admin;
use common\models\AdminAsUser;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
                            'error',
                            'set-timezone-offset',
                            'search-for-location',
                        ],
                        'allow' => true,
                    ],
                    
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'matchCallback' => function () {
                            return Yii::$app->admin->isGuest;
                        }
                    ],
                    
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest;
                        }
                    ],
                    [
                        'actions' => ['login-to-user'],
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
                    'logout' => ['post'],
                    'login-to-user' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'set-timezone-offset') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
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
     * Renders main page
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Renders login page and logs-in admin to current session
     *
     * Admin must be logged-out in order to get to login page.
     * If post is empty, or admin cannot be logged-in, then this function renders login page.
     * Otherwise loads post to login model and tries to login admin to current session and if admin logged-in successfully,
     * then returns him to previous page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->admin->isGuest) {
            return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
        }

        $model = new Admin(['scenario' => Admin::SCENARIO_ADMIN_LOG_IN]);
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('alert', 'LOGIN_INCORRECT_EMAIL_OR_PASSWORD'));
            }
        }
        $this->layout = 'login';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs-out current admin from session
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->admin->logout();

        return $this->goHome();
    }

    /**
     * Adds administration timezone offset to session
     */
    public function actionSetTimezoneOffset()
    {
        $offset = Yii::$app->request->post('offset');
        Yii::$app->session->set('timezone-offset', $offset);
        return;
    }

    /**
     * Administrator login to user account
     *
     * @param null|integer $id User ID, to which administrator wants to connect
     * @return Response
     * @throws NotAcceptableHttpException If adminAsUser model is not valid
     */
    public function actionLoginToUser($id = null)
    {
        $adminAsUser = new AdminAsUser([
            'scenario' => AdminAsUser::SCENARIO_ADMIN_LOGINS_TO_USER,
            'admin_id' => Yii::$app->admin->id,
            'user_id' => $id,
            'token' => AdminAsUser::generateToken(),
            'ip' => Yii::$app->request->userIP,
        ]);

        if (!$adminAsUser->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_ADMIN_AS_USER_CONNECTION'));
        }

        $adminAsUser->save(false);
        return $this->redirect($adminAsUser->generateLinkToUserAccount());
    }

    /**
     * Searches for possible locations by given phrase
     *
     * @param string $phrase Searchable location phrase
     * @param boolean $showDirections Attribute whether in results can be included directions
     * @return null|string
     */
    public function actionSearchForLocation($phrase = '', $showDirections = true)
    {
        if (strlen($phrase) < ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH) {
            return null;
        }

        $items = [];
        $popularCity = Cities::popularCitySearch($phrase);
        if (!$popularCity) {
            Cities::addSimpleCities($phrase, $items);
            return json_encode(compact('items'));
        }

        $popularCityItem = Cities::formatItem($popularCity);
        array_push($items, $popularCityItem);
        Cities::addSimpleCities($phrase, $items, $popularCityItem);

        if (!$showDirections) {
            return json_encode(compact('items'));
        }

        Cities::addPopularDirections($items, $popularCity, $popularCityItem);
        return json_encode(compact('items'));
    }
}
