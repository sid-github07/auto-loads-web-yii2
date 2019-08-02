<?php

use common\models\Company;
use common\models\CompanyDocument;
use odaialali\yii2toastr\ToastrFlash;
use yii\widgets\Pjax;

/** @var Company $company */
/** @var CompanyDocument $companyDocument */
?>

<?php Pjax::begin(['id' => 'document-toastr']);
echo ToastrFlash::widget([
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
            '$(".main-alert").append($("#toast-container"));' .
            '}',
    ]
]);
Pjax::end(); ?>

<div class="document-container">
    <h4 id="A-C-89" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'A-C-89')); ?>
    </h4>
    <?php echo Yii::$app->controller->renderPartial('document/___cmr', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>

<div class="document-container">
    <h4 id="A-C-93" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'A-C-93')); ?>
    </h4>
    <?php echo Yii::$app->controller->renderPartial('document/___eu', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>

<div class="document-container">
    <h4 id="A-C-95" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'A-C-95')); ?>
    </h4>
    <?php echo Yii::$app->controller->renderPartial('document/___im', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>

