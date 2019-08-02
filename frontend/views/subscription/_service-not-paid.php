<?php

/** @var array $steps */

?>
<div class="wizard-elements-container">
    <?php echo Yii::$app->controller->renderPartial('___service-wizard', [
        'steps' => $steps,
    ]); ?>
</div>

<div class="subscription-service-not-paid">
    <p><?php echo Yii::t('app', 'SERVICE_NOT_PAID_AND_NOT_ACTIVATED'); ?></p>
</div>