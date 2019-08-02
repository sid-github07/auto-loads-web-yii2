<?php

use common\components\ElasticSearch;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Load;
use kartik\icons\Icon;
use common\models\City;

/**
 * @var City $loadCity
 * @var City $unloadCity
 * @var array $countries
 * @var string $type
 */

?>

<div class="row" style="border: 2px grey solid;margin: 0px;">
    <div class="col-xs-12 col-lg-10">
        <div class="form-group">
            <label id="L-T-11" class="control-label">
                <?php echo Yii::t('element', 'L-T-11'); ?>
            </label>

            <div class="row">
                <div class="col-xs-12 col-sm-3 col-lg-3">
                    <div class="control-label">
                        <?php echo Yii::t('element', 'IK-C-2') ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-9 col-lg-9">
                    <?php echo Html::radioList('searchRadius',
                        Yii::$app->getRequest()->get('searchRadius', Load::FIRST_RADIUS), Load::getSearchRadius(),
                        ['itemOptions' => ['class' => 'IK-C-3 IK-C-4 IK-C-5']]) ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6" style="padding: 0">
                <div class="col-xs-1">
                    <div class="control-label" style="text-align: right; margin-top: 5px">
                        <?php echo Yii::t('element', 'L-T-12aa'); ?>
                    </div>
                </div>
                <div class="col-xs-11" style="padding: 0;">
                    <div class="form-group">
                        <?php echo Select2::widget([
                            'name' => 'loadCityId',
                            'value' => is_null($loadCity) ? null : $loadCity->id,
                            'initValueText' => is_null($loadCity) ? null : $loadCity->getNameAndCountryCode(),
                            'options' => [
                                'id' => 'L-T-12a',
                                'multiple' => false,
                                'placeholder' => Yii::t('element', 'L-T-12')
                            ],
                            'pluginOptions' => [
                                'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
                                'allowClear' => true,
                                'ajax' => [
                                    'delay' => ElasticSearch::DEFAULT_DELAY,
                                    'dataType' => 'json',
                                    'url' => Url::to([
                                        'site/city-list',
                                        'lang' => Yii::$app->language,
                                    ]),
                                    'data' => new JsExpression(
                                        "function (params) {
                                            return {searchableCity: params.term};
                                        }"
                                    ),
                                    'processResults' => new JsExpression(
                                        "function (data) {
                                            return {results: data.items};
                                        }"
                                    ),
                                    'cache' => true,
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression(
                                    "function (data) {
                                        if (data.loading) {
                                            return data.text;
                                        }
                                        
                                        return '<div>' + 
                                                   data.name + 
                                                   '<div class=\"pull-right\">' + 
                                                       '<span class=\"map-text\" ' + 
                                                           'data-lat=\"' + data.location.lat + '\" ' +
                                                           'data-lon=\"' + data.location.lon + '\" ' +
                                                           'data-zoom=\"' + data.zoom + '\" ' +
                                                           'data-toggle=\"popover\" ' +
                                                           'data-placement=\"top\" ' +
                                                           'data-content=\"<div class=&quot;load-city-map&quot;></div>\"> ' + mapTranslate +
                                                       '</span>' + 
                                                   '</div>' + 
                                               '</div>';
                                    }"
                                ),
                                'templateSelection' => new JsExpression(
                                    "function (city) {
                                        if (typeof city.name != 'undefined') {
                                            return city.name;
                                        }
                                        return city.text;
                                    }"
                                ),
                            ],
                        ]); ?>
                    </div>
                    <div class="form-group load-country-filtration-container">
                        <?php echo Html::dropDownList('loadCountryId', Yii::$app->getRequest()->get('loadCountryId'),
                            $countries, [
                                'prompt' => Yii::t('element', 'L-T-12aaa'),
                                'class' => 'form-control',
                            ]); ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-6" style="padding: 0">
                <div class="col-xs-1">
                    <div class="control-label" style="text-align: right; margin-top: 5px">
                        <?php echo Yii::t('element', 'L-T-12bb'); ?>
                    </div>
                </div>
                <div class="col-xs-11" style="padding: 0">
                    <div class="form-group">
                        <?php echo Select2::widget([
                            'name' => 'unloadCityId',
                            'value' => is_null($unloadCity) ? null : $unloadCity->id,
                            'initValueText' => is_null($unloadCity) ? null : $unloadCity->getNameAndCountryCode(),
                            'options' => [
                                'id' => 'L-T-12b',
                                'multiple' => false,
                                'placeholder' => Yii::t('element', 'L-T-12'),
                            ],
                            'pluginOptions' => [
                                'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
                                'allowClear' => true,
                                'ajax' => [
                                    'delay' => ElasticSearch::DEFAULT_DELAY,
                                    'dataType' => 'json',
                                    'url' => Url::to([
                                        'site/city-list',
                                        'lang' => Yii::$app->language,
                                    ]),
                                    'data' => new JsExpression(
                                        "function (params) {
                                                return {searchableCity: params.term};
                                            }"
                                    ),
                                    'processResults' => new JsExpression(
                                        "function (data) {
                                                return {results: data.items};
                                            }"
                                    ),
                                    'cache' => true,
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression(
                                    "function (data) {
                                            if (data.loading) {
                                                return data.text;
                                            }
                                            
                                            return '<div>' + 
                                                       data.name + 
                                                       '<div class=\"pull-right\">' + 
                                                           '<span class=\"map-text\" ' + 
                                                               'data-lat=\"' + data.location.lat + '\" ' +
                                                               'data-lon=\"' + data.location.lon + '\" ' +
                                                               'data-zoom=\"' + data.zoom + '\" ' +
                                                               'data-toggle=\"popover\" ' +
                                                               'data-placement=\"top\" ' +
                                                               'data-content=\"<div class=&quot;load-city-map&quot;></div>\"> ' + mapTranslate + 
                                                           '</span>' + 
                                                       '</div>' + 
                                                   '</div>';
                                        }"
                                ),
                                'templateSelection' => new JsExpression(
                                    "function (city) {
                                            if (typeof city.name != 'undefined') {
                                                return city.name;
                                            }
                                            return city.text;
                                        }"
                                ),
                            ],
                        ]); ?>
                    </div>
                    <div class="form-group unload-country-filtration-container">
                        <?php echo Html::dropDownList('unloadCountryId',
                            Yii::$app->getRequest()->get('unloadCountryId'), $countries, [
                                'prompt' => Yii::t('element', 'L-T-12bbb'),
                                'class' => 'form-control',
                            ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-lg-2">
        <div class="form-group">
            <label class="control-label">
                <?php echo Yii::t('element', 'L-T-3a'); ?>
            </label>

            <?php echo Html::dropDownList('type', $type, Load::getLoadsTypes(), [
                'class' => 'L-T-3 L-T-4 form-control',
                'prompt' => Yii::t('app', 'DEFAULT_SUGGESTIONS'),
            ]); ?>

            <span class="select-addon"><i class="fa fa-caret-down"></i></span>
        </div>
        <div class="visible-lg" style="height: 33px">
            <?php echo Html::hiddenInput('isNewSearch', true) ?>
        </div>
        <div>
            <?php echo Html::submitButton(Icon::show('search', '', Icon::FA) . Yii::t('element', 'IK-C-16'), [
                'id' => 'IK-C-16',
                'class' => 'primary-button search-btn',
                'style' => 'float: right; margin-bottom: 10px'
            ]); ?>
        </div>
    </div>

</div>