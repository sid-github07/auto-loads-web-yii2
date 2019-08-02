<?php

namespace backend\controllers\migration;

use common\models\Company;
use common\models\CompanyUser;
use common\models\Language;
use common\models\User;
use common\models\UserLanguage;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class UserController
 *
 * This controller is responsible for migrating user and its company data
 *
 * @package backend\controllers\migration
 */
class UserController extends CompanyController
{
    use MigrationTrait;

    const KLASE_IMONES_SAVININKAS = 3;
    const KLASE_IMONES_DARBUOTOJAS = 1;
    const DEFAULT_PASSWORD = 'Au1o-L0ads';
    const DEFAULT_ACCOUNT_TYPE = User::NATURAL;
    const DEFAULT_CITY_ID = 598316; // Kaunas
    const DEFAULT_ADDRESS = '(not set)';
    const DEFAULT_NAME = 'Auto';
    const DEFAUTL_SURNAME = 'Loads';
    const DEFAULT_PHONE = '+37061234567';
    const DEFAULT_LANGUAGE = 1; // English

    /** @var Language[] List of all available languages, that user can speak  */
    private $languages;

    /**
     * @inheritdoc
     */
    public function __construct($id, $module, $config = [])
    {
        $this->setLanguages();
        parent::__construct($id, $module, $config);
    }

    /**
     * Sets languages, that user can speak
     */
    private function setLanguages()
    {
        $this->languages = ArrayHelper::map(Language::find()->all(), 'name', 'id');
    }

    /**
     * Returns languages, that user can speak
     *
     * @return Language[]
     */
    private function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Migrates company owner with its company and everything that is related to it from old system to new one
     *
     * @return null
     */
    public function actionCompanyOwner()
    {
        $vartotojai = $this->getOldCompanyOwners();
        foreach ($vartotojai as $vartotojas) {
            if ($this->userExists($vartotojas['id'])) {
                continue;
            }

            $imone = $this->getOldUserCompany($vartotojas['id']);
            if (!$imone) {
                $this->writeToCSV(User::tableName(), 'Vartotojas neturi ryšio su įmone', $vartotojas['id']);
                continue;
            }

            if ($this->companyExists($imone['id'])) {
                $this->migrateCompanyUser($vartotojas, $imone);
            } else {
                $this->migrateCompanyWithOwner($vartotojas, $imone);
            }
        }

        return null;
    }

    /**
     * Returns list of all companies owners from old system
     *
     * @return array
     */
    private function getOldCompanyOwners()
    {
        $query = "SELECT * FROM vartotojai WHERE klase = :klase LIMIT 1";
        return Yii::$app->db_prod->createCommand($query, [':klase' => self::KLASE_IMONES_SAVININKAS])->queryAll();
    }

    /**
     * Checks whether user has been already migrated
     *
     * @param null|integer $id User ID
     * @return boolean
     */
    private function userExists($id)
    {
        return User::find()->where(compact('id'))->exists();
    }

    /**
     * Returns specific user company from old system
     *
     * @param null|integer $id User ID
     * @return array|false
     */
    private function getOldUserCompany($id)
    {
        $query = "SELECT *
                  FROM imones 
                  LEFT JOIN vartotojai_2_imones ON imones.id = vartotojai_2_imones.imones_id 
                  WHERE vartotojai_2_imones.vartotojo_id = :id";
        return Yii::$app->db_prod->createCommand($query, [':id' => $id])->queryOne();
    }

    /**
     * Checks whether company has been already migrated
     *
     * @param null|integer $id Company ID
     * @return boolean
     */
    private function companyExists($id)
    {
        return Company::find()->where(compact('id'))->exists();
    }

    /**
     * Migrates company user from old system to new one
     *
     * @param array $vartotojas Information about company user
     * @param array $imone Information about user company
     * @return null
     */
    private function migrateCompanyUser($vartotojas, $imone)
    {
        Yii::$app->db->beginTransaction();

        $user = $this->migrateWorker($vartotojas);
        if (is_null($user)) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        if (!$this->migrateLanguages($user->id, $vartotojas['kalbos'])) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        if (!$this->assignUserToCompany($user->id, $imone['id'])) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        Yii::$app->db->transaction->commit();

        return null;
    }

