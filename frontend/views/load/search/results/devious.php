<?php

use common\components\ControllerTrait;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use kartik\icons\Icon;
use yii\bootstrap\Html;
use common\components\Location;

/** @var string $id */
/** @var string $sectionClass */
/** @var string $headingClass */
/** @var string $noResults */
/** @var array $groups */
/** @var Load[] $loads */
/** @var string $params */
/** @var boolean $showHideButton */
?>

<section class="<?php echo $sectionClass; ?>">
    <div id="<?php echo $id; ?>" class="<?php echo $headingClass; ?>">
        <h4><?php echo Yii::t('element', $id); ?></h4>
    </div>

    <?php if (empty($groups) || empty($loads)): ?>
        <div class="custom-table responsive-table-wrapper">
            <table class="table responsive-expandable-table">
                <thead>
                    <tr>
                        <th width="20%" class="text-center"><?php echo Yii::t('element', 'IA-C-23'); ?></th>
                        <th width="20%"><?php echo Yii::t('element', 'IA-C-25'); ?></th>
                        <th width="20%"><?php echo Yii::t('element', 'IA-C-28'); ?></th>
                        <th width="25%" colspan="<?php echo '0'; ?>">
                            <?php echo Yii::t('element', 'IA-C-30a'); ?>
                        </th>
                        <th width="10%"><?php echo Yii::t('element', 'A-L-L-1'); ?></th>
                        <?php if ($showHideButton): ?>
                            <th width="5%" class="text-center">
                                <?php echo Html::a(
                                    Icon::show('eye-slash', '', Icon::FA), 
                                    ['load/hide-load-suggestion', 'ids' => json_encode([$id])],
                                    [
                                        'class' => 'secondary-button load-hide-btn',
                                        'title' => Yii::t('app', 'HIDE_LOAD_SUGGESTION_BUTTON_HINT'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                    ]
                                ); ?>
                            </th>
                        <?php else: ?>
                            <th width="5%" class="text-center">
                            <?php echo '' ?>
                        </th>
                        <?php endif; ?>
                        <th width="5%" class="text-center">
                            <?php echo Yii::t('element', 'L-T-20a'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="empty-content-row">
                        <td colspan="7" class="text-center">
                            <?php echo Html::tag('div', Yii::t('app', 'EMPTY_LOADS_TABLE_TEXT'), ['class' => 'empty-loads-text-wrapper']) .
                                ' ' . Yii::t('app', 'EMPTY_TRY') . ' ' . 
                                Html::a(Yii::t('app', 'EMPTY_ANNOUNCE_CAR_TRANSPORTER_LINK'), [
                                    'car-transporter-announcement/announcement-form',
                                    'lang' => Yii::$app->language,
                                ],        
                                [
                                    'class' => 'primary-button detail-search-btn',
                                ]); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <?php foreach ($groups as $group): ?>
            <?php if (ControllerTrait::isActiveMenuItem('load', 'suggestions')): ?>
                <section class="search-results-container">
                    <span class="search-results-heading">
                        <?php echo Yii::t('element', 'IK-C-25'); ?>
                    </span>

                    <span class="search-results-content clearfix">
                        <span class="load-city">
                            <?php echo LoadCity::getFormattedSuggestionCity($group['searchRequest']['loadCityRequest']); ?>
                        </span>

                        <span class="cities-separator">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </span>

                        <span class="unload-city">
                            <?php echo LoadCity::getFormattedSuggestionCity($group['searchRequest']['unloadCityRequest']); ?>
                        </span>

                        <span class="load-quantity">
                            <?php echo ' (' . $group['searchRequest']['loadRequestQuantity'] . ' ' . Yii::t('element', 'IA-C-81') . ')'; ?>
                        </span>
                    </span>
                </section>
            <?php endif; ?>
            <div class="custom-table responsive-table-wrapper">
                <table class="table table-striped responsive-expandable-table">
                    <thead>
                        <tr>
                            <th width="20%" class="text-center"><?php echo Yii::t('element', 'IA-C-23'); ?></th>
                            <th width="20%"><?php echo Yii::t('element', 'IA-C-25'); ?></th>
                            <th width="20%"><?php echo Yii::t('element', 'IA-C-28'); ?></th>
                            <th width="25%" colspan="<?php echo '0'; ?>">
                                <?php echo Yii::t('element', 'IA-C-30a'); ?>
                            </th>
                            <th width="10%"><?php echo Yii::t('element', 'A-L-L-1'); ?></th>
                            <?php if ($showHideButton): ?>
                            <th width="5%" class="text-center">
                                <?php echo Html::a(
                                    Icon::show('eye-slash', '', Icon::FA), 
                                    ['load/hide-load-suggestion', 'ids' => json_encode([$id])],
                                    [
                                        'class' => 'secondary-button load-hide-btn',
                                        'title' => Yii::t('app', 'HIDE_LOAD_SUGGESTION_BUTTON_HINT'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                    ]
                                ); ?>
                            </th>
                            <?php else: ?>
                                <th width="5%" class="text-center">
                                <?php echo '' ?>
                            </th>
                            <?php endif; ?>
                            <th width="5%" class="text-center">
                                <?php echo Yii::t('element', 'L-T-20a'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php unset($group['searchRequest']); ?>
                        <?php $nextFirstLoadCity = null; ?>
                        <?php $index = 0; ?>
                        <?php foreach ($group as $id): ?>
                            <?php if ($index > 0): ?>
                                <tr class="next-load-stop-row">
                                    <td colspan="7" class="text-center">
                                        <?php echo Yii::t('element', 'IK-C-31'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php $index++; ?>

                            <?php foreach ($loads as $load): ?>
                                <?php if ($load->id == $id): ?>
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
                                        <?php LoadCity::orderByFirstLoadCity($cities, $nextFirstLoadCity); ?>
                                        <td class="load-city-collumn-content"
                                            data-title="<?php echo Yii::t('element', 'IA-C-25'); ?>">
                                            <?php echo LoadCity::getFormattedCities($cities) . $postalCode; ?>
                                        </td>

                                        <?php $cities = []; ?>
                                        <?php foreach ($load->loadCities as $city): ?>
                                            <?php if ($city->type === LoadCity::UNLOADING): ?>
                                                <?php $city->addCitiesToCountryList($cities); ?>
                                                <?php $nextFirstLoadCity = $city; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php 
                                            $postalCode = '';
                                            if (!empty($load->loadCities[0]->load_postal_code)) {
                                                $postalCode = ' ('. $load->loadCities[0]->load_postal_code .')';
                                            } 
                                        ?>
                                        <td class="unload-city-collumn-content"
                                            data-title="<?php echo Yii::t('element', 'IA-C-28'); ?>">
                                            <?php echo LoadCity::getFormattedCities($cities). $postalCode;?>
                                        </td>

                                        <td data-title="<?php echo Yii::t('text', 'LOAD'); ?>">
                                            <?php echo LoadCar::getLoadInfo($load); ?>
                                        </td>
										
                                        <td data-title="<?php echo Yii::t('text', 'A-L-L-1'); ?>">
                                            <?php $location = Location::getGeoLocation(); ?>
                                            <?php $date = date_create(date('Y-m-d H:i:s', $load->created_at)); ?>
                                            <?php $date->setTimeZone(new DateTimeZone($location->timeZone)); ?>
                                            <?php echo $date->format('Y-m-d H:i:s'); ?>
                                        </td>

                                        <td width="5%"
                                            class="load-preview-content text-center"
                                            data-title="<?php echo Yii::t('text', 'DETAIL_INFORMATION'); ?>">
                                            <a href="#"
                                               class="load-preview-icon<?php echo $load->isOpenContacts() ? ' open-contacts' : ''; ?>"
                                               data-id="<?php echo $load->id; ?>"
                                               data-placement="top"
                                               title="<?php echo Yii::t('element', 'IA-C-30b'); ?>"
                                            >
                                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                                            </a>
                                        </td>

                                        <td width="5%"
                                            class="load-link-content text-center"
                                            data-title="<?php echo Yii::t('element', 'L-T-20a'); ?>">
                                            <a href="#"
                                               class="load-link-icon"
                                               data-id="<?php echo $load->id; ?>"
                                               data-placement="top"
                                               title="<?php echo Yii::t('element', 'L-T-20b'); ?>"
                                               onclick="showLoadLink(event, <?php echo $load->id; ?>)"
                                            >
                                                <i class="fa fa-share-alt" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="expanded-content-row load-expanded-content-<?php echo $load->id; ?> collapse<?php echo $load->isOpenContacts() ? ' in' : ''; ?>">
                                        <td class="expanded-load-preview-content 
                                            expanded-load-preview-content-<?php echo $load->id; ?>" 
                                            colspan="7"
                                            ><?php if ($load->isOpenContacts()) {
                                                echo Yii::$app->controller->renderPartial(
                                                    '/load/preview/subscriber', compact('load')
                                                );
                                            } ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
