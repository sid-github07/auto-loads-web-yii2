<?php

namespace frontend\controllers;

use common\components\AjaxValidationAdapter;
use common\components\audit\Create;
use common\components\audit\Log;
use common\components\audit\SystemMessage;
use common\components\audit\Update;
use common\components\document\DocumentFactory;
use common\components\document\DocumentI;
use common\components\MainController;
use common\components\Model;
use common\models\City;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\CompanyInvitation;
use common\models\Country;
use common\models\Language;
use common\models\User;
use common\models\UserLanguage;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class SettingsController
 * @package frontend\controllers
 */
class SettingsController extends MainController
{
    /** @const string Edit my data tab */
    const TAB_EDIT_MY_DATA = 'edit-my-data';

    /** @const string Edit company data tab */
    const TAB_EDIT_COMPANY_DATA = 'edit-company-data';

    /** @const string Change password tab */
    const TAB_CHANGE_PASSWORD = 'change-password';

    /** @const string Contact info sub tab */
    const SUB_TAB_CONTACT_INFO = 'contact-info';

    /** @const string Documents sub tab */
    const SUB_TAB_DOCUMENTS = 'documents';

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
                            'edit-my-data-validation',
                            'edit-my-data',
                            'request-email-change',
                            'contact-info',
                            'contact-info-validation',
                            'request-vat-code-change',
                            'add-document',
//                            'remove-document',
                            'change-password',
                            'download-document',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'invitation',
                            'invitation-successful',
                            'invitation-validation',
                            'send-invitation',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $company = Company::getCompany();
                            return $company->isOwner();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'edit-my-data-validation' => ['POST'],
                    'edit-my-data' => ['POST'],
                    'request-email-change' => ['POST'],
                    'contact-info' => ['POST'],
                    'contact-info-validation' => ['POST'],
                    'request-vat-code-change' => ['POST'],
                    'add-document' => ['POST'],
//                    'remove-document' => ['POST'],
                    'change-password' => ['POST'],
                    'download-document' => ['GET'],
                    'invitation' => ['GET'],
                    'invitation-successful' => ['GET'],
                    'invitation-validation' => ['POST'],
                    'send-invitation' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Renders index page
     *
     * @param string $tab Currently active tab name
     * @param string $subTab Currently active sub tab name
     * @return string
     */
    public function actionIndex($tab = self::TAB_EDIT_MY_DATA, $subTab = self::SUB_TAB_CONTACT_INFO)
    {
        $user = User::findById(Yii::$app->user->id);
        $company = Company::getCompany();
        return $this->render('index', [
            'user' => $user,
            'company' => $company,
            'companyDocument' => new CompanyDocument(),
            'tab' => $tab,
            'subTab' => $subTab,
            'languages' => Language::getIconicNames(),
            'vatRateCountries' => Country::getVatRateCountries(),
            'activeVatRate' => Country::getValidVatRateCountryCode($company->vat_code),
            'city' => City::getNameById($company->city_id),
        ]);
    }

    /**
     * Validates edit my data form
     *
     * @return string The error message array indexed by the attribute IDs in JSON format
     */
    public function actionEditMyDataValidation()
    {
        $userAdapter = new AjaxValidationAdapter(new User(), User::SCENARIO_EDIT_MY_DATA_CLIENT);
        return $userAdapter->validate();
    }

