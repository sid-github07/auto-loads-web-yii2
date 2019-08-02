<?php

use common\models\User;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\Language;
use common\components\document\DocumentFactory;
use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentIM;
use common\components\helpers\Html;

/**
 * @var User $user
 */

?>

<div class="load-preview-modal-content-wrapper">
    <div id="IA-C-63">
        <span>
            <?php echo Yii::t('element', 'IA-C-60') . ':'; ?>
        </span>
        <span>
            <?php echo $user->getCompany()->getTitleByType(); ?>
        </span>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'IA-C-64a'); ?>: </span>
        <span id="IA-C-64">
            <?php echo $user->phone; ?>
        </span>
    </div>
    <div>
        <span id="KP-C-4"><?php echo Yii::t('element', 'KP-C-4'); ?> </span>
        <span id="KP-C-5">
                <?php
                foreach (Language::getUserSelectedLanguages($user->id) as $language) {
                    echo $language;
                }
                ?>
            </span>
    </div>

    <?php if ($user->class == User::CARRIER || $user->class == User::MINI_CARRIER) : ?>
        <?php $currentCompany = $user->companies; ?>
        <?php $company = Company::getCompany(current($currentCompany)->owner_id); ?>
        <?php if (!empty(current($company)) && !empty($company->getDocuments())) : ?>
            <?php foreach ($company->getDocuments() as $document) : ?>
                <div class="document-info document-<?php echo 'cmr'; ?>-info clearfix col-md-4">
                    <?php switch ($document->type) {
                        case CompanyDocument::CMR:
                            $currentDocument = new DocumentCMR($company->owner_id);
                            $type = DocumentFactory::CMR;
                            break;
                        case CompanyDocument::EU:
                            $currentDocument = new DocumentEU($company->owner_id);
                            $type = DocumentFactory::EU;
                            break;
                        case CompanyDocument::IM:
                            $currentDocument = new DocumentIM($company->owner_id);
                            $type = DocumentFactory::IM;
                            break;
                    } ?>


                    <?php echo Html::a(Html::tag('i', '', ['class' => 'fa fa-file-pdf-o']), [
                        'settings/download-document',
                        'lang' => Yii::$app->language,
                        'type' => $type,
                        'companyId' => $company->owner_id,
                    ], [
                        'id' => 'N-C-35',
                        'target' => '_blank',
                        'data-pjax' => '0', // NOTE: this is required in order to open document in new tab
                    ]); ?>
                    <div class="document-file-name">
                        <?php echo $currentDocument->getName() . '.' . $document->extension; ?>
                    </div>

                    <div class="document-end-date">
                        <?php echo Yii::t('element', 'N-C-34') . ': ' . date('Y-m-d', $document->date); ?>
                    </div>
                    <?php echo Html::a(Yii::t('element', 'N-C-35'), [
                        'settings/download-document',
                        'lang' => Yii::$app->language,
                        'type' => $type,
                        'companyId' => $company->owner_id,
                    ], [
                        'id' => 'N-C-35',
                        'class' => 'download-link',
                        'target' => '_blank',
                        'data-pjax' => '0', // NOTE: this is required in order to open document in new tab
                    ]); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