    /**
     * Migrates company user from old system to new one
     *
     * @param array $vartotojas Information about company user
     * @return User|null
     */
    private function migrateWorker($vartotojas)
    {
        $user = new User([
            'scenario' => User::SCENARIO_SYSTEM_MIGRATES_COMPANY_USER_DATA,
            'id' => $vartotojas['id'],
            'name' => $vartotojas['vardas'],
            'surname' => $vartotojas['pavarde'],
            'email' => str_replace(' ', '', $vartotojas['elpastas']),
            'phone' => $vartotojas['telefonai'],
            'password_hash' => $this->convertPassword($vartotojas['raw_password']),
            'password_reset_token' => User::DEFAULT_PASSWORD_RESET_TOKEN,
            'class' => User::DEFAULT_CLASS,
            'original_class' => User::DEFAULT_ORIGINAL_CLASS,
            'account_type' => User::DEFAULT_ACCOUNT_TYPE,
            'personal_code' => User::DEFAULT_PERSONAL_CODE,
            'company_code' => User::DEFAULT_COMPANY_CODE,
            'company_name' => User::DEFAULT_COMPANY_NAME,
            'city_id' => User::DEFAULT_CITY_ID,
            'address' => User::DEFAULT_ADDRESS,
            'vat_code' => User::DEFAULT_VAT_CODE,
            'came_from_id' => User::DEFAULT_CAME_FROM_ID,
            'current_credits' => User::DEFAULT_CURRENT_CREDITS,
            'active' => $this->convertActive($vartotojas['aktyvus']),
            'allow' => $this->convertAllow($vartotojas['leidziamas']),
            'archive' => $this->convertArchive($vartotojas['archive_status']),
            'visible' => $this->convertVisible($vartotojas['rodomas']),
            'last_login' => $this->convertLastLogin($vartotojas['paskpris']),
            'warning_sent' => $this->convertWarningSent($vartotojas['ispejimas_issiustas']),
            'blocked_until' => $this->convertBlockedUntil($vartotojas['uzblokuotas_iki']),
            'token' => User::DEFAULT_TOKEN,
            'created_at' => strtotime($vartotojas['data']),
            'updated_at' => strtotime($vartotojas['data']),
        ]);

        // Email validation
        if (empty($user->email)) {
            $this->writeToCSV(User::tableName(), 'Vartotojas neturi el. pašto adreso', $user->id);
            return null;
        }

        // Security
        $user->generateAuthKey();
        $user->setPasswordExpiration();

        $this->fixValidationErrors($user);

        $user->validate();
        if ($user->errors) {
            $this->writeToCSV(User::tableName(), $user->errors, $user->id);
            return null;
        }

        $user->detachBehaviors(); // Remove timestamp behaviour
        $user->save(false);
        return $user;
    }

    /**
     * Assigns company user to company
     *
     * @param null|integer $userId User ID
     * @param null|integer $companyId Company ID
     * @return boolean
     */
    private function assignUserToCompany($userId, $companyId)
    {
        return CompanyUser::assign($companyId, $userId);
    }

    /**
     * Migrates company owner with its company from old system to new one
     *
     * @param array $vartotojas Information about user
     * @param array $imone Information about user company
     * @return null
     */
    private function migrateCompanyWithOwner($vartotojas, $imone)
    {
        Yii::$app->db->beginTransaction();

        $user = $this->migrateOwner($vartotojas, $imone);
        if (is_null($user)) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        if (!$this->migrateLanguages($user->id, $vartotojas['kalbos'])) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        if (!$this->migrateUserCompany($user, $imone)) {
            Yii::$app->db->transaction->rollBack();
            return null;
        }

        Yii::$app->db->transaction->commit();

        return null;
    }

