<?php

use common\components\document\DocumentFactory;
use common\components\document\DocumentIM;
use common\models\Company;
use common\models\CompanyDocument;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var ActiveForm $form */

$companyDocument->scenario = CompanyDocument::SCENARIO_CLIENT_IM;

/** @var boolean $showForm Attribute whether to show document form */
$showForm = true;

Pjax::begin([
    'id' => 'document-im',
    'options' => [
        'class' => 'document-pjax',
        'data-document-type' => 'im',
        'data-company-id' => $company->id,
    ],
]);

    if (!empty($company->companyDocuments)) {
        /** @var CompanyDocument $document */
        foreach ($company->companyDocuments as $document) {
            if ($document->type === CompanyDocument::IM) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'im',
                    'currentDocument' => new DocumentIM($company->owner_id),
                    'endDateId' => 'A-C-96g',
                    'type' => DocumentFactory::IM,
                    'downloadId' => 'A-C-96h',
                    'updateId' => 'A-C-96',
                    'removeId' => 'A-C-96i',
                    'companyId' => $company->owner_id,
                ]);
            }
        }
    }

    echo Yii::$app->controller->renderPartial('document/____form', [
        'showForm' => $showForm,
        'classType' => 'im',
        'type' => DocumentFactory::IM,
        'companyDocument' => $companyDocument,
        'fileAttribute' => 'im',
        'fileInputId' => 'A-C-96a',
        'browseId' => 'A-C-96b',
        'removeId' => 'A-C-96c',
        'removeTitle' => 'A-C-96d',
        'validationMessageId' => 'DOCUMENT_IM_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateIM',
        'dateInputId' => 'A-C-96e',
        'submitButtonId' => 'A-C-96f',
        'ownerId' => $company->owner_id,
    ]);

Pjax::end();