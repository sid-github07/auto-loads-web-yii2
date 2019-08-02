<?php

/** @var array $steps */

?>
<div class="wizard-elements-container">
    <?php echo Yii::$app->controller->renderPartial('___service-wizard', [
        'steps' => $steps,
    ]); ?>
</div>

<div class="subscription-service-paid">
    <p><?php echo Yii::t('app', 'SERVICE_PAID_AND_ACTIVATED_SUCCESSFULLY'); ?></p>
</div>