    /**
     * Updates user personal information
     *
     * @return Response
     * @throws NotAcceptableHttpException If POST data cannot be loaded to user model
     * @throws ServerErrorHttpException If user information or user languages cannot be updated
     */
    public function actionEditMyData()
    {
        $user = User::findById(Yii::$app->user->id);
        $oldLanguages = $user->language;
        $user->scenario = User::SCENARIO_EDIT_MY_DATA_CLIENT;

        if (!$user->load(Yii::$app->request->post())) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'EDIT_MY_DATA_CANNOT_UPDATE'));
        }

        $user->scenario = User::SCENARIO_EDIT_MY_DATA_SERVER;
        Yii::$app->db->beginTransaction();

        if (Model::hasChanges($user)) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_PROFILE_INFO, [$user]);
        }

        if (UserLanguage::hasChanges($oldLanguages, $user->language)) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_LANGUAGES, []);
        }

        if (!$user->save()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'EDIT_MY_DATA_CANNOT_UPDATE'));
        }

        if (!UserLanguage::updateUserLanguages($user->id, $user->language)) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'EDIT_MY_DATA_CANNOT_UPDATE'));
        }

        Yii::$app->db->transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'EDIT_MY_DATA_UPDATED_SUCCESSFULLY'));
        return $this->redirect([
            'settings/index',
            'lang' => Yii::$app->language,
        ]);
    }

    /**
     * Requests administrator for user email change
     *
     * @return Response
     * @throws BadRequestHttpException If request is not POST
     */
    public function actionRequestEmailChange()
    {
        $user = User::findById(Yii::$app->user->id);
        $user->scenario = User::SCENARIO_CHANGE_EMAIL;
        if ($user->load(Yii::$app->request->post()) && $user->requestEmailChange()) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_REQUESTED_EMAIL_CHANGE, []);
            Yii::$app->session->setFlash('success', Yii::t('alert', 'REQUEST_EMAIL_CHANGE_SENT_SUCCESSFULLY'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'REQUEST_EMAIL_CHANGE_CANNOT_SEND_EMAIL'));
        }
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language]);
    }

    /**
     * Updates company information
     *
     * @return Response
     * @throws NotAcceptableHttpException If company model scenario is invalid or POST data cannot be loaded to model
     * @throws ServerErrorHttpException If company data cannot be saved
     */
    public function actionContactInfo()
    {
        $company = Company::findUserCompany(Yii::$app->user->id);
        $company->setScenarioByAccountType();

        if (!$company->load(Yii::$app->request->post())) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'COMPANY_INFO_CANNOT_UPDATE'));
        }

        Yii::$app->db->beginTransaction();
        if (Model::hasChanges($company)) {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_COMPANY_INFO, [$company]);
        }

        if (!$company->save()) {
            Yii::$app->db->transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'COMPANY_INFO_CANNOT_UPDATE'));
        }

        Yii::$app->db->transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'COMPANY_INFO_UPDATED_SUCCESSFULLY'));
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language, 'tab' => self::TAB_EDIT_COMPANY_DATA]);
    }

    /**
     * Validates contact info form depending on company owners account type
     *
     * @return string
     * @throws NotAcceptableHttpException If company owners account type is invalid
     */
    public function actionContactInfoValidation()
    {
        $company = Company::getCompany();
        $scenario = $company->getScenarioByOwnersAccountType();

        if (is_null($scenario)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'CONTACT_INFO_VALIDATION_INVALID_ACCOUNT_TYPE'));
        }

        $companyAdapter = new AjaxValidationAdapter($company, $scenario);
        return $companyAdapter->validate();
    }

    /**
     * Requests administrator for company VAT code change
     *
     * @return Response
     */
    public function actionRequestVatCodeChange()
    {
        $company = new Company();
        $company->scenario = Company::SCENARIO_CHANGE_VAT_CODE;
        if ($company->load(Yii::$app->request->post()) && $company->requestVatCodeChange()) {
            Log::user(SystemMessage::ACTION, SystemMessage::PLACEHOLDER_USER_REQUESTED_VAT_CODE_CHANGE, []);
            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'REQUEST_VAT_CODE_CHANGE_SENT_SUCCESSFULLY'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'REQUEST_VAT_CODE_CHANGE_CANNOT_SEND_EMAIL'));
        }
        return $this->redirect(['settings/index', 'lang' => Yii::$app->language, 'tab' => self::TAB_EDIT_COMPANY_DATA]);
    }

    /**
     * Adds company documents
     *
     * @param null|string $type Document type
     * @param string $date Document date of expiry
     * @return string
     */
    public function actionAddDocument($type = null, $date = '')
    {
        $document = DocumentFactory::create($type);
        if (is_null($document)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_INVALID_TYPE'));
            exit;
        }

        /** @var CompanyDocument|string $result */
        $result = $document->upload($date);
        if (is_string($result)) {
            Yii::$app->getSession()->setFlash('error', $result); // NOTE: document could not be saved to catalog
            return Json::encode($this->renderPartial('document/___' . strtolower($type), [
                'company' => $document->getCompany(),
                'companyDocument' => new CompanyDocument(),
            ]));
        }

        if ($result->hasErrors()) {
            return Json::encode($this->renderPartial('document/___' . strtolower($type), [
                'company' => $document->getCompany(),
                'companyDocument' => $result,
            ]));
        }

        $document->addWatermark($result);
        $deletedRows = CompanyDocument::deleteByType($document->getCompany()->id, $type);
        if (CompanyDocument::create($document->getCompany()->id, $date, $type, $document->getExtension($result))) {
            $document->setCompany(); // NOTE: refreshes company and company documents
            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'ADD_DOCUMENT_CREATED_SUCCESSFULLY'));
            $this->logUserCompanyDocument($document, $type, $deletedRows);
        } else {
            $document->remove();
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'ADD_DOCUMENT_CANNOT_CREATE'));
        }

        return Json::encode($this->renderPartial('document/___' . strtolower($type), [
            'company' => $document->getCompany(),
            'companyDocument' => new CompanyDocument(),
        ]));
    }

    /**
     * Logs user company document
     *
     * @param DocumentI $document Current document
     * @param string $type Document type
     * @param integer $deletedRows Number of deleted document rows
     */
    private function logUserCompanyDocument(DocumentI $document, $type, $deletedRows)
    {
        $company = new CompanyDocument([
            'company_id' => $document->getCompany()->id,
            'type' => $type,
        ]);

        if ($this->isNewDocument($deletedRows)) {
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_UPLOADED_DOCUMENT, [$company]);
        } else {
            Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_COMPANY_DOCUMENT, [$company]);
        }
    }

    /**
     * Checks whether this company document is newly created or updating already existing
     *
     * @param integer $deletedRows Number of deleted document rows
     * @return boolean
     */
    private function isNewDocument($deletedRows)
    {
        return empty($deletedRows);
    }

    /**
     * Downloads company document by given document type
     *
     * @param null|string $type Document type
     * @return string
     */
    public function actionDownloadDocument($type = null, $companyId = null)
    {
        $document = DocumentFactory::create($type, companyId);
        if (is_null($document)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'DOWNLOAD_DOCUMENT_INVALID_TYPE'));
            exit;
        }

        /** @var CompanyDocument $companyDocument */
        if (is_null($companyId)) {
            $companyDocument = CompanyDocument::findCurrentUserByType($type); 
        }
        if (!is_null($companyId)) {
            $companyDocument = CompanyDocument::findCurrentCompanyByType($type, $document->getCompany()->id); 
        }
        $fullPath = $document->getFullPath($companyDocument);
        if (!file_exists($fullPath)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'DOWNLOAD_DOCUMENT_FILE_NOT_EXISTS'));
            return Json::encode($this->renderPartial('document/___' . strtolower($type), [
                'company' => $document->getCompany(),
                'companyDocument' => new CompanyDocument(),
            ]));
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: ' . CompanyDocument::DOCUMENT_MIME_TYPES);
        header('Content-Disposition: inline; filename="' . $document->getName() . '.' . $companyDocument->extension . '"');
        readfile($fullPath);
        exit;
    }

    /**
     * Removes company document
     *
     * @param null|string $type Document type
     * @return string
     */
