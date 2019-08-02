<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Create;
use common\components\audit\Log;
use common\components\audit\Login;
use common\components\audit\SystemMessage;
use common\components\ElasticSearch;
use common\components\ElasticSearch\Cities;
use common\components\Languages;
use common\components\MainController;
use common\components\Payment\PaymentFactory;
use common\models\AdminAsUser;
use common\models\CameFrom;
use common\models\City;
use common\models\CreditCode;
use common\models\Company;
use common\models\CompanyInvitation;
use common\models\CompanyUser;
use common\models\Country;
use common\models\FaqFeedback;
use common\models\Language;
use common\models\LoadCar;
use common\models\User;
use common\models\UserLanguage;
use common\models\UserServiceActive;
use common\models\UserService;
use common\models\Service;
use common\models\UserInvoice;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class SiteController
 *
 * @package frontend\controllers
 */
class CreditcodeController extends MainController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [[
                    'actions' => [
                        'buy-credits',
                        'buy-credits-validation',
                        'order-payment',

                    ],
                    'allow' => true,
                    'roles' => ['?']
                ]]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'buy-credits' => ['GET', 'POST'],
                    'buy-credits-validation' => ['POST'],
                    'order-payment' => ['GET'],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'service-payment-accept' || $action->id == 'service-payment-callback') {
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
     * Renders sign up page and signs up a new user
     *
     * @return string
     */
    public function actionBuyCredits()
    {
        $user = new User(['scenario' => User::SCENARIO_SYSTEM_MAKES_CREDITBUYER]);

        if (Yii::$app->request->isPost) {
            if ($user->load(Yii::$app->request->post()) && $user->createCreditCodesBuyer(Yii::$app->language)) {
                Log::user(Create::ACTION, Create::PLACEHOLDER_GUEST_BOUGHT_CREDITCODE, [$user], $user->id);
                Yii::$app->session->set('userId', $user->id);
                Yii::$app->session->set('creditCodeService', $user->creditCodeService);
                $this->createUserCompany($user);
                return $this->redirect([
                    'creditcode/order-payment',
                    'lang' => Yii::$app->language
                ]);
            }
            $user->scenario = User::SCENARIO_SYSTEM_MAKES_CREDITBUYER;
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREDITCODE_PURCHASE_FORM_CANNOT_BE_SAVED'));
        }

        $activeVatRateLegalCountryCode = Country::getValidVatRateCountryCode($user->vatCodeLegal);
        $user->vatCodeLegal = empty($user->vatCodeLegal) ? $activeVatRateLegalCountryCode : $user->vatCodeLegal;

        return $this->render('buycredit', [
            'user' => $user,
            'languages' => Language::getIconicNames(),
            'vatRateCountries' => Country::getVatRateCountries(),
            'activeVatRateLegalCountryCode' => $activeVatRateLegalCountryCode,
            'cityLegal' => City::getNameById($user->cityIdLegal),
            'cameFromSources' => CameFrom::getSources()
        ]);
    }

    /**
     * Validates sign up form
     *
     * @return string The error message array indexed by the attribute IDs in JSON format
     */
    public function actionBuyCreditsValidation()
    {
        $userAdapter = new AjaxValidationAdapter(new User(), User::SCENARIO_SYSTEM_MAKES_CREDITBUYER);
        return $userAdapter->validate();
    }

    /**
     * Generates pre-invoice document and renders credit-code purchase page
     *
     * @return string|Response
     */
    public function actionOrderPayment($lang)
    {
        $userId = Yii::$app->session->get('userId', null);
        $creditCodeService = Yii::$app->session->get('creditCodeService', null);
        $returnUrl = Yii::$app->session->get('returnUrl', '');
        if (is_null($userId) || is_null($creditCodeService)) {
            return $this->redirect(['buy-credits']);
        }

        $user = User::findOne(['id' => $userId]);
        if (is_null($user) || !$user->isForbidden()) {
            return $this->redirect(['buy-credits']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        /** @var Service $service Selected CreditCode Service */
        $service = Service::findOne(['name' => $creditCodeService]);
        /** @var UserService $userService */
        $userService = UserService::create($service, false, $user->id);
        if (!$userService) {
            $transaction->rollBack();
            return $this->redirect(['buy-credits']);
        }

        $creditcode = new CreditCode();
        $creditcode->user_service_id = $userService->id;
        $creditcode->creditcode = $creditcode->generateCreditCode();
        $creditcode->creditsleft = $service->credits;
        $creditcode->credits = $service->credits;
        if (!$creditcode->save()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREDITCODE_COULD_NOT_GENERATED'));
            $transaction->rollBack();
            return $this->redirect(['buy-credits']);
        }

        if (!UserInvoice::create($userService->id, $service, UserInvoice::PRE_INVOICE, $userId)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'CREDITCODE_INVOICE_COULD_NOT_GENERATED'));
            $transaction->rollBack();
            return $this->redirect(['buy-credits']);
        }

        Yii::info("CreditCode purchased");
        $transaction->commit();
        return $this->render('creditscode-purchase', [
            'userServiceId' => $userService->id,
            'service' => $service,
            'returnUrl' => $returnUrl
        ]);
    }
    
    /**
     * Creates new user company
     *
     * @param User $user User model
     * @throws ServerErrorHttpException If user company cannot be created
     */
    private function createUserCompany(User $user)
    {
        $company = new Company($user->id);
        if (!$company->createCreditCodeCompany()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CREDITCODE_CANNOT_CREATE_USER_COMPANY'));
        }

        //Log::user(Create::ACTION, Create::PLACEHOLDER_USER_REGISTERED_COMPANY, [$company], $user->id);
    }
}
