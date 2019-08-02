<?php

use backend\controllers\ClientController;
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
/** @var array|Language[] $languages */

?>
<?php if (is_null($load->user)): ?>
    <div id="A-C-344">
        <?php echo Yii::t('element', 'A-C-344') . ':'; ?>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'A-C-345'); ?></span>
        <span id="A-C-345">
            <?php echo $load->phone; ?>
        </span>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'A-C-346'); ?></span>
        <a id="A-C-346" href="mailto:<?php echo $load->email; ?>">
            <?php echo $load->email; ?>
        </a>
    </div>

    <span id="A-C-357" class="search-results-load-code">
        <?php echo '#' . $load->code; ?>
    </span>
<?php else: ?>
    <div class="load-preview-content-heading">
        <span id="A-C-341"><?php echo Yii::t('element', 'A-C-341') . ':'; ?></span>
        <span id="A-C-342">
            <?php
                $company = $load->user->companies;
                if (empty($company)) {
                    echo $load->user->companyUser->company->getTitleByType();
                } else {
                    echo current($company)->getTitleByType();
                }
            ?>
        </span>
    </div>

    <div id="A-C-344" class="load-preview-contacts-heading">
        <?php echo Yii::t('element', 'A-C-344') . ':'; ?>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'A-C-345'); ?></span>
        <span id="A-C-345">
            <?php echo $load->user->phone; ?>
        </span>
    </div>

    <div>
        <span><?php echo Yii::t('element', 'A-C-346'); ?></span>
        <a id="A-C-346" href="mailto:<?php echo $load->user->email; ?>">
            <?php echo $load->user->email; ?>
        </a>
    </div>

    <div>
        <span id="A-C-347"><?php echo Yii::t('element', 'A-C-347') . ':'; ?></span>
        <span id="A-C-348">
            <?php foreach ($languages as $language): ?>
                <span>
                    <i class="flag-icon flag-icon-<?php echo strtolower($language->country_code); ?>"></i>
                    <?php echo $language->name; ?>
                </span>
                <span class="separator">, </span>
            <?php endforeach; ?>
        </span>
    </div>

    <?php $user = $load->user; ?>
	<?php $currentCompany = $load->user->companies; ?>
        <?php if (!empty($user) && ($user->class == User::CARRIER || $user->class == User::MINI_CARRIER)) { ?>
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

    <span id="A-C-357" class="search-results-load-code">
        <?php echo '#' . $load->code; ?>
    </span>
<?php endif; ?>