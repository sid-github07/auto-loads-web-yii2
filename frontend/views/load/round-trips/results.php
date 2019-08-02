<?php

use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;

/** @var View $this */
/** @var array $roundTrips */
/** @var Load[] $loads */

$this->title = Yii::t('seo', 'TITLE_ROUND_TRIPS_RESULTS');
?>

<div class="load-round-trips-results">
    <h1 id="R-T-1">
        <?php echo Yii::t('element', 'R-T-1'); ?>
    </h1>
    
    <section class="roundtrips">
        <?php if (empty($roundTrips) || empty($loads)): ?>
            <?php echo Yii::t('element', 'R-T-4'); ?>
        <?php else: ?>
            <?php foreach ($roundTrips as $trip): ?>
                <section class="roundtrip-summary">
                    <span id="IK-C-25" class="roundtrip-heading">
                        <?php echo Yii::t('element', 'R-T-1'); ?>
                    </span>

                    <span class="roundtrip-cities">
                        <?php foreach ($trip as $loadId): ?>
                            <?php foreach ($loads as $load): ?>
                                <?php if ($load->id == $loadId): ?>
                                    <?php $cities = []; ?>
                                    <?php foreach ($load->loadCities as $city): ?>
                                        <?php if ($city->type === LoadCity::LOADING): ?>
                                            <?php $city->addCitiesToCountryList($cities); ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <span class="load-city">
                                        <?php echo LoadCity::getFormattedCities($cities); ?>
                                    </span>

                                    <span class="cities-separator">
                                        <i class="fa fa-long-arrow-right"></i>
                                    </span>

                                    <?php $cities = []; ?>
                                    <?php foreach ($load->loadCities as $city): ?>
                                        <?php if ($city->type === LoadCity::UNLOADING): ?>
                                            <?php $city->addCitiesToCountryList($cities); ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <span class="unload-city">
                                        <?php echo LoadCity::getFormattedCities($cities); ?>
                                    </span>

                                    <span class="additional-trip">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </span>
                </section>

                <div class="custom-table roundtrips-table-wrapper responsive-table-wrapper">
                    <table class="table table-striped responsive-expandable-table">
                        <thead>
                            <tr>
                                <th width="20%" class="text-center"><?php echo Yii::t('element', 'IA-C-23'); ?></th>
                                <th width="20%"><?php echo Yii::t('element', 'IA-C-25'); ?></th>
                                <th width="20%"><?php echo Yii::t('element', 'IA-C-28'); ?></th>
                                <th width="40%" colspan="2">
                                    <?php echo Yii::t('element', 'IA-C-30a'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trip as $loadId): ?>
                                <?php foreach ($loads as $load): ?>
                                    <?php if ($load->id == $loadId): ?>
                                        <tr class="content-row">
                                            <td class="text-center"
                                                data-title="<?php echo Yii::t('element', 'IA-C-23'); ?>">
                                                <?php
                                                $emptyDate = Html::tag('i', Yii::t('yii', '(not set)'));
                                                $date = empty($load->date) ? 0 : date('Y-m-d', $load->date);
                                                echo empty($date) ? $emptyDate : $date;
                                                ?>
                                            </td>

                                            <?php $cities = []; ?>
                                            <?php foreach ($load->loadCities as $city): ?>
                                                <?php if ($city->type === LoadCity::LOADING): ?>
                                                    <?php $city->addCitiesToCountryList($cities); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php 
                                                $postalCode = '';
                                                if (!empty($load->loadCities[0]->load_postal_code)) {
                                                    $postalCode = ' ('. $load->loadCities[0]->load_postal_code .')';
                                                } 
                                            ?>
                                            <td class="load-city-collumn-content"
                                                data-title="<?php echo Yii::t('element', 'IA-C-25'); ?>">
                                                <?php echo LoadCity::getFormattedCities($cities) . $postalCode; ?>
                                            </td>

                                            <?php $cities = []; ?>
                                            <?php foreach ($load->loadCities as $city): ?>
                                                <?php if ($city->type === LoadCity::UNLOADING): ?>
                                                    <?php $city->addCitiesToCountryList($cities); ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php 
                                                $postalCode = '';
                                                if (!empty($load->loadCities[0]->unload_postal_code)) {
                                                    $postalCode = ' ('. $load->loadCities[0]->unload_postal_code .')';
                                                } 
                                            ?>
                                            <td class="unload-city-collumn-content"
                                                data-title="<?php echo Yii::t('element', 'IA-C-28'); ?>">
                                                <?php echo LoadCity::getFormattedCities($cities) . $postalCode; ?>
                                            </td>

                                            <td data-title="<?php echo Yii::t('text', 'LOAD'); ?>">
                                                <?php echo LoadCar::getLoadInfo($load); ?>
                                            </td>

                                            <td width="5%" 
                                                class="load-preview-content text-center"
                                                data-title="<?php echo Yii::t('text', 'DETAIL_INFORMATION'); ?>"
                                            >
                                                <a href=".roundtrip-expanded-content-<?php echo $load->id; ?>"
                                                   class="load-preview-icon"
                                                   data-id="<?php echo $load->id; ?>"
                                                   data-toggle="collapse"
                                                   data-placement="top"
                                                   title="<?php echo Yii::t('element', 'IA-C-30b'); ?>"
                                                >
                                                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr class="expanded-content-row roundtrip-expanded-content-<?php echo $load->id; ?> collapse">
                                            <td class="expanded-load-preview-content .
                                                expanded-load-preview-content-<?php echo $load->id; ?>" 
                                                colspan="6"
                                            ></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'day-suggestions-direct-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['roundtrips'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
$this->registerJs(
    'var actionLoadPreview = "' . Url::to(['load/preview', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/load/search-results.js', ['depends' => [JqueryAsset::className()]]);