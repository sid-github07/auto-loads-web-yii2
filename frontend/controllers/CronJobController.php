<?php

namespace frontend\controllers;

use common\components\MailLanguage;
use common\models\Company;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserLanguage;
use common\models\UserServiceActive;
use Yii;
use common\models\LoadCar;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;

/**
 * Class CronJobController
 *
 * This controller is responsible for actions with cron-jobs.
 *
 * @package frontend\controllers
 */
class CronJobController extends Controller
{
    const UPDATE_USER_CREDITS_TOKEN = 'CHSLgA5ZrhrMMxh7W3Xz';

    const UNPAID_BILLS_REMINDER_TOKEN = 'Vx0io75cVUMyNc8dfVJk';
    const UNPAID_BILLS_REMINDER_DAYS = 2;
    const COMPANIES_ARCHIVATION_TOKEN = 'TLwY6oIpQBJKSvgHgrCp';
    const USERS_ARCHIVATION_TOKEN = 'UifS8oDIWcBek1chSiTR';
    const RECALCULATE_LOADS_TOTALS_TOKEN = 'Ct61gA9gcKKh8k85gtBx';
    const USERS_ARCHIVATION_DAYS = 120;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!$this->validateToken($action->id)) {
            throw new NotAcceptableHttpException('Invalid action token');
        }

        return parent::beforeAction($action);
    }

    /**
     * Validates action token
     *
     * @param string $actionId Current action ID
     * @return boolean
     */
    private function validateToken($actionId)
    {
        $token = Yii::$app->request->get('token');
        $comparativeToken = $this->identifyComparativeToken($actionId);

        return $token === $comparativeToken;
    }

    /**
     * Identifies which action token must be used in token validation
     *
     * @param string $actionId Current action ID
     * @return string
     * @throws NotAcceptableHttpException If current action ID is invalid
     */
    private function identifyComparativeToken($actionId)
    {
        switch ($actionId) {
            case 'update-users-credits':
                return self::UPDATE_USER_CREDITS_TOKEN;
            case 'unpaid-bills-reminder':
                return self::UNPAID_BILLS_REMINDER_TOKEN;
            case 'companies-archivation':
                return self::COMPANIES_ARCHIVATION_TOKEN;
            case 'users-archivation':
                return self::USERS_ARCHIVATION_TOKEN;
            case 'recalculate-loads-totals':
                return self::RECALCULATE_LOADS_TOTALS_TOKEN;
            default:
                throw new NotAcceptableHttpException('Invalid action ID');
        }
    }

    /**
     * Updates user credits
     */
    public function actionUpdateUsersCredits()
    {
        $this->removeCurrentCredits();
        $this->updateCurrentCredits();
    }

    /**
     * Removes all users current credits
     */
    private function removeCurrentCredits()
    {
        User::updateAll(['current_credits' => User::DEFAULT_CURRENT_CREDITS]);
    }

    /**
     * Updates all users current credits, that have active user services
     */
    private function updateCurrentCredits()
    {
        $activeUserServices = UserServiceActive::find()->all();
        Yii::$app->db->beginTransaction();

        /** @var UserServiceActive $activeUserService */
        foreach ($activeUserServices as $activeUserService) {
            $credits = $activeUserService->service->credits;
            if (isset($activeUserService->service) && $activeUserService->service->service_type_id === ServiceType::SERVICE_CREDITS_TYPE_ID) {
                $activeUserService->user->updateServiceCredits($credits);
            } else {
                $activeUserService->user->updateCurrentCredits($credits);
            }
        }

        Yii::$app->db->transaction->commit();
    }

    /**
     * Reminds users about unpaid bills
     */
    public function actionUnpaidBillsReminder()
    {
        list($startTime, $endTime) = $this->getUnpaidBillsReminderDateRange();
        $preInvoices = UserInvoice::find()
            ->where(['type' => UserInvoice::PRE_INVOICE])
            ->andWhere(['between', 'created_at', $startTime, $endTime])
            ->all();

        /** @var UserInvoice $preInvoice */
        foreach ($preInvoices as $preInvoice) {
            if ($preInvoice->hasInvoice()) {
                continue;
            }

            $this->sendUnpaidBill($preInvoice);
        }
    }

    /**
     * Returns date range for unpaid bills
     *
     * @return array
     */
    private function getUnpaidBillsReminderDateRange()
    {
        $startTime = strtotime(date('Y-m-d') . ' -' . self::UNPAID_BILLS_REMINDER_DAYS . ' days'); // 00:00
        $seconds = 86399; // 23:59
        $endTime = $startTime + $seconds;
        return [$startTime, $endTime];
    }

    /**
     * Sends unpaid bill to user email
     *
     * @param UserInvoice $userInvoice Unpaid user invoice model
     * @return boolean Whether mail was sent successfully
     */
    private function sendUnpaidBill(UserInvoice $userInvoice)
    {
        $filePath = Yii::$app->params['preInvoicePath'] . $userInvoice->getFullName();
        $companyName = Yii::$app->params['companyName'];
		
		$userId = User::findByEmail($userInvoice->buyer->email);
                
        $userLanguageIds = UserLanguage::getUserLanguages($userId->id);
        
        MailLanguage::setMailLanguage($userLanguageIds);

        return Yii::$app->mailer->compose('subscription/unpaid-bill-reminder', compact('companyName'))
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['companyName']])
            ->setTo($userInvoice->buyer->email)
            ->setSubject(Yii::t('mail', 'INFORM_USER_ABOUT_UNPAID_SUBSCRIPTION_SUBJECT', compact('companyName')))
            ->attach($filePath)
            ->send();
    }

    /**
     * Archives companies, that all users are archived
     */
    public function actionCompaniesArchivation()
    {
        $archivedCompaniesIds = $this->collectArchivableCompaniesIds();
        Company::archive($archivedCompaniesIds);

        // Make all archived companies owners as suppliers
        $usersIds = User::find()
            ->select(User::tableName() . '.id')
            ->joinWith('companies')
            ->where([Company::tableName() . '.id' => $archivedCompaniesIds])
            ->column();
        User::updateAll(['class' => User::SUPPLIER], ['id' => $usersIds, 'class' => User::CARRIER]);
    }

    /**
     * Collects all companies IDs that need to be archived
     *
     * @return array
     */
    private function collectArchivableCompaniesIds()
    {
        $companies = Company::find()->where(['archive' => Company::NOT_ARCHIVED])->all();
        $archivedCompaniesIds = [];

        foreach ($companies as $company) {
            $isOwnerArchived = $company->ownerList->isArchived();
            $areCompanyUsersArchived = empty($company->companyUsers);

            if ($isOwnerArchived && $areCompanyUsersArchived) {
                array_push($archivedCompaniesIds, $company->id); // Company has only owner which is archived
                continue;
            }

            $areCompanyUsersArchived = true;
            foreach ($company->companyUsers as $companyUser) {
                if (!$companyUser->user->isArchived()) {
                    $areCompanyUsersArchived = false;
                }
            }

            if ($isOwnerArchived && $areCompanyUsersArchived) {
                array_push($archivedCompaniesIds, $company->id); // All company users and company owner are archived
            }
        }

        return $archivedCompaniesIds;
    }

    /**
     * Archives users, who were not logged in for 120 days
     */
    public function actionUsersArchivation()
    {
        $limit = strtotime("-" . self::USERS_ARCHIVATION_DAYS . " days");
        User::updateAll([
            'active' => User::INACTIVE,
            'archive' => User::ARCHIVED,
            'visible' => User::INVISIBLE,
        ], 'archive = ' . User::NOT_ARCHIVED . ' AND last_login <= ' . $limit);
    }
    
    /**
     * Recalculates ready and transported load car totals
     */
    public function actionRecalculateLoadsTotals()
    {
        LoadCar::recalculateTotals();
    }
}