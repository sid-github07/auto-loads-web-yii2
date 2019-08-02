<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class RedirectsController
 * @package frontend\controllers
 */
class RedirectsController extends Controller
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
                            'about-us',
                            'announce-load',
                            'guidelines',
                            'how-to-use',
                            'imprint',
                            'index',
                            'my-loads',
                            'search-load',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'login',
                            'request-password-reset',
                            'sign-up',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'active-services',
                            'buy-subscription',
                            'change-my-data',
                            'change-company-data',
                            'invitation',
                            'load-suggestions',
                            'paid-invoices',
                            'settings',
                            'services-and-invoices',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Redirects old index url address to new
     * 
     * @return Response
     */
    public function actionIndex()
    {
        return $this->redirect(['site/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects old services and invoices url address to new
     * 
     * @return Response
     */
    public function actionServicesAndInvoices()
    {
        return $this->redirect(['subscription/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects old guidelines url address to new
     * 
     * @return Response
     */
    public function actionGuidelines()
    {
        return $this->redirect(['site/guidelines', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects old password reset url address to new
     * 
     * @return Response
     */
    public function actionRequestPasswordReset()
    {
        return $this->redirect(['site/request-password-reset', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects subscription purchase page url address to new
     * 
     * @return Response
     */
    public function actionBuySubscription()
    {
        return $this->redirect(['subscription/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects active services page url address to new
     * 
     * @return Response
     */
    public function actionActiveServices()
    {
        return $this->redirect(['subscription/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects paid-invoices page url address to new
     * 
     * @return Response
     */
    public function actionPaidInvoices()
    {
        return $this->redirect(['subscription/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects change my data page url address to new
     * 
     * @return Response
     */
    public function actionChangeMyData()
    {
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects change company data page url address to new
     * 
     * @return Response
     */
    public function actionChangeCompanyData()
    {
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects settings page url address to new
     * 
     * @return Response
     */
    public function actionSettings()
    {
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects search load page url address to new
     * 
     * @return Response
     */
    public function actionSearchLoad()
    {
        return $this->redirect(['load/search', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects announce load page url address to new
     * 
     * @return Response
     */
    public function actionAnnounceLoad()
    {
        return $this->redirect(['load/announce', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects my loads page url address to new
     * 
     * @return Response
     */
    public function actionMyLoads()
    {
        return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects load suggestions page url address to new
     * 
     * @return Response
     */
    public function actionLoadSuggestions()
    {
        return $this->redirect(['load/suggestions', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects login page url address to new
     * 
     * @return Response
     */
    public function actionLogin()
    {
        return $this->redirect(['site/login', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects imprint page url address to new
     * 
     * @return Response
     */
    public function actionImprint()
    {
        return $this->redirect(['site/imprint', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects about us page url address to new
     * 
     * @return Response
     */
    public function actionAboutUs()
    {
        return $this->redirect(['site/about-us', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects how to use page url address to new
     * 
     * @return Response
     */
    public function actionHowToUse()
    {
        return $this->redirect(['site/how-to-use', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects invitation page url address to new
     * 
     * @return Response
     */
    public function actionInvitation()
    {
        return $this->redirect(['settings/invitation', 'lang' => Yii::$app->language], 301);
    }
    
    /**
     * Redirects sign up page url address to new
     * 
     * @return Response
     */
    public function actionSignUp()
    {
        return $this->redirect(['site/sign-up', 'lang' => Yii::$app->language], 301);
    }
}