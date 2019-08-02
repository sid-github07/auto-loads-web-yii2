<?php

namespace backend\controllers\migration;

use common\components\ElasticSearch\Cities;
use common\models\Admin;
use common\models\City;
use common\models\Company;
use common\models\CompanyComment;
use common\models\CompanyDocument;
use common\models\CompanyInvitation;
use common\models\CompanyUser;
use common\models\Language;
use common\models\User;
use common\models\UserLanguage;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class UserCompanyController
 *
 * This controller is responsible for migrating users and its companies data
 *
 * @package backend\controllers\migration
 */
class UserCompanyController extends MigrateController
{
    const KLASE_PAPRASTAS_VARTOTOJAS = 1;
    const KLASE_IMONES_SAVININKAS = 3;
    const KLASE_SUPER_ADMINISTRATORIUS = 7;
    const KLASE_BUHALTERE = 8;

    const DEFAULT_USER_NAME = 'Nenustatyta'; // FIXME
    const DEFAULT_PHONE = '+37060000000'; // FIXME
    const DEFAULT_CITY = 11074085; // Lietuva
    const DEFAULT_ADDRESS = 'Nenustatyta'; // FIXME
    const DEFAULT_LANGUAGE = 3;
    const DEFAULT_COMPANY_TITLE = 'Nenustatyta'; // FIXME

    const DEFAULT_ADMIN_ID = 19;