    /**
     * Migrates company owner
     *
     * In old system, information about user was invalid for this system.
     * This method validates that user information, tries to fix it and migrates user from old system to new one.
     *
     * @param array $vartotojas Old user information
     * @param array $imone Old user company information
     * @return User|null
     */
    private function migrateOwner($vartotojas, $imone)
    {
        $user = new User([
            'scenario' => User::SCENARIO_SYSTEM_MIGRATES_COMPANY_OWNER_DATA,
            'id' => $vartotojas['id'],
            'name' => $vartotojas['vardas'],
            'surname' => $vartotojas['pavarde'],
            'email' => str_replace(' ', '', $vartotojas['elpastas']),
            'phone' => $vartotojas['telefonai'],
            'password_hash' => $this->convertPassword($vartotojas['raw_password']),
            'password_reset_token' => User::DEFAULT_PASSWORD_RESET_TOKEN,
            'class' => $this->convertClass($imone['kategorija']),
            'original_class' => $this->convertClass($imone['kategorija']),
            'account_type' => $this->convertAccountType($imone['company_type']),
            'personal_code' => User::DEFAULT_PERSONAL_CODE, // FIXME
            'city_id' => $this->convertCityId($imone['geonameid']),
            'address' => $this->convertAddress($imone['adresas']),
            'vat_code' => str_replace(' ', '', $imone['vat']),
            'came_from_id' => $this->convertCameFromSource($imone['source']),
            'current_credits' => User::DEFAULT_CURRENT_CREDITS,
            'active' => $this->convertActive($vartotojas['aktyvus']),
            'allow' => $this->convertAllow($vartotojas['leidziamas']),
            'archive' => $this->convertArchive($vartotojas['archive_status']),
            'visible' => $this->convertVisible($vartotojas['rodomas']),
            'last_login' => $this->convertLastLogin($vartotojas['paskpris']),
            'warning_sent' => $this->convertWarningSent($vartotojas['ispejimas_issiustas']),
            'blocked_until' => $this->convertBlockedUntil($vartotojas['uzblokuotas_iki']),
            'token' => User::DEFAULT_TOKEN,
            'created_at' => strtotime($vartotojas['data']),
            'updated_at' => strtotime($vartotojas['data']),
        ]);

        // Classes validation
        if ($user->hasInvalidClass()) {
            $this->writeToCSV(User::tableName(), 'Netinkama vartotojo klasė', $user->id);
            return null;
        }

        // Email validation
        if (empty($user->email)) {
            $this->writeToCSV(User::tableName(), 'Vartotojas neturi el. pašto adreso', $user->id);
            return null;
        }

        // Security
        $user->generateAuthKey();
        $user->setPasswordExpiration();

        // Company information
        $user->company_code = $user->isNatural() ? User::DEFAULT_COMPANY_CODE : $this->convertCompanyCode($imone);
        $user->company_name = $user->isNatural() ? User::DEFAULT_COMPANY_NAME : $this->convertCompanyName($imone);

        $this->fixValidationErrors($user);

        $user->validate();
        if ($user->errors) {
            $this->writeToCSV(User::tableName(), $user->errors, $user->id);
            return null;
        }

        $user->detachBehaviors(); // Remove timestamp behaviour
        $user->save(false);
        return $user;
    }

    /**
     * Converts old user password to new one
     *
     * In old system some users passwords were saved in raw format.
     * So if user has saved raw password, this method converts it to new one, dedicated to this system.
     *
     * @param string $rawPassword Old user password in raw format
     * @return string
     */
    private function convertPassword($rawPassword)
    {
        if ($this->hadOldPassword($rawPassword)) {
            return Yii::$app->security->generatePasswordHash($rawPassword);
        }

        return Yii::$app->security->generatePasswordHash(self::DEFAULT_PASSWORD);
    }

    /**
     * Converts old class type to new one
     *
     * In old system there were different company class structure.
     * This method converts user company class type from old system to new one.
     *
     * @param integer $class Old class type
     * @return null|integer
     */
    private function convertClass($class)
    {
        switch ($class) {
            case '6': // Al vežėjas
            case '11': // Ekspeditorius
                return User::CARRIER;
            case '7': // Auto salonas
            case '12': // Auto pardavėjas
                return User::SUPPLIER;
            case '13': // Mini vežėjas
                return User::MINI_CARRIER;
            default: // 14 - not registered, 16 - for advertising, 18 - other
                return null;
        }
    }

    /**
     * Converts old account type to new one
     *
     * In old system there were different account type structure.
     * This method converts user account type from old system to new one.
     *
     * @param integer $accountType Old account type
     * @return integer
     */
    private function convertAccountType($accountType)
    {
        switch ($accountType) {
            case '1': // Juridinis
                return User::LEGAL;
            case '2': // Fizinis
                return User::NATURAL;
            default: // 0 - Not registered
                return self::DEFAULT_ACCOUNT_TYPE;
        }
    }

    /**
     * Converts old city ID to new one
     *
     * In old system not every company had set city ID.
     * Some companies city ID is zero, therefore we set default city ID.
     *
     * @param integer $cityId Old city ID
     * @return integer
     */
    private function convertCityId($cityId)
    {
        if (empty($cityId)) {
            return self::DEFAULT_CITY_ID;
        }

        return $cityId;
    }

