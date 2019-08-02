<?php

use common\models\CompanyDocument;
use dosamigos\datepicker\DatePicker;
use dosamigos\fileinput\BootstrapFileInput;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var boolean $showForm */
/** @var string $classType */
/** @var string $type */
/** @var ActiveForm $form */
/** @var CompanyDocument $companyDocument */
/** @var string $fileAttribute */
/** @var string $fileInputId */
/** @var string $browseId */
/** @var string $removeId */
/** @var string $removeTitle */
/** @var string $validationMessageId */
/** @var string $dateAttribute */
/** @var string $dateInputId */
/** @var string $submitButtonId */

?>

<div class="document-form-container clearfix <?php echo ($showForm ? '' : ' hidden'); ?>">
    <?php if (!$showForm): ?>
        <div class="document-form-close">✕</div>
    <?php endif; ?>
        
    <div class="required-fields-text">
        <?php echo Yii::t('element', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>
        
    <?php $form = ActiveForm::begin([
        'id' => 'document-' . $classType . '-close',
        'action' => ['client/add-document', 'lang' => Yii::$app->language, 'type' => $type],
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'document-form',
        ],
    ]); ?>
        
        <?php echo $form->field($companyDocument, $fileAttribute)->widget(BootstrapFileInput::className(), [
            'options' => [
                'id' => $fileInputId,
                'class' => 'document-file',
                'accept' => CompanyDocument::DOCUMENT_MIME_TYPES,
            ],
            'clientOptions' => [
                'language' => Yii::$app->language,
                'showPreview' => false,
                'showUpload' => false,
                'showCancel' => false,
                'showClose' => false,
                'allowedFileExtensions' => [CompanyDocument::DOCUMENT_EXTENSIONS],
                'browseLabel' => Yii::t('element', $browseId),
                'browseIcon' => Icon::show('folder-open', [], Icon::FA),
                'browseClass' => 'btn primary-button',
                'removeLabel' => Yii::t('element', $removeId),
                'removeIcon' => Icon::show('trash', [], Icon::FA),
                'removeClass' => 'btn secondary-button',
                'removeTitle' => Yii::t('element', $removeTitle),
                'maxFileSize' => CompanyDocument::DOCUMENT_MAX_SIZE_KB,
                'minFileCount' => CompanyDocument::DOCUMENT_MAX_FILES,
                'maxFileCount' => CompanyDocument::DOCUMENT_MAX_FILES,
                // TODO: sutvarkyti klaidos pranešimo rodymą, nes dabar netelpa į input fieldą
                'msgValidationError' => Yii::t('app', $validationMessageId, [
                    'extension' => CompanyDocument::DOCUMENT_EXTENSIONS,
                    'size' => CompanyDocument::convertFileSize(CompanyDocument::DOCUMENT_MAX_SIZE),
                ]),
                'msgValidationErrorClass' => 'text-danger',
                'msgValidationErrorIcon' => Html::icon('exclamation-sign'),
            ],
        ])->label(Yii::t('element', $fileInputId))->hint(Yii::t('document', 'UPLOAD_PDF_FILES_ONLY')); ?>
        
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                <?php echo $form->field($companyDocument, $dateAttribute, [
                    'options' => [
                        'class' => 'form-group field-company-document-date',
                    ]
                ])->widget(DatePicker::className(), [
                    'options' => [
                        'class' => 'form-control document-date',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', $dateInputId)); ?>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <div class="document-submit">
                    <?php echo Html::button(Icon::show('save', [], Icon::FA) . Yii::t('element', $submitButtonId), [
                        'id' => $submitButtonId,
                        'class' => 'primary-button document-submit-btn',
                    ]); ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>