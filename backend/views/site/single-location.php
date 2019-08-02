<?php

use common\components\ElasticSearch;
use kartik\select2\Select2;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;

/** @var View $this */
/** @var ActiveForm $form */
/** @var Model $model */
/** @var string $attribute */
/** @var string $label */
/** @var array $labelOptions */
/** @var string $id */
/** @var string $url */

echo $form->field($model, $attribute)->label($label, $labelOptions)->widget(Select2::className(), [
    'options' => [
        'id' => $id,
        'multiple' => false,
        'placeholder' => '',
    ],
    'pluginOptions' => [
        'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
        'allowClear' => true,
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'delay' => ElasticSearch::DEFAULT_DELAY,
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
                return location.name;
            }"
        ),
    ],
]);

$this->registerJsFile(Url::base() . '/dist/js/site/location-suggestion.js', ['depends' => [JqueryAsset::className()]]);