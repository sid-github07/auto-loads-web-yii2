<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use frontend\controllers\SubscriptionController;

/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var integer $serviceCredits */
/** @var integer $subscriptionCredits */
/** @var string $subscriptionEndTime */
/** @var array $advertDayList */
/** @var array $openContactsDayList */
/** @var array $openContactsCost */

$form = ActiveForm::begin([
    'id' => 'announcement-form',
    'validationUrl' => ['car-transporter-announcement/validate-available-from-date'],
    'action' => ['car-transporter-announcement/announcement', 'lang' => Yii::$app->language],
]); ?>
    <div class="clearfix wizard-h-wrapper">
        <div class="col-lg-1 visible-lg wizard-icon-no-padding">
            <div class="wizard-element-circle-h">
                <img src="<?php echo Yii::getAlias('@web') . '/images/info.png'; ?>"
                     class="wizard-element-image"
                     alt="<?php echo Yii::t('text', 'ALT_TAG_INFO'); ?>" />
            </div>
        </div>
            
        <div class="col-lg-11 col-xs-12">
            <div class="wizard-card">
                <div class="row">
                    <div class="col-sm-6 col-xs-12 select">
                        <?php echo $form->field($carTransporter, 'quantity')
                            ->dropDownList(CarTransporter::getQuantities(), ['prompt' => Yii::t('element', 'C-T-29'), 'id' => 'C-T-29']
                            )->label(Yii::t('element', 'C-T-28'), ['id' => 'C-T-28']);
                        ?>
                        <span class="select-addon"><i class="fa fa-caret-down"></i></span>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <?php echo $form->field($carTransporter, 'available_from', [
                            'enableAjaxValidation' => true,
                        ])->widget(DatePicker::className(), [
                            'options' => ['id' => 'C-T-31'],
                            'language' => Yii::$app->language,
                            'clientOptions' => [
                                'startDate' => date('Y-m-d'),
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                            ],
                        ])->label(Yii::t('element', 'C-T-30'), ['id' => 'C-T-30']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix wizard-h-wrapper">
        <div class="col-lg-1 visible-lg wizard-icon-no-padding">
            <div class="wizard-element-circle-h">
                <img src="<?php echo Yii::getAlias('@web') . '/images/location.png'; ?>"
                     class="wizard-element-image"
                     alt="<?php echo Yii::t('text', 'ALT_TAG_LOCATION'); ?>" />
            </div>
        </div>
            
        <div class="col-lg-11 col-xs-12">
            <div class="wizard-card">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <?php echo Yii::$app->controller->renderPartial('/site/partial/multiple-locations', [
                            'form' => $form,
                            'model' => $carTransporterCity,
                            'attribute' => 'loadLocations',
                            'data' => null,
                            'label' => Html::tag('div', Yii::t('element', 'C-T-32'), ['class' => 'city-label']) .
                                Html::tag('span', Yii::t('element', 'IA-C-5a'), ['class' => 'select-few-cities']),
                            'labelOptions' => ['id' => 'C-T-32'],
                            'placeholder' => '',
                            'id' => 'C-T-33',
                            'unloadId' => 'C-T-35',
                            'onchange' => null,
                            'url' => Url::to([
                                'site/search-for-location',
                                'lang' => Yii::$app->language,
                                'showDirections' => true,
                            ]),
                        ]); ?>
                    </div>

                    <div class="col-sm-6 col-xs-12">
                        <?php echo Yii::$app->controller->renderPartial('/site/partial/multiple-locations', [
                            'form' => $form,
                            'model' => $carTransporterCity,
                            'attribute' => 'unloadLocations',
                            'data' => null,
                            'label' => Html::tag('div', Yii::t('element', 'C-T-34'), ['class' => 'city-label']) .
                                Html::tag('span', Yii::t('element', 'IA-C-5a'), ['class' => 'select-few-cities']),
                            'labelOptions' => ['id' => 'C-T-34'],
                            'placeholder' => '',
                            'id' => 'C-T-35',
                            'unloadId' => null,
                            'onchange' => null,
                            'url' => Url::to([
                                'site/search-for-location',
                                'lang' => Yii::$app->language,
                                'showDirections' => false,
                            ]),
                        ]); ?>
                    </div>
                </div>
                <div class="row load-location-row">
                    <div class="load-unload-city-container col-sm-6 col-xs-12">
                        <?php echo $form->field($carTransporterCity, 'load_postal_code', [
                            'inputOptions' => [
                                'class' => 'form-control IA-C-12',
                                'maxlength' => CarTransporterCity::POSTAL_CODE_MAX_LENGTH,
                            ],
                            ])->label(Yii::t('app', 'LOAD_POSTAL_CODE_LABEL')); 
                        ?> 
                    </div>
                    <div class="load-unload-city-container col-sm-6 col-xs-12">
                        <?php echo $form->field($carTransporterCity, 'unload_postal_code', [
                            'inputOptions' => [
                                'class' => 'form-control IA-C-12',
                                'maxlength' => CarTransporterCity::POSTAL_CODE_MAX_LENGTH,
                            ],
                            ])->label(Yii::t('app', 'UNLOAD_POSTAL_CODE_LABEL')); 
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix wizard-h-wrapper">
        <div class="col-lg-1 visible-lg wizard-icon-no-padding">
            <div class="wizard-element-circle-h">
                <i class="fa fa-bullhorn"></i>
            </div>
        </div>

        <div class="col-lg-11 col-xs-12">
            <div class="wizard-card adv-card">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="row">
                        <h4 class="wizard-card-title">
                            <?php echo Yii::t('element', 'car_transporter_advertisement')?>
                        </h4>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="row">
                        <h4 class="wizard-card-title">
                            <?php echo Yii::t('element', 'car_transporter_open_contacts')?>
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning adv-alert" style="display: none;">
                            <?php echo Yii::t('element', 'not_enough_total_credits', [
                                'creditTopupLink' => Html::a(
                                    Yii::t('element', 'adv_credits_topup'),
                                    Url::to(['subscription/', 
                                        'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER]
                                    ), [
                                        'target' => '_blank',
                                        'data-pjax' => 0,
                                        'class' => 'credit-topup-link',
                                    ]
                                ),
                                'subscriptionEndTime' => $subscriptionEndTime,
                                'subscriptionCredits' => $subscriptionCredits,
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm12">
                                <div class="material-icons adv-icon" >directions_car</div>
                            </div>
                            <div class="col-lg-10 col-md-10 col-sm-10">
                                <?php echo $form->field($carTransporter, 'car_pos_adv', [
                                        'enableClientValidation' => false,
                                        'enableAjaxValidation' => false,
                                    ])->dropDownList([
                                            0 => Yii::t('element', 'select_car_pos_num'),
                                            1,2,3,4,5,6,7,8,9,10
                                    ], ['disabled' => $serviceCredits + $subscriptionCredits === 0 ? 'disabled' : false, 'id' => 'car-count'])
                                    ->label(Yii::t('element', 'car_pos_adv_number'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm12">
                                <div class="material-icons adv-icon">today</div>
                            </div>
                            <div class="col-lg-10 col-md-10 col-sm-10">
                                <?php echo $form->field($carTransporter, 'days_adv', [
                                        'enableClientValidation' => false,
                                        'enableAjaxValidation' => false,
                                    ])->dropDownList($advertDayList, [
                                        'disabled' => $serviceCredits + $subscriptionCredits === 0 ? 'disabled' : false, 
                                        'id' => 'car-days'
                                    ])->label(Yii::t('element', 'adv_day_number')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="row">
                            <div class="col-lg-11 col-md-11 col-sm-12">
                                <div class="form-group field-car-count">
                                    <?php echo Html::label(Yii::t('element', 'car_pos_adv_number'), 
                                            '', [
                                                'class' => 'control-label'
                                            ]);

                                        echo Html::tag('div', Yii::t('element', 
                                            'adv_open_contacts_service_cost', 
                                            ['credits' => $openContactsCost]
                                        ), [
                                            'id' => 'open-contacts-cost',
                                            'data-service-credits' => $serviceCredits,
                                            'data-subscription-credits' => $subscriptionCredits,
                                            'data-open-contacts-cost' => $openContactsCost,
                                            'class' => 'form-control adv-open-contacts-cost'
                                        ]); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-11 col-md-11 col-sm-12 field-position-number">
                                <?php echo $form->field($carTransporter, 'open_contacts_days', [
                                        'enableClientValidation' => false,
                                        'enableAjaxValidation' => false,
                                    ])->dropDownList($openContactsDayList, [
                                        'disabled' => $serviceCredits + $subscriptionCredits === 0 ? 'disabled' : false, 
                                        'id' => 'open_contacts_days',
                                        'class' => 'form-control IA-C-14 field-service-service_type_id',
                                    ])->label(Yii::t('element', 'adv_day_number'));
                                ?>
                                <span class="select-addon"><i class="fa fa-caret-down"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-offset-6 col-xs-6 stats-wrap">
                        <span class="adv-credits-cost pull-right"><?php echo Yii::t('element', 'adv_credits_cost');?>
                            <span class="cred-val">0</span>
                        </span>
                        <span class="adv-total-credits pull-right"><?php echo Yii::t('element', 'adv_total_credits {0}', [$serviceCredits]);?></span>
                        <span id="total-creds-topup-tr" class="adv-total-credits-topup pull-right">
                             <a target="_blank" href="<?php echo Url::to(['subscription/', 'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER])?>">
                                 <?php echo Yii::t('element', 'adv_credits_topup'); ?>
                             </a>
                        </span>
                    </div>
                </div>
            </div>
    </div>

    <div class="text-center">
        <button type="submit" id="C-T-36" class="primary-button load-announce-button">
            <i class="fa fa-bullhorn"></i>
            <?php echo Yii::t('element', 'C-T-36'); ?>
        </button>
    </div>
<?php ActiveForm::end();

$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/car-transporter/announce.js', ['depends' => [JqueryAsset::className()]]);