<?php

use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentFactory;
use common\components\document\DocumentIM;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\Language;
use common\models\LoadCar;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use common\models\Load;

/**
 * @var Load $load
 */

?>

    <div class="search-results-load-code">
        <?php echo '#' . $load->code; ?>
    </div>

<?php if ($showLoadInfo): ?>
    <div class="load-info">
        <?php echo LoadCar::getLoadInfo($load); ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->user->isGuest): ?>
    <div>
        <div class="text-center not-logged-in-preview">
            <?php echo Yii::t('element', 'IA-C-90'); ?>
        </div>

        <div class="text-center">
            <a href="<?php echo Url::to(['site/login', 'lang' => Yii::$app->language]); ?>"
               class="primary-button search-load-sign-in-btn"
            >
                <i class="fa fa-sign-in" aria-hidden="true"></i> <?php echo Yii::t('element', 'IA-C-91'); ?>
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="load-preview-modal-content-wrapper">
        <div>
            <span id="IA-C-60">
                <?php if (!empty($load->user)) {
                    echo Yii::t('element', 'IA-C-60') . ':';
                } ?>
                <?php ?>
            </span>
            <span id="IA-C-61">
                <?php
                if (empty($load->user)) {
                    echo '';
                } else {
                    $company = $load->user->companies;
                    echo empty($load->user->companies) ? '' : current($company)->getTitleByType();
                }
                ?>
            </span>
        </div>

        <div id="IA-C-63">
            <?php echo Yii::t('element', 'IA-C-63') . ':'; ?>
        </div>

        <div>
            <span><?php echo Yii::t('element', 'IA-C-64a'); ?></span>
            <span id="IA-C-64">
                <?php
                if (empty($load->user)) {
                    echo $load->phone;
                } else {
                    echo $load->user->phone;
                }
                ?>
            </span>
        </div>

        <div>
            <?php
            if (empty($load->user)) {
                $email = $load->email;
            } else {
                $email = $load->user->email;
            }
            ?>
            <span><?php echo Yii::t('element', 'IA-C-65a'); ?></span>
            <a id="IA-C-65" href="mailto:<?php echo $email ?>">
                <?php echo $email ?>
            </a>
        </div>

        <div>
            <?php if (!empty($load->user)): ?>
                <span id="KP-C-4"><?php echo Yii::t('element', 'KP-C-4'); ?></span>
            <?php endif; ?>
            <span id="KP-C-5">
                <?php
                if (empty($load->user)) {
                    echo '';
                } else {
                    foreach (Language::getUserSelectedLanguages($load->user->id) as $language) {
                        echo $language;
                    }
                }
                ?>
            </span>
        </div>

        <?php $user = $load->user; ?>
        <?php if (!empty($user) && ($user->class == User::CARRIER || $user->class == User::MINI_CARRIER)) { ?>
            <?php $currentCompany = $load->user->companies; ?>
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

        <div class="btn-wrapper clearfix">
            <button
                    id="L-T-2"
                    class="flat-btn map-button"
                    data-rendered="false"
                    data-map-open="false"
                    data-load="<?php echo $load->id ?>"
            >
                <i class="btn-icon fa fa-plus-circle"></i>
                <span class="btn-text"><?php echo Yii::t('element', 'Open Map'); ?></span>
            </button>
            <div class="map-container"></div>
            <?php echo Yii::t('element', 'KP-C-6') . ': '; ?>
            <b><span id="distance-gdirectionsService<?php echo $load->id; ?>"></span></b>
            <?php echo ' km'; ?>
        </div>

        <span id="IA-C-68" class="search-results-load-code">
            <?php echo '#' . $load->code; ?>
        </span>

    </div>
<?php endif; ?>
<?php
$this->registerJs('var distance = 0;', View::POS_BEGIN);