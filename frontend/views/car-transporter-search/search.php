<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use yii\bootstrap\Modal;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use common\components\Location;
use common\models\Company;
use common\models\Language;
use odaialali\yii2toastr\ToastrFlash;
use odaialali\yii2toastr\ToastrAsset;

ToastrAsset::register($this);

/** @var View $this */
/** @var City $loadLocation */
/** @var City $unloadLocation */
/** @var array $ids */
/** @var CarTransporter[] $carTransporters */
/** @var array $searchCriteria */
/** @var Pagination $pagination */

$this->title = Yii::t('seo', 'TITLE_CAR_TRANSPORTER_SEARCH');
?>
<div class="car-transporter-search">
    <h1 id="C-T-79">
        <?php echo Yii::t('element', 'C-T-79'); ?>
    </h1>

    <section class="search-results-container">
        <span id="C-T-80" class="search-results-heading">
            <?php echo Yii::t('element', 'C-T-80'); ?>
        </span>

        <span class="search-results-content clearfix">
            <span id="C-T-81">
                <?php
                    $loadLocation = $searchCriteria[CarTransporterCity::TYPE_LOAD];
                    echo $loadLocation->getSearchLocation();
                ?>
            </span>

            <span id="C-T-82" class="cities-separator">
                <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
            </span>

            <span id="C-T-83">
                <?php
                    $unloadLocation = $searchCriteria[CarTransporterCity::TYPE_UNLOAD];
                    echo $unloadLocation->getSearchLocation();
                ?>
            </span>

            <span id="C-T-84">
                <?php echo ' (' . $searchCriteria['quantity'] . ' ' . Yii::t('element', 'C-T-84') . ')'; ?>
            </span>
        </span>
    </section>

    <div class="custom-table responsive-table-wrapper">
        <table class="table table-striped responsive-expandable-table">
            <thead>
                <tr>
                    <th id="C-T-85">
                        <?php echo Yii::t('element', 'C-T-85'); ?>
                    </th>
                    <th id="C-T-86">
                        <?php echo Yii::t('element', 'C-T-86'); ?>
                    </th>
                    <th id="C-T-87">
                        <?php echo Yii::t('element', 'C-T-87'); ?>
                    </th>
                    <th id="C-T-88">
                        <?php echo Yii::t('element', 'C-T-88'); ?>
                    </th>
					<th id="A-L-L-1">
                        <?php echo Yii::t('element', 'A-L-L-1'); ?>
                    </th>
                    <th id="C-T-89" class="text-center">
                        <?php echo Yii::t('element', 'C-T-89'); ?>
                    </th>
                    <th id="C-T-18a" class="text-center">
                        <?php echo Yii::t('element', 'C-T-18a'); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($ids as $id): ?>
                    <?php foreach ($carTransporters as $carTransporter): ?>
                        <?php if ($carTransporter->id == $id): ?>
                            <tr class="content-row">
                                <td class="C-T-89" data-title="<?php echo Yii::t('element', 'C-T-85'); ?>">
                                    <?php
                                    if (empty($carTransporter->available_from)) {
                                        echo Yii::t('yii', '(not set)');
                                    } else {
                                        $carTransporter->convertAvailableFromDate();
                                        echo $carTransporter->available_from;
                                    }
                                    ?>
                                </td>
                                <td class="C-T-90" data-title="<?php echo Yii::t('element', 'C-T-86'); ?>">
                                    <?php foreach ($carTransporter->carTransporterCities as $carTransporterCity) {
                                        $postalCode = '';
                                        if (!empty($carTransporterCity->load_postal_code)) {
                                            $postalCode = '> (' . $carTransporterCity->load_postal_code . ')';   
                                        }
                                        echo $carTransporterCity->formatTableCities(CarTransporterCity::TYPE_LOAD) . $postalCode;
                                        break;
                                    } ?>
                                </td>
                                <td class="C-T-91" data-title="<?php echo Yii::t('element', 'C-T-87'); ?>">
                                    <?php foreach ($carTransporter->carTransporterCities as $carTransporterCity) {
                                        $postalCode = '';
                                        if (!empty($carTransporterCity->unload_postal_code)) {
                                            $postalCode = '> (' . $carTransporterCity->unload_postal_code . ')';   
                                        }
                                        echo $carTransporterCity->formatTableCities(CarTransporterCity::TYPE_UNLOAD) . $postalCode;
                                        break;
                                    } ?>
                                </td>
                                <td class="C-T-92" data-title="<?php echo Yii::t('element', 'C-T-88'); ?>">
                                    <?php if(is_null($carTransporter->quantity)): ?>
                                        <?php echo Yii::t('element', 'C-T-17a'); ?>
                                    <?php endif; ?>
                                    <?php if(!is_null($carTransporter->quantity)): ?>
                                        <?php echo $carTransporter->quantity; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="A-L-L-1" data-title="<?php echo Yii::t('element', 'A-L-L-1'); ?>">
                                    <?php if(!is_null($carTransporter->created_at)): ?>
                                        <?php $location = Location::getGeoLocation(); ?>
                                        <?php $date = date_create(date('Y-m-d H:i:s', $carTransporter->created_at)); ?>
                                        <?php $date->setTimeZone(new DateTimeZone($location->timeZone)); ?>
                                        <?php echo $date->format('Y-m-d H:i:s'); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="C-T-93 load-preview-content text-center" 
                                    data-title="<?php echo Yii::t('element', 'C-T-89'); ?>"
                                >
                                    <a href="#"
                                       class="load-preview-icon<?php echo $carTransporter->isOpenContacts() ? ' open-contacts' : ''; ?>"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="<?php echo Yii::t('element', 'C-T-93'); ?>"
                                       onclick="collapseCarTransporterPreview(event, <?php echo $carTransporter->id; ?>)"
                                    >
                                        <i class="fa fa-caret-down"></i>
                                    </a>
                                </td>
                                <td class="C-T-23a car-transporter-link-content text-center"
                                    data-title="<?php echo Yii::t('element', 'C-T-18a'); ?>"
                                >
                                    <a href="#"
                                       class="car-transporter-link-icon"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="<?php echo Yii::t('element', 'C-T-23a'); ?>"
                                       onclick="showCarTransporterLink(event, <?php echo $carTransporter->id; ?>)"
                                    >
                                        <i class="fa fa-share-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr class="<?php echo $carTransporter->isOpenContacts() ? '' : 'hidden'; ?>">
                                <td id="car-transporter-preview-<?php echo $carTransporter->id; ?>"
                                    class="expanded-load-preview-content"
                                    colspan="7"
                                ><?php if ($carTransporter->isOpenContacts()) {
                                    $company = Company::findUserCompany($carTransporter->user_id);
                                    $languages = Language::getUserSelectedLanguages($carTransporter->user_id);
                                    $showInfo = false;
                                    $boughtByCreditCode = false;
                                    echo Yii::$app->controller->renderPartial(
                                        '/car-transporter/preview', 
                                        compact('carTransporter', 'showInfo', 'company', 'languages', 'boughtByCreditCode')
                                    );
                                } ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
				<?php if (empty($id)) : ?>
                    <tr class="content-row">
                        <td colspan="7" class="text-center">
                        <?php echo Html::tag('div', Yii::t('app', 'EMPTY_CAR_TRANSPORTER_TABLE_TEXT'), ['class' => 'empty-loads-text-wrapper']) .
                                     ' ' . Yii::t('app', 'EMPTY_TRY') . ' ' . 
                                     Html::a(Yii::t('app', 'EMPTY_ANNOUNCE_LOAD_LINK'), [
                                         'load/announce',
                                         'lang' => Yii::$app->language,
                                     ], 
                                     [
                                         'class' => 'primary-button detail-search-btn',
                                     ]); ?>
                        <?php endif; ?>            
                        </td>
                    </tr> 
            </tbody>
        </table>
    </div>

    <?php echo LinkPager::widget([
        'pagination' => $pagination,
        'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
        'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
    ]); ?>
</div>

<?php
Modal::begin([
    'id' => 'car-transporter-link-preview-modal',
    'header' => Yii::t('element', 'C-T-8bb'),
]);

    Pjax::begin(['id' => 'car-transporter-link-preview-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionPreview = "' . Url::to(['car-transporter/preview', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionPreviewLink = "' . Url::to(['car-transporter/preview-link', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/index.js', ['depends' => [JqueryAsset::className()]]);
