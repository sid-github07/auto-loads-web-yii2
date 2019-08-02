<?php

use common\components\document\DocumentEU;
use common\components\document\DocumentFactory;
use common\models\Company;
use common\models\CompanyDocument;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var ActiveForm $form */

$companyDocument->scenario = CompanyDocument::SCENARIO_CLIENT_EU;

/** @var boolean $showForm Attribute whether to show document form */
$showForm = true;

Pjax::begin([
    'id' => 'document-eu',
    'options' => [
        'class' => 'document-pjax',
        'data-document-type' => 'eu',
        'data-company-id' => $company->id,
    ],
]);

    if (!empty($company->companyDocuments)) {
        /** @var CompanyDocument $document */
        foreach ($company->companyDocuments as $document) {
            if ($document->type === CompanyDocument::EU) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'eu',
                    'currentDocument' => new DocumentEU($company->owner_id),
                    'endDateId' => 'A-C-94g',
                    'type' => DocumentFactory::EU,
                    'downloadId' => 'A-C-94h',
                    'updateId' => 'A-C-94',
                    'removeId' => 'A-C-94i',
                    'companyId' => $company->owner_id,
                ]);
            }
        }
    }

    echo Yii::$app->controller->renderPartial('document/____form', [
        'showForm' => $showForm,
        'classType' => 'eu',
        'type' => DocumentFactory::EU,
        'companyDocument' => $companyDocument,
        'fileAttribute' => 'eu',
        'fileInputId' => 'A-C-94a',
        'browseId' => 'A-C-94b',
        'removeId' => 'A-C-94c',
        'removeTitle' => 'A-C-94d',
        'validationMessageId' => 'DOCUMENT_EU_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateEU',
        'dateInputId' => 'A-C-94e',
        'submitButtonId' => 'A-C-94f',
        'ownerId' => $company->owner_id,
    ]);

Pjax::end();