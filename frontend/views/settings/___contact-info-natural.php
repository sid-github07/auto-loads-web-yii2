<?php

use borales\extensions\phoneInput\PhoneInput;
use common\components\ElasticSearch;
use common\models\Company;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/** @var Company $company */
/** @var ActiveForm $form */
/** @var string $city */

$company->scenario = Company::SCENARIO_CONTACT_INFO_NATURAL;
?>
<?php $form = ActiveForm::begin([
    'id' => 'contact-info-natural-form',
    'action' => ['settings/contact-info', 'lang' => Yii::$app->language],
    'validationUrl' => ['settings/contact-info-validation', 'lang' => Yii::$app->language],
]); ?>

<div class="required-fields-text">
    <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'name', [
            'enableAjaxValidation' => true,
            'inputOptions' => [
                'id' => 'N-C-19a',
            ],
        ])->label(Yii::t('element', 'N-C-18a')); ?>
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'surname', [
            'enableAjaxValidation' => true,
            'inputOptions' => [
                'id' => 'N-C-19b',
            ],
        ])->label(Yii::t('element', 'N-C-18b')); ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'address', [
            'inputOptions' => [
                'id' => 'N-C-21',
            ],
        ])->label(Yii::t('element', 'N-C-20')); ?>
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'city_id', [
            'options' => [
                'class' => 'form-group required',
            ],
            'inputOptions' => [
                'id' => 'N-C-23',
            ],
        ])->label(Yii::t('element', 'N-C-22'))->widget(Select2::className(), [
            'options' => [
                'placeholder' => '', // NOTE: if placeholder is not set, then allowClear is not working
            ],
            'pluginOptions' => [
                'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
                'allowClear' => true,
                'ajax' => [
                    'url' => Url::to(['site/city-list']),
                    'dataType' => 'json',
                    'delay' => ElasticSearch::DEFAULT_DELAY,
                    'data' => new JsExpression(
                        'function (params) { ' .
                            'return { ' .
                                'searchableCity:params.term' .
                            '};' .
                        '}'),
                    'processResults' => new JsExpression(
                        'function (data) { ' .
                            'return { ' .
                                'results: data.items' .
                            '};' .
                        '}'),
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
                    'function (city) { ' .
                        'if (typeof city.name != "undefined") { ' .
                            'return city.name; ' .
                        '} else { ' .
                            'return "' . $city . '";' .
                        '}' .
                    '}'),
            ],
        ]); ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'phone')->widget(PhoneInput::className(), [
            'defaultOptions' => [
                'id' => 'N-C-25',
                'class' => 'form-control',
            ],
            'jsOptions' => [
                // NOTE: there is no such country as 'en' therefore it is changed to 'gb'
                'initialCountry' => Yii::$app->language == 'en' ? 'gb' : Yii::$app->language,
                'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) 34567 to +370 612 34567
            ],
        ])->label(Yii::t('element', 'N-C-24')); ?>
    </div>
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <?php echo $form->field($company, 'email', [
            'inputOptions' => [
                'id' => 'N-C-27',
            ],
        ])->label(Yii::t('element', 'N-C-26')); ?>
    </div>
</div>

<div class="text-center">
    <?php echo Html::submitButton(Icon::show('floppy-o', '', Icon::FA) . Yii::t('element', 'N-C-30'), [
        'id' => 'N-C-30',
        'class' => 'primary-button settings-save-btn',
        'name' => 'contact-info-natural-button',
    ]); ?>
</div>
<?php
//    echo $form->field($company, 'personal_code', [
//        'enableAjaxValidation' => true,
//        'inputOptions' => [
//            'id' => 'N-C-17',
//        ],
//    ])->label(Yii::t('element', 'N-C-16a'));
?>
<?php ActiveForm::end();

$this->registerJs(
    'var mapTranslate = "'. Yii::t('element', 'MAP_TRANSLATE') . '";',
View::POS_BEGIN);