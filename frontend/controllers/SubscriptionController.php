<?php

namespace frontend\controllers;

use common\components\audit\Log;
use common\components\audit\SystemMessage;
use common\components\MainController;
use common\components\Payment\PaymentFactory;
use common\models\Country;
use common\models\Service;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SubscriptionController
 * @package frontend\controllers
 */
class SubscriptionController extends MainController
{
    /** @const string New service order tab */
    const TAB_NEW_SERVICE_ORDER = 'new-service-order';

    /** @var string  */
    const TAB_CREDIT_TOP_UP_ORDER = 'credit-topup-order';

    /** @const string Active services tab */
    const TAB_ACTIVE_SERVICES = 'active-services';

    /** @const string Paid accounts tab */
    const TAB_PAID_ACCOUNTS = 'paid-accounts';

    /** @var string  */
    const CREDIT_TOPUP_ORDER_PJAX_ID = 'credit-topup-order-pjax';

    /** @const string New service order PJAX container ID */
    const NEW_SERVICE_ORDER_PJAX_ID = 'new-service-order-pjax';
    
    /** @const string Service selection step */
    const STEP_SERVICE_SELECTION = 'service-selection';
    
    /** @const string Service confirmation step */
    const STEP_SERVICE_CONFIRMATION = 'service-confirmation';
    
    /** @const string Service purchase step */
    const STEP_SERVICE_PURCHASE = 'service-purchase';
    
    /** @const string Service activation step */
    const STEP_SERVICE_ACTIVATION = 'service-activation';

    /** @const string Subscription reminder token */
    const REMINDER_TOKEN = 'lccSY6ppSN2r4fW2kFFf';

    /** @const string Active user services remove token */
    const REMOVE_ACTIVE_SERVICES_TOKEN = '2yvG5MIG6zEfJ9kOIBm0';

    /** @const string Current credits update token */
    const UPDATE_CURRENT_CREDITS_TOKEN = 'YiQRRSrQ5JWMuHiDAwQa';

    /** @const integer Day of the month, that current credits need to be updated */
    const UPDATE_CURRENT_CREDITS_MONTH_DAY = 01;

    /** @var null|Service */
    private $service = null;

    /** @var null|UserService */
    private $userService = null;

    /**
     * Sets service
     *
     * @param null|Service $service
     */
    public function setService($service = null)
    {
        $this->service = $service;
    }

    /**
     * Returns service
     *
     * @return Service|null
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets user service
     *
     * @param null|UserService $userService
     */
    public function setUserService($userService = null)
    {
        $this->userService = $userService;
    }

