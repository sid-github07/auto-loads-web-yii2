<?php

use common\components\ElasticSearch;
use common\models\User;
use common\models\Service;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;

/** @var View $this */
/** @var User $user */
/** @var array $languages */
/** @var array $vatRateCountries */
/** @var string $activeVatRateLegalCountryCode */
/** @var string $cityLegal */

Icon::map($this, Icon::FA);
$this->title = Yii::t('seo', 'TITLE_BUY_CREDIT');
?>

<div class="site-sign-up">
    <h1 id="RG-F-1">
        <?php echo Yii::t('element', 'CC-F-1'); ?>
    </h1>
    
    <div class="sign-up-form-container">
        <?php $form = ActiveForm::begin([
            'id' => 'buy-credits-form',
            'options' => ['data-pjax' => ''],
            'validationUrl' => ['creditcode/buy-credits-validation', 'lang' => Yii::$app->language],
        ]); ?>
            <span class="required-fields-text">
                <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
            </span>
            
            <div class="clearfix">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php 
                        echo $form->field($user, 'creditCodeService', [
                        'inputOptions' => [
                            'id' => 'CC-F-18',
                            'class' => 'form-control account-type-input',
                        ],
                        ])->dropDownList(Service::getCreditCodeServices(), [
                            'prompt' => Yii::t('element', 'CC-F-25'),
                        ])->label(Yii::t('element', 'CC-F-24'));
                        ?>
                        <span class="select-addon"><i class="fa fa-caret-down"></i></span>
                    </div>
                </div>
            </div>


            <div class="clearfix">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'name', [
                            'enableAjaxValidation' => true,
                            'inputOptions' => [
                                'id' => 'RG-F-6',
                            ],
                        ])->label(Yii::t('element', 'CC-F-5')); ?>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'surname', [
                            'enableAjaxValidation' => true,
                            'inputOptions' => [
                                'id' => 'RG-F-6a',
                            ],
                        ])->label(Yii::t('element', 'CC-F-5a')); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <?php echo $form->field($user, 'addressNatural', [
                            'options' => [
                                'class' => 'form-group required',
                            ],
                            'inputOptions' => [
                                'id' => 'RG-F-18-jd',
                            ],
                        ])->label(Yii::t('element', 'CC-F-18-jd')); ?>
                    </div>
                    
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'cityIdNatural', [
                            'options' => [
                                'class' => 'form-group required',
                            ],
                            'inputOptions' => [
                                'id' => 'CC-F-18-jc',
                            ],
                        ])->label(Yii::t('element', 'CC-F-18-jc'))->widget(Select2::className(), [
                            'options' => [
                                'placeholder' => '', /* NOTE: if placeholder is not set, then allowClear is not working */
                            ],
                            'pluginOptions' => [
                                'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
                                'allowClear' => true,
                                'ajax' => [
                                    'url' => Url::to(['site/city-list']),
                                    'dataType' => 'json',
                                    'delay' => ElasticSearch::DEFAULT_DELAY,
                                    'data' => new JsExpression("function (params) {
                                        return {searchableCity:params.term};
                                    }"),
                                    'processResults' => new JsExpression("function (data) {
                                        return {results: data.items};
                                    }"),
                                    'cache' => true,
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression("function (data) {
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
                                }"),
                                'templateSelection' => new JsExpression("function (city) {
                                    if (typeof city.name != 'undefined') {
                                        return city.name;
                                    }
                                    return '" . $cityLegal . "';
                                }"),
                            ],
                        ]); ?>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'email', [
                            'errorOptions' => [
                                'encode' => false,
                            ],
                            'enableAjaxValidation' => true,
                            'inputOptions' => [
                                'id' => 'RG-F-4',
                            ],
                        ])->label(Yii::t('element', 'CC-F-3')); ?>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'account_type', [
                                'inputOptions' => [
                                    'id' => 'RG-F-18',
                                    'class' => 'form-control account-type-input',
                                ],
                            ])->dropDownList(User::getAccountTypes(), [
                                'prompt' => Yii::t('app', 'ACCOUNT_TYPE_DROP_DOWN_LIST_PROMPT'),
                            ])->label(Yii::t('element', 'CC-F-17')); ?>
                        <span class="select-addon"><i class="fa fa-caret-down"></i></span>
                    </div>
                </div>

            <div class="legal-container clearfix hidden">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo Yii::$app->controller->renderPartial('/site/partial/vat-code', [
                            'form' => $form,
                            'model' => $user,
                            'attribute' => 'vatCodeLegal',
                            'id' => 'RG-F-18-je',
                            'containerClass' => '',
                            'activeCountryCode' => $activeVatRateLegalCountryCode,
                            'disabled' => '', // NOTE: if input should be disabled, then write 'disabled' otherwise leave empty
                            'countries' => $vatRateCountries,
                            'label' => Yii::t('element', 'CC-F-18-je'),
                            'inputClass' => 'form-control vat-code-input',
                        ]); ?>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php echo $form->field($user, 'company_code', [
                            'enableAjaxValidation' => true,
                            'options' => [
                                'class' => 'form-group required',
                            ],
                            'errorOptions' => [
                                'encode' => false,
                            ],
                            'inputOptions' => [
                                'id' => 'RG-F-18ja',
                            ],
                        ])->label(Yii::t('element', 'CC-F-18ja')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <?php echo $form->field($user, 'company_name', [
                                'enableAjaxValidation' => true,
                                'options' => [
                                    'class' => 'form-group required',
                                ],
                                'errorOptions' => [
                                    'encode' => false,
                                ],
                                'inputOptions' => [
                                    'id' => 'RG-F-18-jb',
                                ],
                            ])->label(Yii::t('element', 'CC-F-18-jb')); ?>
                    </div>
                </div>
            </div>
        
            <?php echo $form->field($user, 'rulesAgreement', [
                'options' => [
                    'class' => 'custom-checkbox rules-agreement'
                ]])->checkbox([
                    'id' => 'RG-F-21',
                    'class' => 'checkbox-input',
                ])->label(Yii::t('element', 'CC-F-22', [
                    'rules' => Html::a(
                        Yii::t('element', 'CC-F-22a'),
                        Url::to(['site/guidelines', 'lang' => Yii::$app->language]),
                        ['target' => '_blank']),
                ])
            ); ?>
            <div class="clearfix">
                <?php echo Html::submitButton(Icon::show('user-plus', '', Icon::FA) . Yii::t('element', 'CC-F-23'), [
                    'id' => 'CC-F-23',
                    'class' => 'primary-button sign-up-submit-btn',
                ]); ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs(
    'var ACCOUNT_TYPE_NATURAL = "' . User::NATURAL . '"; ' .
    'var ACCOUNT_TYPE_LEGAL = "' . User::LEGAL . '"; ' .
    'var accountType = "' . $user->account_type . '"; ' .
    'var vatCodeInputIds = []; ' .
    'var companyInfoByVatCode = "' . Url::to(['site/company-info-by-vat-code', 'lang' => Yii::$app->language]) . '"; '.
    'var mapTranslate = "'. Yii::t('element', 'MAP_TRANSLATE') . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/site/sign-up.js', ['depends' => [JqueryAsset::className()]]);