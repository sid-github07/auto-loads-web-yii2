<?php

namespace backend\controllers\migration;

use common\models\Admin;
use Yii;

/**
 * Class AdminController
 *
 * This controller is responsible for migrating administrators
 *
 * @package backend\controllers\migration
 */
class AdminController extends MigrateController
{
    use MigrationTrait;

    const KLASE_ADMINISTRATORIUS = 7;
    const DEFAULT_PASSWORD = 'Au1o-L0ads';
    const DEFAULT_NAME = 'Auto';
    const DEFAULT_SURNAME = 'Loads';
    const DEFAULT_PHONE = '+37061234567';

    /**
     * Migrates system administrators from old system to new one
     */
    public function actionAdmin()
    {
        $query = "SELECT * FROM vartotojai WHERE klase = :klase";
        $vartotojai = Yii::$app->db_prod->createCommand($query, [':klase' => self::KLASE_ADMINISTRATORIUS])->queryAll();
        foreach ($vartotojai as $vartotojas) {
            if ($this->adminExists($vartotojas['id'])) {
                continue;
            }

            $admin = new Admin([
                'scenario' => Admin::SCENARIO_SYSTEM_MIGRATES_ADMIN_DATA,
                'id' => $vartotojas['id'],
                'name' => $vartotojas['vardas'],
                'surname' => $vartotojas['pavarde'],
                'email' => $vartotojas['elpastas'],
                'phone' => $vartotojas['telefonai'],
                'password_reset_token' => Admin::DEFAULT_PASSWORD_RESET_TOKEN,
                'admin' => Admin::IS_ADMIN,
                'archived' => $this->convertArchiveStatus($vartotojas['archive_status']),
                'created_at' => strtotime($vartotojas['data']),
                'updated_at' => strtotime($vartotojas['data']),
            ]);

            $admin->generateAuthKey();

            if ($this->hadOldPassword($vartotojas['raw_password'])) {
                $admin->setPassword($vartotojas['raw_password']);
            } else {
                $admin->setPassword(self::DEFAULT_PASSWORD);
            }

            $this->fixValidationErrors($admin);
            $admin->validate();
            if ($admin->errors) {
                $this->writeToCSV(Admin::tableName(), $admin->errors, $admin->id);
                continue;
            }

            $admin->detachBehaviors(); // Remove timestamp behaviour
            $admin->save(false);
        }
    }

    /**
     * Checks whether administrator has already been migrated
     *
     * @param null|integer $id Administrator ID
     * @return boolean
     */
    private function adminExists($id)
    {
        return Admin::find()->where(compact('id'))->exists();
    }

    /**
     * Converts administrator archive status from old system to new one
     *
     * In old system administrator account could have two archive status:
     * 1 - archived
     * 0 - not archived
     *
     * @param integer $status Old system administrator archive status
     * @return integer
     */
    private function convertArchiveStatus($status)
    {
        return $status ? Admin::ARCHIVED : Admin::NOT_ARCHIVED;
    }

    /**
     * Fixes administrator model validation errors
     *
     * This method checks whether administrator name, surname and phone are valid. If not, then fixes them.
     *
     * @param Admin $admin Administrator model
     */
    private function fixValidationErrors(Admin &$admin)
    {
        if (!$admin->validate(['name', 'surname'])) {
            $this->fixFullName($admin);
        }

        if (!$admin->validate(['phone'])) {
            $this->fixPhoneNumber($admin);
        }

        return;
    }

    /**
     * Fixes administrator name and surname
     *
     * This method removes all special characters from administrator name and surname.
     * If afterwards name or surname is still invalid, then sets default administrator name and surname.
     *
     * @param Admin $admin Administrator model
     */
    private function fixFullName(Admin &$admin)
    {
        $admin->name = preg_replace('/[^a-zA-Z\p{L}\s]/u', '', $admin->name);
        $admin->surname = preg_replace('/[^a-zA-Z\p{L}\s]/u', '', $admin->surname);

        if (!$admin->validate(['name', 'surname'])) {
            $admin->name = self::DEFAULT_NAME;
            $admin->surname = self::DEFAULT_SURNAME;
        }

        return;
    }

    /**
     * Fixes administrator phone number
     *
     * @param Admin $admin Administrator model
     */
    private function fixPhoneNumber(Admin &$admin)
    {
        // TODO: implement fixPhoneNumber method.
        $admin->phone = self::DEFAULT_PHONE;
    }
}