//    public function actionRemoveDocument($type = null)
//    {
//        $document = DocumentFactory::create($type);
//        if (is_null($document)) {
//            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'REMOVE_DOCUMENT_INVALID_TYPE'));
//            exit;
//        }
//
//        if ($document->remove()) {
//            CompanyDocument::deleteByType($document->getCompany()->id, $type);
//            $document->setCompany(); // NOTE: refreshes company and company documents
//            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'REMOVE_DOCUMENT_REMOVED_SUCCESSFULLY'));
//        } else {
//            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'REMOVE_DOCUMENT_CANNOT_REMOVE'));
//        }
//
//        return Json::encode($this->renderPartial('document/___' . strtolower($type), [
//            'company' => $document->getCompany(),
//            'companyDocument' => new CompanyDocument(),
//        ]));
//    }

    /**
     * Changes current user password
     *
     * @return Response
     */
    public function actionChangePassword()
    {
        $user = User::findById(Yii::$app->getUser()->getId());
        $user->scenario = User::SCENARIO_CHANGE_PASSWORD_CLIENT;

        if ($user->load(Yii::$app->request->post()) && $user->validatePassword($user->currentPassword)) {
            if ($user->changePassword()) {
                Log::user(Update::ACTION, Update::PLACEHOLDER_USER_UPDATED_PASSWORD, []);
                Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'CHANGE_PASSWORD_SAVED_SUCCESSFULLY'));
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'CHANGE_PASSWORD_CANNOT_LOAD_OR_CHANGE'));
            }
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'CHANGE_PASSWORD_INVALID_CURRENT_PASSWORD'));
        }

        return $this->redirect([
            'settings/index',
            'lang' => Yii::$app->language,
            'tab' => self::TAB_CHANGE_PASSWORD,
        ]);
    }

    /**
     * Renders user invitation to company page
     *
     * @return string
     */
    public function actionInvitation()
    {
        return $this->render('invitation.php', [
            'invitation' => new CompanyInvitation(['scenario' => CompanyInvitation::SCENARIO_CLIENT]),
        ]);
    }

    /**
     * Validates user invitation to company form
     *
     * @return string The error message array indexed by the attribute IDs in JSON format
     */
    public function actionInvitationValidation()
    {
        $invitationAdapter = new AjaxValidationAdapter(new CompanyInvitation(), CompanyInvitation::SCENARIO_CLIENT);
        return $invitationAdapter->validate();
    }

    /**
     * Sends user invitation to company
     *
     * @return Response
     */
    public function actionSendInvitation()
    {
        $invitation = new CompanyInvitation(['scenario' => CompanyInvitation::SCENARIO_CLIENT]);
        $invitation->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        $invitation->deleteByEmail();
        if ($invitation->create() && $invitation->send()) {
            $transaction->commit();
            Log::user(Create::ACTION, Create::PLACEHOLDER_USER_INVITES_TO_JOIN_COMPANY, [$invitation]);
            return $this->redirect([
                'settings/invitation-successful', 
                'lang' => Yii::$app->language, 
                'email' => $invitation->email,
            ]);
        }
        Yii::$app->getSession()->setFlash('error', Yii::t('alert', 'SEND_INVITATION_CANNOT_SEND'));
        $transaction->rollBack();
        return $this->redirect(['settings/invitation', 'lang' => Yii::$app->language]);
    }

    /**
     * Renders invitation successful page
     * 
     * @param string $email Email which got invitation sent link
     * @return string
     * @throws NotFoundHttpException If email is empty
     */
    public function actionInvitationSuccessful($email = '')
    {
        if (empty($email)) {
            throw new NotFoundHttpException(Yii::t('alert', 'INVITATION_SUCCESSFUL_INVALID_EMAIL'));
        }
        
        return $this->render('invitation-successful', [
            'email' => $email,
        ]);
    }
}
