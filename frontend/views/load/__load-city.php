<?php

use common\components\ElasticSearch;
use common\models\LoadCity;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use yii\web\View;

/** @var ActiveForm $form */
/** @var string $id */
/** @var LoadCity $loadCity */
/** @var string $attribute */
/** @var string $label */
/** @var string $placeholder */
/** @var boolean $multiple */
/** @var array $cities */
/** @var string $url */
/** @var boolean $fillLoadCity */

?>
<?php
echo $form->field($loadCity, $attribute)->label($label)->widget(Select2::className(), [
    'initValueText' => (isset($fillLoadCity) && $fillLoadCity && !is_null($loadCity->city)) ? $loadCity->city->getNameAndCountryCode() : null,
    'options' => [
        'id' => $id,
        'multiple' => $multiple,
        'placeholder' => $placeholder,
    ],
    'showToggleAll' => false,
    'pluginOptions' => [
        'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'delay' => ElasticSearch::DEFAULT_DELAY,
            'data' => new JsExpression(
                "function (params) {
                    return {searchableCity: params.term};
                }"
            ),
            'processResults' => new JsExpression(
                "function (data) {
                    $.each(data.items, function(key, city) {
                        if (!(city.id in archivedCities)) {
                            archivedCities[city.id] = city;
                        }
                    });
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
                if (isDirection(data.id.toString())) {
                    return '<div class=\'direction\'>' + data.name + '</div>';
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
                
                var cityName = '';
                if ($.isEmptyObject(archivedCities) && !($.isEmptyObject(cities))) {
                    $.each(cities, function(key, cityInfo) {
                        if (city.id == cityInfo.id) {
                            cityName = cityInfo.name + ' (' + cityInfo.country_code + ')';
                        }
                    });
                } else {
                    $.each(archivedCities, function(key, archivedCity) {
                        var cityId = archivedCity.id.toString();
                        if (cityId.indexOf('-') >= 0) {
                            var loadCityId = cityId.split('-')[0];
                            var unloadCityId = cityId.split('-')[1];
                            if (city.id == loadCityId) {
                                cityName = archivedCity.popularName;
                            } else if (city.id == unloadCityId) {
                                cityName = archivedCity.directionName;
                            }
                        } else {
                            if (city.id == cityId) {
                                cityName = archivedCity.name;
                            }
                        }
                    });
                }
                if (cityName == '') {
                    return '" . ((isset($fillLoadCity) && $fillLoadCity && !is_null($loadCity->city)) ? $loadCity->city->getNameAndCountryCode() : null) . "';
                }
                return cityName;
            }"
        ),
    ],
]);

$this->registerJs(
    'var archivedCities = {}; ' .
    'var cities = ' . json_encode($cities) . ';' .
	'var mapTranslate = "'. Yii::t('element', 'MAP_TRANSLATE') . '";', 
View::POS_BEGIN);