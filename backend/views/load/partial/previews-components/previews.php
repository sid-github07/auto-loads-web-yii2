<?php

use common\models\Company;
use common\models\Load;

/**
 * @var null|Load $load
 */

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
        </tr>
        </thead>
        <tbody>
        <?php foreach ($load->loadPreviews as $preview): ?>
            <tr>
                <td class="A-C-304">
                    <?php echo date('Y-m-d H:i', $preview->created_at); ?>
                </td>
                <td class="A-C-306">
                    <?php echo $preview->user_id; ?>
                </td>
                <td class="A-C-308">
                    <?php echo Company::getCompany($preview->user->id)->getTitleByType() . ' | ' . $preview->user->getNameAndSurname(); ?>
                </td>
                <td class="A-C-310">
                    <?php echo $preview->ip; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
