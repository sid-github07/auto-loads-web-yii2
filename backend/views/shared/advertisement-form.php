<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\Load;

$form = ActiveForm::begin([
    'id' => 'advertize-form',
    'action' => [
        Url::to([
            $model->getEntityType() === Load::ENTITY_TYPE_LOAD ?  'load/adv-load' : 'car-transporter/adv-transporter',
            'id' => $model->id
        ])
   ],
]);
?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <?php if ($model->days_adv !== 0 && $model->car_pos_adv !== 0): ?>
                    <div class="alert alert-success" role="alert">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4><?php echo Yii::t('element', 'advert_current_data') ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'advert_current_position_heading')?></div>
                            <div class="col-sm-6"><?php echo $model->car_pos_adv ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'advert_current_days_heading')?></div>
                            <div class="col-sm-6"><?php echo $model->days_adv ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'advert_current_time_heading')?></div>
                            <div class="col-sm-6"><?php echo $model->submit_time_adv ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3"><?php echo Yii::t('element', 'advert_current_price_heading')?></div>
                            <div class="col-sm-6"><?php echo $model->days_adv * $model->car_pos_adv ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm12">
                    <div class="material-icons adv-icon" >directions_car</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($model, 'car_pos_adv')
                        ->dropDownList($model->getCarPosRanges())
                        ->label(Yii::t('element', 'car_pos_adv_number'));
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1 col-md-1 col-sm-12">
                    <div class="material-icons adv-icon">today</div>
                </div>
                <div class="col-lg-11 col-md-11 col-sm-12">
                    <?php echo $form->field($model, 'days_adv')
                        ->dropDownList($model->getDaysRanges())
                        ->label(Yii::t('element', 'adv_day_number'));
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-form-footer-center text-center">
        <button id="submit-adv" type="submit" class="primary-button">
            <i class="fa fa-bullhorn"></i>
            <?php echo Yii::t('element', 'advertise'); ?>
        </button>
    </div>

<?php
ActiveForm::end();
?>
