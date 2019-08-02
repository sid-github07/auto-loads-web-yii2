<?php

use backend\controllers\ClientController;
use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentFactory;
use common\components\document\DocumentIM;
use common\models\CarTransporter;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\User;
use yii\helpers\Html;

/** @var CarTransporter $carTransporter */
/** @var Company $company */
/** @var array $languages */

?>
<div class="C-T-131 car-transporter-code">
    <?php echo "#{$carTransporter->code}"; ?>
</div>

<div class="car-transporter-info">
    <?php echo Yii::t('element', 'C-T-8b') . ": {$carTransporter->quantity}"; ?>
</div>

<div class="car-transporter-info">
    <div>
        <span class="C-T-122">
            <?php echo Yii::t('element', 'C-T-8e') . ':'; ?>
        </span>
        <span class="C-T-123">
            <?php echo $company->getTitleByType(); ?>
        </span>
    </div>

    <div class="C-T-124">
        <?php echo Yii::t('element', 'C-T-8f') . ':'; ?>
    </div>

    <div>
        <span class="C-T-125">
            <?php echo Yii::t('element', 'C-T-8g') . ':'; ?>
        </span>
        <span class="C-T-126">
            <?php echo $carTransporter->user->phone; ?>
        </span>
    </div>

    <div>
        <span class="C-T-127">
            <?php echo Yii::t('element', 'C-T-8h') . ':'; ?>
        </span>
        <a href="mailto:<?php echo $carTransporter->user->email; ?>" class="C-T-128">
            <?php echo $carTransporter->user->email; ?>
        </a>
    </div>

    <div>
        <span class="C-T-129">
            <?php echo Yii::t('element', 'C-T-8i') . ':'; ?>
        </span>
        <span class="C-T-130">
            <?php foreach ($languages as $language) {
                echo $language;
            } ?>
        </span>
    </div>
    <?php $user = $carTransporter->user; ?>
        <?php if (!empty($user) && ($user->class == User::CARRIER || $user->class == User::MINI_CARRIER)) { ?>
            <?php $currentCompany = $carTransporter->user->companies; ?>
            <?php $company = Company::getCompany(current($currentCompany)->owner_id); ?>
            <?php if (!empty(current($company)) && !empty($company->getDocuments())) { ?>
                <?php foreach ($company->getDocuments() as $document) { ?>
                    <div class="document-info document-<?php echo 'cmr'; ?>-info clearfix col-md-4">
                        <?php switch($document->type) { 
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
                            'client/download-document',
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
                            'client/download-document',
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
                <?php } ?>
            <?php } ?> 
        <?php } ?>
</div>