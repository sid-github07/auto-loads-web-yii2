<?php

use common\models\Load;
use yii\helpers\Html;

/** @var string $id */

?>
<div class="load-index-control-buttons">
    <div class="paginator-options">
        <span class="posts-per-page-select">
            <label class="page-size-filter-label">
                <?php echo Yii::t('element', 'MK-C-12'); ?>
            </label>
            <?php echo Html::dropDownList('load-per-page', Yii::$app->request->get('load-page'), Load::getPageSizes(), [
                'id' => $id,
                'class' => 'MK-C-13',
                'onchange' => 'changeLoadPageSize(this)',
            ]); ?>
        </span>
    </div>

    <div class="control-buttons-selections">
        <button class="MK-C-8 secondary-button grid-view-control-btn" onclick="makeLoadsInvisible(event, null)">
            <?php echo Yii::t('element', 'MK-C-8'); ?>
        </button>
        <button class="MK-C-9 secondary-button grid-view-control-btn" onclick="makeLoadsVisible(event, null)">
            <?php echo Yii::t('element', 'MK-C-9') ?>
        </button>
        <button class="MK-C-10 secondary-button grid-view-control-btn" onclick="removeLoads(event, null)">
            <?php echo Yii::t('element', 'MK-C-10'); ?>
        </button>
        <button class="MK-C-11 primary-button grid-view-control-btn" onclick="renderLoadAnnouncementForm()">
            <?php echo Yii::t('element', 'MK-C-11'); ?>
        </button>
    </div>
</div>
