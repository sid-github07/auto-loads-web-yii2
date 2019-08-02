<?php

use common\models\LoadCity;

/** @var array $container */
?>
<table class="L-T-8 table table__map-popover">
    <?php foreach ($container as $loadId => $item): ?>
        <tr>
            <td class="L-T-10">
                <?php
                    echo LoadCity::formatCountryCodeAndCitiesNames($item[LoadCity::LOADING]);
                    echo ' - ';
                    echo LoadCity::formatCountryCodeAndCitiesNames($item[LoadCity::UNLOADING]);
                ?>
            </td>
        </tr>
        
        <tr>
            <td>
                <a href="#"
                   class="L-T-9"
                   title="<?php echo Yii::t('element', 'L-T-9'); ?>"
                   onclick="previewLoadInfo(event, <?php echo $loadId; ?>)"
                >
                    <?php echo Yii::t('element', 'L-T-9'); ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
