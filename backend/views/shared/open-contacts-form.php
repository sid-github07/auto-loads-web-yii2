<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var View $this */
/** @var Load $model */
/** @var string $actionUrl */
/** @var integer $openContactsCost */
/** @var array $dayList */

$form = ActiveForm::begin([
    'id' => 'open-contacts-form',
    'action' => $actionUrl,
]);
?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <?php if ($model->isOpenContacts()): ?>
                    <div class="alert alert-success" role="alert">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4><?php echo Yii::t('element', 'open_contacts_current_data') ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'current_open_contacts_days_label')?></div>
                            <div class="col-sm-6"><?php echo $model->open_contacts_days; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'current_open_contacts_credits_label')?></div>
                            <div class="col-sm-6"><?php echo $model->open_contacts_days * $openContactsCost; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'current_open_contacts_expiry_label')?></div>
                            <div class="col-sm-6"><?php echo $model->getOpenContactsExpiryTime(); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm12">
                    <div class="material-icons adv-icon" >directions_car</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo Html::label(Yii::t('element', 'open_contacts_credits'), 
                        '', [
                            'class' => 'control-label'
                        ]);

                    echo Html::tag('div', Yii::t('element', 
                        'open_contacts_client_service_cost', 
                        ['credits' => $openContactsCost]
                    ), [
                        'id' => 'open-contacts-cost',
                        'class' => 'form-control adv-open-contacts-cost'
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm-12">
                    <div class="material-icons adv-icon">today</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($model, 'open_contacts_days', [
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ])->dropDownList($dayList); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-form-footer-center text-center">
        <button id="submit-adv" type="submit" class="primary-button">
            <i class="fa fa-eye"></i>
            <?php echo Yii::t('element', 'update_open_contacts'); ?>
        </button>
    </div>

<?php
ActiveForm::end();
?>
