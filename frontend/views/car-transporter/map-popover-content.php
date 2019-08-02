<?php

/** @var array $citiesContainer */

use common\models\CarTransporterCity;

?>
<table class="C-T-6 table table__map-popover">
    <?php foreach ($citiesContainer as $carTransporterId => $items): ?>
        <tr>
            <td class="C-T-7">
                <?php
                    echo CarTransporterCity::formatPopoverCities($items[CarTransporterCity::TYPE_LOAD]);
                    echo ' - ';
                    echo CarTransporterCity::formatPopoverCities($items[CarTransporterCity::TYPE_UNLOAD]);
                ?>
            </td>
        </tr>

        <tr>
            <td>
                <a href="#"
                   class="C-T-8"
                   title="<?php echo Yii::t('element', 'C-T-8'); ?>"
                   onclick="previewContactInfo(event, <?php echo $carTransporterId; ?>)"
                >
                    <?php echo Yii::t('element', 'C-T-8'); ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
