<?php

use common\models\Company;
use common\models\CompanyDocument;
use common\models\User;
use frontend\controllers\SettingsController;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrAsset;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var User $user */
/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var string $tab */
/** @var string $subTab */
/** @var array $languages */
/** @var array $vatRateCountries */
/** @var string $activeVatRate */
/** @var string $city */

ToastrAsset::register($this);
Icon::map($this, Icon::FA);
$this->title = Yii::t('seo', 'TITLE_SETTINGS');
?>
<div class="settings-index">
    <div class="settings-headline clearfix">
        <h1 id="N-M-1">
            <?php echo Yii::t('element', 'N-M-1'); ?>
        </h1>

        <?php if ($company->isOwner()): ?>
            <a href="<?php echo Url::to(['settings/invitation', 'lang' => Yii::$app->language]); ?>"
               id="V-C-66m"
               class="primary-button send-invitation">
                <?php echo Icon::show('envelope', '', Icon::FA) . Yii::t('element', 'V-C-66m'); ?>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="settings-tabs-container">
        <?php echo Tabs::widget([
            'navType' => 'nav-tabs nav-justified tabs-navigation',
            'encodeLabels' => false,
            'items' => [[
                'label' => Icon::show('user-o', [], Icon::FA). '<span class="tab-label-text">' .
                Yii::t('element', 'N-M-2'). '</span>',
                'content' => Yii::$app->controller->renderPartial('_edit-my-data', [
                    'user' => $user,
                    'languages' => $languages,
                ]),
                'active' => $tab === SettingsController::TAB_EDIT_MY_DATA,
                'linkOptions' => [
                    'id' => 'N-M-2',
                    'class' => 'change-my-data settings-tab',
                    'title' => Yii::t('element', 'N-M-2'),
                ],
            ], [
                'label' => Icon::show('pencil-square-o', [], Icon::FA). '<span class="tab-label-text">' .
                Yii::t('element', 'N-M-3', [
                    'text' => (
                        $user->account_type == User::NATURAL ?
                            Yii::t('element', 'N-M-3a') :
                            Yii::t('element', 'N-M-3b')
                    ),
                ]). '</span>',
                'content' => Yii::$app->controller->renderPartial('_edit-company-data', [
                    'user' => $user,
                    'company' => $company,
                    'companyDocument' => $companyDocument,
                    'subTab' => $subTab,
                    'vatRateCountries' => $vatRateCountries,
                    'activeVatRate' => $activeVatRate,
                    'city' => $city,
                ]),
                'active' => $tab === SettingsController::TAB_EDIT_COMPANY_DATA,
                'linkOptions' => [
                    'id' => 'N-M-3',
                    'class' => 'change-data settings-tab',
                    'title' => Yii::t('element', 'N-M-3a'),
                ],
            ], [
                'label' => Icon::show('lock', [], Icon::FA). '<span class="tab-label-text">' .
                Yii::t('element', 'N-M-4'). '</span>',
                'content' => Yii::$app->controller->renderPartial('_change-password', [
                    'user' => $user,
                ]),
                'active' => $tab === SettingsController::TAB_CHANGE_PASSWORD,
                'linkOptions' => [
                    'id' => 'N-M-4',
                    'class' => 'change-password settings-tab',
                    'title' => Yii::t('element', 'N-M-4'),
                ],
            ]],
        ]); ?>
    </div>
</div>

<?php
$this->registerJs('var vatCodeInputIds = []; ', View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/settings/index.js', ['depends' => [JqueryAsset::className()]]);