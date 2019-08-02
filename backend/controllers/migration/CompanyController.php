<?php

namespace backend\controllers\migration;

use common\models\Company;
use common\models\CompanyComment;
use common\models\CompanyDocument;
use common\models\CompanyInvitation;
use common\models\User;
use Yii;

/**
 * Class CompanyController
 *
 * This controller is responsible for migrating user company, company comments, company documents and company invitations
 *
 * @package backend\controllers\migration
 */
class CompanyController extends MigrateController
{
    const DEFAULT_ADMIN_ID = 1;

    /**
     * Migrates user company from old system to new one
     *
     * @param User $user User model
     * @param array $imone Information about user company
     * @return boolean|null
     */
    protected function migrateUserCompany(User $user, $imone)
    {
        if ($this->companyExists($imone['id'])) {
            return null;
        }

        $company = $this->migrateCompany($user, $imone);
        if (is_null($company)) {
            return null;
        }

        $this->migrateCompanyComments($company->id);
        $this->migrateCompanyDocuments($company->id);
        $this->migrateCompanyInvitations($company->id);

        return true;
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
     * Migrates user company
     *
     * @param User $user User model
     * @param array $imone Old user company information
     * @return Company|null
     */
    private function migrateCompany(User $user, $imone)
    {
        $query = "SELECT * FROM company_detail WHERE id_imones = :id";
        $companyDetail = Yii::$app->db_prod->createCommand($query, [':id' => $imone['id']])->queryOne();

        $company = new Company(null, Company::SCENARIO_SYSTEM_MIGRATES_COMPANY_DATA, [
            'id' => $imone['id'],
            'owner_id' => $user->id,
            'title' => $user->isNatural() ? Company::TITLE_DEFAULT_VALUE : $this->convertCompanyName($user->company_name),
            'code' => $user->isNatural() ? Company::CODE_DEFAULT_VALUE : $this->convertCompanyCode($user->company_code),
            'vat_code' => $user->vat_code,
            'address' => $user->address,
            'city_id' => $user->city_id,
            'phone' => $user->phone,
            'email' => $user->email,
            'website' => $companyDetail['url'],
            'name' => $user->isNatural() ? $user->name : Company::NAME_DEFAULT_VALUE,
            'surname' => $user->isNatural() ? $user->surname : Company::SURNAME_DEFAULT_VALUE,
            'personal_code' => $user->personal_code,
            'active' => $this->convertActive($imone['aktyvi']),
            'allow' => $this->convertAllow($imone['leidziamas']),
            'archive' => $this->convertArchive($imone['archive_status']),
            'visible' => $this->convertVisible($imone['rodomas']),
            'suggestions' => Company::SEND_SUGGESTIONS,
            'created_at' => strtotime($imone['data']),
            'updated_at' => strtotime($imone['data']),
        ]);

        $this->fixWebsite($company);

        $company->validate();
        if ($company->errors) {
            $this->writeToCSV(Company::tableName(), $company->errors, $company->id);
            return null;
        }

        $company->detachBehaviors(); // Remove timestamp behaviour
        $company->save(false);
        return $company;
    }

    /**
     * Converts user company name from old system to new one
     *
     * @param null|string $name Old user company name
     * @return null|string
     */
    private function convertCompanyName($name)
    {
        if (empty($name)) {
            return Company::NAME_DEFAULT_VALUE;
        }

        return $name;
    }

    /**
     * Converts user company code from old system to new one
     *
     * @param null|string $code Old user company code
     * @return null|string
     */
    private function convertCompanyCode($code)
    {
        if (empty($code)) {
            return Company::CODE_DEFAULT_VALUE;
        }

        return $code;
    }

    /**
     * Converts company activity status from old system to new one
     *
     * @param integer $active Old company activity status
     * @return boolean
     */
    private function convertActive($active)
    {
        return $active ? Company::ACTIVE : Company::INACTIVE;
    }

    /**
     * Converts company allow status from old system to new one
     *
     * @param integer $allow Old company allow status
     * @return boolean
     */
    private function convertAllow($allow)
    {
        return $allow ? Company::ALLOW : Company::FORBIDDEN;
    }

    /**
     * Converts company archive status from old system to new one
     *
     * @param integer $archive Old company archive status
     * @return boolean
     */
    private function convertArchive($archive)
    {
        return $archive ? Company::ARCHIVED : Company::NOT_ARCHIVED;
    }

    /**
     * Converts company visibility status from old system to new one
     *
     * @param integer $visible Old company visibility status
     * @return boolean
     */
    private function convertVisible($visible)
    {
        return $visible ? Company::VISIBLE : Company::INVISIBLE;
    }

    /**
     * Fixes company website
     *
     * @param Company $company Company model
     * @return null
     */
    private function fixWebsite(Company &$company)
    {
        if (!$company->validate(['website'])) {
            $company->website = Company::WEBSITE_DEFAULT_VALUE;
        }

        return null;
    }

    /**
     * Migrates user company comments from old system to new one
     *
     * @param null|integer $id Company ID
     * @return null
     */
    private function migrateCompanyComments($id)
    {
        $query = "SELECT * FROM imoniu_komentarai WHERE imones_id = :id";
        $imoniuKomentarai = Yii::$app->db_prod->createCommand($query, [':id' => $id])->queryAll();
        foreach ($imoniuKomentarai as $komentaras) {
            $this->migrateCompanyComment($id, $komentaras);
        }

        return null;
    }

    /**
     * Migrates user company comment from old system to new one
     *
     * @param null|integer $id Company ID
     * @param array $komentaras Information about old user company comment
     * @return boolean|null
     */
    private function migrateCompanyComment($id, $komentaras)
    {
        $companyComment = new CompanyComment([
            'scenario' => CompanyComment::SCENARIO_SYSTEM_MIGRATES_COMPANY_COMMENTS_DATA,
            'id' => $komentaras['id'],
            'company_id' => $id,
            'admin_id' => self::DEFAULT_ADMIN_ID,
            'comment' => substr($komentaras['tekstas'], 0, CompanyComment::COMMENT_MAX_LENGTH),
            'archived' => $komentaras['rodomas'] ? CompanyComment::NOT_ARCHIVED : CompanyComment::ARCHIVED,
            'created_at' => strtotime($komentaras['data']),
            'updated_at' => strtotime($komentaras['data']),
        ]);

        $companyComment->validate();
        if ($companyComment->errors) {
            $this->writeToCSV(CompanyComment::tableName(), $companyComment->errors, $companyComment->id);
            return null;
        }

        $companyComment->detachBehaviors(); // Remove timestamp behaviour
        return $companyComment->save(false);
    }

    /**
     * Migrates user company document from old system to new one
     *
     * @param null|integer $id Company ID
     * @return null
     */
    private function migrateCompanyDocuments($id)
    {
        $query = "SELECT * FROM files WHERE type = 'cmr' AND object_id = :id";
        $files = Yii::$app->db_prod->createCommand($query, [':id' => $id])->queryAll();
        foreach ($files as $dokumentas) {
            $companyDocument = $this->migrateCompanyDocument($id, $dokumentas);
            if (is_null($companyDocument)) {
                continue;
            }

            $this->migrateCompanyFile($companyDocument, $dokumentas);
        }

        return null;
    }

    /**
     * Migrates user company document from old system to new one
     *
     * @param null|integer $id Company ID
     * @param array $dokumentas Information about user company document
     * @return null|CompanyDocument
     */
    private function migrateCompanyDocument($id, $dokumentas)
    {
        $companyDocument = new CompanyDocument([
            'scenario' => CompanyDocument::SCENARIO_SYSTEM_MIGRATES_COMPANY_DOCUMENTS_DATA,
            'id' => $dokumentas['id'],
            'company_id' => $id,
            'date' => strtotime($dokumentas['flexfield1']),
            'type' => CompanyDocument::CMR,
            'extension' => $this->convertExtension($dokumentas['name']),
            'created_at' => strtotime($dokumentas['created']),
            'updated_at' => strtotime($dokumentas['created']),
        ]);

        $companyDocument->validate();
        if ($companyDocument->errors) {
            $this->writeToCSV(CompanyDocument::tableName(), $companyDocument->errors, $companyDocument->id);
            return null;
        }

        $companyDocument->detachBehaviors(); // Remove timestamp behaviour
        $companyDocument->save(false);
        return $companyDocument;
    }

    /**
     * Converts user company document file extension from old system to new one
     *
     * @param string $name File name with extension
     * @return null|string
     */
    private function convertExtension($name)
    {
        $name = explode('.', $name);
        if (count($name) == 2) {
            return end($name);
        }

        return null;
    }

    /**
     * Migrates user company document file from old system to new one
     *
     * @param CompanyDocument $companyDocument User company document model
     * @param array $dokumentas Information about user company document
     */
    private function migrateCompanyFile(CompanyDocument $companyDocument, $dokumentas)
    {
        $id = $companyDocument->company_id;

        // Remote file
        $remoteFileName = $dokumentas['name'];
        $remoteFileUrl = $this->getRemoteCompanyDocumentFileUrl($id, $remoteFileName);

        // Local file
        $localName = $companyDocument->getCompanyTypeName();
        $localFileName = $localName . '.' . $companyDocument->extension;
        $localPath = Yii::$app->params['CMRPath'] . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;

        if (!is_dir($localPath)) {
            mkdir($localPath, 0777, true); // FIXME
        }

        copy($remoteFileUrl, $localPath . $localFileName); // Copies remote file to local path
    }

    /**
     * Returns URL to remote company document file
     *
     * @param null|integer $id Company ID, whom belongs document file
     * @param string $name Company document file name
     * @return string
     */
    private function getRemoteCompanyDocumentFileUrl($id, $name)
    {
        return "http://auto-loads.lt/lt/adminuzsakovai/download-document?name=$name&imonesid=$id";
    }

    /**
     * Migrates company invitations from old system to new one
     *
     * @param null|integer $id Company ID, that invites user
     * @return null
     */
    private function migrateCompanyInvitations($id)
    {
        $query = "SELECT * FROM vartotojai_invitation WHERE company_id = :id";
        $vartotojaiInvitation = Yii::$app->db_prod->createCommand($query, [':id' => $id])->queryAll();
        foreach ($vartotojaiInvitation as $pakvietimas) {
            if ($this->userHasAlreadyBeenInvited($pakvietimas['receiver_email'])) {
                $message = 'Vartotojas su tokiu el. paštu jau pakviestas';
                $invitationId = $pakvietimas['invitation_id'];
                $this->writeToCSV(CompanyInvitation::tableName(), $message, $invitationId);
                continue;
            }

            $this->migrateCompanyInvitation($pakvietimas);
        }

        return null;
    }

    /**
     * Checks whether user has been already invited to join the company
     *
     * @param string $email User email, for whom invitation was sent
     * @return boolean
     */
    private function userHasAlreadyBeenInvited($email)
    {
        return CompanyInvitation::find()->where(compact('email'))->exists();
    }

    /**
     * Migrates company invitation from old system to new one
     *
     * @param array $pakvietimas Information about company invitation
     * @return boolean|null
     */
    private function migrateCompanyInvitation($pakvietimas)
    {
        $companyInvitation = new CompanyInvitation([
            'scenario' => CompanyInvitation::SCENARIO_SYSTEM_MIGRATES_COMPANY_INVITATION_DATA,
            'id' => $pakvietimas['invitation_id'],
            'company_id' => $pakvietimas['company_id'],
            'email' => $pakvietimas['receiver_email'],
            'token' => CompanyInvitation::DEFAULT_TOKEN_VALUE,
            'accepted' => $this->convertAccepted($pakvietimas['active']),
            'created_at' => strtotime($pakvietimas['date']),
            'updated_at' => strtotime($pakvietimas['date']),
        ]);

        $companyInvitation->validate();
        if ($companyInvitation->errors) {
            $this->writeToCSV(CompanyInvitation::tableName(), $companyInvitation->errors, $companyInvitation->id);
            return null;
        }

        $companyInvitation->detachBehaviors(); // Remove timestamp behaviour
        return $companyInvitation->save(false);
    }

    /**
     * Converts company invitation accept status from old system to new one
     *
     * @param integer $accepted Old company invitation accept status
     * @return boolean
     */
    private function convertAccepted($accepted)
    {
        return $accepted ? CompanyInvitation::ACCEPTED : CompanyInvitation::NOT_ACCEPTED;
    }
}