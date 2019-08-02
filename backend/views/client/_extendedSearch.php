<?php

use common\models\City;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\User;
use common\models\UserService;
use common\models\UserServiceActive;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var User $user */
/** @var CompanyDocument $companyDocument */
/** @var UserServiceActive $userServiceActive */
/** @var UserService $userService */
/** @var Company $company */
/** @var string $searchText */
?>

<?php $form = ActiveForm::begin([
    'id' => 'extended-search-form',
    'action' => ['client/index'],
    'method' => 'GET',
]); ?>

    <div class="client-search-container">
        <div class="form-group">
            <label class="control-label">
                <?php echo Yii::t('element', 'A-C-2'); ?>
            </label>
            
            <div class="search-wrapper">
                <div class="search-control">
                    <?php echo Html::input('text', Company::COMPANY_FILTER_INPUT_NAME, $searchText, [
                        'class' => 'form-control search-input',
                        'placeholder' => Yii::t('element', 'A-C-3'),
                    ]); ?>

                    <button type="submit" 
                            id="A-C-6" 
                            class="primary-button search-btn" 
                            name="textSearch" 
                            title="<?php echo Yii::t('element', 'A-C-6'); ?>"
                            data-toggle="tooltip"
                            data-placement="top"
                    >
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                
                <span id="A-C-4" class="A-C-4 detail-search" data-toggle="collapse" data-target="#detailed-search">
                    <?php echo Yii::t('element', 'A-C-4'); ?>
                </span>
            </div>
        </div>
        
        <div id="detailed-search" class="detailed-search collapse clearfix">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <?php echo $form->field($user, 'class')->widget(Select2::className(), [
                        'data' => User::getClasses(),
                        'language' => Yii::$app->language,
                        'hideSearch' => true,
                        'options' => [
                            'multiple' => false,
                            'placeholder' => Yii::t('app', 'CLASS_LABEL_ALL_CATEGORIES_DEFAULT'),
                        ],
                        'pluginOptions' => [
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        ],
                    ])->label(Yii::t('element', 'A-C-30')); ?>
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo $form->field($companyDocument, 'documentActivity')
                    ->dropDownList($companyDocument->getTranslatedDocumentActivity(), [
                        'prompt' => Yii::t('app', 'DOCUMENT_EXPIRATION_DEFAULT')
                    ])->label(Yii::t('element', 'A-C-32')); ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo $form->field($userServiceActive, 'status')
                    ->dropDownList($userServiceActive->getTranslatedActiveService(), [
                        'prompt' => Yii::t('app', 'COMPANY_SUBSCRIPTION_ACTIVE_DEFAULT')
                    ])->label(Yii::t('element', 'A-C-34')); ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php //TODO: add scenarios to userService module
                    echo $form->field($userService, 'paid')
                    ->dropDownList($userService->getTranslatedSubscriptionOptions(), [
                        'prompt' => Yii::t('app', 'COMPANY_SUBSCRIPTION_DEFAULT')
                    ])->label(Yii::t('element', 'A-C-36')); ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo $form->field($company, 'invitedUsers')
                    ->dropDownList($company->getTranslatedInvitedUsers(), [
                        'prompt' => Yii::t('app', 'COMPANY_INVITED_USER_HAS_DEFAULT')
                    ])->label(Yii::t('element', 'A-C-38')); ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo $form->field($company, 'companyStatus')
                    ->dropDownList($company->getTranslatedCompanyActivityType(), [
                        'prompt' => Yii::t('app', 'COMPANY_ACTIVE_DEFAULT')
                    ])->label(Yii::t('element', 'A-C-40')); ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <label class="control-label">
                            <?php echo Yii::t('element', 'A-C-42'); ?>
                        </label>

                        <?php echo DatePicker::widget([
                            'model' => $user,
                            'attribute' => 'start_last_login',
                            'attribute2' => 'end_last_login',
                            'language' => 'lt',
                            'type' => DatePicker::TYPE_RANGE,
                            'form' => $form,
                            'separator' => Yii::t('element', 'A-C-45a'),
                            'pluginOptions' => [
                                'previous' => '<',
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]); ?>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <label class="control-label">
                            <?php echo Yii::t('element', 'A-C-47'); ?>
                        </label>

                        <?php echo DatePicker::widget([
                            'model' => $user,
                            'attribute' => 'start_created_at',
                            'attribute2' => 'end_created_at',
                            'language' => 'lt',
                            'type' => DatePicker::TYPE_RANGE,
                            'form' => $form,
                            'separator' => Yii::t('element', 'A-C-45a'),
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <label class="control-label">
                            <?php echo Yii::t('element', 'A-C-52'); ?>
                        </label>

                        <?php echo DatePicker::widget([
                            'model' => $userServiceActive,
                            'attribute' => 'dateSubscribeFrom',
                            'attribute2' => 'dateSubscribeTo',
                            'language' => 'lt',
                            'type' => DatePicker::TYPE_RANGE,
                            'form' => $form,
                            'separator' => Yii::t('element', 'A-C-45a'),
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ]); ?>
                    </div>
                </div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group clearfix">
                        <?php $emptySelection[0] = Yii::t('text', 'EXTENDED_SEACRH_BY_COUNTRY'); ?>
                        <?php $countryList = City::getCountryCodes(); ?>
                        <?php array_unshift($countryList, $emptySelection[0]) ?>
                        <?php  echo $form->field($company, 'city_id')->dropDownList($countryList, 
                                ['id' => 'countryFilter'])->label(Yii::t('element', 'A-C-88')); ?>
					</div>
                </div>  
            </div>
			<div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <?php echo $form->field($company, 'potential')->dropDownList(Company::potentialitySelect(), 
                            ['id' => 'potentialFilter',
                             'prompt' => Yii::t('app', 'COMPANY_POTENTIAL_DEFAULT')   
                            ]
                            )->label(Yii::t('element', 'A-C-89a')) ?>
                </div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <?php echo $form->field($user, 'came_from_id')->dropDownList(Company::reasonsSelect(), 
                            ['id' => 'reasonFilter',
                             'prompt' => Yii::t('app', 'COMPANY_REASON_DEFAULT')   
                            ]
                            )->label(Yii::t('element', 'A-C-89b')) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="search-secondary-buttons clearfix">
        <button type="submit" id="A-C-6a" class="secondary-button" name="extendedSearch">
            <?php echo Yii::t('element', 'A-C-6a'); ?>
        </button>
        
        <?php echo Html::a(Yii::t('element', 'A-C-8'), '#', [
            'id' => 'A-C-8',
            'class' => 'secondary-button',
        ]); ?>

        <?php echo Html::a(Yii::t('element', 'A-C-9'), ['/client/index', 'lang' => Yii::$app->language], [
            'id' => 'A-C-9',
            'class' => 'secondary-button',
        ]); ?>
    </div>
<?php ActiveForm::end(); ?>