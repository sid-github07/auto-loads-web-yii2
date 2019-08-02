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
    ],
]);

    if (!empty($company->getDocuments())) {
        /** @var CompanyDocument $document */
        foreach ($company->getDocuments() as $document) {
            if ($document->type === CompanyDocument::IM) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'im',
                    'currentDocument' => new DocumentIM(),
                    'endDateId' => 'N-C-39g',
                    'type' => DocumentFactory::IM,
                    'downloadId' => 'N-C-39h',
                    'updateId' => 'N-C-39',
                    'removeId' => 'N-C-39i',
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
        'fileInputId' => 'N-C-39a',
        'browseId' => 'N-C-39b',
        'removeId' => 'N-C-39c',
        'removeTitle' => 'N-C-39d',
        'validationMessageId' => 'DOCUMENT_IM_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateIM',
        'dateInputId' => 'N-C-39e',
        'submitButtonId' => 'N-C-39f',
    ]);

Pjax::end();