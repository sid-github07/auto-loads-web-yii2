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
/** @var null|array $data */
/** @var string $label */
/** @var array $labelOptions */
/** @var string $placeholder */
/** @var string $id */
/** @var null|string $unloadId */
/** @var null|string $onchange */
/** @var string $url */

echo $form->field($model, $attribute)->label($label, $labelOptions)->widget(Select2::className(), [
    'options' => [
        'id' => $id,
        'multiple' => true,
        'placeholder' => $placeholder,
        'onchange' => $onchange,
    ],
    'showToggleAll' => false,
    'data' => $data,
    'pluginOptions' => [
        'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
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
                    archiveLocations(data.items);
                    return { results: data.items };
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
                
                if (isDirection(data)) {
                    return getDirectionSuggestion(data.name);
                }
                
                return getSimpleSuggestion(data);
            }"
        ),
        'templateSelection' => new JsExpression(
            "function (location) {
                return getLocationName(location);
            }"
        ),
    ],
    'pluginEvents' => [
        'select2:unselect' => new JsExpression(
            "function () {
                removeLocation(this.id);
            }"
        ),
    ],
]);

if (!is_null($unloadId)) {
    $this->registerJs('var loadId = "' . $id . '";', View::POS_BEGIN);
    $this->registerJs('var unloadId = "' . $unloadId . '";', View::POS_BEGIN);
}
$this->registerJs('var translateMap = "' . Yii::t('element', 'MAP_TRANSLATE') . '";', View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/partial/multiple-locations.js', ['depends' => [JqueryAsset::className()]]);