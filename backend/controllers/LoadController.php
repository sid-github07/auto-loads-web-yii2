<?php

namespace backend\controllers;

use common\components\ElasticSearch\Loads;
use common\models\Country;
use common\models\Language;
use common\models\Load;
use common\models\LoadCity;
use common\models\CreditService;
use common\models\User;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\Url;

/**
 * Class LoadController
 *
 * @package backend\controllers
 */
class LoadController extends Controller
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
                            'previews',
                            'load-preview',
                            'show-load',
                            'remove-load',
                            'toggle-visibility',
                            'load-adv-form',
                            'adv-load',
                            'open-contacts-form',
                            'open-contacts',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest &&
                                Yii::$app->admin->identity->isAdmin();
                        }
                    ],
                    [
                        'allow' => false,
                        'denyCallback' => function ($rule, $action) {
                            throw new ForbiddenHttpException(Yii::t(
                                'alert', 'ERROR_ACTION_NEEDS_RIGHTS'));
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'previews' => ['POST'],
                    'load-preview' => ['POST'],
                    'show-load' => ['GET'],
                    'remove-load' => ['GET'],
                    'toggle-visibility' => ['GET'],
                    'load-adv-form' => ['POST'],
                    'adv-load' => ['POST'],
                    'open-contacts-form' => ['POST'],
                    'open-contacts' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders load index page
     *
     * @return string
     */
    public function actionIndex()
    {
        $load = new Load(['scenario' => Load::SCENARIO_ADMIN_FILTERS_LOADS]);
        $loadCity = new LoadCity(['scenario' => LoadCity::SCENARIO_ADMIN_FILTERS_LOADS]);
        $load->load(Yii::$app->request->get());
        $loadCity->load(Yii::$app->request->get());

        return $this->render('index', [
            'load' => $load,
            'loadCity' => $loadCity,
            'countries' => Country::getAssociativeNames(),
            'loadTypes' => Load::getTranslatedTypes(),
            'dataProvider' => $load->getAdminDataProvider($loadCity),
        ]);
    }

    /**
     * Renders loads previews
     *
     * @return string
     */
    public function actionPreviews()
    {
        $id = Yii::$app->request->post('id');
        return $this->renderAjax('/load/partial/previews', [
            'load' => Load::findOne($id),
        ]);
    }

    /**
     * Renders load preview
     *
     * @return null|string
     */
    public function actionLoadPreview()
    {
        $id = Yii::$app->request->post('id');
        $load = Load::findOne($id);
        if (is_null($load)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_PREVIEW_LOAD_NOT_FOUND'));
            return null;
        }

        return $this->renderAjax('/load/partial/preview', [
            'load' => $load,
            'languages' => isset($load->user) ? Language::findUserLanguages($load->user->id) : [],
        ]);
    }

    /**
     * Renders only specific load
     *
     * @param null|integer $id Load ID
     * @return string
     */
    public function actionShowLoad($id = null)
    {
        return $this->render('load', [
            'dataProvider' => new ActiveDataProvider([
                'query' => Load::find()->where(['id' => $id]),
                'sort' => false,
            ]),
        ]);
    }

    /**
     * Removes specific load
     *
     * @param null|integer $id Load ID that needs to be removed
     * @return Response
     * @throws NotAcceptableHttpException If load was not removed
     * @throws NotFoundHttpException If load was not found
     */
    public function actionRemoveLoad($id = null)
    {
        $load = Load::findOne($id);
        if (is_null($load)) {
            throw new NotFoundHttpException(Yii::t('alert', 'LOAD_PREVIEW_LOAD_NOT_FOUND'));
        }

        $removeStatus = Load::updateAll([
            'status' => Load::INACTIVE,
            'active' => Load::NOT_ACTIVATED,
        ], [
            'id' => $load->id,
        ]);

        if (!$removeStatus) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CANNOT_REMOVE_LOAD'));
        }

        if (Loads::removeByAdmin($load->id)) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_REMOVED_SUCCESSFULLY'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CANNOT_REMOVE_LOAD'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionToggleVisibility($id)
    {
        $load = Load::findOne($id);
        if (is_null($load)) {
            throw new NotFoundHttpException(Yii::t('alert', 'LOAD_NOT_FOUND'));
        }

        $load->scenario = Load::SCENARIO_CHANGE_ACTIVE;
        $load->active = $load->active === 1 ? 0 : 1;
        if ($load->save() === true) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_VISIBILITY_CHANGE_SUCCESS'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        throw new NotFoundHttpException(Yii::t('alert', 'LOAD_FAILED_TOGGLE_VISIBILITY'));
    }

    /**
     * @return string
     */
    public function actionLoadAdvForm()
    {
        $model = Load::findOne(['id' => Yii::$app->request->post('id')]);
        if (is_null($model)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        return $this->renderAjax('/shared/advertisement-form', compact('model'));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionAdvLoad()
    {
        $load = Load::findOne(['id' => Yii::$app->request->get('id')]);
        $data = Yii::$app->request->post('Load');
        $date = new \DateTime();
        $load->submit_time_adv = $date->format('Y-m-d H:i:s');
        $load->car_pos_adv = ((int) $data['car_pos_adv']);
        $load->days_adv = ((int) $data['days_adv']);
        $load->scenario = Load::SCENARIO_UPDATE_LOAD_ADV;
        
        if ($load->save() === true) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_ADVERTISE_SUCCESS'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        throw new NotFoundHttpException(Yii::t('alert', 'LOAD_FAILED_TO_SAVE'));
    }
    
    /**
     * @return string
     */
    public function actionOpenContactsForm()
    {
        $model = Load::findOne(['id' => Yii::$app->request->post('id')]);
        if (is_null($model)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }

        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        $dayList = $model->getDaysRanges();
        $actionUrl = Url::to(['load/open-contacts', 'id' => $model->id]);

        return $this->renderAjax('/shared/open-contacts-form', compact(
            'model', 'actionUrl', 'openContactsCost', 'dayList'));
    }

    /**
     * Handles request to set load open contacts
     * 
     * @return string
     */
    public function actionOpenContacts()
    {
        $post = Yii::$app->request->post();
        
        $load = Load::findOne(['id' => Yii::$app->request->get('id')]);
        $load->setScenario(Load::SCENARIO_UPDATE_OPEN_CONTACTS);
        if (is_null($load)) {
            return Yii::t('alert', 'LOAD_NOT_FOUND_BY_ID');
        }
        
        if (!$load->load($post)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INCORRECT_OPEN_CONTACTS_DATA'));
            $this->redirect(Yii::$app->request->referrer);
        }

        if (!empty($load->open_contacts_days)) {
            $load->setOpenContactsExpiry();
        }

        if ($load->validate() && $load->save()) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'LOAD_OPEN_CONTACTS_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'LOAD_OPEN_CONTACTS_FAILURE'));
        }

        $this->redirect(Yii::$app->request->referrer);
    }
}
