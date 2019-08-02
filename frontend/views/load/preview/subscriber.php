<?php

use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentFactory;
use common\components\document\DocumentIM;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\Language;
use common\models\Load;
use common\models\User;
use yii\helpers\Html;

/** @var Load $load */

if (is_null($load->user)): ?>
    <div id="IA-C-63">
        <p><?php echo Yii::t('element', 'IA-C-63') . ':'; ?></p>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'IA-C-64a'); ?></span>
        <span id="IA-C-64">
            <?php echo $load->phone; ?>
        </span>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'IA-C-65a'); ?></span>
        <a id="IA-C-65" href="<?php echo $load->email; ?>">
            <?php echo $load->email; ?>
        </a>
    </div>

    <span id="IA-C-68" class="search-results-load-code">
        <?php echo '#' . $load->code; ?>
    </span>
<?php else: ?>
    <div class="load-preview-content-heading">
        <p>
            <span id="IA-C-60">
                <?php echo Yii::t('element', 'IA-C-60') . ':'; ?>
            </span>
            <span id="IA-C-61">
                <?php
                $company = $load->user->companies;
                if (empty($company)) {
                    echo $load->user->companyUser->company->getTitleByType();
                } else {
                    echo current($company)->getTitleByType();
                }
                ?>
            </span>
        </p>
    </div>

    <div id="IA-C-63">
        <p><?php echo Yii::t('element', 'IA-C-63') . ':'; ?></p>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'IA-C-64a'); ?></span>
        <span id="IA-C-64">
            <?php echo $load->user->phone; ?>
        </span>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'IA-C-65a'); ?></span>
        <a id="IA-C-65" href="mailto:<?php echo $load->user->email; ?>">
            <?php echo $load->user->email; ?>
        </a>
    </div>

    <div>
        <span id="KP-C-4"><?php echo Yii::t('element', 'KP-C-4'); ?></span>
        <span id="KP-C-5">
            <?php foreach(Language::getUserSelectedLanguages($load->user->id) as $language) {
                echo $language;
            } ?>
        </span>
    </div>

    <?php $user = $load->user; ?>
        <?php if (!empty($user) && ($user->class == User::CARRIER || $user->class == User::MINI_CARRIER)) { ?>
            <?php $currentCompany = $load->user->companies; ?>
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
                            'settings/download-document',
                            'lang' => Yii::$app->language,
                            'type' => $type,
                            'companyId' => $company->owner_id,
                        ], [
                            'id' => 'N-C-35',
                            'target' => '_blank',
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
                        ]); ?>
                </div>
                <?php } ?>
            <?php } ?> 
        <?php } ?>

    <span id="IA-C-68" class="search-results-load-code">
        <?php echo '#' . $load->code; ?>
    </span>
<?php endif; ?>