<?php

use common\models\Service;
use frontend\controllers\SubscriptionController;
use yii\widgets\Pjax;

/** @var Service[] $services */
/** @var array $steps */
/** @var boolean $applyVAT */

?>
<div class="new-service-order">
    <?php
        Pjax::begin(['id' => SubscriptionController::NEW_SERVICE_ORDER_PJAX_ID]);
            echo Yii::$app->controller->renderPartial('__service-selection', [
                'services' => $subscriptionServices,
                'steps' => $steps,
                'applyVAT' => $applyVAT,
            ]);
        Pjax::end();
    ?>
</div>