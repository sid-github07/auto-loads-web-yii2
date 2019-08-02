<?php

use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentFactory;
use common\components\document\DocumentIM;
use common\models\CarTransporter;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\User;
use yii\helpers\Html;

/**
 * @var CarTransporter $carTransporter
 * @var boolean $showInfo
 * @var Company $company
 * @var array $languages
 */

?>
<div class="search-results-load-code">
    <?php echo '#' . $carTransporter->code; ?>
</div>

<?php if ($showInfo === 'true'): ?>
    <div class="load-info">
        <?php if (is_null($carTransporter->quantity)): ?>
            <?php echo Yii::t('element', 'C-T-8b') . ': ' . Yii::t('element', 'C-T-17a'); ?>
        <?php endif; ?>
        <?php if (!is_null($carTransporter->quantity)): ?>
            <?php echo Yii::t('element', 'C-T-8b') . ': ' . $carTransporter->quantity; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="load-info">
    <?php if (!Yii::$app->user->isGuest || $boughtByCreditCode) : ?>
        <div>
            <span><?php echo Yii::t('element', 'C-T-8e') . ':'; ?></span>
            <span><?php echo $company->getTitleByType(); ?></span>
        </div>
    <?php endif; ?>

    <div><?php echo Yii::t('element', 'C-T-8f') . ':'; ?></div>

    <div>
        <span><?php echo Yii::t('element', 'C-T-8g') . ':'; ?></span>
        <span><?php echo $carTransporter->user->phone; ?></span>
    </div>
    <?php if (!Yii::$app->user->isGuest || $boughtByCreditCode) : ?>
        <div>
            <span><?php echo Yii::t('element', 'C-T-8h') . ':'; ?></span>
            <a href="mailto:<?php echo $carTransporter->user->email; ?>">
                <?php echo $carTransporter->user->email; ?>
            </a>
        </div>
    <?php endif; ?>
    <div>
        <span><?php echo Yii::t('element', 'C-T-8i') . ':'; ?></span>
        <span>
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
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <div style="margin-top: 15px" class="clearfix">
        <button
                id="L-T-2"
                class="flat-btn map-button"
                data-rendered="false"
                data-map-open="false"
                data-transporter="<?php echo $carTransporter->id ?>"
        >
            <i class="btn-icon fa fa-plus-circle"></i>
            <span class="btn-text"><?php echo Yii::t('element', 'Open Map'); ?></span>
        </button>
        <div class="map-container"></div>
        <?php echo Yii::t('element', 'KP-C-6') . ': '; ?>
        <b><span id="distance-gdirectionsService<?php echo $carTransporter->id; ?>"></span></b>
        <?php echo ' km'; ?>
    </div>
</div>