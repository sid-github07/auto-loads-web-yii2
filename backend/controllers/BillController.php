<?php

namespace backend\controllers;

use common\models\Company;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class BillController
 *
 * @package backend\controllers
 */
class BillController extends Controller
{
    /** @var UserInvoice User invoice model */
    private $userInvoice;
    
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
                            'list',
                            'download',
                            'regenerate',
                            'mark-as-paid',
                            'planned-income',
                            'send-pre-invoice-document-to-user',
                            'export-invoices-xml',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest &&
                                Yii::$app->admin->identity->isAdmin();
                        },
                    ],
                    [
                        'actions' => [
                            'list',
                            'download',
                            'planned-income',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->admin->isGuest &&
                                Yii::$app->admin->identity->isModerator();
                        },
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
                    'list' => ['GET', 'POST'],
                    'download' => ['GET'],
                    'regenerate' => ['GET'],
                    'mark-as-paid' => ['GET'],
                    'planned-income' => ['GET', 'POST'],
                ],
            ],
        ];
    }
    
    /**
     * Renders bills list
     *
     * @return boolean $returnFromXmlExport
     * @return string
     */
    public function actionList($returnFromXmlExport = false)
    {
        $userInvoice = new UserInvoice(['scenario' => UserInvoice::SCENARIO_ADMIN_FILTERS_USER_INVOICES]);
        $userInvoice->load(Yii::$app->request->get());
        $dateRanges = $userInvoice->getFiltrationDateRanges();
        $query = $userInvoice->getBillListDataProviderQuery($dateRanges);
        $dataProvider = $userInvoice->getAdminDataProvider($query);
        $bills = $query->all();
        $paidBillsAmount = UserInvoice::calculateBillsAmount($bills, UserInvoice::INVOICE);
        $unpaidBillsAmount = UserInvoice::calculateBillsAmount($bills, UserInvoice::PRE_INVOICE);
        $userInvoice->fixPeriodDateConflict();
        
        return $this->render('list', compact(
            'userInvoice',
            'dataProvider',
            'paidBillsAmount',
            'unpaidBillsAmount',
            'dateRanges',
            'returnFromXmlExport'  
        ));
    }
    
    /**
     * Downloads user invoice
     *
     * @param null|integer $id User invoice ID
     * @param boolean $preview Whether open PDF for preview instead of download
     * @throws NotAcceptableHttpException If user invoice ID is not defined
     * @throws NotFoundHttpException If user invoice or user invoice file not found
     */
    public function actionDownload($id = null, $preview = true)
    {
        if (is_null($id)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_DOWNLOAD_ID_NOT_DEFINED'));
        }
        
        $userInvoice = UserInvoice::findOne($id);
        if (is_null($userInvoice)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_DOWNLOAD_INVOICE_NOT_FOUND'));
        }
        
        $fullPath = $userInvoice->getPath() . $userInvoice->getFullName();
        if (!file_exists($fullPath)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_DOWNLOAD_FILE_NOT_FOUND'));
        }
        
        $disposition = $preview ? 'inline' : 'attachment'; // inline - opens in tab, attachment - downloads file
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: ' . UserInvoice::DOCUMENT_MIME_TYPE);
        header('Content-Disposition: ' . $disposition . '; filename="' . $userInvoice->getFullName() . '"');
        readfile($fullPath);
        exit;
    }
    
    /**
     * Regenerates user invoice
     *
     * @param null|integer $id User invoice ID
     * @param null|integer $companyId Company ID
     * @param string $tab Company tab
     * @return Response
     * @throws NotAcceptableHttpException If user invoice data is invalid
     * @throws NotFoundHttpException If user invoice ID is not defined or user invoice not found or user company not found
     * @throws ServerErrorHttpException If user invoice failed to update
     */
    public function actionRegenerate($id = null, $companyId = null, $tab = '')
    {
        if (is_null($id)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_REGENERATE_INVALID_USER_INVOICE_ID'));
        }
        
        $userInvoice = UserInvoice::findOne($id);
        if (is_null($userInvoice)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_REGENERATE_USER_INVOICE_NOT_FOUND'));
        }
        
        $company = Company::findOne($userInvoice->buyer_id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_REGENERATE_USER_COMPANY_NOT_FOUND'));
        }
        
        $userInvoice->setInvoiceData($company, $userInvoice->number);
        $userInvoice->scenario = UserInvoice::SCENARIO_ADMIN_REGENERATES_USER_INVOICE;
        if (!$userInvoice->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_REGENERATE_INVALID_USER_INVOICE_DATA'));
        }
        
        $userService = $userInvoice->userService;
        $userService->generated_by = Yii::$app->admin->id;
        $userService->scenario = UserService::SCENARIO_ADMIN_REGENERATES_USER_INVOICE;
        if (!$userService->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_REGENERATE_INVALID_USER_SERVICE_DATA'));
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        $userService->save(false);
        $userInvoice->detachBehaviors();
        if (!$userInvoice->save(false)) {
            $transaction->rollBack();
            throw new ServerErrorHttpException(Yii::t('alert', 'BILL_REGENERATE_CANNOT_UPDATE_USER_INVOICE'));
        }
        
        $userInvoice->generateDocument();
        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('alert', 'BILL_REGENERATE_REGENERATED_SUCCESSFULLY'));
        
        if (empty($id) || empty($tab)) {
            return $this->redirect(['bill/list', 'lang' => Yii::$app->language]);
        }
        
        return $this->redirect(['client/company', 'lang' => Yii::$app->language, 'id' => $companyId, 'tab' => $tab]);
    }
    
    /**
     * Marks user pre-invoice as paid
     *
     * @param null|integer $id User pre-invoice ID
     * @param null|integer $companyId Company ID
     * @param string $tab Company tab
     * @return Response
     * @throws NotFoundHttpException If user pre-invoice is not found
     * @throws ServerErrorHttpException If email cannot be send to user informing about successful payment
     */
    public function actionMarkAsPaid($id = null, $companyId = null, $tab = '')
    {
        $userInvoice = UserInvoice::findOne($id);
        if (is_null($userInvoice)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_MARK_AS_PAID_USER_PRE_INVOICE_NOT_FOUND'));
        }
        
        $this->userInvoice = $userInvoice;
        
        Yii::$app->db->beginTransaction();
        $this->markUserServiceAsPaid();
        $this->activateUserService();
        $this->updateUserCurrentCredits();
        $this->createUserInvoice();
        Yii::$app->db->transaction->commit();
        
        if (!$this->userInvoice->userService->user->sendSuccessfulPayment()) {
            Yii::$app->session->setFlash('error', Yii::t('alert', 'BILL_MARK_AS_PAID_CANNOT_SEND_EMAIL'));
        }
        
        Yii::$app->session->setFlash('success', Yii::t('alert', 'BILL_MARK_AS_PAID_SUCCESSFULLY_MARKED_AS_PAID'));
        if (empty($companyId) || empty($tab)) {
            return $this->redirect(['bill/list', 'lang' => Yii::$app->language]);
        }
        
        return $this->redirect(['client/company', 'lang' => Yii::$app->language, 'id' => $companyId, 'tab' => $tab]);
    }
    
    /**
     * Marks user service as paid
     *
     * @return false|integer Number of updated rows, or false if [[beforeSave()]] stops the updating process
     * @throws NotAcceptableHttpException If user service data is not valid
     */
    private function markUserServiceAsPaid()
    {
        $userService = $this->userInvoice->userService;
        $userService->markAsPaidByAdmin();
        $userService->scenario = UserService::SCENARIO_SYSTEM_MARKS_AS_PAID;
        
        if (!$userService->validate()) {
            Yii::$app->db->transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_MARK_USER_SERVICE_AS_PAID_INVALID_USER_SERVICE'));
        }
        
        return $userService->update(false);
    }
    
    /**
     * Activates user service
     *
     * @throws NotAcceptableHttpException If user active service data is not valid
     */
    private function activateUserService()
    {
        $userService = $this->userInvoice->userService;
        if (!isset($userService->service)) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_MARK_USER_SERVICE_NOT_FOUND'));
        }
        if ($userService->service->service_type_id == ServiceType::MEMBER_TYPE_ID) {
            UserServiceActive::findAllActiveServicesToDelete($userService->user_id);
        };
        
        $userService->start_date = time();
        $userService->calculateServiceEndDate();

        if (is_null($userService->generated_by)) {
            $userService->paid = UserService::NOT_PAID;
            $userService->update(false);
            $startDate = UserService::findLastEndDate($userService->user_id);
            if (! is_null($startDate)) {
                $userService->start_date = $startDate->end_date;
            }
            if ($userService->start_date < time()) {
                $userService->start_date = time();
            }
            $userService->calculateServiceEndDate();
            $userService->paid = UserService::PAID;
            $userService->update(false);
        }
        if ($userService->start_date <= time() && $userService->end_date >= time())
        {
            $userServiceActive = new UserServiceActive([
                'scenario' => UserServiceActive::SCENARIO_CREATE_SERVER,
                'user_id' => $userService->user_id,
                'service_id' => $userService->service_id,
                'date_of_purchase' => $userService->start_date,
                'status' => UserServiceActive::ACTIVE,
                'end_date' => $userService->end_date,
                'credits' => $userService->service->credits,
            ]);
            if (!$userServiceActive->validate()) {
                Yii::$app->db->transaction->rollBack();
                throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_ACTIVATE_USER_SERVICE_INVALID_ACTIVE_USER_SERVICE'));
            }
            $userServiceActive->save(false);
        }
        $userService->scenario = UserService::SCENARIO_SYSTEM_SETS_END_DATE;
        if (!$userService->validate()) {
            Yii::$app->db->transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_ACTIVATE_USER_SERVICE_INVALID_USER_SERVICE_DATA'));
        }
        $userService->save(false);
        
        return null;
    }
    
    /**
     * Updates current user credits
     *
     * @return boolean
     * @throws NotAcceptableHttpException If user data is not valid
     */
    private function updateUserCurrentCredits()
    {
        $userService = $this->userInvoice->userService;
        $user = $userService->user;
        $credits = $userService->service->credits;
        if ($userService->service->isCreditsType()) {
            $userActiveServices = $user->userServiceActives;
            foreach ($userActiveServices as $userActiveService) {
                if ($userActiveService->service->isMemberType()) {
                    $credits += $user->current_credits;
                    break;
                }
            }
        }
        if ($userService->service->isCreditsServiceType()) {
            $user->updateServiceCredits($credits);
            $user->scenario = User::SCENARIO_UPDATE_SERVICE_CREDITS;
        } else {
            $user->setCurrentCredits($credits);
            $user->scenario = User::SCENARIO_UPDATE_CURRENT_CREDITS;
        }
        if (!$user->validate()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'UPDATE_USER_CREDITS_INVALID_USER_DATA'));
        }
        
        return $user->save(false);
    }
    
    /**
     * Creates user invoice entry and PDF document
     *
     * @return null
     * @throws NotAcceptableHttpException If user invoice data is not valid
     * @throws NotFoundHttpException If user service user was not found or user invoice PDF document was not generated
     */
    private function createUserInvoice()
    {
        $userService = $this->userInvoice->userService;
        $company = Company::findUserCompany($userService->user_id);
        if (is_null($company)) {
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_CREATE_USER_INVOICE_USER_COMPANY_NOT_FOUND'));
        }
        
        $userInvoice = new UserInvoice([
            'scenario' => UserInvoice::SCENARIO_ADMIN_CREATES_INVOICE,
            'user_service_id' => $userService->id,
            'type' => UserInvoice::INVOICE,
            'buyer_id' => $company->id,
        ]);
        
        $userInvoice->setInvoiceData($company);
        if (!$userInvoice->validate()) {
            Yii::$app->db->transaction->rollBack();
            throw new NotAcceptableHttpException(Yii::t('alert', 'BILL_CREATE_USER_INVOICE_INVALID_USER_INVOICE_DATA'));
        }
        
        $userInvoice->save(false);
        $userInvoice->generateDocument();
        if (!$userInvoice->isDocumentExist()) {
            Yii::$app->db->transaction->rollBack();
            throw new NotFoundHttpException(Yii::t('alert', 'BILL_CREATE_USER_INVOICE_CANNOT_GENERATE_USER_INVOICE'));
        }
        
        return null;
    }
    
    /**
     * Renders planned income page
     *
     * @return string
     */
    public function actionPlannedIncome()
    {
        $userService = new UserService(['scenario' => UserService::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES]);
        $company = new Company(null, Company::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES);
        $userInvoice = new UserInvoice(['scenario' => UserInvoice::SCENARIO_ADMIN_FILTERS_PLANNED_INCOMES]);
        
        $userService->load(Yii::$app->request->get());
        $company->load(Yii::$app->request->get());
        $userInvoice->load(Yii::$app->request->get());
        
        $dateRanges = $userService->getFiltrationDateRanges();
        $query = $userService->getPlannedIncomeQuery($company, $userInvoice, $dateRanges);
        $dataProvider = $userService->getAdminDataProvider($query);
        $userServices = $query->all();
        $plannedIncome = UserService::calculatePlannedIncome($userServices);
        $userService->fixPeriodDateConflict();
        
        return $this->render('planned-income', compact(
            'userService',
            'company',
            'userInvoice',
            'dataProvider',
            'plannedIncome',
            'dateRanges'
        ));
    }
    
    /**
     * Sends pre-invoice document to user
     *
     * @param null|integer $preInvoiceId Pre-invoice document ID
     * @param null|integer $companyId Company ID
     * @param string $tab Company tab
     * @return Response
     * @throws NotAcceptableHttpException If pre-invoice document type is not pre-invoice
     * @throws NotFoundHttpException If pre-invoice entry not found or pre-invoice document file not found
     * @throws ServerErrorHttpException If pre-invoice document cannot be sent to user
     */
    public function actionSendPreInvoiceDocumentToUser($preInvoiceId = null, $companyId = null, $tab = '')
    {
        /** @var UserInvoice $preInvoice */
        $preInvoice = UserInvoice::find()
            ->joinWith('userService')
            ->joinWith('userService.user')
            ->where([UserInvoice::tableName() . '.id' => $preInvoiceId])
            ->one();
        
        if (is_null($preInvoice)) {
            throw new NotFoundHttpException(Yii::t('alert', 'SEND_PRE_INVOICE_DOCUMENT_TO_USER_PRE_INVOICE_NOT_FOUND'));
        }
        
        if (!$preInvoice->isPreInvoice()) {
            throw new NotAcceptableHttpException(Yii::t('alert', 'SEND_PRE_INVOICE_DOCUMENT_TO_USER_NOT_PRE_INVOICE'));
        }
        
        if (!$preInvoice->isDocumentExist()) {
            throw new NotFoundHttpException(Yii::t('alert', 'SEND_PRE_INVOICE_DOCUMENT_TO_USER_DOCUMENT_NOT_EXIST'));
        }
        
        if (!$preInvoice->sendPreInvoiceDocumentToUser()) {
            throw new ServerErrorHttpException(Yii::t('alert', 'SEND_PRE_INVOICE_DOCUMENT_TO_USER_CANNOT_SEND_MAIL'));
        }
        
        Yii::$app->session->setFlash('success', Yii::t('alert', 'SEND_PRE_INVOICE_DOCUMENT_TO_USER_SEND_SUCCESSFULLY'));
        return $this->redirect(['client/company', 'lang' => Yii::$app->language, 'id' => $companyId, 'tab' => $tab]);
    }
    
    /**
     * Downloads user invoices in xml format
     *
     * @param null|integer $id User invoice ID
     * @param boolean $preview Whether open PDF for preview instead of download
     * @throws NotAcceptableHttpException If user invoice ID is not defined
     * @throws NotFoundHttpException If user invoice or user invoice file not found
     */
    public function actionExportInvoicesXml()
    {
        $userInvoice = new UserInvoice(['scenario' => UserInvoice::SCENARIO_ADMIN_FILTERS_USER_INVOICES]);
        $userInvoice->load(Yii::$app->request->get());
        $dateRanges = $userInvoice->getFiltrationDateRanges();
        $query = $userInvoice->getBillListDataProviderQuery($dateRanges);
        $dataProvider = $userInvoice->getAdminDataProvider($query);     
        
        $userInvoice->setScenario(UserInvoice::SCENARIO_ADMIN_EXPORTS_USER_INVOICES_XML);
        if (!$userInvoice->validate()) {
            Yii::$app->session->setFlash('error', 
                Yii::t('alert', 'XML_INVOICES_DATE_RANGE_REQUIRED'));
            
            return $this->redirect(array_merge([
                    'bill/list',
                    'returnFromXmlExport' => true,
                ], 
                Yii::$app->request->get()));
        }
        $userInvoice->setScenario(UserInvoice::SCENARIO_ADMIN_FILTERS_USER_INVOICES);
        
        $periodStart = date('ymd', $dateRanges['dateFrom']);
        $periodEnd = date('ymd', $dateRanges['dateTo']);
        
        $filename = "invoices_{$periodStart}_{$periodEnd}.xml";
        
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: application/xml');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $userInvoice->streamXmlData($dataProvider, $dateRanges);
        exit;
    }
}