    /**
     * Converts user company address from old system to new one
     *
     * In old system some companies address is empty, therefore we have to set some default company address.
     *
     * @param $address
     * @return string
     */
    private function convertAddress($address)
    {
        if (empty($address)) {
            return self::DEFAULT_ADDRESS;
        }

        return $address;
    }

    /**
     * Converts old came from source to new one
     *
     * In old system there were different structure of came from source.
     * This method converts user came from source from old system to new one.
     *
     * @param string $source Old came from source
     * @return integer
     */
    private function convertCameFromSource($source)
    {
        switch ($source) {
            case 'pamaciau_iskabinta_reklama':
                return 18;
            case 'pamaciau_reklama_internete':
                return 17;
            case 'per_facebook':
                return 19;
            case '':
            case '0':
            case 'auto24lv':
            case 'autoplius':
            case 'per_kita':
                return 21;
            case 'per_youtube':
                return 20;
            case 'radau_per_paieska':
                return 16;
            case 'rekomendavo_draugas':
                return 15;
            default:
                return 21;
        }
    }

    /**
     * Converts user activity status from old system to new one
     *
     * @param integer $active User activity status in old system
     * @return boolean
     */
    private function convertActive($active)
    {
        return $active ? User::ACTIVE : User::INACTIVE;
    }

    /**
     * Converts user allow to login status from old system to new one
     *
     * @param integer $allowed Old system user allow to login status
     * @return boolean
     */
    private function convertAllow($allowed)
    {
        return $allowed ? User::ALLOWED : User::FORBIDDEN;
    }

    /**
     * Converts user archive status from old system to new one
     *
     * @param integer $archived Old system user archive status
     * @return boolean
     */
    private function convertArchive($archived)
    {
        return $archived ? User::ARCHIVED : User::NOT_ARCHIVED;
    }

    /**
     * Converts user visibility status from old system to new one
     *
     * @param integer $visible Old system user visibility status
     * @return boolean
     */
    private function convertVisible($visible)
    {
        return $visible ? User::VISIBLE : User::INVISIBLE;
    }

    /**
     * Converts user last login time to timestamp
     *
     * In old system not every user has set last login.
     * Therefore, if user does not have last login time, we set it to current time.
     *
     * @param null|string $lastLogin User last login
     * @return false|integer
     */
    private function convertLastLogin($lastLogin)
    {
        if (is_null($lastLogin)) {
            return time();
        }

        return strtotime($lastLogin);
    }

    /**
     * Converts time to timestamp, when the warning was sent to user
     *
     * In old system not every user got warnings, so we check if it has and if so, we convert it to timestamp.
     *
     * @param null|string $warningSent Time, when the warning was sent to the user
     * @return false|integer|null
     */
    private function convertWarningSent($warningSent)
    {
        if (is_null($warningSent)) {
            return null;
        }

        return strtotime($warningSent);
    }

    /**
     * Converts time to timestamp, until the user is blocked
     *
     * In old system not every user is blocked,
     * so we check if it is blocked and if so,
     * we convert its time to timestamp.
     *
     * @param null|string $blockedUntil Time, until the user is blocked
     * @return false|integer|null
     */
    private function convertBlockedUntil($blockedUntil)
    {
        if (is_null($blockedUntil)) {
            return null;
        }

        return strtotime($blockedUntil);
    }

    /**
     * Converts old system company code to new one
     *
     * @param array $imone Old user company
     * @return null|string
     */
    private function convertCompanyCode($imone)
    {
        if (empty($imone['kodas'])) {
            return User::DEFAULT_COMPANY_CODE;
        }

        return $imone['kodas'];
    }

    /**
     * Converts old system company name to new one
     *
     * @param array $imone Old user company
     * @return null|string
     */
    private function convertCompanyName($imone)
    {
        if (!empty($imone['pavadinimas'])) {
            return $imone['pavadinimas'];
        }

        if (!empty($imone['vardas'])) {
            return $imone['vardas'];
        }

        return User::DEFAULT_COMPANY_NAME;
    }

    /**
     * Fixes user model validation errors
     *
     * This method checks whether user name, surname, phone number and VAT code are valid. If not, then fixes them.
     *
     * @param User $user User model
     */
    private function fixValidationErrors(User &$user)
    {
        $this->fixFullName($user);
        $this->fixPhone($user);
        $this->fixVatCode($user);
    }

