<?php

use borales\extensions\phoneInput\PhoneInput;
use common\components\ElasticSearch;
use common\models\Company;
use common\widgets\VatCode;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/** @var Company $company */
/** @var array $vatRateCountries */
/** @var string $activeVatRate */
/** @var string $city */

$company->scenario = Company::SCENARIO_CONTACT_INFO_LEGAL;
?>
<?php $form = ActiveForm::begin([
    'id' => 'contact-info-legal-form',
    'action' => ['settings/contact-info', 'lang' => Yii::$app->language],
    'validationUrl' => ['settings/contact-info-validation', 'lang' => Yii::$app->language],
]); ?>
    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>

    <div class="contact-admin-wrapper">
        <div class="change-vat-code-wrapper">
            <?php echo VatCode::widget([
                'model' => $company,
                'attribute' => 'vat_code',
                'vatRateCountries' => $vatRateCountries,
                'activeVatRateCountryCode' => $activeVatRate,
                'label' => Yii::t('element', 'N-C-12'),
                'inputOptions' => [
                    'id' => 'N-C-14',
                    'disabled' => true,
                    'class' => 'form-control vat-code-input',
                ],
            ]); ?>

            <?php echo Icon::show('question-circle-o', [
                'id' => 'N-C-15',
                'class' => 'fa-2x how-to-change-icon',
                'title' => Yii::t('element', 'N-C-15a'),
                'data-toggle' => 'popover',
                'data-content' => Yii::t('element', 'N-C-15b'),
                'data-placement' => 'top',
            ], Icon::FA); ?>
        </div>
        
        <?php echo Html::button(Yii::t('element', 'N-C-15c'), [
            'id' => 'N-C-15c',
            'class' => 'primary-button contact-admin change-vat-code',
            'name' => 'change-vat-code-button',
        ]); ?>
    </div>


    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'code', [
                'enableAjaxValidation' => true,
                'inputOptions' => [
                    'id' => 'N-C-17b',
                ],
            ])->label(Yii::t('element', 'N-C-16b')); ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'title', [
                'enableAjaxValidation' => true,
                'errorOptions' => [
                    'encode' => false,
                ],
                'inputOptions' => [
                    'id' => 'N-C-19c',
                ],
            ])->label(Yii::t('element', 'N-C-18c')); ?>
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
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
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

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($company, 'email', [
                'inputOptions' => [
                    'id' => 'N-C-27',
                ]
            ])->label(Yii::t('element', 'N-C-26')); ?>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($company, 'website', [
                'inputOptions' => [
                    'id' => 'N-C-29',
                ],
            ])->label(Yii::t('element', 'N-C-28')); ?>
        </div>
    </div>

    <div class="text-center">
        <?php echo Html::submitButton(Icon::show('floppy-o', '', Icon::FA) . Yii::t('element', 'N-C-30'), [
            'id' => 'N-C-30',
            'class' => 'primary-button settings-save-btn',
            'name' => 'contact-info-legal-button',
        ]); ?>
    </div>
<?php ActiveForm::end(); ?>

<?php Modal::begin([
    'id' => 'change-vat-code',
    'header' => Yii::t('app', 'CHANGE_VAT_CODE_MODAL_HEADER'),
    'size' => 'modal-lg'
]); ?>
    <?php echo Yii::$app->controller->renderPartial('____change-vat-code-modal', [
        'company' => $company,
    ]); ?>
<?php Modal::end();

$this->registerJs(
    'var mapTranslate = "'. Yii::t('element', 'MAP_TRANSLATE') . '";',    
View::POS_BEGIN);
