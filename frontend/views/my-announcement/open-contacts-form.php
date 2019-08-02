<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use frontend\controllers\SubscriptionController;
use yii\helpers\Html;
use common\models\Load;

/* @var View $this */
/* @var Load $model */
/* @var string $actionUrl */
/** @var integer $serviceCredits */
/** @var integer $subscriptionCredits */
/** @var string $subscriptionEndTime */
/** @var array $dayList */
/** @var array $openContactsCost */

$form = ActiveForm::begin([
    'id' => 'open-contacts-form',
    'action' => $actionUrl,
]);
?>
    <div class="row">
        <div class="col-xs-12">
            <div id="alert" class="alert alert-warning"<?php echo $serviceCredits > 0 ? ' style="display: none;"' : ''; ?>>
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
                <div class="col-lg-1 col-md-1 col-sm-12">
                    <div class="material-icons adv-icon" >directions_car</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo Html::label(Yii::t('element', 'car_pos_adv_number'), 
                        '', [
                            'class' => 'control-label'
                        ]);

                    echo Html::tag('div', Yii::t('element', 
                        'adv_open_contacts_service_cost', 
                        ['credits' => $openContactsCost]
                    ), [
                        'id' => 'open-contacts-modal-cost',
                        'data-service-credits' => $serviceCredits,
                        'data-subscription-credits' => $subscriptionCredits,
                        'data-open-contacts-cost' => $openContactsCost,
                        'class' => 'form-control adv-open-contacts-cost'
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm-12">
                    <div class="material-icons adv-icon">today</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($model, 'open_contacts_days')
                        ->dropDownList($dayList, ['id' => 'open_contacts_days']);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-offset-7 col-xs-5 stats-wrap">
            <input type="text" name="id" hidden value="<?php echo $model->id?>"/>
            <span class="adv-credits-cost pull-right"><?php echo Yii::t('element', 'adv_credits_cost');?>
                <span class="total-credits">0</span>
            </span>
            <span class="adv-total-credits pull-right"><?php echo Yii::t('element', 'adv_total_credits {0}', [$serviceCredits]);?></span>
            <span id="total-creds-topup" class="adv-total-credits-topup pull-right"<?php echo $serviceCredits > 0 ? ' style="display: none;"' : ''; ?>>
                <?php echo Html::a(
                    Yii::t('element', 'adv_credits_topup'),
                    Url::to([
                        'subscription/', 
                        'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER
                    ]), [
                        'target' => '_blank',
                        'data-pjax' => 0,       
                    ]
                ); ?>
            </span>
        </div>
    </div>
    <div class="modal-form-footer-center text-center">
        <button id="submit-open-contacts" type="submit" class="primary-button">
            <i class="fa fa-eye"></i>
            <?php echo Yii::t('element', 'set_open_contacts'); ?>
        </button>
    </div>

<?php
ActiveForm::end();
$this->registerJsFile(Url::base() . '/dist/js/my-announcement/open-contacts-form.js', ['depends' => [JqueryAsset::className()]]);
?>
