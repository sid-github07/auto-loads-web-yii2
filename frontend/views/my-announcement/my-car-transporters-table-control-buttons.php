<?php

use common\models\CarTransporter;
use yii\helpers\Html;

/** @var string $id */
/** @var string $name */

$name = 'car-transporter-per-page';
?>
<div class="my-car-transporters-table-control-buttons">
    <div class="paginator-options">
        <span class="posts-per-page-select">
            <label class="C-T-44 C-T-63 page-size-filter-label">
                <?php echo Yii::t('element', 'C-T-44'); ?>
            </label>
            <?php echo Html::dropDownList($name, Yii::$app->request->get($name), CarTransporter::getPageSizes(), [
                'id' => $id,
                'class' => 'C-T-45 C-T-64',
                'onchange' => 'changeCarTransporterPageSize(this)',
            ]); ?>
        </span>
    </div>

    <div class="control-buttons-selections">
        <button class="C-T-40 C-T-59 secondary-button grid-view-control-btn" onclick="makeCarTransporterInvisible(event, null)">
            <?php echo Yii::t('element', 'C-T-40'); ?>
        </button>
        <button class="C-T-41 C-T-60 secondary-button grid-view-control-btn" onclick="makeCarTransporterVisible(event, null)">
            <?php echo Yii::t('element', 'C-T-41'); ?>
        </button>
        <button class="C-T-42 C-T-61 secondary-button grid-view-control-btn" onclick="removeCarTransporters(event, null)">
            <?php echo Yii::t('element', 'C-T-42'); ?>
        </button>
        <button class="C-T-43 C-T-62 primary-button grid-view-control-btn" onclick="renderCarTransporterAnnouncementForm()">
            <?php echo Yii::t('element', 'C-T-43'); ?>
        </button>
    </div>
</div>
