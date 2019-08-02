<?php

use common\models\Service;
use frontend\controllers\SubscriptionController;
use yii\widgets\Pjax;

/** @var Service[] $services */
/** @var array $steps */
/** @var boolean $applyVAT */

?>
<div class="credit-popup-order">
    <?php
        Pjax::begin(['id' => SubscriptionController::CREDIT_TOPUP_ORDER_PJAX_ID ]);
            echo Yii::$app->controller->renderPartial('__credit-topup-selection', [
                'services' => $creditServices,
                'steps' => $steps,
                'applyVAT' => $applyVAT,
            ]);
        Pjax::end();
    ?>
</div>