<?php

use voime\GoogleMaps\Map;
use common\models\City;

/**
 * @var City $loadCity
 * @var string $mapType
 * @var array $markers
 */

$failedToRenderMap = false;
try {
    echo Map::widget([
        'width' => '100%',
        'height' => '500px',
        'center' => is_null($loadCity) ? Yii::$app->params['gmapsEuropeCenter'] : $loadCity->getCoordinates(),
        'zoom' => isset(Yii::$app->params['gmapsZoom']) ? Yii::$app->params['gmapsZoom'] : 5,
        'mapType' => Map::MAP_TYPE_ROADMAP,
        'markers' => $markers,
    ]);
} catch (\Exception $e) {
    $failedToRenderMap = true;
}

?>
<?php if ($failedToRenderMap === false): ?>

    <?php if ($mapType === 'transporter'): ?>
        <div id="C-T-4" class="map-legend">
            <div class="map-legend__item">
                <img src="<?php echo Yii::getAlias('@web') . '/images/marker-truck.png'; ?>" />
                <span class="map-legend__label">
                        <?php echo Yii::t('element', 'C-T-4'); ?>
                    </span>
            </div>
        </div>
    <?php else: ?>
        <div id="L-T-6" class="map-legend">
            <div class="map-legend__item">
                <img src="<?php echo Yii::getAlias('@web') . '/images/marker-partial.png'; ?>" />
                <span class="map-legend__label">
                    <?php echo Yii::t('element', 'L-T-3'); ?>
                </span>
            </div>

            <div class="map-legend__item">
                <img src="<?php echo Yii::getAlias('@web') . '/images/marker-full.png'; ?>" />
                <span class="map-legend__label">
                    <?php echo Yii::t('element', 'L-T-4'); ?>
                </span>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <p style="text-align:center;"> <?php echo Yii::t('element', 'map_render_error_text') ?></p>
<?php endif; ?>