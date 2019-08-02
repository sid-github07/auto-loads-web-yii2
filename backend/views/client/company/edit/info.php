<?php

use backend\controllers\ClientController;
use borales\extensions\phoneInput\PhoneInput;
use common\components\ElasticSearch;
use common\models\Company;
use common\models\User;
use common\widgets\VatCode;
use kartik\icons\Icon;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;

/** @var View $this */
/** @var Company $company */
/** @var string $activeVatRateLegalCountryCode */
/** @var array $vatRateCountries */
/** @var string $cityLegal */
/** @var array $phoneNumbers */
/** @var string $activePhoneNumber */

Icon::map($this, Icon::FI);
?>

<?php
$company->scenario = Company::SCENARIO_EDIT_COMPANY_DATA_CLIENT;
$company->ownerList->scenario = User::SCENARIO_CHANGE_COMPANY_CLASS;
foreach($company->companyUsers as $companyUser) {
    $companyUser->user->scenario = User::SCENARIO_EDIT_COMPANY_DATA_CLIENT;
}
?>
<div class="company-imprints-container">
    <?php $form = ActiveForm::begin([
        'id' => 'editCompanyInfoForm',
        'validationUrl' => ['client/edit-company-info-validation',  'lang' => Yii::$app->language],
        'action' => [
            'client/company',
            'lang' => Yii::$app->language,
            'id' => $company->id,
            'tab' => ClientController::TAB_COMPANY_INFO,
        ]
    ]); ?>

    <?php echo $form->field($company, 'visible')->checkbox([
        'id' => 'A-C-60',
        'disabled' => $company->archive == 1 ? true : false,
    ])->label(Yii::t('element', 'A-C-59'), [
        'class' => 'custom-checkbox'
    ]); ?>

    <?php echo $form->field($company, 'suggestions')
        ->checkbox(['id' => 'A-C-62'])
        ->label(Yii::t('element', 'A-C-61'), [
            'class' => 'custom-checkbox',
        ]); ?>

    <div class="row">
        <div class="select col-sm-6 col-xs-12">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo $form->field($company, 'owner_id')
                ->dropDownList($company->getAllCompanyUsersForDropDownList())
                ->label(Yii::t('element', 'A-C-65')); ?>
        </div>

        <div class="select col-sm-6 col-xs-12">
            <?php echo $form->field($company->ownerList, 'class')->widget(Select2::className(), [
                'data' => User::getClasses(),
                'language' => Yii::$app->language,
                'hideSearch' => true,
                'options' => ['multiple' => false],
                'pluginOptions' => [
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                ],
            ])->label(Yii::t('element', 'A-C-67')); ?>
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12 vat-code-container">
            <?php echo VatCode::widget([
                'model' => $company,
                'attribute' => 'vat_code',
                'vatRateCountries' => $vatRateCountries,
                'activeVatRateCountryCode' => $activeVatRateLegalCountryCode,
                'label' => Yii::t('element', 'A-C-69'),
                'inputOptions' => [
                    'id' => 'A-C-69',
                    'class' => 'form-control vat-code-input',
                ],
                'validationUrl' => Url::to(['client/edit-company-info-validation', 'lang' => Yii::$app->language]),
            ]); ?>

            <button id="N-C-15"
                    class="secondary-button validate-vat-code-button"
                    type="button"
                    data-toggle="popover"
                    data-content=""
                    onclick="validateVatCode()"
            >
                <?php echo Yii::t('element', 'N-C-15'); ?>
            </button>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'code', [
                'errorOptions' => [
                    'encode' => false,
                ],
                'inputOptions' => [
                    'id' => 'A-C-73',
                ],
            ])->label(Yii::t('element', 'A-C-73')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'address', [
                'inputOptions' => [
                    'id' => 'A-C-75',
                ],
            ])->label(Yii::t('element', 'A-C-75')); ?>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'city_id', [
                'inputOptions' => [
                    'id' => 'A-C-77',
                ],
            ])->label(Yii::t('element', 'A-C-77'))->widget(Select2::className(), [
                'options' => [
                    'placeholder' => '', /* NOTE: if placeholder is not set, then allowClear is not working */
                ],
                'pluginOptions' => [
                    'minimumInputLength' => ElasticSearch::MINIMUM_SEARCH_TEXT_LENGTH,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to(['client/city-list']),
                        'dataType' => 'json',
                        'delay' => ElasticSearch::DEFAULT_DELAY,
                        'data' => new JsExpression(
                            'function (params) { ' .
                            'return { ' .
                            'searchableCity:params.term' .
                            '};' .
                            '}'
                        ),
                        'processResults' => new JsExpression(
                            'function (data) { ' .
                            'return { ' .
                            'results: data.items' .
                            '};' .
                            '}'
                        ),
                        'cache' => true,
                    ],
                    'templateResult' => new JsExpression(
                        'function (data) { ' .
                        'if (data.loading) { ' .
                        'return data.text ' .
                        '}' .
                        'return data.name;' .
                        '}'
                    ),
                    'templateSelection' => new JsExpression(
                        'function (city) { ' .
                        'if (typeof city.name != "undefined") {' .
                        'return city.name; ' .
                        '} else {' .
                        'return "' . $cityLegal . '";' .
                        '}' .
                        '}'
                    ),
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'phone')->widget(PhoneInput::className(), [
                'defaultOptions' => [
                    'id' => 'A-C-79',
                    'class' => 'form-control'
                ],
                'jsOptions' => [
                    'initialCountry' => 'lt', // Only lithuanian language is in administration panel
                    'nationalMode' => false, // Changes Lithuanian placeholder from (8-612) 34567 to +370 612 34567
                ],
            ])->label(Yii::t('element', 'A-C-79')); ?>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'email')->label(Yii::t('element', 'A-C-81')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php echo $form->field($company, 'website')->label(Yii::t('element', 'A-C-83')); ?>
        </div>
    </div>

    <div class="company-sign-up-date-container">
        <span id="A-C-85" class="company-sign-up-date-title">
            <?php echo Yii::t('element', 'A-C-85'); ?>
        </span>

        <span id="A-C-86" class="company-sign-up-date-value">
            <?php echo date('Y-m-d H:i:s', $company->created_at); ?>
        </span>
    </div>

    <div class="text-center">
        <?php echo Html::submitButton(Icon::show('save', [], Icon::FA) . Yii::t('element', 'A-C-87'), [
            'id' => 'A-C-87',
            'class' => 'primary-button',
        ]); ?>
        
        <?php echo Html::a('Peržiūrėti anketą', [
            'client/index',
            'lang' => Yii::$app->language,
            Company::COMPANY_FILTER_INPUT_NAME => $company->email
        ], [
            'id' => 'A-C-88',
            'class' => 'secondary-button review-account-btn',
            'target' => '_blank'
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(
    'var vatCodeInputIds = []; ' .
    'var companyInfoByVatCode = "' . Url::to(['client/company-info-by-vat-code', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionValidateVatCode = "' . Url::to(['client/validate-vat-code', 'lang' => Yii::$app->language]) . '";',
View::POS_BEGIN);

$this->registerJsFile(Url::base() . '/dist/js/widgets/vat-code.js', ['depends' => [JqueryAsset::className()]]);