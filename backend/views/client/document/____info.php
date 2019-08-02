<?php

use common\components\document\DocumentCMR;
use common\components\document\DocumentEU;
use common\components\document\DocumentIM;
use common\models\CompanyDocument;
use yii\bootstrap\Html;

/** @var CompanyDocument $document */
/** @var string $classType */
/** @var DocumentCMR|DocumentEU|DocumentIM $currentDocument */
/** @var string $endDateId */
/** @var string $type */
/** @var string $downloadId */
/** @var string $updateId */
/** @var string $removeId */

?>

<div class="document-info document-<?php echo $classType; ?>-info clearfix">
    <i class="fa fa-file-pdf-o"></i>
    
    <div class="document-file-name">
        <?php echo $currentDocument->getName() . '.' . $document->extension; ?>
    </div>
    
    <div class="document-end-date">
        <?php echo Yii::t('element', $endDateId) . ': ' . date('Y-m-d', $document->date); ?>
    </div>
    
    <div class="document-action-buttons">
        <?php echo Html::a(Yii::t('element', $downloadId), [
            'client/download-document',
            'lang' => Yii::$app->language,
            'type' => $type,
            'companyId' => $companyId,
        ], [
            'id' => $downloadId,
            'class' => 'download-link',
            'target' => '_blank',
            'data-pjax' => '0', // NOTE: this is required in order to open document in new tab
        ]); ?>

        <span id="<?php echo $updateId; ?>" class="document-update">
            <?php echo Yii::t('element', $updateId); ?>
        </span>
    </div>
</div>