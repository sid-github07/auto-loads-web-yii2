<?php

namespace backend\controllers\migration;

use common\components\invoice\InvoiceDirector;
use common\models\Service;
use common\models\ServiceType;
use common\models\User;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;

/**
 * Class SubscriptionController
 *
 * This controller is responsible for migrating user subscriptions
 *
 * @package backend\controllers\migration
 */
class SubscriptionController extends MigrateController
{
    const DEFAULT_ADMIN_ID = 1;

    /** @var Service[] */
    private $service;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = [])
    {
        $this->setService();
        parent::__construct($id, $module, $config);
    }

    /**
     * Finds and sets all system services
     */
    private function setService()
    {
        $this->service = Service::find()->all();
    }

    /**
     * Returns all system services
     *
     * @return Service[]
     */
    private function getService()
    {
        return $this->service;
    }

    /**
     * Migrates users unpaid services from old system to new one
     */
    public function actionUnpaidService()
    {
        $query = "SELECT * FROM saskaitos_isankstines LIMIT 1";
        $saskaitosIsankstines = Yii::$app->db_prod->createCommand($query)->queryAll();
        foreach ($saskaitosIsankstines as $saskaita) {
            if ($this->serviceHasNoUser($saskaita)) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita neturi vartotojo', $saskaita['id']);
                continue;
            }

            Yii::$app->db->beginTransaction();

            $userService = $this->migrateUnpaidService($saskaita);
            if (is_null($userService)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateUserInvoice($userService)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateUserInvoiceFile($userService, $saskaita)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            Yii::$app->db->transaction->commit();
        }
    }

    /**
     * Checks whether user service has no owner
     *
     * @param array $saskaita Information about the user service
     * @return boolean
     */
    private function serviceHasNoUser($saskaita)
    {
        return empty($saskaita['vartotojas']);
    }

    /**
     * Migrates unpaid user service from old system to new one
     *
     * @param array $saskaita Information about the user service
     * @return UserService|null
     */
    private function migrateUnpaidService($saskaita)
    {
        $userService = new UserService([
            'scenario' => UserService::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_DATA,
            'user_id' => $saskaita['vartotojas'],
            'service_id' => $this->convertServiceId($saskaita['turinys']),
            'paid' => UserService::NOT_PAID,
            'paid_by' => UserService::DEFAULT_PAID_BY,
            'admin_id' => UserService::DEFAULT_ADMIN_ID,
            'generated_by' => $this->convertGeneratedBy($saskaita['invoice_generator']),
            'start_date' => UserService::DEFAULT_START_DATE,
            'end_date' => UserService::DEFAULT_END_DATE,
            'response' => UserService::DEFAULT_RESPONSE,
            'created_at' => strtotime($saskaita['data']),
            'updated_at' => $this->convertUpdatedAt($saskaita),
        ]);

        if (is_null($userService->service_id)) {
            $this->writeToCSV(UserService::tableName(), 'Nepavyko nustatyti paslaugos', $saskaita['id']);
            return null;
        }

        $userService->setPriceFromService();

        $userService->validate();
        if ($userService->errors) {
            $this->writeToCSV(UserService::tableName(), $userService->errors, $saskaita['id']);
            return null;
        }

        $userService->detachBehaviors(); // Remove timestamp behaviors
        $userService->save(false);
        return $userService;
    }

    /**
     * Converts service ID from old system to new one
     *
     * @param string $turinys Old service name with months
     * @return integer|null
     */
    private function convertServiceId($turinys)
    {
        $oldService = explode(',', $turinys);
        if (!isset($oldService[0]) || !isset($oldService[1])) {
            return null;
        }

        $oldName = $oldService[0];
        $oldMonths = $oldService[1];

        $services = $this->getService();
        foreach ($services as $service) {
            $serviceName = $service->name;
            if (strpos($oldName, $serviceName) !== false) {
                return $service->id;
            }

            $serviceTypeName = $service->serviceType->name;
            if (strpos($oldName, $serviceTypeName) === false) {
                continue;
            }

            $currentMonths = $service->getMonthsByDays();
            if ($oldMonths == $currentMonths) {
                return $service->id;
            }
        }

        return null;
    }

    /**
     * Converts generated by value from old system to new one
     *
     * @param string $invoiceGenerator Old generated by value
     * @return integer|null
     */
    private function convertGeneratedBy($invoiceGenerator)
    {
        switch ($invoiceGenerator) {
            case 'agent':
                return self::DEFAULT_ADMIN_ID;
            case 'web':
            default:
                return null;
        }
    }

    /**
     * Converts user service update time from old system to new one
     *
     * @param array $saskaita Information about the user service
     * @return false|integer
     */
    private function convertUpdatedAt($saskaita)
    {
        if (!empty($saskaita['atnaujinimo_data'])) {
            return strtotime($saskaita['atnaujinimo_data']);
        }

        return strtotime($saskaita['data']);
    }

    /**
     * Migrates user invoice from old system to new one
     *
     * @param UserService $userService User service model
     * @return boolean|null
     */
    private function migrateUserInvoice(UserService $userService)
    {
        $type = $userService->isPaid() ? UserInvoice::INVOICE : UserInvoice::PRE_INVOICE;
        $id = $userService->id;
        $name = $userService->service->name;
        $price = $userService->service->price;

        $invoiceDirector = new InvoiceDirector($type, $id, $name, $price);
        $invoiceDirector->makeInvoice($userService->user_id, false);
        $userInvoice = $invoiceDirector->getUserInvoice();
        $userInvoice->scenario = UserInvoice::SCENARIO_USER_BUYS_SERVICE;

        $userInvoice->validate();
        if ($userInvoice->errors) {
            $this->writeToCSV(UserInvoice::tableName(), $userInvoice->errors, $userInvoice->id);
            return null;
        }

        return $userInvoice->save(false);
    }

    /**
     * Migrates user invoice file from old system to new one
     *
     * @param UserService $userService User service model
     * @param array $saskaita Information about the user service
     * @return boolean|null
     */
    private function migrateUserInvoiceFile(UserService $userService, $saskaita)
    {
        $company = $userService->user->getCompany();
        if (is_null($company)) {
            $this->writeToCSV(UserService::tableName(), 'Nepavyko rasti vartotojo įmonės', $userService->id);
            return null;
        }

        // Remote file
        $remoteFileUrl = $this->getRemoteInvoiceFileUrl($saskaita['id'], $company->id);

        // Local file
        if ($userService->isPaid()) {
            $localFileName = Yii::$app->params['invoiceFileName'] . $userService->id . '.' . Yii::$app->params['invoiceFileExtension'];
            $localPath = Yii::$app->params['invoicePath'];
        } else {
            $localFileName = Yii::$app->params['preInvoiceFileName'] . $userService->id . '.' . Yii::$app->params['preInvoiceFileExtension'];
            $localPath = Yii::$app->params['preInvoicePath'];
        }

        // Create local directory
        if (!is_dir($localPath)) {
            mkdir($localPath, 0777, true); // FIXME
        }

        return copy($remoteFileUrl, $localPath . $localFileName); // Copies remote file to local path
    }

    /**
     * Returns URL to remote user invoice file
     *
     * @param null|integer $userServiceId User service ID
     * @param null|integer $companyId Company ID
     * @return string
     */
    private function getRemoteInvoiceFileUrl($userServiceId, $companyId)
    {
        return "http://auto-loads.lt/lt/adminuzsakovai/download-proformainvoice?&id=$userServiceId&imonesid=$companyId";
    }

    /**
     * Migrates users paid services from old system to new one
     */
    public function actionPaidService()
    {
        $query = "SELECT * FROM saskaitos LIMIT 1";
        $saskaitos = Yii::$app->db_prod->createCommand($query)->queryAll();
        foreach ($saskaitos as $saskaita) {
            if ($this->serviceHasNoUser($saskaita)) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita neturi vartotojo', $saskaita['id']);
                continue;
            }

            if ($this->serviceHasNoPayment($saskaita)) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita neturi mokėjimo', $saskaita['id']);
                continue;
            }

            Yii::$app->db->beginTransaction();

            $userService = $this->migratePaidService($saskaita);
            if (is_null($userService)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->activateUserService($userService, $saskaita)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateUserInvoice($userService)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (!$this->migrateUserInvoiceFile($userService, $saskaita)) {
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            Yii::$app->db->transaction->commit();
        }
    }

    /**
     * Checks whether user service has payment
     *
     * @param array $saskaita Information about the user service
     * @return boolean
     */
    private function serviceHasNoPayment($saskaita)
    {
        return empty($saskaita['mokejimo_id']);
    }

    /**
     * Migrates paid user service from old system to new one
     *
     * @param array $saskaita Information about the user service
     * @return UserService|null
     */
    private function migratePaidService($saskaita)
    {
        $query = "SELECT * FROM mokejimai WHERE id = :id";
        $mokejimas = Yii::$app->db_prod->createCommand($query, [':id' => $saskaita['mokejimo_id']])->queryOne();

        if ($this->paymentNotConfirmed($mokejimas)) {
            $this->writeToCSV(UserService::tableName(), 'Paslaugos mokėjimas nepatvirtintas', $saskaita['id']);
            return null;
        }

        $userService = new UserService([
            'scenario' => UserService::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_DATA,
            'user_id' => $saskaita['vartotojas'],
            'service_id' => $this->convertServiceId($saskaita['turinys']),
            'paid' => $this->convertPaid($saskaita['apmoketa']),
            'paid_by' => $this->convertPaidBy($mokejimas['sistema']),
            'generated_by' => $this->convertGeneratedBy($saskaita['invoice_generator']),
            'start_date' => $this->convertStartDate($mokejimas, $saskaita),
            'response' => $mokejimas['responsas'],
            'created_at' => strtotime($saskaita['data']),
            'updated_at' => $this->convertUpdatedAt($saskaita),
        ]);

        if (is_null($userService->service_id)) {
            $this->writeToCSV(UserService::tableName(), 'Nepavyko nustatyti paslaugos', $saskaita['id']);
            return null;
        }

        if (is_null($userService->start_date)) {
            $this->writeToCSV(UserService::tableName(), 'Neteisinga prenumeratos pradžios data', $saskaita['id']);
            return null;
        }

        if ($userService->isPaidByAdmin()) {
            $userService->admin_id = self::DEFAULT_ADMIN_ID;
        }

        $userService->calculateEndDateFromService();
        $userService->setPriceFromService();

        $userService->validate();
        if ($userService->errors) {
            $this->writeToCSV(UserService::tableName(), $userService->errors, $saskaita['id']);
            return null;
        }

        $userService->detachBehaviors(); // Remove timestamp behaviors
        $userService->save(false);
        return $userService;
    }

    /**
     * Checks whether user service payment is not confirmed
     *
     * @param array $mokejimas Information about the user service payment
     * @return boolean
     */
    private function paymentNotConfirmed($mokejimas)
    {
        return empty($mokejimas['patvirtintas']);
    }

    /**
     * Converts user service paid value from old system to new one
     *
     * @param integer $paid Old user service paid value
     * @return boolean
     */
    private function convertPaid($paid)
    {
        return $paid ? UserService::PAID : UserService::NOT_PAID;
    }

    /**
     * Converts user service paid by value from old system to new one
     *
     * @param string $paidBy Old user service paid by value
     * @return integer
     */
    private function convertPaidBy($paidBy)
    {
        switch ($paidBy) {
            case 'mokejimai.lt':
                return UserService::PAYSERA;
            case 'paypal':
                return UserService::PAYPAL;
            default:
                return UserService::ADMIN;
        }
    }

    /**
     * Converts user service start date from old system to new one
     *
     * @param array $mokejimas Information about the user service payment
     * @param array $saskaita Information about the user service
     * @return false|integer|null
     */
    private function convertStartDate($mokejimas, $saskaita)
    {
        $dates = [
            $mokejimas['responso_data'],
            $saskaita['atnaujinimo_data'],
            $saskaita['data'],
        ];

        foreach ($dates as $date) {
            if (!is_null($date) && !empty($date)) {
                return strtotime($date);
            }
        }

        return UserService::DEFAULT_START_DATE;
    }

    /**
     * Migrates active user services from old system to new one
     *
     * @param UserService $userService User service model
     * @param array $saskaita Information about the user service
     * @return bool|null
     */
    private function activateUserService(UserService $userService, $saskaita)
    {
        $query = "SELECT * FROM privilegijos WHERE kr_transakcijos_id = :id";
        $privilegija = Yii::$app->db_prod->createCommand($query, [':id' => $saskaita['mokejimo_id']])->queryOne();
        if (!$privilegija) {
            return true; // User does not have privilege
        }

        if (!$this->privilegeIsValid($privilegija)) {
            return true; // User has privilege but it is expired or not valid
        }

        $userServiceActive = $this->migrateUserServiceActive($userService, $privilegija);
        if (!$userServiceActive) {
            return null;
        }

        if (is_null($userService->service) === false) {
            $service = $userService->service;
            if ($service->service_type_id === ServiceType::SERVICE_CREDITS_TYPE_ID) {
                if (!$this->migrateServiceCredits($userService, $userServiceActive)) {
                    return null;
                }
            } else {
                if (!$this->migrateUserCredits($userService, $userServiceActive)) {
                    return null;
                }
            }
        }


        return true;
    }

    /**
     * Checks whether user privilege is valid
     *
     * @param array $privilegija Information about the user privilege
     * @return boolean
     */
    private function privilegeIsValid($privilegija)
    {
        if (is_null($privilegija['galioja_nuo']) || is_null($privilegija['galioja_iki'])) {
            return false;
        }

        $startDate = strtotime($privilegija['galioja_nuo']);
        $endDate = strtotime($privilegija['galioja_iki']);

        return $startDate < time() && $endDate > time();
    }

    /**
     * Migrates active user service from old system to new one
     *
     * @param UserService $userService User service model
     * @param array $privilegija Information about the user privilege
     * @return UserServiceActive|null
     */
    private function migrateUserServiceActive(UserService $userService, $privilegija)
    {
        $userServiceActive = new UserServiceActive([
            'scenario' => UserServiceActive::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE_DATA,
            'id' => $privilegija['id'],
            'user_id' => $privilegija['vartotojas'],
            'service_id' => $userService->service->id,
            'date_of_purchase' => $this->convertDateOfPurchase($privilegija['galioja_nuo']),
            'status' => $this->convertStatus($privilegija['aktyvus']),
            'end_date' => $this->convertEndDate($privilegija['galioja_iki']),
            'credits' => $userService->service->credits,
            'reminder' => $this->convertReminder($privilegija['pranesta_apie_pabaiga']),
            'created_at' => strtotime($privilegija['data']),
            'updated_at' => strtotime($privilegija['data']),
        ]);

        if (is_null($userServiceActive->date_of_purchase)) {
            $this->writeToCSV(UserServiceActive::tableName(), 'Neteisinga paslaugos pradžios data', $privilegija['id']);
            return null;
        }

        if (is_null($userServiceActive->end_date)) {
            $this->writeToCSV(UserServiceActive::tableName(), 'Neteisinga paslaugos pabaigos data', $privilegija['id']);
        }

        $userServiceActive->validate();
        if ($userServiceActive->errors) {
            $this->writeToCSV(UserServiceActive::tableName(), $userServiceActive->errors, $privilegija['id']);
            return null;
        }

        $userServiceActive->detachBehaviors(); // Remove timestamp behaviors
        $userServiceActive->save(false);
        return $userServiceActive;
    }

    /**
     * Converts user service active date of purchase from old system to new one
     *
     * @param string $date Old user service active date of purchase value
     * @return false|integer|null
     */
    private function convertDateOfPurchase($date)
    {
        if (is_null($date) || empty($date)) {
            return null;
        }

        return strtotime($date);
    }

    /**
     * Converts user service active status from old system to new one
     *
     * @param integer $status Old user service active status value
     * @return integer
     */
    private function convertStatus($status)
    {
        return $status ? UserServiceActive::ACTIVE : UserServiceActive::NOT_ACTIVE;
    }

    /**
     * Converts user service active end date from old system to new one
     *
     * @param string $date Old user service active end date value
     * @return false|integer|null
     */
    private function convertEndDate($date)
    {
        if (is_null($date) || empty($date)) {
            return null;
        }

        return strtotime($date);
    }

    /**
     * Converts user service active reminder from old system to new one
     *
     * @param integer $reminder Old user service active reminder value
     * @return integer|null
     */
    private function convertReminder($reminder)
    {
        return $reminder ? UserServiceActive::REMINDER_SEND : UserServiceActive::DEFAULT_REMINDER;
    }

    /**
     * Migrates user credits from old system to new one
     *
     * @param UserService $userService User service model
     * @param UserServiceActive $userServiceActive User service active model
     * @return boolean|null
     */
    private function migrateUserCredits(UserService $userService, UserServiceActive $userServiceActive)
    {
        $user = User::findOne($userService->user_id);
        $user->scenario = User::SCENARIO_UPDATE_CURRENT_CREDITS;
        $user->setCurrentCredits($userServiceActive->credits);
        $user->validate();
        if ($user->errors) {
            $this->writeToCSV(UserService::tableName(), $user->errors, $userService->id);
            return null;
        }

        $user->detachBehaviors(); // Remove timestamp behaviors
        return $user->save(false);
    }

    /**
     * @param UserService $userService
     * @param UserServiceActive $userServiceActive
     * @return bool|null
     */
    private function migrateServiceCredits(UserService $userService, UserServiceActive $userServiceActive)
    {
        $user = User::findOne($userService->user_id);
        $user->scenario = User::SCENARIO_UPDATE_SERIVCE_CREDITS;
        $user->setServiceCredits($userServiceActive->credits);
        $user->validate();
        if ($user->errors) {
            $this->writeToCSV(UserService::tableName(), $user->errors, $userService->id);
            return null;
        }

        $user->detachBehaviors(); // Remove timestamp behaviors
        return $user->save(false);
    }
}