    public function actionUserCompany()
    {
        $languages = ArrayHelper::map(Language::find()->all(), 'id', 'name');

        // Get all company owners
        $query = "SELECT * FROM vartotojai WHERE klase = :klase LIMIT 1";
        $vartotojai = Yii::$app->db_prod->createCommand($query, [':klase' => self::KLASE_IMONES_SAVININKAS])->queryAll();
//        $vartotojai = Yii::$app->db_prod->createCommand($query, [':klase' => self::KLASE_PAPRASTAS_VARTOTOJAS])->queryAll();

        foreach ($vartotojai as $vartotojas) {
            // Check if user has been already migrated
            if (User::find()->where(['id' => $vartotojas['id']])->exists()) {
                continue;
            }

            // Get user company
            $query = "
                SELECT * 
                FROM imones 
                LEFT JOIN vartotojai_2_imones 
                    ON vartotojai_2_imones.imones_id = imones.id 
                WHERE vartotojai_2_imones.vartotojo_id = :id";
            $imone = Yii::$app->db_prod->createCommand($query, [':id' => $vartotojas['id']])->queryOne();

            // Check if user has company
            if (!$imone) {
                $this->writeToCSV(User::tableName(), 'Vartotojas nepriskirtas jokiai įmonei', $vartotojas['id']);
                continue;
            }

            $user = new User(['scenario' => User::SCENARIO_SYSTEM_MIGRATES_USER]);

            // ID
            $user->id = $vartotojas['id'];

            // Name and surname
            if (empty($vartotojas['pavarde'])) {
                $user->name = self::DEFAULT_USER_NAME;
                $user->surname = self::DEFAULT_USER_NAME;
            } else {
                if ($vartotojas['pavarde'] == 'Bosman -Akire' || $vartotojas['pavarde'] == 'Wojciech ------') {
                    if ($vartotojas['pavarde'] == 'Bosman -Akire') {
                        $user->name = 'Bosman';
                        $user->surname = 'Akire';
                    }

                    if ($vartotojas['pavarde'] == 'Wojciech ------') {
                        $user->name = 'Wojciech';
                        $user->surname = self::DEFAULT_USER_NAME;
                    }
                } else {
                    $vardasIrPavarde = preg_replace('/[^\sa-zA-Z\p{L}\-]/u', '', $vartotojas['pavarde']);
                    $vardasIrPavarde = ucwords(mb_strtolower($vardasIrPavarde, 'UTF-8'));
                    $vardasIrPavarde = preg_replace('!\s+!', ' ', $vardasIrPavarde);
                    $vardasIrPavarde = explode(' ', $vardasIrPavarde);

                    if (count($vardasIrPavarde) <= 1) {
                        $user->name = (strlen($vardasIrPavarde[0]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[0];
                        $user->surname = self::DEFAULT_USER_NAME;
                    } else {
                        $user->name = (strlen($vardasIrPavarde[0]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[0];
                        $user->surname = (strlen($vardasIrPavarde[1]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[1];
                    }
                }

                $user->validate(['name', 'surname']);
                if ($user->errors) {
                    var_dump($user->id);
                    var_dump($user->errors);
                }
            }

            // Email
            if (empty($vartotojas['elpastas'])) {
                $this->writeToCSV(User::tableName(), 'Vartotojas neturi el. pašto adreso', $vartotojas['id']);
                continue;
            }

            if (User::find()->where(['email' => $vartotojas['elpastas']])->exists()) {
                $this->writeToCSV(User::tableName(), 'Vartotojas su tokiu el. pašto adresu jau registruotas', $vartotojas['id']);
                continue;
            }

            $user->email = preg_replace('/\s+/', '', $vartotojas['elpastas']);
            $user->validate(['email']);
            if ($user->errors) {
                $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
                preg_match_all($pattern, $user->email, $matches);
                if (isset($matches[0][0]) && !empty($matches[0][0])) {
                    $user->email = $matches[0][0];
                }
                $user->validate(['email']);
                if ($user->errors) {
                    if ($user->email == '+37067837373') {
                        $user->email = $vartotojas['telefonai'];
                    }
                    if ($user->email == 'a.pieper(at)jvg-autologistik.de') {
                        $user->email = 'a.pieper@jvg-autologistik.de';
                    }
                    $user->validate(['email']);
                    if ($user->errors) {
                        var_dump($user->id);
                        var_dump($user->errors);
                    }
                }
            }

            // Phone
            if (empty($vartotojas['telefonai'])) {
                $user->phone = self::DEFAULT_PHONE;
            } else {
                $user->phone = preg_replace('/\s+/', '', $vartotojas['telefonai']);
            }

            $user->validate(['phone']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Auth_key, password, password_reset_token, password_expires
            $user->generateAuthKey();
            if (empty($vartotojas['raw_password'])) {
                $user->setPassword(Yii::$app->security->generateRandomString());
                $user->password_reset_token = Yii::$app->security->generateRandomString(64);
            } else {
                $user->setPassword($vartotojas['raw_password']);
                $user->password_reset_token = null;
            }
            $user->setPasswordExpiration();

            $user->validate(['auth_key', 'password_hash', 'password_reset_token', 'password_expires']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Personal_code
            $user->personal_code = null;
            $user->validate(['personal_code']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Company_code, company_name

            // City_id
            if (!empty($imone['geonameid'])) {
                $user->city_id = $imone['geonameid'];
            } elseif (!empty($imone['miestas'])) {
                $cities = Cities::getSimpleSearchCities($imone['miestas']);
                if (!empty($cities)) {
                    $user->city_id = $cities[0]['id'];
                }
            }

            if (empty($user->city_id)) {
                if (empty($imone['salis'])) {
                    $user->city_id = self::DEFAULT_CITY;
                } else {
                    $user->city_id = City::find()
                        ->select('id')
                        ->where([
                            'country_code' => strtoupper($imone['salis']),
                            'modification_date' => null,
                        ])
                        ->scalar();
                }
            }

            $user->validate(['city_id']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Address
            $user->address = $imone['adresas'];
            if (empty($user->address) || $user->id == 1334) {
                $user->address = self::DEFAULT_ADDRESS;
            }

            $user->validate(['address']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Vat_code
            $user->vat_code = empty($imone['vat']) ? null : $imone['vat'];
            $user->validate(['vat_code']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Came_from_id
            switch ($imone['source']) {
                case 'pamaciau_iskabinta_reklama':
                    $user->came_from_id = 18;
                    break;
                case 'pamaciau_reklama_internete':
                    $user->came_from_id = 17;
                    break;
                case 'per_facebook':
                    $user->came_from_id = 19;
                    break;
                case '':
                case '0':
                case 'auto24lv':
                case 'autoplius':
                case 'per_kita':
                    $user->came_from_id = 21;
                    break;
                case 'per_youtube':
                    $user->came_from_id = 20;
                    break;
                case 'radau_per_paieska':
                    $user->came_from_id = 16;
                    break;
                case 'rekomendavo_draugas':
                    $user->came_from_id = 15;
                    break;
                default:
                    $user->came_from_id = User::DEFAULT_CAME_FROM_ID;
                    break;
            }

            $user->validate(['came_from_id']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Current_credits
            $user->current_credits = null;

            // Active, allow, archive, visible
            $user->active = $vartotojas['aktyvus'];
            $user->allow = $vartotojas['leidziamas'];
            $user->archive = $vartotojas['archive_status'];
            $user->visible = $vartotojas['rodomas'];

            $user->validate(['active', 'allow', 'archive', 'visible']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Last_login
            $user->last_login = empty($vartotojas['paskpris']) ? time() : strtotime($vartotojas['paskpris']);
            $user->validate(['last_login']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Warning_sent
            $user->warning_sent = empty($vartotojas['ispejimas_issiustas']) ? null : strtotime($vartotojas['ispejimas_issiustas']);
            $user->validate(['warning_sent']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Blocked_until
            $user->blocked_until = empty($vartotojas['uzblokuotas_iki']) ? null : strtotime($vartotojas['uzblokuotas_iki']);
            $user->validate(['blocked_until']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Token
            $user->token = null;
            $user->validate(['token']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Created_at, updated_at
            $user->created_at = strtotime($vartotojas['data']);
            $user->updated_at = strtotime($vartotojas['data']);
            $user->validate(['created_at', 'updated_at']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Class, original_class
            switch ($imone['kategorija']) {
                case '6': // Al vežėjas
                case '14': // Neregistruotas
                    $user->class = User::CARRIER;
                    $user->original_class = User::CARRIER;
                    break;
                case '7': // Auto salonas
                case '11': // Ekspeditorius
                case '12': // Auto pardavėjas
                    $user->class = User::SUPPLIER;
                    $user->original_class = User::SUPPLIER;
                    break;
                case '13': // Mini vežėjas
                    $user->class = User::MINI_CARRIER;
                    $user->original_class = User::MINI_CARRIER;
                    break;
                case '16': // Reklamai
                case '18': // Kita
                    if (empty($imone['pavadinimas'])) {
                        $user->class = User::CARRIER;
                        $user->original_class = User::CARRIER;
                    } else {
                        $user->class = User::SUPPLIER;
                        $user->original_class = User::SUPPLIER;
                    }
                    break;
                case null:
                    if (empty($imone['pavadinimas'])) {
                        $user->class = User::CARRIER;
                        $user->original_class = User::CARRIER;
                    } else {
                        $user->class = User::SUPPLIER;
                        $user->original_class = User::SUPPLIER;
                    }
                    break;
                default:
                    $this->writeToCSV(User::tableName(), 'Vartotojo įmonė nepriskirta jokiai kategorijai', $vartotojas['id']);
                    continue;
            }

            $user->validate(['class', 'original_class']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Account_type
            switch ($imone['company_type']) {
                case '1': // Juridinis
                    $user->account_type = User::LEGAL;
                    break;
                case '2': // Fizinis
                    $user->account_type = User::NATURAL;
                    break;
                default: // 0 - Not registered
                    $user->account_type = empty($imone['pavadinimas']) ? User::NATURAL : User::LEGAL;
                    break;
            }

            $user->validate(['account_type']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            // Company_code, company_name
            if ($user->account_type == User::LEGAL) {
                if (empty($imone['pavadinimas'])) {
                    $user->company_name = $user->name . ' ' . $user->surname;
                } else {
                    $user->company_name = $imone['pavadinimas'];
                }
                $user->company_code = $imone['kodas'];
            } else {
                $user->company_name = null;
                $user->company_code = null;
            }

            $user->validate(['company_name', 'company_code']);
            if ($user->errors) {
                var_dump($user->id);
                var_dump($user->errors);
            }

            $user->suggestions = User::DO_NOT_SEND_SUGGESTIONS;
            $user->suggestions_token = Yii::$app->security->generateRandomString();

            Yii::$app->db->beginTransaction();

            $user->detachBehaviors();
            if (!$user->save()) {
                var_dump($user->id);
                var_dump($user->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            // User language
            if (empty($vartotojas['kalbos'])) {
                $userLanguage = new UserLanguage([
                    'user_id' => $user->id,
                    'language_id' => self::DEFAULT_LANGUAGE,
                ]);

                $userLanguage->validate();
                if ($userLanguage->errors) {
                    var_dump($user->id);
                    var_dump($userLanguage->errors);
                    Yii::$app->db->transaction->rollBack(); continue;
                }

                $userLanguage->save();
            } else {
                $explodedLanguages = explode(',', $vartotojas['kalbos']);
                foreach ($explodedLanguages as $language) {
                    // NOTE: in our database all languages names start with uppercase
                    $languageName = ucfirst(trim($language));
                    if (in_array($languageName, $languages)) {
                        $userLanguage = new UserLanguage([
                            'user_id' => $user->id,
                            'language_id' => array_search($languageName, $languages),
                        ]);

                        $userLanguage->validate();
                        if ($userLanguage->errors) {
                            var_dump($user->id);
                            var_dump($userLanguage->errors);
                            Yii::$app->db->transaction->rollBack(); continue 2;
                        }
                        $userLanguage->save();
                    }
                }
            }

            if (Company::find()->where(['id' => $imone['id']])->exists()) {
                // User must be saved as company user, not like company owner
                $user->city_id = null;
                $user->address = null;
                $user->vat_code = null;
                $user->came_from_id = null;
                $user->class = null;
                $user->original_class = null;
                $user->account_type = null;
                $user->company_name = null;
                $user->company_code = null;
                $user->validate();
                if ($user->errors) {
                    var_dump($user->id);
                    var_dump($user->errors);
                    Yii::$app->db->transaction->rollBack();
                } else {
                    $user->update();
                    $companyUser = new CompanyUser([
                        'company_id' => $imone['id'],
                        'user_id' => $user->id,
                    ]);
                    $companyUser->validate();
                    if ($companyUser->errors) {
                        var_dump($user->id);
                        var_dump($companyUser->errors);
                        Yii::$app->db->transaction->rollBack();
                    } else {
                        $companyUser->save();
                        Yii::$app->db->transaction->commit();
                    }
                }
                continue;
            }

            // Company
            $company = new Company(null, Company::SCENARIO_SYSTEM_MIGRATES_COMPANY);

            // ID, owner_id
            $company->id = $imone['id'];
            $company->owner_id = $user->id;


            $company->validate(['id', 'owner_id']);
            if ($company->errors) {
                var_dump($user->id);
                var_dump($company->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            // Title, code, vat_code, name, surname, personal_code
            if ($user->account_type == User::NATURAL) {
                $company->title = null;
                $company->code = null;
                $company->vat_code = null;
                $company->name = $user->name;
                $company->surname = $user->surname;
                $company->personal_code = null;
            } else {
                if (empty($imone['pavadinimas'])) {
                    if (empty($imone['vardas'])) {
                        $company->title = self::DEFAULT_COMPANY_TITLE;
                    } else {
                        $company->title = $imone['vardas'];
                    }
                } else {
                    $company->title = $imone['pavadinimas'];
                }
                $company->code = $imone['kodas'];
                $company->vat_code = $imone['vat'];
                $company->name = null;
                $company->surname = null;
                $company->personal_code = null;
            }

            $company->validate(['title', 'code', 'vat_code', 'name', 'surname', 'personal_code']);
            if ($company->errors) {
                var_dump($user->id);
                var_dump($company->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            // Adress, city_id, phone, email, website
            $company->address = empty($imone['adresas']) ? $user->address : $imone['adresas'];
            $company->city_id = $user->city_id;
            $company->phone = empty($imone['telefonai']) ? $user->phone : $imone['telefonai'];
            $company->email = empty($imone['elpastas']) ? null : $imone['elpastas'];
            $company->website = null;
            $company->validate(['address', 'city_id', 'phone', 'email', 'website']);
            if ($company->errors) {
                var_dump($user->id);
                var_dump($company->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            // Active, allow, archive, visible, suggestions, created_at, updated_at
            $company->active = $imone['aktyvi'] ? Company::ACTIVE : Company::INACTIVE;
            $company->allow = $imone['leidziamas'] ? Company::ALLOW : Company::FORBIDDEN;
            $company->archive = $imone['archive_status'] ? Company::ARCHIVED : Company::NOT_ARCHIVED;
            $company->visible = $imone['rodomas'] ? Company::VISIBLE : Company::INVISIBLE;
            $company->suggestions = Company::SEND_SUGGESTIONS;
            $company->created_at = empty($imone['data']) ? time() : strtotime($imone['data']);
            $company->updated_at = empty($imone['data']) ? time() : strtotime($imone['data']);
            $company->validate(['active', 'allow', 'archive', 'visible', 'suggestions', 'created_at', 'updated_at']);
            if ($company->errors) {
                var_dump($user->id);
                var_dump($company->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            $company->detachBehaviors();
            if (!$company->save()) {
                var_dump($user->id);
                var_dump($company->errors);
                Yii::$app->db->transaction->rollBack(); continue;
            }

            // Company comments
            $imoniuKomentarai = Yii::$app->db_prod->createCommand("SELECT * FROM imoniu_komentarai WHERE imones_id = :id", [':id' => $company->id])->queryAll();
            foreach ($imoniuKomentarai as $komentaras) {
                $companyComment = new CompanyComment(['scenario' => CompanyComment::SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS]);

                // ID, company_id
                $companyComment->id = $komentaras['id'];
                $companyComment->company_id = $komentaras['imones_id'];
                $companyComment->validate(['id', 'company_id']);
                if ($companyComment->errors) {
                    var_dump($user->id);
                    var_dump($companyComment->errors);
                }

                // Admin_id
                $companyComment->admin_id = $komentaras['vartotojo_id'];
                $companyComment->validate(['admin_id']);
                if ($companyComment->errors) {
                    if (isset($companyComment->errors['admin_id'])) {
                        $companyComment->admin_id = self::DEFAULT_ADMIN_ID;
                        $companyComment->validate(['admin_id']);
                        if ($companyComment->errors) {
                            var_dump($user->id);
                            var_dump($companyComment->errors);
                        }
                    } else {
                        var_dump($user->id);
                        var_dump($companyComment->errors);
                    }
                }

                // Comment
                $companyComment->comment = $komentaras['tekstas'];
                $companyComment->validate(['comment']);
                if ($companyComment->errors) {
                    var_dump($user->id);
                    var_dump($companyComment->errors);
                }

                // Archived, created_at, updated_at
                $companyComment->archived = $komentaras['rodomas'];
                $companyComment->created_at = strtotime($komentaras['data']);
                $companyComment->updated_at = strtotime($komentaras['data']);
                $companyComment->validate(['archived', 'created_at', 'updated_at']);
                if ($companyComment->errors) {
                    var_dump($user->id);
                    var_dump($companyComment->errors);
                }

                $companyComment->detachBehaviors();
                $companyComment->save();
            }

            // Company documents
            $files = Yii::$app->db_prod->createCommand("SELECT * FROM files WHERE object_id = :id", [':id' => $company->id])->queryAll();
            foreach ($files as $file) {
                $companyDocument = new CompanyDocument(['scenario' => CompanyDocument::SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS]);

                // ID, company_id
                $companyDocument->id = $file['id'];
                $companyDocument->company_id = $file['object_id'];
                $companyDocument->validate(['id', 'company_id']);
                if ($companyDocument->errors) {
                    var_dump($user->id);
                    var_dump($companyDocument->errors);
                }

                // Date
                if (empty($file['flexfield1']) || (strpos($file['flexfield1'], '-') === false)) {
                    $companyDocument->date = strtotime($file['created']);
                } else {
                    $companyDocument->date = strtotime($file['flexfield1']);
                }
                if ($companyDocument->date < 0 || $companyDocument->date >= 2147483647) {
                    $companyDocument->date = strtotime($file['created']);
                }
                $companyDocument->validate(['date']);
                if ($companyDocument->errors) {
                    var_dump($user->id);
                    var_dump($companyDocument->errors);
                }

                // Type
                switch ($file['type']) {
                    case 'cmr':
                        $companyDocument->type = CompanyDocument::CMR;
                        break;
                    case 'lic':
                        $companyDocument->type = CompanyDocument::EU;
                        break;
                    case 'mini':
                    case 'reg':
                        $companyDocument->type = CompanyDocument::IM;
                        break;
                }
                $companyDocument->validate(['type']);
                if ($companyDocument->errors) {
                    var_dump($user->id);
                    var_dump($companyDocument->errors);
                }

                // Extension
                $companyDocument->extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $companyDocument->validate(['extension']);
                if ($companyDocument->errors) {
                    var_dump($user->id);
                    var_dump($companyDocument->errors);
                }

                // Created_at, updated_at
                $companyDocument->created_at = strtotime($file['created']);
                $companyDocument->updated_at = strtotime($file['created']);
                $companyDocument->validate(['created_at', 'updated_at']);
                if ($companyDocument->errors) {
                    var_dump($user->id);
                    var_dump($companyDocument->errors);
                }

                // Copy remote file to local server
                $id = $companyDocument->company_id;
                $name = $file['name'];
                $remoteFileUrl = "http://auto-loads.lt/lt/adminuzsakovai/download-document?name=$name&imonesid=$id";
                $localName = $companyDocument->getCompanyTypeName();
                $localFileName = $localName . '.' . $companyDocument->extension;
                switch ($companyDocument->type) {
                    case CompanyDocument::CMR:
                        $localPath = Yii::$app->params['CMRPath'] . $id . DIRECTORY_SEPARATOR;
                        break;
                    case CompanyDocument::EU:
                        $localPath = Yii::$app->params['EUPath'] . $id . DIRECTORY_SEPARATOR;
                        break;
                    case CompanyDocument::IM:
                        $localPath = Yii::$app->params['IMPath'] . $id . DIRECTORY_SEPARATOR;
                        break;
                }

                if (!is_dir($localPath)) {
                    mkdir($localPath, 0755, true);
                }

                if (copy($remoteFileUrl, $localPath . $localFileName)) {
                    $companyDocument->detachBehaviors();
                    $companyDocument->save();
                } else {
                    var_dump($user->id);
                    var_dump('Dokumentas, kurio ID yra ' . $companyDocument->id . ' negali būti nukopijuotas');
                }
            }

            // Company invitations
            $vartotojaiInvitation = Yii::$app->db_prod->createCommand("SELECT * FROM vartotojai_invitation WHERE company_id = :id", [':id' => $company->id])->queryAll();
            foreach ($vartotojaiInvitation as $pakvietimas) {
                if (CompanyInvitation::find()->where(['email' => $pakvietimas['receiver_email']])->exists()) {
                    continue;
                }

                $companyInvitation = new CompanyInvitation(['scenario' => CompanyInvitation::SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION]);

                // ID, company_id, email, token, accepted, created_at, updated_at
                $companyInvitation->id = $pakvietimas['invitation_id'];
                $companyInvitation->company_id = $company->id;
                $companyInvitation->email = $pakvietimas['receiver_email'];
                $companyInvitation->token = CompanyInvitation::DEFAULT_TOKEN_VALUE;
                $companyInvitation->accepted = $pakvietimas['active'] ? CompanyInvitation::ACCEPTED : CompanyInvitation::NOT_ACCEPTED;
                $companyInvitation->created_at = strtotime($pakvietimas['date']);
                $companyInvitation->updated_at = strtotime($pakvietimas['date']);

                $companyInvitation->validate();
                if ($companyInvitation->errors) {
                    var_dump($user->id);
                    var_dump($companyInvitation->errors);
                }

                $companyInvitation->detachBehaviors();
                $companyInvitation->save();
            }

            Yii::$app->db->transaction->commit();
        }
    }

    public function actionAdmin()
    {
        $query = "SELECT * FROM vartotojai WHERE klase = " . self::KLASE_SUPER_ADMINISTRATORIUS . " OR klase = " . self::KLASE_BUHALTERE;
        $vartotojai = Yii::$app->db_prod->createCommand($query)->queryAll();
        foreach ($vartotojai as $vartotojas) {
            if (Admin::find()->where(['id' => $vartotojas['id']])->exists()) {
                continue;
            }

            $admin = new Admin(['scenario' => Admin::SCENARIO_SYSTEM_MIGRATES_ADMIN]);

            // ID
            $admin->id = $vartotojas['id'];

            // Name and surname
            $vardasIrPavarde = preg_replace('/[^\sa-zA-Z\p{L}\-]/u', '', $vartotojas['pavarde']);
            $vardasIrPavarde = ucwords(mb_strtolower($vardasIrPavarde, 'UTF-8'));
            $vardasIrPavarde = preg_replace('!\s+!', ' ', $vardasIrPavarde);
            $vardasIrPavarde = explode(' ', $vardasIrPavarde);

            if (count($vardasIrPavarde) <= 1) {
                $admin->name = (strlen($vardasIrPavarde[0]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[0];
                $admin->surname = self::DEFAULT_USER_NAME;
            } else {
                $admin->name = (strlen($vardasIrPavarde[0]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[0];
                $admin->surname = (strlen($vardasIrPavarde[1]) <= 1) ? self::DEFAULT_USER_NAME : $vardasIrPavarde[1];
            }

            $admin->validate(['name', 'surname']);
            if ($admin->errors) {
                var_dump($admin->id);
                var_dump($admin->errors);
            }

            // Email
            $admin->email = $vartotojas['elpastas'];
            $admin->validate(['email']);
            if ($admin->errors) {
                var_dump($admin->id);
                var_dump($admin->errors);
            }

            // Phone, auth_key, password, password_reset_token, admin, archived, created_at, updated_at
            $admin->phone = $vartotojas['telefonai'];
            $admin->generateAuthKey();
            $admin->setPassword(Yii::$app->security->generateRandomString());
            $admin->password_reset_token = Yii::$app->security->generateRandomString(64);
            $admin->admin = $vartotojas['klase'] == self::KLASE_SUPER_ADMINISTRATORIUS ? Admin::IS_ADMIN : Admin::IS_MODERATOR;
            $admin->archived = $vartotojas['archive_status'] ? Admin::ARCHIVED : Admin::NOT_ARCHIVED;
            $admin->created_at = strtotime($vartotojas['data']);
            $admin->updated_at = strtotime($vartotojas['data']);

            $admin->validate(['phone', 'auth_key', 'password', 'password_reset_token', 'admin', 'archived', 'created_at', 'updated_at']);
            if ($admin->errors) {
                var_dump($admin->id);
                var_dump($admin->errors);
            }

            $admin->detachBehaviors();
            $admin->save();
        }
    }
}
