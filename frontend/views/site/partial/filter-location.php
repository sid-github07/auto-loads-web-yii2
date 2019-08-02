<?php

use common\components\ElasticSearch;
use common\models\City;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var string $name
 * @var null|City $city
 * @var string $id
 * @var string $placeholder
 * @var string $onchange
 * @var string $url
 */

if (!isset($onchange)) {
    $onchange = '';
}

echo Select2::widget([
    'name' => $name,
    'value' => is_null($city) ? null : $city->id,
    'initValueText' => is_null($city) ? null : $city->getNameAndCountryCode(),
    'options' => [
        'id' => $id,
        'multiple' => false,
        'placeholder' => $placeholder,
        'onchange' => $onchange,
    ],
    'pluginOptions' => [
        'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
        'allowClear' => true,
        'ajax' => [
            'delay' => ElasticSearch::DEFAULT_DELAY,
            'dataType' => 'json',
            'url' => $url,
            'data' => new JsExpression(
                "function (params) {
                    return { phrase: params.term };
                }"
            ),
            'processResults' => new JsExpression(
                "function (data) {
                    return { results: data.items };
                }"
            ),
            'cache' => true,
        ],
        'escapeMarkup' => new JsExpression(
            "function (markup) {
                return markup;
            }"
        ),
        'templateResult' => new JsExpression(
            "function (data) {
                if (data.loading) {
                    return data.text;
                }
                
                return getSimpleSuggestion(data);
            }"
        ),
        'templateSelection' => new JsExpression(
            "function (location) {
                if (typeof location.name != 'undefined') {
                    return location.name;
                }
                
                return location.text;
            }"
        ),
    ],
]);

$this->registerJs(
    'var mapTranslate = "'. Yii::t('element', 'MAP_TRANSLATE') . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/partial/location-suggestion.js', ['depends' => [JqueryAsset::className()]]);