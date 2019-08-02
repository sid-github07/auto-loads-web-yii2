<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use frontend\controllers\SubscriptionController;
use yii\web\JqueryAsset;

/* @var View $this */
/* @var CarTransporter $transporter */
/** @var integer $subscriptionCredits */
/** @var string $subscriptionEndTime */
/** @var array $advDayList */
/** @var array $advPositionList */

$form = ActiveForm::begin([
    'id' => 'transporter-adv-form',
    'action' => [
        Url::to([
            'my-car-transporter/adv-transporter',
            'lang' => Yii::$app->language,
            'id' => $transporter->id,
        ])
   ],
]);
?>
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-warning adv-alert">
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
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm12">
                    <div class="material-icons adv-icon" >directions_car</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($transporter, 'car_pos_adv', [
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ])->dropDownList($advPositionList)
                    ->label(Yii::t('element', 'car_pos_adv_number')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm-12">
                    <div class="material-icons adv-icon">today</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($transporter, 'days_adv', [
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ])->dropDownList($advDayList)
                    ->label(Yii::t('element', 'adv_day_number')); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-offset-7 col-xs-5 stats-wrap">
            <input type="text" name="id" hidden value="<?php echo $transporter->id?>"/>
            <input type="text" class="credits" name="credits" hidden value="1"/>
            <input type="text" class="service-credits" name="adv-credits" hidden value="<?php echo $advCredits?>"/>
            <input type="text" class="subscription-credits" name="adv-credits" hidden value="<?php echo $subscriptionCredits?>"/>

            <span class="adv-credits-cost pull-right"><?php echo Yii::t('element', 'adv_credits_cost');?>
                <span class="cred-val">1</span>
            </span>
            <span class="adv-total-credits pull-right"><?php echo Yii::t('element', 'adv_total_credits {0}', [$advCredits]);?></span>
            <span id="total-creds-topup-tr" class="adv-total-credits-topup pull-right">
                <a href="<?php echo Url::to(['subscription/index', 'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER])?>"><?php echo Yii::t('element', 'adv_credits_topup'); ?></a>
            </span>
        </div>
    </div>
    <div class="modal-form-footer-center text-center">
        <button type="submit" class="primary-button" id="submit-adv-tr">
            <i class="fa fa-bullhorn"></i>
            <?php echo Yii::t('element', 'advertise'); ?>
        </button>
    </div>

<?php
ActiveForm::end();
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/transporter-advertisement-form.js', ['depends' => [JqueryAsset::className()]]);
?>
