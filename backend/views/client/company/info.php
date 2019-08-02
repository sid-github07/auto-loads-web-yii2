<?php

use backend\controllers\ClientController;
use common\models\Company;
use common\models\CompanyDocument;
use kartik\icons\Icon;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

/** @var Company $company */
/** @var string $activeVatRateLegalCountryCode */
/** @var array $vatRateCountries */
/** @var string $cityLegal */
/** @var array $phoneNumbers */
/** @var string $activePhoneNumber */
/** @var CompanyDocument $companyDocument */
/** @var string $tab */

?>
<div class="settings-tabs-container">
    <?php echo Tabs::widget([
        'navType' => 'nav-tabs nav-justified tabs-navigation',
        'encodeLabels' => false,
        'items' => [
            [
                'label' => Icon::show('address-card', [], Icon::FA) .
                           Html::tag('span', Yii::t('element', 'A-M-19'), ['class' => 'tab-label-text']),
                'content' => Yii::$app->controller->renderPartial('/client/company/edit/info', [
                    'company' => $company,
                    'activeVatRateLegalCountryCode' => $activeVatRateLegalCountryCode,
                    'vatRateCountries' => $vatRateCountries,
                    'cityLegal' => $cityLegal,
                    'phoneNumbers' => $phoneNumbers,
                    'activePhoneNumber' => $activePhoneNumber,
                ]),
                'active' => $tab === ClientController::TAB_COMPANY_INFO,
                'linkOptions' => [
                    'id' => 'A-M-19',
                    'class' => 'change-company-info settings-tab',
                    'title' => Yii::t('element', 'A-M-19'),
                    'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_INFO . '")',
                ],
            ],
            [
                'label' => Icon::show('file-o', [], Icon::FA) .
                           Html::tag('span', Yii::t('element', 'A-M-20'), ['class' => 'tab-label-text']),
                'content' => Yii::$app->controller->renderPartial('/client/company/edit/document', [
                    'company' => $company,
                    'companyDocument' => $companyDocument,
                ]),
                'active' => $tab === ClientController::TAB_COMPANY_DOCUMENTS,
                'linkOptions' => [
                    'id' => 'A-M-20',
                    'class' => 'company-users settings-tab',
                    'title' => Yii::t('element', 'A-M-20'),
                    'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_DOCUMENTS . '")',
                ],
            ]
        ]
    ]); ?>
</div>