<?php

use common\models\Load;
use common\models\LoadPreview;
use common\components\PotentialHaulier;

/**
 * @var null|Load $load
 * @var LoadPreview $preview
 */

$potentialHaulier = new PotentialHaulier($load);

?>

<div class="table-responsive custom-table">
    <table class="table load-previews-table">
        <thead>
        <tr>
            <th id="A-C-303">
                <?php echo Yii::t('element', 'A-C-303'); ?>
            </th>
            <th id="A-C-305">
                <?php echo Yii::t('element', 'A-C-305'); ?>
            </th>
            <th id="A-C-307">
                <?php echo Yii::t('element', 'A-C-307'); ?>
            </th>
            <th id="A-C-309">
                <?php echo Yii::t('element', 'A-C-309'); ?>
            </th>
            <th id="A-C-310">
                <?php echo Yii::t('element', 'A-C-310'); ?>
            </th>
            <th id="A-C-311">
                <?php echo Yii::t('element', 'A-C-311'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($potentialHaulier->getPotentialHauliersPreviews() as $info): ?>
            <tr>
                <td class="A-C-304">
                    <?php echo $info['created_at'] ?>
                </td>
                <td class="A-C-306">
                    <?php echo $info['user_id'] ?>
                </td>
                <td class="A-C-308">
                    <?php echo $info['company'] ?>
                </td>
                <td class="A-C-309">
                    <?php echo $info['ip'] ?>
                </td>
                <td class="A-C-310">
                    <?php echo $info['last_seen'] ?>
                </td>
                <td class="A-C-311">
                    <?php echo $info['similar_views'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
