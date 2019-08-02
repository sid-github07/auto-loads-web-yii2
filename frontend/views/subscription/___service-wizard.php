<?php

use frontend\controllers\SubscriptionController;
use kartik\icons\Icon;

/** @var array $steps */

Icon::map($this, Icon::FA);
?>
<div class="wizard-container clearfix">
    <div class="wizard-element">
        <div class="wizard-element-circle <?php echo (in_array(SubscriptionController::STEP_SERVICE_SELECTION, $steps) ? 'finished-step' : ''); ?>">
            <i class="fa fa-tag" aria-hidden="true"></i>
        </div>
    </div>

    <span class="wizard-line-between-containers <?php echo (in_array(SubscriptionController::STEP_SERVICE_CONFIRMATION, $steps) ? 'finished-step' : ''); ?>"></span>

    <div class="wizard-element">
        <div class="wizard-element-circle <?php echo (in_array(SubscriptionController::STEP_SERVICE_CONFIRMATION, $steps) ? 'finished-step' : ''); ?>">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        </div>
    </div>

    <span class="wizard-line-between-containers <?php echo (in_array(SubscriptionController::STEP_SERVICE_PURCHASE, $steps) ? 'finished-step' : ''); ?>"></span>

    <div class="wizard-element">
        <div class="wizard-element-circle <?php echo (in_array(SubscriptionController::STEP_SERVICE_PURCHASE, $steps) ? 'finished-step' : ''); ?>">
            <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
        </div>
    </div>
</div>