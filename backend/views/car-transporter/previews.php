<?php

use common\models\CarTransporter;
use common\models\Company;

/** @var CarTransporter $carTransporter */

?>
<div class="table-responsive custom-table">
    <table class="table car-transporter-previews-table">
        <thead>
            <tr>
                <th id="C-T-115a">
                    <?php echo Yii::t('element', 'C-T-115a'); ?>
                </th>
                <th id="C-T-115b">
                    <?php echo Yii::t('element', 'C-T-115b'); ?>
                </th>
                <th id="C-T-115c">
                    <?php echo Yii::t('element', 'C-T-115c'); ?>
                </th>
                <th id="C-T-115d">
                    <?php echo Yii::t('element', 'C-T-115d'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carTransporter->carTransporterPreviews as $preview): ?>
                <tr>
                    <td class="C-T-115e">
                        <?php echo date('Y-m-d H:i', $preview->created_at); ?>
                    </td>
                    <td class="C-T-115f">
                        <?php echo $preview->user_id; ?>
                    </td>
                    <td class="C-T-115g">
                        <?php
                            // TODO: optimize this, because now it requests DB with each entry
                            $companyName = Company::findUserCompany($preview->user_id)->getTitleByType();
                            $userName = $preview->user->getNameAndSurname();
                            echo $companyName . ' | ' . $userName;
                        ?>
                    </td>
                    <td class="C-T-115h">
                        <?php echo $preview->ip; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
