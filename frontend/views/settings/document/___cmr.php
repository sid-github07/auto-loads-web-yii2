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
    ],
]);

    if (!empty($company->getDocuments())) {
        /** @var CompanyDocument $document */
        foreach ($company->getDocuments() as $document) {
            if ($document->type === CompanyDocument::CMR) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'cmr',
                    'currentDocument' => new DocumentCMR(),
                    'endDateId' => 'N-C-34',
                    'type' => DocumentFactory::CMR,
                    'downloadId' => 'N-C-35',
                    'updateId' => 'N-C-33',
                    'removeId' => 'N-C-33g',
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
        'fileInputId' => 'N-C-33a',
        'browseId' => 'N-C-33b',
        'removeId' => 'N-C-33c',
        'removeTitle' => 'N-C-33d',
        'validationMessageId' => 'DOCUMENT_CMR_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateCMR',
        'dateInputId' => 'N-C-33e',
        'submitButtonId' => 'N-C-33f',
    ]);

Pjax::end();