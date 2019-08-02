<?php

namespace backend\controllers\migration;

use common\components\invoice\InvoiceDirector;
use common\models\Company;
use common\models\UserInvoice;
use common\models\UserService;
use common\models\UserServiceActive;
use Yii;

class ServiceController extends MigrateController
{
    public function actionUnpaidService()
    {
        $saskaitosIsankstines = Yii::$app->db_prod->createCommand("SELECT * FROM saskaitos_isankstines LIMIT 1")->queryAll();
        foreach ($saskaitosIsankstines as $saskaita) {
            if (UserService::find()->where(['old_id' => $saskaita['id'], 'paid' => UserService::NOT_PAID])->exists()) {
                continue; // User service has been already migrated
            }

            Yii::$app->db->beginTransaction();

            // Migrate user service
            $userService = new UserService(['scenario' => UserService::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE]);

            // Old_id
            $userService->old_id = $saskaita['id'];
            $userService->validate(['old_id']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // User_id
            if (empty($saskaita['vartotojas'])) {
                $company = Company::findOne($saskaita['imone']);
                if (is_null($company)) {
                    $this->writeToCSV(UserService::tableName(), 'Išankstinės sąskaitos, kurios ID: ' . $userService->old_id . ' įmonė neperkelta', $userService->old_id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                } else {
                    $userService->user_id = $company->owner_id;
                }
            } else {
                $userService->user_id = $saskaita['vartotojas'];
            }
            $userService->validate(['user_id']);
            if ($userService->errors) {
                $this->writeToCSV(UserService::tableName(), 'Išankstinės sąskaitos, kurios ID: ' . $userService->old_id . ' neperkeltas vartotojas', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            // Service_id
            if (strpos($saskaita['turinys'], 'MEMBER') !== 0) {
                $this->writeToCSV(UserService::tableName(), 'Išankstinės sąskaitos, kurios ID: ' . $userService->old_id . ' tipas neegzistuoja naujoje sistemoje', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            $turinys = explode(',', $saskaita['turinys']);
            switch ($turinys[1]) {
                case '1':
                    $userService->service_id = 1; // Month
                    break;
                case '12':
                    $userService->service_id = 2; // Year
                    break;
                default:
                    $this->writeToCSV(UserService::tableName(), 'Išankstinės sąskaitos, kurios ID: ' . $userService->old_id . ' prenumeratos dydis neegzistuoja naujoje sistemoje', $userService->old_id);
                    Yii::$app->db->transaction->rollBack();
                    continue 2;
            }

            $userService->validate(['service_id']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Paid, paid_by, admin_id, start_date, end_date, response
            $userService->paid = UserService::NOT_PAID;
            $userService->paid_by = UserService::DEFAULT_PAID_BY;
            $userService->admin_id = UserService::DEFAULT_ADMIN_ID;
            $userService->start_date = UserService::DEFAULT_START_DATE;
            $userService->end_date = UserService::DEFAULT_END_DATE;
            $userService->response = UserService::DEFAULT_RESPONSE;
            $userService->validate(['paid', 'paid_by', 'admin_id', 'start_date', 'end_date', 'response']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Generated_by
            $userService->generated_by = $saskaita['invoice_generator'] == 'agent' ? UserCompanyController::DEFAULT_ADMIN_ID : null;
            $userService->validate(['generated_by']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Price
            $userService->price = $userService->service->price;
            $userService->validate(['price']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Created_at, updated_at
            $userService->created_at = strtotime($saskaita['data']);
            $userService->updated_at = empty($saskaita['atnaujinimo_data']) ? $userService->created_at : strtotime($saskaita['atnaujinimo_data']);
            $userService->validate(['created_at', 'updated_at']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            $userService->detachBehaviors();
            $userService->save();

            // Migrate user invoice
            $invoiceDirector = new InvoiceDirector(UserInvoice::PRE_INVOICE, $userService->id, $userService->service->name, $userService->price);
            $invoiceDirector->makeInvoice($userService->user_id, false);
            $userInvoice = $invoiceDirector->getUserInvoice();
            $userInvoice->scenario = UserInvoice::SCENARIO_USER_BUYS_SERVICE;
            $userInvoice->validate();
            if ($userInvoice->errors) {
                var_dump($userService->old_id);
                var_dump($userInvoice->errors);
                Yii::$app->db->transaction->rollBack(); // FIXME
                continue;
            }

            $userInvoice->save();

            // Migrate user invoice file
            $remoteFileUrl = "http://auto-loads.lt/lt/adminuzsakovai/download-proformainvoice?&id=" . $saskaita['id'] . "&imonesid=" . $saskaita['imone'];
            $localPath = Yii::$app->params['preInvoicePath'];
            if (!is_dir($localPath)) {
                mkdir($localPath, 0777, true);
            }

            $localFileName = Yii::$app->params['preInvoiceFileName'] . $userService->id . '.' . Yii::$app->params['preInvoiceFileExtension'];
            if (copy($remoteFileUrl, $localPath . $localFileName)) {
                Yii::$app->db->transaction->commit();
            } else {
                $this->writeToCSV(UserInvoice::tableName(), 'Išankstinės sąskaitos, kurios ID: ' . $userService->old_id . ' PDF failas negali būti perkeltas', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

        }
    }

    public function actionPaidService()
    {
        $saskaitos = Yii::$app->db_prod->createCommand("SELECT * FROM saskaitos LIMIT 1")->queryAll();
        foreach ($saskaitos as $saskaita) {
            if (UserService::find()->where(['old_id' => $saskaita['id'], 'paid' => UserService::PAID])->exists()) {
                continue; // User service has already been migrated
            }

            if (empty($saskaita['apmoketa'])) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita, kurios ID: ' . $saskaita['id'] . ' nėra apmokėta', $saskaita['id']);
                continue;
            }

            if (empty($saskaita['mokejimo_id'])) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita, kurios ID: ' . $saskaita['id'] . ' neturi mokėjimo', $saskaita['id']);
                continue;
            }

            Yii::$app->db->beginTransaction();

            // Migrate user service
            $userService = new UserService(['scenario' => UserService::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE]);

            // Old_id
            $userService->old_id = $saskaita['id'];
            $userService->validate(['old_id']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // User_id
            if (empty($saskaita['vartotojas'])) {
                $company = Company::findOne($saskaita['imone']);
                if (is_null($company)) {
                    $this->writeToCSV(UserService::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' įmonė neperkelta', $userService->old_id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                } else {
                    $userService->user_id = $company->owner_id;
                }
            } else {
                $userService->user_id = $saskaita['vartotojas'];
            }
            $userService->validate(['user_id']);
            if ($userService->errors) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' neperkeltas vartotojas', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            // Service_id
            $mokejimas = Yii::$app->db_prod->createCommand("SELECT * FROM mokejimai WHERE id = :id", [':id' => $saskaita['mokejimo_id']])->queryOne();
            if (empty($mokejimas)) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita, kurios ID: ' . $userService->old_id . ' neturi mokėjimo', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            if (empty($mokejimas['krepselis'])) {
                if (empty($saskaita['turinys'])) {
                    $this->writeToCSV(UserService::tableName(), 'Sąskaita, kurios ID: ' . $userService->old_id . ' neturi krepšelio turinio, dėl to neįmanoma nustatyti paslaugos', $userService->old_id);
                    Yii::$app->db->transaction->rollBack();
                    continue;
                } else {
                    $turinys = $saskaita['turinys'];
                }
            } else {
                $turinys = $mokejimas['krepselis'];
            }

            if (strpos($turinys, 'MEMBER') !== 0) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' tipas neegzistuoja naujoje sistemoje', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            $turinys = explode(',', $turinys);
            switch ($turinys[1]) {
                case '1':
                    $userService->service_id = 1; // Month
                    break;
                case '12':
                    $userService->service_id = 2; // Year
                    break;
                default:
                    $this->writeToCSV(UserService::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' prenumeratos dydis neegzistuoja naujoje sistemoje', $userService->old_id);
                    Yii::$app->db->transaction->rollBack();
                    continue 2;
            }

            $userService->validate(['service_id']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Paid
            $userService->paid = UserService::PAID;
            $userService->validate(['paid']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Paid_by
            switch ($mokejimas['sistema']) {
                case 'mokejimai.lt':
                    $userService->paid_by = UserService::PAYSERA;
                    break;
                case 'paypal':
                    $userService->paid_by = UserService::PAYPAL;
                    break;
                case 'manual':
                    $userService->paid_by = UserService::ADMIN;
                    break;
                default:
                    $userService->paid_by = UserService::DEFAULT_PAID_BY;
                    break;
            }
            $userService->validate(['paid_by']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Admin_id
            $userService->admin_id = UserService::DEFAULT_ADMIN_ID;
            $userService->validate(['admin_id']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Generated_by
            switch ($saskaita['invoice_generator']) {
                case 'agent':
                    $userService->generated_by = UserCompanyController::DEFAULT_ADMIN_ID;
                    break;
                case 'web':
                default:
                    $userService->generated_by = UserService::DEFAULT_GENERATED_BY;
                    break;
            }
            $userService->validate(['generated_by']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            $privilegija = Yii::$app->db_prod->createCommand("SELECT * FROM privilegijos WHERE kr_transakcijos_id = :id", [':id' => $mokejimas['id']])->queryOne();
            if (empty($privilegija)) {
                $this->writeToCSV(UserService::tableName(), 'Sąskaita, kurios ID: ' . $userService->old_id . ' neturi vartotojui suteiktos privilegijos', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            // Start_date
            $userService->start_date = strtotime($privilegija['galioja_nuo']);
            $userService->validate(['start_date']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // End_date
            $userService->end_date = strtotime($privilegija['galioja_iki']);
            $userService->validate(['end_date']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Price
            $userService->price = $userService->service->price;
            $userService->validate(['price']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Response
            $userService->response = $mokejimas['responsas'];
            $userService->validate(['response']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Created_at
            $userService->created_at = strtotime($saskaita['data']);
            $userService->validate(['created_at']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            // Updated_at
            $userService->updated_at = empty($saskaita['atnaujinimo_data']) ? strtotime($saskaita['data']) : strtotime($saskaita['atnaujinimo_data']);
            $userService->validate(['updated_at']);
            if ($userService->errors) {
                var_dump($userService->old_id);
                var_dump($userService->errors);
            }

            $userService->detachBehaviors();
            $userService->save();

            // Migrate user service active
            if (strtotime($privilegija['galioja_iki']) > time()) {
                // activate user service
                $userServiceActive = new UserServiceActive(['scenario' => UserServiceActive::SCENARIO_SYSTEM_MIGRATES_USER_SERVICE_ACTIVE]);

                // ID
                $userServiceActive->id = $privilegija['id'];
                $userServiceActive->validate(['id']);
                if ($userServiceActive->errors) {
                    var_dump($userService->old_id);
                    var_dump($userServiceActive->errors);
                }

                // User_id
                $userServiceActive->user_id = $privilegija['vartotojas'];
                $userServiceActive->validate(['user_id']);
                if ($userServiceActive->errors) {
                    $this->writeToCSV(UserServiceActive::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' privilegijos vartotojas neperkeltas');
                } else {
                    // Service_id
                    $userServiceActive->service_id = $userService->service_id;
                    $userServiceActive->validate(['service_id']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // Date_of_purchase
                    $userServiceActive->date_of_purchase = strtotime($privilegija['galioja_nuo']);
                    $userServiceActive->validate(['date_of_purchase']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // Status
                    $userServiceActive->status = $privilegija['aktyvus'] ? UserServiceActive::ACTIVE : UserServiceActive::NOT_ACTIVE;
                    $userServiceActive->validate(['status']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // End date
                    $userServiceActive->end_date = strtotime($privilegija['galioja_iki']);
                    $userServiceActive->validate(['end_date']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // Credits
                    $userServiceActive->credits = $userService->service->credits;
                    $userServiceActive->validate(['credits']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // Reminder
                    $userServiceActive->reminder = $privilegija['pranesta_apie_pabaiga'] ? UserServiceActive::REMINDER_SEND : UserServiceActive::DEFAULT_REMINDER;
                    $userServiceActive->validate(['reminder']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    // Created_at, updated_at
                    $userServiceActive->created_at = strtotime($privilegija['data']);
                    $userServiceActive->updated_at = strtotime($privilegija['data']);
                    $userServiceActive->validate(['created_at', 'updated_at']);
                    if ($userServiceActive->errors) {
                        var_dump($userService->old_id);
                        var_dump($userServiceActive->errors);
                    }

                    $userServiceActive->detachBehaviors();
                    $userServiceActive->save();
                }
            }

            // Migrate user invoice
            $invoiceDirector = new InvoiceDirector(UserInvoice::INVOICE, $userService->id, $userService->service->name, $userService->price);
            $invoiceDirector->makeInvoice($userService->user_id, false);
            $userInvoice = $invoiceDirector->getUserInvoice();
            $userInvoice->scenario = UserInvoice::SCENARIO_USER_BUYS_SERVICE;
            $userInvoice->validate();
            if ($userInvoice->errors) {
                var_dump($userService->old_id);
                var_dump($userInvoice->errors);
                Yii::$app->db->transaction->rollBack();
                continue;
            }

            $userInvoice->save();

            // Migrate user invoice file
            $remoteFileUrl = "http://auto-loads.lt/lt/adminuzsakovai/download-proformainvoice?&id=" . $saskaita['id'] . "&imonesid=" . $saskaita['imone'];
            $localPath = Yii::$app->params['invoicePath'];
            if (!is_dir($localPath)) {
                mkdir($localPath, 0777, true);
            }

            $localFileName = Yii::$app->params['invoiceFileName'] . $userService->id . '.' . Yii::$app->params['invoiceFileExtension'];
            if (copy($remoteFileUrl, $localPath . $localFileName)) {
                Yii::$app->db->transaction->commit();
            } else {
                $this->writeToCSV(UserInvoice::tableName(), 'Sąskaitos, kurios ID: ' . $userService->old_id . ' PDF failas negali būti perkeltas', $userService->old_id);
                Yii::$app->db->transaction->rollBack();
                continue;
            }
        }
    }
}
