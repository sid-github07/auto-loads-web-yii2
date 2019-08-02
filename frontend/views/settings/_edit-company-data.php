<?php

use common\models\Company;
use common\models\CompanyDocument;
use common\models\User;
use frontend\controllers\SettingsController;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\Tabs;
use yii\widgets\Pjax;

/** @var User $user */
/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var string $subTab */
/** @var array $vatRateCountries */
/** @var string $activeVatRate */
/** @var string $city */
?>

<?php Pjax::begin(['id' => 'document-toastr']); ?>
    <?php echo ToastrFlash::widget([
        'options' => [
            'closeButton' => true,
            'debug' => false,
            'newestOnTop' => true,
            'progressBar' => false,
            'positionClass' => 'toast-top-center',
            'preventDuplicates' => true,
            'showDuration' => 0, // how long it takes to show the alert in milliseconds
            'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
            'timeOut' => 45000, // how long the alert must be visible to user in milliseconds
            'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
            'onShown' => 'function() { ' .
                '$(".alert-container").append($("#toast-container"));' .
            '}',
        ]
    ]); ?>
<?php Pjax::end(); ?>

<?php $contactInfo = Yii::$app->controller->renderPartial('__contact-info', [
    'user' => $user,
    'company' => $company,
    'vatRateCountries' => $vatRateCountries,
    'activeVatRate' => $activeVatRate,
    'city' => $city,
]); ?>

<?php if ($company->ownerList->class === User::CARRIER) {
    echo Tabs::widget([
        'navType' => 'nav-tabs nav-justified tabs-navigation',
        'encodeLabels' => false,
        'items' => [[
            'label' => Icon::show('id-card', [], Icon::FA). '<span class="tab-label-text">' .
            Yii::t('element', 'N-M-5'). '</span>',
            'content' => $contactInfo,
            'active' => $subTab === SettingsController::SUB_TAB_CONTACT_INFO,
            'linkOptions' => [
                'id' => 'N-M-5',
                'class' => 'imprint settings-tab',
            ],
        ], [
            'label' => Icon::show('file', [], Icon::FA). '<span class="tab-label-text">' .
            Yii::t('element', 'N-M-6'). '</span>',
            'content' => Yii::$app->controller->renderPartial('__documents', [
                'company' => $company,
                'companyDocument' => $companyDocument,
            ]),
            'active' => $subTab === SettingsController::SUB_TAB_DOCUMENTS,
            'linkOptions' => [
                'id' => 'N-M-6',
                'class' => 'documents settings-tab',
            ],
        ]],
    ]);
} else {
    echo $contactInfo;
}

