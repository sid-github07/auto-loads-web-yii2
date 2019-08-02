<?php

use common\models\Load;
use common\models\LoadCity;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/** @var View $this */
/** @var array $day */
/** @var array $previous */
/** @var array $signUpCityLoads */
/** @var Load[] $loads */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var boolean $showHideButton */
/** @var array $pages */

$this->title = Yii::t('seo', 'TITLE_LOAD_SUGGESTIONS');
?>
<div class="load-suggestions">
    <h1 id="KP-C-1">
        <?php echo Yii::t('element', 'KP-C-1'); ?>
    </h1>

    <?php $form = ActiveForm::begin([
        'id' => 'search-results-filter-form',
        'validationUrl' => ['load/validate-suggestions-filter', 'lang' => Yii::$app->language],
        'method' => 'GET',
    ]); ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo $form->field($load, 'date', [
                    'enableAjaxValidation' => true,
                ])->widget(DatePicker::className(), [
                    'options' => [
                        'id' => 'KP-C-1a',
                        'class' => 'form-control',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'startDate' => date('Y-m-d'),
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', 'KP-C-1a')); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-10',
                    'loadCity' => $loadCity,
                    'attribute' => 'loadCityId',
                    'label' => Yii::t('element', 'KP-C-1b'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => null,
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'load' => true,
                    ]),
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-11',
                    'loadCity' => $loadCity,
                    'attribute' => 'unloadCityId',
                    'label' => Yii::t('element', 'KP-C-1c'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => null,
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'unload' => true,
                    ]),
                ]) ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo $form->field($load, 'suggestionsType', [
                    'options' => [
                        'id' => 'KP-C-1d',
                    ],
                ])
                    ->dropDownList(Load::getSuggestionsTypes())
                    ->label(Yii::t('element', 'KP-C-1d')); ?>
                
                <span class="select-addon"><i class="fa fa-caret-down"></i></span>
            </div>
        </div>

        <div class="text-right">
            <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'KP-C-1e'), [
                'id' => 'KP-C-1e',
                'class' => 'primary-button filter-loads-btn',
            ]); ?>
            <a href="<?php echo Url::to(['load/suggestions', 'lang' => Yii::$app->language]); ?>" 
               class="secondary-button clear-suggestions-filter"
            >
                <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'KP-C-1f'); ?>
            </a>
        </div>

    <?php ActiveForm::end(); ?>

    <section class="day-searches">
        <?php if (!empty($day['direct']) || !empty($day['additional']) || !empty($day['fullUnload'])) : ?>
            <div class="filter-result-heading">
                <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1h'); ?></h2>
            </div>
        <?php endif; ?>
        <?php if (!empty($day['direct']) && ($load->isDefaultSuggestion() || $load->isDirectSuggestion())): ?>
            <h4><?php echo Yii::t('element', 'KP-C-1g'); ?></h4>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/direct', [
                'sectionClass' => 'direct-loads',
                'noResults' => Yii::t('alert', 'NO_DIRECT_TRANSPORTATION_RESULTS'),
                'directLoads' => $day['direct'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'day-suggestions-direct-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['daySuggestionsPages']['direct'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($day['additional']) && ($load->isDefaultSuggestion() || $load->isAdditionalSuggestion())): ?>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
                'id' => 'IK-C-30',
                'sectionClass' => 'devious-loads',
                'headingClass' => 'devious-loads-heading',
                'noResults' => 'IK-C-30a',
                'groups' => $day['additional'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'day-suggestions-additional-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['daySuggestionsPages']['additional'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($day['fullUnload']) && ($load->isDefaultSuggestion() || $load->isFullUnloadSuggestion())): ?>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
                'id' => 'IK-C-32',
                'sectionClass' => 'devious-loads',
                'headingClass' => 'devious-loads-heading',
                'noResults' => 'IK-C-30a',
                'groups' => $day['fullUnload'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'day-suggestions-full-unload-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['daySuggestionsPages']['fullUnload'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="previous-searches">
        <?php if (!empty($previous['direct']) || !empty($previous['additional']) || !empty($previous['fullUnload'])) : ?>
            <div class="filter-result-heading">
                <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1j'); ?></h2>
            </div>
        <?php endif; ?>
        <?php if (!empty($previous['direct']) && ($load->isDefaultSuggestion() || $load->isDirectSuggestion())): ?>
            <h4><?php echo Yii::t('element', 'KP-C-1g'); ?></h4>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/direct', [
                'sectionClass' => 'direct-loads',
                'noResults' => Yii::t('alert', 'NO_DIRECT_TRANSPORTATION_RESULTS'),
                'directLoads' => $previous['direct'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'previous-suggestions-direct-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['previousSuggestionsPages']['direct'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($previous['additional']) && ($load->isDefaultSuggestion() || $load->isAdditionalSuggestion())): ?>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
                'id' => 'IK-C-30',
                'sectionClass' => 'devious-loads',
                'headingClass' => 'devious-loads-heading',
                'noResults' => 'IK-C-30a',
                'groups' => $previous['additional'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'previous-suggestions-additional-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['previousSuggestionsPages']['additional'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($previous['fullUnload']) && ($load->isDefaultSuggestion() || $load->isFullUnloadSuggestion())): ?>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/devious', [
                'id' => 'IK-C-32',
                'sectionClass' => 'devious-loads',
                'headingClass' => 'devious-loads-heading',
                'noResults' => 'IK-C-30a',
                'groups' => $previous['fullUnload'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'previous-suggestions-full-unload-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['previousSuggestionsPages']['fullUnload'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="sign-up-city-searches">
        <?php if (!empty($signUpCityLoads['direct'])) : ?>
            <div class="filter-result-heading">
                <h2 class="text-center"><?php echo Yii::t('element', 'KP-C-1k'); ?></h2>
            </div>
        <?php endif; ?>
        <?php if (!empty($signUpCityLoads['direct']) && ($load->isDefaultSuggestion() || $load->isDirectSuggestion())): ?>
            <h4><?php echo Yii::t('element', 'KP-C-1g'); ?></h4>
            <?php echo Yii::$app->controller->renderPartial('/load/search/results/sign-up-city', [
                'sectionClass' => 'direct-loads',
                'noResults' => Yii::t('alert', 'NO_DIRECT_TRANSPORTATION_RESULTS'),
                'directLoads' => $signUpCityLoads['direct'],
                'loads' => $loads,
                'showHideButton' => $showHideButton,
            ]); ?>
            
            <div class="text-center">
                <?php echo LinkPager::widget([
                    'options' => [
                        'id' => 'sign-up-city-suggestions-direct-pagination',
                        'class' => 'pagination'
                    ],
                    'pagination' => $pages['signUpCitySuggestionsPages']['direct'],
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                    'registerLinkTags' => false,
                ]); ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
Modal::begin([
    'id' => 'load-preview-modal',
    'header' => Yii::t('element', 'IA-C-56a'),
    'size' => 'modal-lg',
]);

    echo Html::tag('div', null, [
        'id' => 'load-preview-modal-content',
    ]);

Modal::end();

Modal::begin([
    'id' => 'load-link-preview-modal',
    'header' => Yii::t('element', 'IA-C-55a'),
]);

    Pjax::begin(['id' => 'load-link-preview-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionLoadPreview = "' . Url::to(['load/preview', 'lang' => Yii::$app->language]) . '"; '.
	'var actionPreviewLoadLink = "' . Url::to(['load/preview-load-link', 'lang' => Yii::$app->language]) . '"; ', 
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search-results.js', ['depends' => [JqueryAsset::className()]]);