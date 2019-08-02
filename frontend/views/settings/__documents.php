<?php

use common\models\Company;
use common\models\CompanyDocument;

/** @var Company $company */
/** @var CompanyDocument $companyDocument */
?>
<div class="document-cmr-container document-container">
    <h2 id="N-C-32" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'N-C-32')); ?>
    </h2>
    <?php echo Yii::$app->controller->renderPartial('document/___cmr', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>

<div class="document-eu-container document-container">
    <h2 id="N-C-36" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'N-C-36')); ?>
    </h2>
    <?php echo Yii::$app->controller->renderPartial('document/___eu', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>

<div class="document-im-container document-container">
    <h2 id="N-C-38" class="document-title">
        <?php echo strtoupper(Yii::t('element', 'N-C-38')); ?>
    </h2>
    <?php echo Yii::$app->controller->renderPartial('document/___im', [
        'company' => $company,
        'companyDocument' => $companyDocument,
    ]); ?>
</div>