    /**
     * Fixes user name and surname
     *
     * @param User $user User model
     */
    private function fixFullName(User &$user)
    {
        $user->name = preg_replace('/[^a-zA-Z\p{L}\s]/u', '', $user->name);
        $user->surname = preg_replace('/[^a-zA-Z\p{L}\s]/u', '', $user->surname);
        if (!$user->validate(['name', 'surname'])) {
            $user->name = self::DEFAULT_NAME;
            $user->surname = self::DEFAUTL_SURNAME;
        }

        return;
    }

    /**
     * Fixes user phone number
     *
     * @param User $user User model
     */
    private function fixPhone(User &$user)
    {
        $user->phone = trim(str_replace(' ', '', $user->phone));
        if (!$user->validate(['phone'])) {
            $user->phone = self::DEFAULT_PHONE;
        }

        return;
    }

    /**
     * Fixes user VAT code
     *
     * @param User $user User model
     */
    private function fixVatCode(User &$user)
    {
        if (!$user->validate(['vat_code'])) {
            $user->vat_code = User::DEFAULT_VAT_CODE;
        }

        return;
    }

    /**
     * Migrates user languages from old system to new one
     *
     * @param null|integer $id User ID, that speaks presented languages
     * @param integer|array $languages User languages
     * @return boolean|null
     */
    private function migrateLanguages($id, $languages)
    {
        $languages = $this->convertLanguage($languages);
        if (is_integer($languages)) {
            return $this->migrateLanguage($id, $languages);
        }

        foreach ($languages as $language) {
            if (!$this->migrateLanguage($id, $language)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Converts user languages from old system to new one
     *
     * @param string $languages Old user languages
     * @return array|integer
     */
    private function convertLanguage($languages)
    {
        if (empty($languages)) {
            return self::DEFAULT_LANGUAGE;
        }

        return explode(',', $languages);
    }

    /**
     * Migrates user language from old system to new one
     *
     * @param null|integer $id User ID, that speaks presented language
     * @param string $language Language name
     * @return boolean|null
     */
    private function migrateLanguage($id, $language)
    {
        $userLanguage = new UserLanguage([
            'user_id' => $id,
            'language_id' => $this->convertLanguageName($language),
        ]);

        $userLanguage->validate();
        if ($userLanguage->errors) {
            $id = json_encode([$userLanguage->user_id, $userLanguage->language_id]);
            $this->writeToCSV(UserLanguage::tableName(), $userLanguage->errors, $id);
            return null;
        }

        return $userLanguage->save(false);
    }

    /**
     * Converts user language from old system to new one
     *
     * @param string $name Language name
     * @return Language|integer
     */
    private function convertLanguageName($name)
    {
        $name = ucfirst(trim($name)); // NOTE: in our database all languages names start with uppercase
        if ($this->isLanguageNameValid($name)) {
            return $this->getLanguages()[$name];
        }

        return self::DEFAULT_LANGUAGE;
    }

    /**
     * Checks whether language name is valid
     *
     * @param string $name Language name
     * @return boolean
     */
    private function isLanguageNameValid($name)
    {
        return array_key_exists($name, $this->getLanguages());
    }

    /**
     * Migrates company users from old system to new one
     *
     * @return null
     */
    public function actionCompanyUser()
    {
        $vartotojai = $this->getOldCompanyUsers();
        foreach ($vartotojai as $vartotojas) {
            if ($this->userExists($vartotojas['id'])) {
                continue;
            }

            $imone = $this->getOldUserCompany($vartotojas['id']);
            if (!$imone) {
                $this->writeToCSV(User::tableName(), 'Vartotojas neturi ryšio su įmone', $vartotojas['id']);
                continue;
            }

            if ($this->companyExists($imone['id'])) {
                $this->migrateCompanyUser($vartotojas, $imone);
            } else {
                $this->writeToCSV(User::tableName(), 'Vartotojo įmonė neegzistuoja', $vartotojas['id']);
            }
        }

        return null;
    }

    /**
     * Returns list of all companies users from old system
     *
     * @return array
     */
    private function getOldCompanyUsers()
    {
        $query = "SELECT * FROM vartotojai WHERE klase = :klase LIMIT 100";
        return Yii::$app->db_prod->createCommand($query, [':klase' => self::KLASE_IMONES_DARBUOTOJAS])->queryAll();
    }
}