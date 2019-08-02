<?php

use common\models\Load;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrAsset;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/** @var View $this */
/** @var integer $quantity */
/** @var string $loadCity */
/** @var string $unloadCity */
/** @var array $directLoads */
/** @var array $additionalLoads */
/** @var array $fullUnloadLoads */
/** @var Load[] $loads */
/** @var string $params */
/** @var boolean $showHideButton */
/** @var array $pages */

$this->title = Yii::t('seo', 'TITLE_LOAD_SEARCH_RESULTS');
Icon::map($this, Icon::FA);
ToastrAsset::register($this);

Pjax::begin(['id' => 'search-results-toastr-pjax']);
    echo ToastrFlash::widget([
        'options' => [
            'closeButton' => true,
            'debug' => false,
            'newestOnTop' => true,
            'progressBar' => false,
            'positionClass' => 'toast-top-center',
            'preventDuplicates' => true,
            'showDuration' => 0, // how long it takes to show the alert in milliseconds
            'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
            'timeOut' => 45000, // how long the alert must be visible to user in milliseconds
            'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
            'onShown' => 'function() { ' . 
                '$(".alert-container").append($("#toast-container"));' .
            '}',
        ]
    ]);
Pjax::end();
?>

<div class="load-search-results">
    <h1 id="IK-C-24">
        <?php echo Yii::t('element', 'IK-C-24'); ?>
    </h1>
    
    <section class="search-results-container">
        <span id="IK-C-25" class="search-results-heading">
            <?php echo Yii::t('element', 'IK-C-25'); ?>
        </span>
        
        <span class="search-results-content clearfix">
            <span class="IA-C-79 IA-C-77">
                <?php echo $loadCity; ?>
            </span>
            
            <span id="IA-C-78" class="cities-separator">
                <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
            </span>
            
            <span class="IA-C-79 IA-C-80">
                <?php echo $unloadCity; ?>
            </span>
            
            <span class="IA-C-81">
                <?php echo ' (' . $quantity . ' ' . Yii::t('element', 'IA-C-81') . ')'; ?>
            </span>
        </span>
    </section>


	<?php echo Yii::$app->controller->renderPartial('/load/search/results/direct', [
            'sectionClass' => 'direct-loads',
            'noResults' => Yii::t('alert', 'NO_DIRECT_TRANSPORTATION_RESULTS'),
            'directLoads' => $directLoads,
            'loads' => $loads,
            'params' => $params,
            'showHideButton' => $showHideButton,
	]); ?>

	<div class="text-center">
            <?php echo LinkPager::widget([
                'options' => [
                        'id' => 'direct-pagination',
                        'class' => 'pagination'
                ],
                'pagination' => $pages['direct'],
                'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                'registerLinkTags' => false,
            ]); ?>
	</div>
		
	<?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
		'id' => 'IK-C-30',
		'sectionClass' => 'devious-loads',
		'headingClass' => 'devious-loads-heading',
		'noResults' => 'IK-C-30a',
		'groups' => $additionalLoads,
		'loads' => $loads,
                'params' => $params,
		'showHideButton' => $showHideButton,
	]); ?>

	<div class="text-center">
		<?php echo LinkPager::widget([
			'options' => [
				'id' => 'additional-pagination',
				'class' => 'pagination'
			],
			'pagination' => $pages['additional'],
			'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
			'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
			'registerLinkTags' => false,
		]); ?>
	</div>

	<?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
		'id' => 'IK-C-32',
		'sectionClass' => 'devious-loads',
		'headingClass' => 'devious-loads-heading',
		'noResults' => 'IK-C-33a',
		'groups' => $fullUnloadLoads,
		'loads' => $loads,
                'params' => $params,
		'showHideButton' => $showHideButton,
	]); ?>

	<div class="text-center">
            <?php echo LinkPager::widget([
                'options' => [
                    'id' => 'full-unload-pagination',
                    'class' => 'pagination'
                ],
                'pagination' => $pages['fullUnload'],
                'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                'registerLinkTags' => false,
            ]); ?>
	</div>
</div>

<?php
Modal::begin([
    'id' => 'load-link-preview-modal',
    'header' => Yii::t('element', 'IA-C-55a'),
]);

    Pjax::begin(['id' => 'load-link-preview-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionLoadPreview = "' . Url::to(['load/preview', 'lang' => Yii::$app->language, 'params' => $params]) . '"; ' .
    'var actionPreviewLoadLink = "' . Url::to(['load/preview-load-link', 'lang' => Yii::$app->language]) . '"; ',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/load/search-results.js', ['depends' => [JqueryAsset::className()]]);
