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
    ],
]);

    if (!empty($company->getDocuments())) {
        /** @var CompanyDocument $document */
        foreach ($company->getDocuments() as $document) {
            if ($document->type === CompanyDocument::EU) {
                $showForm = false;
                echo Yii::$app->controller->renderPartial('document/____info', [
                    'document' => $document,
                    'classType' => 'eu',
                    'currentDocument' => new DocumentEU(),
                    'endDateId' => 'N-C-37g',
                    'type' => DocumentFactory::EU,
                    'downloadId' => 'N-C-37h',
                    'updateId' => 'N-C-37',
                    'removeId' => 'N-C-37i',
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
        'fileInputId' => 'N-C-37a',
        'browseId' => 'N-C-37b',
        'removeId' => 'N-C-37c',
        'removeTitle' => 'N-C-37d',
        'validationMessageId' => 'DOCUMENT_EU_MSG_VALIDATION_ERROR',
        'dateAttribute' => 'dateEU',
        'dateInputId' => 'N-C-37e',
        'submitButtonId' => 'N-C-37f',
    ]);

Pjax::end();