<?php

use yii\web\View;
use common\models\Load;
use yii\helpers\Html;
use common\models\LoadCity;
use kartik\icons\Icon;

Icon::map($this, Icon::FI);

/**
 * @var View $this
 * @var array $mainRoutes
 */

?>


<?php if (count($mainRoutes)) : ?>
    <div class="table-responsive custom-table">
        <table class="table load-previews-table">
            <thead>
            <tr>
                <th>
                    <?php echo Yii::t('element', 'A-C-401'); ?>
                </th>
                <th colspan="2" style="text-align: center">
                    <?php echo Yii::t('element', 'Route'); ?>
                </th>
                <th></th>
                <th style="text-align: center">
                    <?php echo Yii::t('element', 'A-C-311'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 0;
            foreach ($mainRoutes

            as $loadId => $views):
            $load = Load::findOne($loadId);
            $loadCityId = $unloadCityId = '';
            ?>
            <tr>
                <td>
                    <?php echo ++$num ?>
                </td>
                <td>
                    <?php
                    $cities = [];
                    foreach ($load->loadCities as $loadCity) {
                        if ($loadCity->type === LoadCity::LOADING) {
                            $loadCityId = $loadCity->city_id;
                            $loadCity->addCitiesToCountryList($cities);
                        }
                    }
                    $postalCode = '';
                    if (!empty($load->loadCities[0]->load_postal_code)) {
                        $postalCode = ' (' . $load->loadCities[0]->load_postal_code . ')';
                    }

                    echo LoadCity::getFormattedCities($cities) . $postalCode;
                    ?>
                </td>
                <td>
                    <?php
                    $cities = [];
                    foreach ($load->loadCities as $unloadCity) {
                        if ($unloadCity->type === LoadCity::UNLOADING) {
                            $unloadCityId = $unloadCity->city_id;
                            $unloadCity->addCitiesToCountryList($cities);
                        }
                    }
                    $postalCode = '';
                    if (!empty($load->loadCities[0]->unload_postal_code)) {
                        $postalCode = ' (' . $load->loadCities[0]->unload_postal_code . ')';
                    }
                    echo LoadCity::getFormattedCities($cities) . $postalCode;
                    ?>
                </td>
                <td style="text-align: center">
                    <?php
                    $url = sprintf('%s/kroviniai?loadCityId=%s&unloadCityId=%s', Yii::$app->params['frontendHost'],
                        $loadCityId, $unloadCityId);
                    echo Html::a(Yii::t('element', 'to_the_market'), $url, ['target' => '_blank'])
                    ?>
                </td>
                <td style="text-align: center">
                    <?php echo $views ?>
                </td>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else : ?>
    <div><?php echo Yii::t('element', 'no_main_routes_found') ?></div>
<?php endif; ?>
