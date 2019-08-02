<?php

use common\components\document\DocumentCMR;
use common\components\document\DocumentFactory;
use common\models\Company;
use common\models\CompanyDocument;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var ActiveForm $form */

$companyDocument->scenario = CompanyDocument::SCENARIO_CLIENT_CMR;

/** @var boolean $showForm Attribute whether to show document form */
$showForm = true;

Pjax::begin([
    'id' => 'document-cmr',
    'options' => [
        'class' => 'document-pjax',
        'data-document-type' => 'cmr',
        'data-company-id' => $company->id,
    ],
]);

    if (!empty($company->companyDocuments)) {
        /** @var CompanyDocument $document */
        foreach ($company->companyDocuments as $document) {
            if ($document->type === CompanyDocument::CMR) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'cmr',
                    'currentDocument' => new DocumentCMR($company->owner_id),
                    'endDateId' => 'A-C-91',
                    'type' => DocumentFactory::CMR,
                    'downloadId' => 'A-C-92',
                    'updateId' => 'A-C-90',
                    'removeId' => 'A-C-90g',
                    'companyId' => $company->owner_id,
                ]);
            }
        }
    }

    echo Yii::$app->controller->renderPartial('document/____form', [
        'showForm' => $showForm,
        'classType' => 'cmr',
        'type' => DocumentFactory::CMR,
        'companyDocument' => $companyDocument,
        'fileAttribute' => 'cmr',
        'fileInputId' => 'A-C-90a',
        'browseId' => 'A-C-90b',
        'removeId' => 'A-C-90c',
        'removeTitle' => 'A-C-90d',
        'validationMessageId' => 'DOCUMENT_CMR_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateCMR',
        'dateInputId' => 'A-C-90e',
        'submitButtonId' => 'A-C-90f',
    ]);

Pjax::end();