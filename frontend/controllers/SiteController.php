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
use common\models\AdminAsUser;
use common\models\CameFrom;
use common\models\City;
use common\models\Company;
use common\models\CompanyInvitation;
use common\models\CompanyUser;
use common\models\Country;
use common\models\FaqFeedback;
use common\models\Language;
use common\models\PreparedValue;
use common\models\User;
use common\models\UserServiceActive;
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
class SiteController extends MainController
{
    const SHOW_ANNOUNCEMENT_MESSAGE_SESSION_KEY = 'show_announcement_message';

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
                            'imprint',
                            'about-us',
                            'guidelines',
                            'how-to-use',
                            'help',
                            'faq-feedback',
                            'company-info-by-vat-code',
                            'city-list',
                            'change-language',
                            'set-timezone',
                            'bug-report',
                            'error',
                            'login-for-admin',
                            'search-for-location',
                            'filter-location',
                            'disable-announcement-alert',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'login',
                            'sign-up',
                            'sign-up-validation',
                            'sign-up-successful',
                            'confirm-sign-up',
                            'sign-up-invitation',
                            'sign-up-invitation-validation',
                            'request-password-reset',
                            'password-reset-sent',
                            'reset-password',
                            'reset-password-successful',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'logout',
                            'disable-subscription-alert',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'imprint' => ['GET'],
                    'about-us' => ['GET'],
                    'guidelines' => ['GET'],
                    'how-to-use' => ['GET'],
                    'help' => ['GET'],
                    'faq-feedback' => ['POST'],
                    'company-info-by-vat-code' => ['POST'],
                    'city-list' => ['GET'],
                    'change-language' => ['GET'],
                    'set-timezone-offset' => ['POST'],
                    'login' => ['GET', 'POST'],
                    'sign-up' => ['GET', 'POST'],
                    'sign-up-validation' => ['POST'],
                    'sign-up-successful' => ['GET'],
                    'confirm-sign-up' => ['GET'],
                    'sign-up-invitation' => ['GET', 'POST'],
                    'sign-up-invitation-validation' => ['POST'],
                    'request-password-reset' => ['GET', 'POST'],
                    'password-reset-sent' => ['GET'],
                    'reset-password' => ['GET', 'POST'],
                    'reset-password-successful' => ['GET'],
                    'logout' => ['POST'],
                    'disable-subscription-alert' => ['POST'],
                    'login-for-admin' => ['GET'],
                    'search-for-location' => ['GET'],
                    'filter-location' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id === 'set-timezone-offset') {
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
     * Displays homepage
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'index';
        
        $values = PreparedValue::loadModel();
        $loadCarsSum = $values->total_cars_ready;
        $transportedLoadCarsSum = $values->total_cars_transported;
        
        return $this->render('index', [
            'loadCarsSum' => $loadCarsSum,
            'transportedLoadCarsSum' => $transportedLoadCarsSum,
        ]);
    }

    /**
     * Renders imprint page
     *
     * @return string
     */
    public function actionImprint()
    {
        return $this->render('footer/imprint');
    }

    /**
     * Renders about us page
     *
     * @return string
     */
    public function actionAboutUs()
    {
        return $this->goHome();
    }

    /**
     * Renders guidelines page
     *
     * @return string
     */
    public function actionGuidelines()
    {
        return $this->render('footer/guidelines');
    }

    /**
     * Renders how to use page
     *
     * @return string
     */
    public function actionHowToUse()
    {
        return $this->goHome();
    }

    /**
     * Renders help page
     *
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('footer/help', [
            'model' => new FaqFeedback(['scenario' => FaqFeedback::SCENARIO_CLIENT_SIDE]),
        ]);
    }

    /**
     * Registers FAQ feedback and informs admin/client about feedback
     *
     * @param string $question Question placeholder, that client is commenting
     * @return Response
     * @throws BadRequestHttpException If request is not POST
     */
    public function actionFaqFeedback($question = '')
    {
        $feedback = new FaqFeedback(['scenario' => FaqFeedback::SCENARIO_CLIENT_SIDE]);
        if (!$feedback->load(Yii::$app->request->post()) || !$feedback->create($question)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'FAQ_FEEDBACK_CANNOT_LOAD_OR_SAVE'));
            return $this->redirect(['site/help', 'lang' => Yii::$app->language]);
        }

        if ($feedback->sendEmailToAdmin() && $feedback->sendEmailToClient()) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'FAQ_FEEDBACK_SAVED_SUCCESSFULLY'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'FAQ_FEEDBACK_CANNOT_SEND_ADMIN/CLIENT_EMAIL'));
        }

        return $this->redirect(['site/help', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders user login page and logs in user to system
     *
     * @param string $params JSON encoded load search params
     * @return string|Response
     */
    public function actionLogin($params = '')
    {
        $guest = new User(['scenario' => User::SCENARIO_USER_LOGINS]);
        if (Yii::$app->request->isGet) {
            return $this->render('login', ['user' => $guest]);
        }

        $guest->load(Yii::$app->request->post());
        if (!$guest->validate()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_EMAIL_OR_PASSWORD'));
            return $this->render('login', ['user' => $guest]);
        }

        $user = User::findOne(['email' => $guest->email]);
        if (!$this->canUserLogin($user, $guest->password)) {
            return $this->render('login', ['user' => $guest]);
        }

        Yii::$app->user->login($user);
        $this->afterLogin($user);

        $redirectPageAfterLogin = Yii::$app->session->get('redirect_after_login', 'load/suggestions');
        Yii::$app->session->remove('redirect_after_login');
        if (empty($params)) {
            return $this->redirect([$redirectPageAfterLogin, 'lang' => Yii::$app->language]);
        }

        return $this->redirect([$redirectPageAfterLogin, 'lang' => Yii::$app->language, 'params' => $params]);
    }

    /**
     * Checks whether user can login to website
     *
     * @param User $user User model to which the connection is attempted
     * @param string $password Password, that guest entered in login form
     * @return boolean
     */
    private function canUserLogin($user, $password)
    {
        if (is_null($user)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_BY_EMAIL_NOT_FOUND'));
            return false;
        }

        if (!$user->validatePassword($password)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'INVALID_PASSWORD'));
            return false;
        }

        if (!is_null($user->token)) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_EMAIL_NOT_CONFIRMED'));
            return false;
        }

        if (!$user->isAllowed()) {
            if ($user->isBlocked()) {
                Yii::$app->session->setFlash('error', Yii::t('alert', 'USER_IS_BLOCKED'));
                return false;
            }

            User::removeBlockedUntil($user->id);
        }

        return true;
    }

    /**
     * Things after login
     *
     * This method is responsible for things after user is logged in:
     * - logs user action, that he logged in;
     * - updates user last login time;
     * - makes user not archived if user was archived. Also makes not archived user company to which he belongs;
     * - removes blocked time if user blocked time was expired;
     *
     * @param User $user Currently logged in user model
     */
    private function afterLogin(User $user)
    {
        Log::user(Login::ACTION, Login::PLACEHOLDER_USER_LOGGED_IN, []);

        $company = Company::findUserCompany($user->id);

        if ($user->isArchived()) {
            $user->active = User::ACTIVE;
            $user->archive = User::NOT_ARCHIVED;
            $user->visible = User::VISIBLE;
            Company::unarchive($company->id);
        }

        if (!is_null($company)) {
            if (!$company->isActive() || !$user->isActive()) {
                $user->active = User::ACTIVE;
                Company::unarchive($company->id);
            }
        }

        if ($user->expiredBlockedUntil()) {
            $user->blocked_until = User::DEFAULT_BLOCKED_UNTIL;
        }

        $user->last_login = time();
        $user->scenario = User::SCENARIO_SYSTEM_LOGINS_USER;
        $user->update();
    }

    /**
     * Logs out the current user
     *
     * @return Response the current response object
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders sign up page and signs up a new user
     *
     * @return string
     */
    public function actionSignUp()
    {
        $user = new User(['scenario' => User::SCENARIO_SIGN_UP_CLIENT]);

        if (Yii::$app->request->isPost) {
            if ($user->load(Yii::$app->request->post()) && $user->create()) {
                Log::user(Create::ACTION, Create::PLACEHOLDER_USER_SIGNED_UP, [$user], $user->id);
                return $this->redirect([
                    'site/sign-up-successful',
                    'lang' => Yii::$app->language,
                    'email' => $user->email,
                    'token' => $user->token,
                ]);
            }
            $user->scenario = User::SCENARIO_SIGN_UP_CLIENT;
            Yii::$app->session->setFlash('error', Yii::t('alert', 'SIGN_UP_CANNOT_LOAD_OR_CREATE'));
        }

        $activeVatRateNaturalCountryCode = Country::getValidVatRateCountryCode($user->vatCodeNatural);
        $activeVatRateLegalCountryCode = Country::getValidVatRateCountryCode($user->vatCodeLegal);
        $user->vatCodeNatural = empty($user->vatCodeNatural) ? $activeVatRateNaturalCountryCode : $user->vatCodeNatural;
        $user->vatCodeLegal = empty($user->vatCodeLegal) ? $activeVatRateLegalCountryCode : $user->vatCodeLegal;

        return $this->render('sign-up', [
            'user' => $user,
            'languages' => Language::getIconicNames(),
            'cityNatural' => City::getNameById($user->cityIdNatural),
            'vatRateCountries' => Country::getVatRateCountries(),
            'activeVatRateNaturalCountryCode' => $activeVatRateNaturalCountryCode,
            'activeVatRateLegalCountryCode' => $activeVatRateLegalCountryCode,
            'cityLegal' => City::getNameById($user->cityIdLegal),
            'cameFromSources' => CameFrom::getSources(),
        ]);
    }

    /**
     * Validates sign up form
     *
     * @return string The error message array indexed by the attribute IDs in JSON format
     */
    public function actionSignUpValidation()
    {
        $userAdapter = new AjaxValidationAdapter(new User(), User::SCENARIO_SIGN_UP_CLIENT);
        return $userAdapter->validate();
    }

    /**
     * Renders successful sign up page
     *
     * @param null|string $email Currently signed up user email
     * @param null|string $token Currently signed up user token
     * @return string|Response
     * @throws NotFoundHttpException If email and/or token is null or empty
     */
    public function actionSignUpSuccessful($email = null, $token = null)
    {
        if (is_null($email) || is_null($token) || empty($email) || empty($token)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SIGN_UP_SUCCESSFUL_INVALID_EMAIL_OR_TOKEN'));
        }

        $user = User::findByEmail($email);
        if (is_null($user) || !$user->isForbidden() || $user->token !== $token) {
            return $this->redirect(['site/index']);
        }

        return $this->render('sign-up-successful', [
            'email' => $user->email,
        ]);
    }

    /**
     * Confirms user sign up
     *
     * @param null|string $token User token
     * @return Response
     * @throws NotFoundHttpException If user not found by token
     * @throws ServerErrorHttpException If user sign up cannot be confirmed
     */
    public function actionConfirmSignUp($token = null)
    {
        $user = User::findByToken($token);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'CONFIRM_SIGN_UP_USER_NOT_FOUND_BY_TOKEN'));
        }

        $user->scenario = User::SCENARIO_CONFIRM_SIGN_UP;
        Yii::$app->db->beginTransaction();
        if (!$user->confirmSignUp()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CONFIRM_SIGN_UP_CANNOT_CONFIRM'));
        }

        $invitation = CompanyInvitation::findByEmail($user->email);
        if (is_null($invitation)) {
            $this->createUserCompany($user);
        } else {
            $this->acceptCompanyInvitation($user, $invitation);
        }

        Yii::$app->db->transaction->commit();
        Yii::$app->user->login($user);
        return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
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
        if (!$company->create()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CONFIRM_SIGN_UP_CANNOT_CREATE_USER_COMPANY'));
        }

        Log::user(Create::ACTION, Create::PLACEHOLDER_USER_REGISTERED_COMPANY, [$company], $user->id);
    }

    /**
     * User accepts company invitation
     *
     * @param User $user User model
     * @param CompanyInvitation $invitation Company invitation model
     * @throws ServerErrorHttpException If company invitation cannot be accepted or user cannot be assigned to company
     */
    private function acceptCompanyInvitation(User $user, CompanyInvitation $invitation)
    {
        $invitation->scenario = CompanyInvitation::SCENARIO_ACCEPT;
        if (!$invitation->accept()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CONFIRM_SIGN_UP_CANNOT_ACCEPT_INVITATION'));
        }

        if (!CompanyUser::assign($invitation->company_id, $user->id)) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'CONFIRM_SIGN_UP_CANNOT_ASSIGN_USER_TO_COMPANY'));
        }

        $company = Company::findOne($invitation->company_id);
        Log::user(Create::ACTION, Create::PLACEHOLDER_USER_JOINS_TO_COMPANY, [$company], $user->id);
    }

    /**
     * Renders sign up by invitation page
     *
     * @param null|string $token Invitation token
     * @return string
     * @throws NotFoundHttpException If token is not set or invalid, or invitation is not found
     */
    public function actionSignUpInvitation($token = null)
    {
        if (is_null($token) || empty($token)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SIGN_UP_INVITATION_INVALID_TOKEN'));
        }

        $invitation = CompanyInvitation::findByToken($token);
        if (is_null($invitation)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SIGN_UP_INVITATION_NOT_FOUND_BY_TOKEN'));
        }

        $user = new User(['scenario' => User::SCENARIO_SIGN_UP_INVITATION_CLIENT]);
        if (Yii::$app->request->isPost) {
            if ($user->load(Yii::$app->request->post()) && $user->createByInvitation($invitation->email)) {
                Log::user(Create::ACTION, Create::PLACEHOLDER_USER_SIGNED_UP, [$user], $user->id);
                return $this->redirect([
                    'site/sign-up-successful',
                    'lang' => Yii::$app->language,
                    'email' => $user->email,
                    'token' => $user->token,
                ]);
            }
            $user->scenario = User::SCENARIO_SIGN_UP_INVITATION_CLIENT;
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'SIGN_UP_INVITATION_CANNOT_SAVE'));
        }

        return $this->render('sign-up-invitation', [
            'user' => $user,
            'email' => $invitation->email,
            'token' => $invitation->token,
            'languages' => Language::getIconicNames(),
        ]);
    }

    /**
     * Validates sign up by invitation form
     *
     * @return string
     */
    public function actionSignUpInvitationValidation()
    {
        $userAdapter = new AjaxValidationAdapter(new User(), User::SCENARIO_SIGN_UP_INVITATION_CLIENT);
        return $userAdapter->validate();
    }

    /**
     * Returns information about user/company by VAT code from POST
     *
     * @return string
     * @throws BadRequestHttpException If request is not AJAX or not POST or POST has invalid parameters
     */
    public function actionCompanyInfoByVatCode()
    {
        $user = new User(['scenario' => User::SCENARIO_SIGN_UP_CLIENT]);
        $post = Yii::$app->request->post();
        $user->vatCodeLegal = isset($post['vatCode']) ? $post['vatCode'] : '';
        if (!User::isVatCodeLengthValid($user->vatCodeLegal) || !$user->validate(['vatCodeLegal'])) {
            return json_encode(['address' => '', 'companyName' => '', 'valid' => false]);
        }

        list($code, $number) = User::splitVatCode($user->vatCodeLegal, User::VAT_CODE_MIN_LENGTH);
        $response = User::getInfoFromECByVatCode($code, $number);
        return json_encode([
            'address' => $response['address'],
            'companyName' => $response['name'],
            'valid' => $response['valid'],
        ]);
    }

    /**
     * Searches for city/cities by user entered phrase
     *
     * @param string $searchableCity Phrase that user entered to city search input
     * @param boolean $load Attribute whether searchable city is for load
     * @param boolean $unload Attribute whether searchable city is for unload city
     * @param boolean $filter Attribute whether searchable city is for my loads filter
     * @param null|string $token Unique string to identify announced load
     * @return string List of user searchable cities in JSON format. NOTE: return structure must be exactly the same
     * as provided. If 'items' is changed, then also it must be changed in view file.
     * @throws NotAcceptableHttpException If searchable city is shorter than required
     */
    public function actionCityList($searchableCity = '', $load = false, $unload = false, $filter = false, $token = null)
    {
        if (strlen($searchableCity) < ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'ELASTIC_SEARCH_TEXT_TOO_SHORT', [
                'length' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
            ]));
        }

        if ($filter) {
            if (Yii::$app->user->isGuest && is_null($token)) {
                return json_encode(['items' => []]);
            }
            return json_encode(['items' => ElasticSearch::filterMyLoads($searchableCity, $token)]);
        }

        if ($load || $unload) {
            return json_encode(['items' => Cities::getLoadCitiesSuggestions($searchableCity, $unload)]);
        }

        return json_encode(['items' => Cities::getSimpleSearchCities($searchableCity)]);
    }

    /**
     * Renders password reset request page and requests password reset
     *
     * @return string
     * @throws NotAcceptableHttpException If user provided email is not valid
     * @throws NotFoundHttpException If user not found by provided email
     * @throws ServerErrorHttpException If user password reset token cannot be saved or email cannot be sent
     */
    public function actionRequestPasswordReset()
    {
        $user = new User(['scenario' => User::SCENARIO_USER_REQUESTS_PASSWORD_RESET]);
        if (!Yii::$app->request->isPost) {
            return $this->render('request-password-reset', compact('user'));
        }

        $user->load(Yii::$app->request->post());
        if (!$user->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'REQUEST_PASSWORD_RESET_INVALID_USER_DATA'));
        }

        $emailTypedByUser = $user->email;
        $user = User::findOne(['email' => $user->email]);
        if (is_null($user)) {
            return $this->redirect([
                'site/password-reset-sent',
                'lang' => Yii::$app->language,
                'email' => $emailTypedByUser,
            ]);
        }

        $user->scenario = User::SCENARIO_SERVER_PROCESS_USER_PASSWORD_RESET_REQUEST;
        $user->generatePasswordResetToken();

        Yii::$app->db->beginTransaction();
        if (!$user->save()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'REQUEST_PASSWORD_RESET_CANNOT_SAVE_PASSWORD_RESET_TOKEN'));
        }

        if (!$user->sendPasswordReset()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'REQUEST_PASSWORD_RESET_CANNOT_SEND_EMAIL'));
        }

        Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_REQUESTED_PASSWORD_RESET, [], $user->id);
        Yii::$app->db->transaction->commit();
        return $this->redirect([
            'site/password-reset-sent',
            'lang' => Yii::$app->language,
            'email' => $user->email,
        ]);
    }

    /**
     * Renders password reset sent page
     *
     * @param string $email Email which got password reset link
     * @return string
     * @throws NotFoundHttpException If email is empty
     */
    public function actionPasswordResetSent($email = '')
    {
        if (empty($email)) {
            throw new NotFoundHttpException(Yii::t('alert', 'PASSWORD_RESET_SENT_INVALID_EMAIL'));
        }

        return $this->render('password-reset-sent', [
            'email' => $email,
        ]);
    }

    /**
     * Renders reset password page
     *
     * @param string $token Password reset token generated when user requested on password reset
     * @return string
     * @throws NotFoundHttpException If token is empty or invalid
     */
    public function actionResetPassword($token = '')
    {
        if (empty($token)) {
            throw new NotFoundHttpException(Yii::t('alert', 'RESET_PASSWORD_TOKEN_IS_EMPTY'));
        }

        $user = User::findByPasswordResetToken($token);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'RESET_PASSWORD_INVALID_TOKEN'));
        }

        $user->scenario = User::SCENARIO_RESET_PASSWORD_CLIENT;
        if (Yii::$app->request->isPost) {
            if ($user->load(Yii::$app->request->post()) && $user->resetPassword()) {
                return $this->redirect(['site/reset-password-successful', 'lang' => Yii::$app->language]);
            }
            Yii::$app->session->setFlash('error', Yii::t('alert', 'RESET_PASSWORD_CANNOT_LOAD_OR_RESET_PASSWORD'));
        }

        return $this->render('reset-password', [
            'user' => $user,
        ]);
    }

    /**
     * Renders reset password successful page
     *
     * @return string
     */
    public function actionResetPasswordSuccessful()
    {
        return $this->render('reset-password-successful');
    }

    /**
     * Changes website language
     *
     * @param string $language Language name in short version
     * @return Response
     */
    public function actionChangeLanguage($language = Languages::SHORT_ENGLISH_NAME)
    {
        Languages::setLanguage($language);
        $cookie = new Cookie([
            'name' => 'currentLanguage',
            'value' => Yii::$app->language,
        ]);
        Yii::$app->response->cookies->add($cookie);

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Closes subscription alert
     */
    public function actionDisableSubscriptionAlert()
    {
        return UserServiceActive::hideSubscriptionAlert();
    }

    /**
     * Closes announcement message alert
     */
    public function actionDisableAnnouncementAlert()
    {
        return Yii::$app->session->set(self::SHOW_ANNOUNCEMENT_MESSAGE_SESSION_KEY, false);
    }

    /**
     * Saves user timezone name to session
     *
     * @return null
     */
    public function actionSetTimezone()
    {
        $name = Yii::$app->request->post('name');
        Yii::$app->session->set('timezone', $name);
        return null;
    }

    /**
     * Registers a bug from a client and sends it to admin
     *
     * @return response
     */
    public function actionBugReport()
    {
        $bugReport = new FaqFeedback(['scenario' => FaqFeedback::SCENARIO_CLIENT_SIDE]);
        $bugReport->load(Yii::$app->request->post());

        if ($bugReport->sendToAdmin()) {
            Yii::$app->session->setFlash('success', Yii::t('alert', 'REPORT_A_BUG_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'REPORT_A_BUG_ERROR'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Logins administrator to user account
     *
     * @param null|string $token Random string to identify administrator connection to user account
     * @return Response
     * @throws NotAcceptableHttpException If token is empty
     * @throws NotFoundHttpException If administrator connection cannot be determined or user model not found
     */
    public function actionLoginForAdmin($token = null)
    {
        if (empty($token)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'INVALID_TOKEN'));
        }

        $adminAsUser = AdminAsUser::findOne(compact('token'));
        if (is_null($adminAsUser)) {
            throw new NotFoundHttpException(Yii::t('alert', 'INVALID_ADMIN_AS_USER_CONNECTION'));
        }

        $user = User::findOne($adminAsUser->user_id);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('alert', 'USER_NOT_FOUND'));
        }

        Yii::$app->user->logout();
        Yii::$app->user->login($user);
        $adminAsUser->removeToken();
        return $this->redirect(['site/index', 'lang' => Yii::$app->language]);
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

    /**
     * Filters my announcement locations
     *
     * @param string $phrase Searchable location phrase
     * @param null $token Load token to identify user
     * @return string
     */
    public function actionFilterLocation($phrase = '', $token = null)
    {
        if (strlen($phrase) < ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH) {
            return json_encode(['items' => []]);
        }

        if (Yii::$app->user->isGuest && is_null($token)) {
            return json_encode(['items' => []]);
        }

        return json_encode(['items' => ElasticSearch::filterMyLoads($phrase, $token)]);
    }
}
