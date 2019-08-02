<?php

use common\models\UserLog;

/** @var UserLog[] $logs */
?>
<div class="custom-gridview">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th id="A-C-156">
                    <?php echo Yii::t('element', 'A-C-156'); ?>
                </th>
                <th id="A-C-157">
                    <?php echo Yii::t('element', 'A-C-157'); ?>
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php foreach ($logs as $log): ?>
                <?php 
                    if ($log->data === '[]'):
                        continue;
                    endif; 
                ?>
                <tr>
                    <td class="A-C-158">
                        <?php echo $log->getDateTime(); ?>
                    </td>
                    <td class="A-C-159">
                        <?php echo $log->getMessage(); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>