    /**
     * Returns user service
     *
     * @return UserService|null
     */
    public function getUserService()
    {
        return $this->userService;
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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'download-invoice',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'service-payment-accept',
                            'service-payment-cancel',
                        ],
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {

                            // Normally the methods are for logged in users the exceptions are purchasing of creditcodes.
                            $get = Yii::$app->request->get();
                            $isLoggedIn = !Yii::$app->user->getIsGuest();
                            if (isset($get['order'])) {
                                $userService = UserService::findById($get['order']);
                                $userServiceType = $userService->service->service_type_id;
                                if (!$isLoggedIn && $userServiceType == ServiceType::CREDITCODE_TYPE_ID) {
                                    return true;
                                }
                            }
                            return $isLoggedIn;
                        },
                    ],
                    [
                        'actions' => [
                            'service-payment-method',
                        ],
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function () {
                            $methodIsValid = false;
                            $get = Yii::$app->request->get();
                            $isLoggedIn = !Yii::$app->user->getIsGuest();
                            if (isset($get['method'])) {
                                $methodIsValid = UserService::isMethodValid($get['method']);
                            }
                            // Normally the methods are for logged in users the exceptions are purchasing of creditcodes.
                            if (isset($get['id'])) {
                                $userService = UserService::findById($get['id']);
                                $userServiceType = $userService->service->service_type_id;
                                if (!$isLoggedIn && $userServiceType == ServiceType::CREDITCODE_TYPE_ID) {
                                    return $methodIsValid;
                                }
                            }
                            return $isLoggedIn && $methodIsValid;
                        },
                    ],
                    [
                        'actions' => ['service-payment-callback'],
                        'allow' => true,
                        'roles' => ['?'],
                        'matchCallback' => function () {
                            $get = Yii::$app->request->get();
                            if (!isset($get['method'])) {
                                return false;
                            }
                            return UserService::isMethodValid($get['method']);
                        },
                    ],
                    [
                        'actions' => ['service-selection'],
                        'allow' => true,
                        'matchCallback' => function () {
                            $post = Yii::$app->request->post();
                            $pjax = isset($post['_pjax']) ? $post['_pjax'] : null;
                            return $pjax === '#' . self::NEW_SERVICE_ORDER_PJAX_ID || $pjax === '#' . self::CREDIT_TOPUP_ORDER_PJAX_ID;
                        },
                    ],
                    [
                        'actions' => ['service-confirmation', 'service-purchase'],
                        'allow' => true,
                        'matchCallback' => function () {
                            $post = Yii::$app->request->post();
                            if (!isset($post['serviceId'])) {
                                return false;
                            }
                            $service = Service::findUserAvailableById($post['serviceId']);
                            if (is_null($service)) {
                                return false;
                            }
                            $this->setService($service);
                            $pjax = isset($post['_pjax']) ? $post['_pjax'] : null;
                            return $pjax === '#' . self::NEW_SERVICE_ORDER_PJAX_ID || $pjax === '#' . self::CREDIT_TOPUP_ORDER_PJAX_ID;
                        },
                    ],
                    [
                        'actions' => ['service-activation', 'back-to-service-purchase'],
                        'allow' => true,
                        'matchCallback' => function () {
                            $post = Yii::$app->request->post();
                            if (!isset($post['userServiceId'])) {
                                return false;
                            }
                            $userService = UserService::findById($post['userServiceId']);
                            if (!$userService->isUserServiceOwner()) {
                                return false;
                            }
                            $this->setUserService($userService);
                            $pjax = isset($post['_pjax']) ? $post['_pjax'] : null;
                            return $pjax === '#' . self::NEW_SERVICE_ORDER_PJAX_ID || $pjax === '#' . self::CREDIT_TOPUP_ORDER_PJAX_ID;
                        },
                    ],
                    [
                        'actions' => ['reminder'],
                        'allow' => true,
                        'roles' => ['?'],
                        'matchCallback' => function () {
                            $get = Yii::$app->request->get();
                            if (!isset($get['token'])) {
                                return false;
                            }
                            return $get['token'] === self::REMINDER_TOKEN;
                        },
                    ],
                    [
                        'actions' => ['remove-ended-active-services'],
                        'allow' => true,
                        'roles' => ['?'],
                        'matchCallback' => function () {
                            $get = Yii::$app->request->get();
                            if (!isset($get['token'])) {
                                return false;
                            }
                            return $get['token'] === self::REMOVE_ACTIVE_SERVICES_TOKEN;
                        },
                    ],
                    [
                        'actions' => ['update-current-credits'],
                        'allow' => true,
                        'roles' => ['?'],
                        'matchCallback' => function () {
                            $get = Yii::$app->request->get();
                            if (!isset($get['token'])) {
                                return false;
                            }
                            return $get['token'] === self::UPDATE_CURRENT_CREDITS_TOKEN &&
                                   ((int) date('d') === self::UPDATE_CURRENT_CREDITS_MONTH_DAY);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'download-invoice' => ['GET'],
                    'service-payment-accept' => ['GET', 'POST'],
                    'service-payment-cancel' => ['GET'],
                    'service-payment-method' => ['GET'],
                    'service-payment-callback' => ['GET', 'POST'],
                    'service-selection' => ['POST'],
                    'service-confirmation' => ['POST'],
                    'service-purchase' => ['POST'],
                    'service-activation' => ['POST'],
                    'back-to-service-purchase' => ['POST'],
                    'reminder' => ['GET'],
                    'remove-ended-active-services' => ['GET'],
                    'update-current-credits' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Renders index page
     *
     * @param string $tab Current active tab
     * @param null|integer $isPaid Attribute, whether user is redirected to this action from accept or cancel payment
     * @return string
     */
    public function actionIndex($tab = self::TAB_NEW_SERVICE_ORDER, $isPaid = null)
    {
        list($preInvoices, $invoices) = UserInvoice::getAllUserInvoicesAndPreInvoices();
        return $this->render('index', [
            'title' => Yii::t('seo', 'TITLE_SUBSCRIPTION_INDEX'),
            'tab' => $tab,
            'subscriptionServices' => Service::getUserAvailableSubscriptionServices(),
            'adCredits' => Service::getUserAvailableCreditServices(),
            'steps' => is_null($isPaid) ? [self::STEP_SERVICE_SELECTION] : [
                self::STEP_SERVICE_SELECTION,
                self::STEP_SERVICE_CONFIRMATION,
                self::STEP_SERVICE_PURCHASE,
                self::STEP_SERVICE_ACTIVATION,
            ],
            'isPaid' => $isPaid,
            'preInvoices' => $preInvoices,
            'invoices' => $invoices,
            'activeServices' => UserServiceActive::getAllActiveUserServicesWithName(),
            'currentCredits' => User::getCurrentCredits(),
            'advCredits' => User::getServiceCredits(),
            'applyVAT' => Country::applyVAT(Yii::$app->user->id),
        ]);
    }

    /**
     * Renders service selection page
     *
     * @return string
     */
    public function actionServiceSelection()
    {
        $data = Yii::$app->request->post();
        if (! isset($data['serviceTypeId'])) {
            $serviceTypeId = '__service-selection';
        } else {
            $serviceTypeId = $data['serviceTypeId'];
        }

        // there are two type of services being sold - adv credits / subscriptions - depending which is selected
        // we render appropriate view
        $view = $serviceTypeId == ServiceType::MEMBER_TYPE_ID ? '__service-selection' : '__credit-topup-selection';
        $services =  $serviceTypeId == ServiceType::MEMBER_TYPE_ID  ?
            Service::getUserAvailableSubscriptionServices() : Service::getUserAvailableCreditServices();

        return $this->renderPartial($view, [
            'services' => $services,
            'steps' => [self::STEP_SERVICE_SELECTION],
            'applyVAT' => Country::applyVAT(Yii::$app->user->id),
        ]);
    }

    /**
     * Renders service confirmation page
     *
     * @return string|Response
     */
    public function actionServiceConfirmation()
    {
        if (is_null($this->getService())) {
            return $this->redirect(['service-selection', 'lang' => Yii::$app->language]);
        }

        return $this->renderPartial('__service-confirmation', [
            'service' => $this->getService(),
            'steps' => [
                self::STEP_SERVICE_SELECTION, 
                self::STEP_SERVICE_CONFIRMATION,
            ],
            'applyVAT' => Country::applyVAT(Yii::$app->user->id),
        ]);
    }

    /**
     * Generates pre-invoice document and renders service purchase page
     *
     * @return string|Response
     */
    public function actionServicePurchase()
    {
        if (is_null($this->getService())) {
            return $this->redirect(['service-selection', 'lang' => Yii::$app->language]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        /** @var UserService $userService */
        $userService = UserService::create($this->getService());
        if (!$userService) {
            return $this->redirect(['service-selection', 'lang' => Yii::$app->language]);
        }

        if (!UserInvoice::create($userService->id, $this->getService())) {
            // TODO: there must be set error message to flash
            $transaction->rollBack();
            return $this->redirect(['service-selection', 'lang' => Yii::$app->language]);
        }
        Yii::info("Service purchased");
        $transaction->commit();
        return $this->renderPartial('__service-purchase', [
            'userServiceId' => $userService->id,
            'service' => $this->getService(),
            'steps' => [
                self::STEP_SERVICE_SELECTION, 
                self::STEP_SERVICE_CONFIRMATION,
                self::STEP_SERVICE_PURCHASE,
            ],
        ]);
    }

    /**
     * Returns user to purchase page
     *
     * @return string
     */
    public function actionBackToServicePurchase()
    {
        return $this->renderPartial('__service-purchase', [
            'userServiceId' => $this->getUserService()->id,
            'steps' => [
                self::STEP_SERVICE_SELECTION,
                self::STEP_SERVICE_CONFIRMATION,
                self::STEP_SERVICE_PURCHASE,
            ],
        ]);
    }

    /**
     * Downloads invoice document
     *
     * @param null|integer $id User service ID
     * @param integer $type Invoice type
     * @throws ForbiddenHttpException If current user is not user service owner
     * @throws NotFoundHttpException If invoice file not found
     */
    public function actionDownloadInvoice($id = null, $type = UserInvoice::PRE_INVOICE)
    {
        $userService = UserService::findById($id);
        if (!$userService->isUserServiceOwner()) {
            throw new ForbiddenHttpException(Yii::t('alert', 'SUBSCRIPTION_DOWNLOAD_INVOICE_NOT_USER_SERVICE_OWNER'));
        }

        /** @var UserInvoice $userInvoice */
        $userInvoice = UserInvoice::findByUserServiceId($userService->id);
        $fullName = $userInvoice->file_name . '.' . $userInvoice->file_extension;
        $pathToInvoice = Yii::$app->params[($type == UserInvoice::PRE_INVOICE) ? 'preInvoicePath' : 'invoicePath'];
        $fullPath = $pathToInvoice . $fullName;
        if (!file_exists($fullPath)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SUBSCRIPTION_DOWNLOAD_INVOICE_FILE_NOT_FOUND'));
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: ' . UserInvoice::DOCUMENT_MIME_TYPE);
        header('Content-Disposition: attachment; filename="' . $fullName . '"');
        readfile($fullPath);
        exit;
    }

    /**
     * Redirects user to service payment page by selected payment method
     *
     * @param null|integer $id User service ID
     * @param integer $method Payment method
     * @return Response
     * @throws NotAcceptableHttpException If payment method is invalid or user service not found
     */
    public function actionServicePaymentMethod($id = null, $method = UserService::PAYSERA)
    {
        $user = null;
        $userService = UserService::findById($id);
        if ($userService->service->service_type_id == ServiceType::CREDITCODE_TYPE_ID && Yii::$app->user->getIsGuest()) {
            // Creditcodes are paid by not logged in users.
            $userId = Yii::$app->session->get('userId', null);
            $user = User::findbyId($userId);
            if (!$userService->isUserServiceOwner($userId) || is_null($user)) {
                throw new NotAcceptableHttpException(Yii::t('alert', 'SERVICE_PAYMENT_METHOD_USER_IS_NOT_OWNER'));
            }
        } else {
            if (!$userService->isUserServiceOwner()) {
                throw new NotAcceptableHttpException(Yii::t('alert', 'SERVICE_PAYMENT_METHOD_USER_IS_NOT_OWNER'));
            }
        }

        if ($userService->isPaid()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'SERVICE_PAYMENT_METHOD_USER_SERVICE_IS_PAID'));
        }

        $payment = PaymentFactory::create($method);
        $price = $userService->getBruttoPrice();
        if (is_null($price)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'SERVICE_PAYMENT_METHOD_SERVICE_PRICE_IS_UNDEFINED'));
        }
        $payment->setDefaultAttributes($userService->id, $price, $user);
        $payment->pay();
        exit();
    }

    /**
     * Validates payment and marks user service as paid
     *
     * @param null|integer $method Payment method
     */
    public function actionServicePaymentCallback($method = null)
    {
        $payment = PaymentFactory::create($method);
        if (!$payment->validate()) {
            echo 'FAILED'; exit();
        }
        $payment->setDefaultAttributes();
        $transaction = Yii::$app->db->beginTransaction();
        if (!$payment->markAsPaid() ||//Atnaujinam prenumeratu istorijos irasa
            !$payment->activateUserService() || //Jei neegzistuoja aktyvi paslauga, sukuriam nauja
            !$payment->updateCurrentCredits() ||
            !$payment->generateInvoice()
        ) {
            $transaction->rollBack();

            echo 'FAILED'; exit();
        }
        $transaction->commit();
        echo 'OK';
        exit();
    }

    /**
     * Checks subscription alert visibility and redirects user to index page and shows successful payment
     *
     * @return Response
     */
    public function actionServicePaymentAccept()
    {
        $get = Yii::$app->request->get();
        if (isset($get['order'])) {
            $userService = UserService::findById($get['order']);
            $this->setUserService($userService);
        }
        if ($this->userService->service->service_type_id === ServiceType::CREDITCODE_TYPE_ID) {
            // If creditcode purchase redirect to last search result and flash msg + creditcode
            Yii::$app->session->setFlash('success', Yii::t('alert', 'CREDITCODE_SUCCESSFULLY_PURCHASED', [
                'creditCode' => $this->userService->creditCode->creditcode,
                'invoiceNumber' => $this->userService->userInvoices[0]->number,
                'email' => $this->userService->user->email,
            ]));
            $this->sendCreditCodeEmail($this->userService->user, $this->userService->userInvoices[0], $this->userService->creditCode->creditcode);
            $returnUrl = Yii::$app->session->get('returnUrl', null);
            if (!is_null($returnUrl)) {
                return $this->redirect($returnUrl);
            }
            return $this->redirect(['creditcode/buy-credits',
                'lang' => Yii::$app->language,
            ]);
        }

        UserServiceActive::checkSubscriptionAlertVisibility();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'SERVICE_PAID_AND_ACTIVATED_SUCCESSFULLY'));

        if (isset($this->userService) && isset($this->userService->service) && $this->userService->service->service_type_id === ServiceType::SERVICE_CREDITS_TYPE_ID) {
            return $this->redirect(['my-announcement/index', 'lang' => Yii::$app->language]);
        }

        return $this->redirect(['subscription/index',
            'lang' => Yii::$app->language,
            'tab' => self::TAB_ACTIVE_SERVICES,
            'isPaid' => null,
        ]);
    }

    /**
     * Redirects user to index page and shows unsuccessful payment
     *
     * @return Response
     */
    public function actionServicePaymentCancel()
    {
        Yii::$app->session->setFlash('error', Yii::t('app', 'SERVICE_NOT_PAID_AND_NOT_ACTIVATED'));

        if (Yii::$app->user->getIsGuest() && !is_null(Yii::$app->session->get('userId', null))) {
            return $this->redirect(['creditcode/order-payment',
                'lang' => Yii::$app->language,
            ]);
        }
        return $this->redirect(['subscription/index',
            'lang' => Yii::$app->language,
            'tab' => self::TAB_NEW_SERVICE_ORDER,
            'isPaid' => null,
        ]);
    }

    private function sendCreditCodeEmail(User $user, UserInvoice $userInvoice, $creditCode, $type = UserInvoice::PRE_INVOICE) {
        $fullName = $userInvoice->file_name . '.' . $userInvoice->file_extension;
        $pathToInvoice = Yii::$app->params[($type == UserInvoice::PRE_INVOICE) ? 'preInvoicePath' : 'invoicePath'];
        $fullPath = $pathToInvoice . $fullName;
        if (!file_exists($fullPath)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SUBSCRIPTION_DOWNLOAD_INVOICE_FILE_NOT_FOUND'));
        }

        return Yii::$app->mailer->compose('creditcode/creditcode', [
                'companyName' => Yii::$app->params['companyName'],
                'creditCode' => $creditCode,
            ])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($user->email)
            ->setSubject(Yii::t('mail', 'USER_SUCCESSFULL_CREDITCODE_PURCHASE_SUBJECT', [
                'companyName' => Yii::$app->params['companyName'],
            ]))
            ->attach($fullPath)
            ->send();
    }

    /**
     * Renders service activation page
     *
     * @return string|Response
     */
    public function actionServiceActivation()
    {
        if (is_null($this->getUserService()) || !$this->getUserService()->isUserServiceOwner()) {
            return $this->redirect(['service-selection', 'lang' => Yii::$app->language]);
        }

        return $this->renderPartial('__service-activation', [
            'userInvoice' => UserInvoice::findByUserServiceId($this->getUserService()->id),
            'userServiceId' => $this->getUserService()->id,
            'steps' => [
                self::STEP_SERVICE_SELECTION,
                self::STEP_SERVICE_CONFIRMATION,
                self::STEP_SERVICE_PURCHASE,
                self::STEP_SERVICE_ACTIVATION,
            ],
        ]);
    }

    /**
     * Sends subscription reminder
     *
     * @return null
     */
    public function actionReminder()
    {
        $activeServices = UserServiceActive::findAllEndingActiveServices();
        if (empty($activeServices)) {
            return null;
        }

        Yii::$app->db->beginTransaction();
        /** @var UserServiceActive $activeService */
        foreach ($activeServices as $activeService) {
            $isSent = $activeService->sendSubscriptionReminder();
            $activeService->reminder = $isSent ? UserServiceActive::REMINDER_SEND : UserServiceActive::REMINDER_FAILED;
            $activeService->scenario = UserServiceActive::SCENARIO_SYSTEM_SENDS_SUBSCRIPTION_REMINDER;
            $activeService->save();

            if ($isSent) {
                $systemMessagePlaceholder = SystemMessage::PLACEHOLDER_USER_RECEIVED_SUBSCRIPTION_REMINDER_EMAIL;
                Log::user(SystemMessage::ACTION, $systemMessagePlaceholder, [], $activeService->user_id);
            }
        }

        Yii::$app->db->transaction->commit();
        return null;
    }

    /**
     * Removes all ended active services
     *
     * @return null
     */
    public function actionRemoveEndedActiveServices()
    {
        $subscriptionsHistory = UserService::getAvailableSubscriptions();
        
        $subscriptionsToActivate = $this->findRightSubscription($subscriptionsHistory);
        
        $activeSubscriptions = UserServiceActive::findCurrentlyValidSubscriptions();
                
        Yii::$app->db->beginTransaction();
        
        foreach($subscriptionsToActivate as $userId => $subscription) {
            
            if (isset($activeSubscriptions[$userId])) {
                continue;
            }
            
            $userServiceActive = new UserServiceActive();
            
            $userServiceActive->saveRightSubscriptions($subscription);
        }
        $subscriptions = UserServiceActive::findExpiredSubscriptions();
//      /** @var UserServiceActive $subscription */
        foreach ($subscriptions as $subscription) {
            if (!isset($subscriptionsToActivate[$subscription->user_id])
                && ($subscription->service->service_type_id == ServiceType::MEMBER_TYPE_ID)) {
                $subscription->user->updateCurrentCredits(User::DEFAULT_CURRENT_CREDITS);
                $subscription->user->informAboutExpiredSubscription();      
            }
            
            $subscription->delete();
        }
        Yii::$app->db->transaction->commit();
        return null;
    }

    /**
     * Updates current user credits
     *
     * @return null
     */
    public function actionUpdateCurrentCredits()
    {
        $activeServices = UserServiceActive::findAllActiveServices();
        if (empty($activeServices)) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        /** @var UserServiceActive $activeService */
        foreach ($activeServices as $activeService) {
            $activeService->user->updateCurrentCredits($activeService->credits);
        }
        $transaction->commit();
        return null;
    }
    
    /**
     * Finds available subscription for each user from purchase history 
     * 
     * @return array
     */
    private function findRightSubscription($subscriptionsHistory)
    {
        $subscriptionsToActivate = [];
        
        foreach($subscriptionsHistory as $historyItem) {
            $userId = $historyItem->user_id;
            if (!isset($subscriptionsToActivate[$userId])) {
                $subscriptionsToActivate[$userId] = $historyItem;
                continue;
            }
            if ($historyItem->admin_id && !$subscriptionsToActivate[$userId]->admin_id) {
                $subscriptionsToActivate[$userId] = $historyItem;
                continue;
            }
            
            if ($historyItem->admin_id 
                && $subscriptionsToActivate[$userId]->admin_id 
                && ($historyItem->created_at > $subscriptionsToActivate[$userId]->created_at)) {
                $subscriptionsToActivate[$userId] = $historyItem;
                continue;
            }
            
            if (!$historyItem->admin_id
                && !$subscriptionsToActivate[$userId]->admin_id   
                && ($historyItem->created_at < $subscriptionsToActivate[$userId]->created_at)) {
                $subscriptionsToActivate[$userId] = $historyItem;
            }
        }

        return $subscriptionsToActivate;
    }
    
}
