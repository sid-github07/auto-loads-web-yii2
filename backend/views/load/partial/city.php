<?php

use common\components\ElasticSearch;
use common\models\City;
use common\models\LoadCity;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

/** @var ActiveForm $form */
/** @var LoadCity $loadCity */
/** @var string $attribute */
/** @var string $label */
/** @var string $id */
/** @var boolean $multiple */
/** @var string $placeholder */
/** @var boolean $isLoad */
/** @var string $containerClass */
/** @var string $url */

echo $form->field($loadCity, $attribute, [
    'options' => [
        'class' => $containerClass,
    ],
])->widget(Select2::className(), [
    'initValueText' => isset($loadCity->$attribute) ? City::getNameById($loadCity->$attribute) : '', // TODO: refactor
    'options' => [
        'id' => $id,
        'multiple' => $multiple,
        'placeholder' => $placeholder,
    ],
    'pluginOptions' => [
        'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'delay' => ElasticSearch::DEFAULT_DELAY,
            'data' => new JsExpression(
                "function (params) {
                    var countrySelector = ('" . $isLoad . "') ? '.load-country' : '.unload-country';
                    var countryCode = $(countrySelector).val();
                    return {phrase: params.term, code: countryCode};
                }"
            ),
            'processResults' => new JsExpression(
                "function (data) {
                    return {results: data.items};
                }"
            ),
            'cache' => true,
        ],
        'escapeMarkup' => new JsExpression("function (markup) { return markup; }"),
        'templateResult' => new JsExpression(
            "function (data) {
                if (data.loading) {
                    return data.text;
                }
                return data.name;
            }"
        ),
        'templateSelection' => new JsExpression(
            "function (city) {
                if (city.name != undefined) {
                    return city.name;
                }
                
                return city.text;
            }"
        ),
    ],
])->label($label);