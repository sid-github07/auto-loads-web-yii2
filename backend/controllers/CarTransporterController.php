<?php

namespace backend\controllers;

use common\components\ElasticSearch\CarTransporters;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\Company;
use common\models\Language;
use common\models\CreditService;
use common\models\User;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\helpers\Url;

/**
 * Class CarTransporterController
 *
 * This controller is responsible for car transporters management in administration panel
 *
 * @package backend\controllers
 */
class CarTransporterController extends Controller
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
                            'contact-info-preview',
                            'show-load',
                            'remove-transporter',
                            'toggle-visibility',
                            'load-adv-form',
                            'adv-transporter',
                            'open-contacts-form',
                            'open-contacts',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest && Yii::$app->admin->identity->isAdmin();
                        }
                    ],
                    [
                        'allow' => false,
                        'denyCallback' => function () {
                            throw new ForbiddenHttpException(Yii::t('alert', 'ERROR_ACTION_NEEDS_RIGHTS'));
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'previews' => ['POST'],
                    'contact-info-preview' => ['POST'],
                    'remove-transporter' => ['GET'],
                    'toggle-visibility' => ['GET'],
                    'load-adv-form' => ['POST'],
                    'adv-transporter' => ['POST'],
                    'open-contacts-form' => ['POST'],
                    'open-contacts' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders car transporters page
     *
     * @return string
     */
    public function actionIndex()
    {
        $carTransporter = new CarTransporter(['scenario' => CarTransporter::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS]);
        $carTransporter->load(Yii::$app->request->get());

        $carTransporterCity = new CarTransporterCity([
            'scenario' => CarTransporterCity::SCENARIO_ADMIN_FILTERS_CAR_TRANSPORTERS,
        ]);
        $carTransporterCity->load(Yii::$app->request->get());
        $dataProvider = $carTransporter->getAdminDataProvider($carTransporterCity);

        return $this->render('/car-transporter/index', compact('carTransporter', 'carTransporterCity', 'dataProvider'));
    }

    /**
     * Renders specific car transporter previews
     *
     * @return string
     */
    public function actionPreviews()
    {
        $id = Yii::$app->request->post('id');
        $carTransporter = CarTransporter::findOne($id);
        return $this->renderAjax('/car-transporter/previews', compact('carTransporter'));
    }

    /**
     * Renders specific car transporter owner contact information preview
     *
     * @return string
     */
    public function actionContactInfoPreview()
    {
        $id = Yii::$app->request->post('id');
        $carTransporter = CarTransporter::findOne($id);
        if (is_null($carTransporter)) {
            return Yii::t('alert', 'CAR_TRANSPORTER_NOT_FOUND');
        }

        $company = Company::findUserCompany($carTransporter->user_id);
        $languages = Language::getUserSelectedLanguages($carTransporter->user_id);
        return $this->renderAjax('contact-info-preview', compact('carTransporter', 'company', 'languages'));
    }
    
    /**
     * Renders only specific car transporter
     *
     * @param null|integer $id car transporter ID
     * @return string
     */
    public function actionShowLoad($id = null)
    {
        return $this->render('car-transporter', [
            'dataProvider' => new ActiveDataProvider([
                'query' => CarTransporter::find()->where(['id' => $id]),
                'sort' => false,
            ]),
        ]);
    }

    /**
     * Removes specific transporter
     *
     * @param null|integer $id transporter ID that needs to be removed
     * @return Response
     * @throws NotAcceptableHttpException If load was not removed
     * @throws NotFoundHttpException If load was not found
     */
    public function actionRemoveTransporter($id = null)
    {
        $model = CarTransporter::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'TRANSPORTER_NOT_FOUND'));
        }

        $removeStatus = $model::updateAll([
            'archived' => CarTransporter::ARCHIVED,
            'visible' => CarTransporter::NOT_ACTIVATED,
        ], [
            'id' => $model->id,
        ]);

        if (!$removeStatus) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CANNOT_REMOVE_TRANSPORTER'));
        }

        if (CarTransporters::removeByAdmin($model->id)) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_REMOVED_SUCCESSFULLY'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CANNOT_REMOVE_TRANSPORTER'));
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
        $model = CarTransporter::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('alert', 'TRANSPORTER_NOT_FOUND'));
        }

        $model->scenario = CarTransporter::SCENARIO_UPDATE_TRANSPORTER_ADV;
        $model->visible = $model->visible === 1 ? 0 : 1;
        if ($model->save() === true) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_VISIBILITY_CHANGE_SUCCESS'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        throw new NotFoundHttpException(Yii::t('alert', 'TRANSPORTER_FAILED_TOGGLE_VISIBILITY'));
    }

    /**
     * @return string
     */
    public function actionLoadAdvForm()
    {
        $model = CarTransporter::findOne(['id' => Yii::$app->request->post('id')]);
        if (is_null($model)) {
            return Yii::t('alert', 'TRANSPORTER_NOT_FOUND');
        }
        return $this->renderAjax('/shared/advertisement-form', compact('model'));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionAdvTransporter()
    {
        $model = CarTransporter::findOne(['id' => Yii::$app->request->get('id')]);
        $data = Yii::$app->request->post('CarTransporter');
        $date = new \DateTime();
        $model->submit_time_adv = $date->format('Y-m-d H:i:s');
        $model->car_pos_adv = ((int) $data['car_pos_adv']);
        $model->days_adv = ((int) $data['days_adv']);
        $model->scenario = CarTransporter::SCENARIO_UPDATE_TRANSPORTER_ADV;
        
        if ($model->save() === true) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_ADVERTISE_SUCCESS'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        throw new NotFoundHttpException(Yii::t('alert', 'TRANSPORTER_FAILED_TO_SAVE'));
    }
    
    /**
     * Render open contacts modal form
     * 
     * @return string
     */
    public function actionOpenContactsForm()
    {
        $model = CarTransporter::findOne(['id' => Yii::$app->request->post('id')]);
        if (is_null($model)) {
            return Yii::t('alert', 'TRANSPORTER_NOT_FOUND');
        }
        
        $openContactsCost = CreditService::getCost(CreditService::CREDIT_TYPE_OPEN_CONTACTS);
        $dayList = $model->getDaysRanges();
        $actionUrl = Url::to(['car-transporter/open-contacts', 'id' => $model->id]);

        return $this->renderAjax('/shared/open-contacts-form', compact(
            'model', 'actionUrl', 'openContactsCost', 'dayList'));
    }
    
    /**
     * Handles request to set car transporter open contacts
     * 
     * @return string
     */
    public function actionOpenContacts()
    {
        $post = Yii::$app->request->post();
        
        $model = CarTransporter::findOne(['id' => Yii::$app->request->get('id')]);
        $model->setScenario(CarTransporter::SCENARIO_UPDATE_OPEN_CONTACTS);
        if (is_null($model)) {
            return Yii::t('alert', 'TRANSPORTER_NOT_FOUND_BY_ID');
        }
        
        if (!$model->load($post)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INCORRECT_OPEN_CONTACTS_DATA'));
            $this->redirect(Yii::$app->request->referrer);
        }

        if (!empty($model->open_contacts_days)) {
            $model->setOpenContactsExpiry();
        }

        if ($model->validate() && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'TRANSPORTER_OPEN_CONTACTS_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'TRANSPORTER_OPEN_CONTACTS_FAILURE'));
        }

        $this->redirect(Yii::$app->request->referrer);
    }
}