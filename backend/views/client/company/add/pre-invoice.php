<?php

use backend\controllers\ClientController;
use common\components\Model;
use common\models\User;
use common\models\UserService;
use dosamigos\datepicker\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var array $types */
/** @var null|integer $serviceTypeId */
/** @var null|integer $subscription */
/** @var array $subscriptions */
/** @var array $durations */
/** @var null|integer $companyId */
/** @var array|User[] $companyUsers */
/** @var UserService $userService */
/** @var ActiveForm $form */
?>
<div id="add-pre-invoice">
    <?php $form = ActiveForm::begin([
            'id' => 'pre-invoice-creation-form',
            'action' => [
                'client/create-pre-invoice',
                'lang' => Yii::$app->language,
                'companyId' => $companyId,
                'tab' => ClientController::TAB_COMPANY_PRE_INVOICES,
            ],
    ]); ?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>

                    <label class="control-label A-C-260" for="types">
                        <?php echo Yii::t('element', 'A-C-260'); ?>
                    </label>

                    <?php echo Html::dropDownList('types', $serviceTypeId, $types, [
                        'id' => 'types',
                        'prompt' => Yii::t('app', 'NOT_SELECTED'),
                        'class' => 'form-control A-C-260',
                        'onchange' => 'updateSubscriptions();',
                    ]); ?>
                </div>
            </div>
        </div>

        <h4 id="A-C-261" class="pre-invoice-category-title">
            <?php echo Yii::t('element', 'A-C-261'); ?>
        </h4>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>

                    <label id="A-C-262" class="control-label" for="subscriptions">
                        <?php echo Yii::t('element', 'A-C-262'); ?>
                    </label>

                    <?php echo Html::dropDownList('subscriptions', $subscription, $durations, [
                        'prompt' => Yii::t('app', 'NOT_SELECTED'),
                        'id' => 'A-C-263',
                        'class' => 'form-control subscriptions',
                        'disabled' => empty($durations),
                    ]); ?>
                </div>
            </div>
        </div>
    
        <div class="text-left form-group">
            <button type="button" id="A-C-264" class="secondary-button" onclick="addSubscriptionToList();">
                <?php echo Yii::t('element', 'A-C-264'); ?>
            </button>
        </div>

        <h4 id="A-C-265" class="pre-invoice-category-title">
            <?php echo Yii::t('element', 'A-C-265'); ?>
        </h4>

        <div id="selected-subscriptions-container">
            <?php $i = 0; ?>
            <?php foreach ($subscriptions as $id => $name): ?>
                <div class="selected-subscription">
                    <?php echo $name; ?>
                    <a href="#"
                       id="A-C-265a"
                       class="danger radius-btn delete-pre-invoice-btn"
                       title="<?php echo Yii::t('element', 'A-C-265a'); ?>"
                       data-toggle="tooltip"
                       data-placement="top"
                       onclick="removeSubscriptionFromList(event, <?php echo $id; ?>)"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                <input type="hidden"
                       name="<?php echo Model::getClassName($userService) . '[' . $i . '][service_id]'; ?>"
                       value="<?php echo $id; ?>">
                <?php $i++; ?>
            <?php endforeach; ?>
        </div>

        <h4 id="A-C-266" class="pre-invoice-category-title">
            <?php echo Yii::t('element', 'A-C-266'); ?>
        </h4>

        <?php echo $form->field($userService, 'start_date', [
            'labelOptions' => [
                'id' => 'A-C-267',
                'class' => 'control-label',
            ],
        ])->widget(DatePicker::className(), [
            'options' => [
                'id' => 'A-C-268',
                'class' => 'form-control start-date',
                'value' => date('Y-m-d'),
            ],
            'language' => Yii::$app->language,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'startDate' => date('Y-m-d'),
            ],
        ])->label(Yii::t('element', 'A-C-267')); ?>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group select">
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    
                    <?php echo $form->field($userService, 'user_id', [
                        'inputOptions' => [
                            'id' => 'A-C-270',
                            'class' => 'form-control user',
                        ],
                        'labelOptions' => [
                            'id' => 'A-C-269',
                            'class' => 'control-label',
                        ],
                    ])->dropDownList($companyUsers, [
                        'prompt' => Yii::t('app', 'NOT_SELECTED'),
                    ])->label(Yii::t('element', 'A-C-269')); ?>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" id="A-C-271" class="primary-button">
                <?php echo Yii::t('element', 'A-C-271'); ?>
            </button>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    "var subscriptions = '" . json_encode($subscriptions) . "'; " .
    "var ACTION_ADD_SUBSCRIPTION_TO_LIST = '" . ClientController::ACTION_ADD_SUBSCRIPTION_TO_LIST . "'; " .
    "var ACTION_REMOVE_SUBSCRIPTION_FROM_LIST = '" . ClientController::ACTION_REMOVE_SUBSCRIPTION_FROM_LIST . "'; ",
View::POS_BEGIN);