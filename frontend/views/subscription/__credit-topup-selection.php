<?php

use common\models\Service;

/** @var Service[] $services */
/** @var Service $service */
/** @var array $steps */
/** @var boolean $applyVAT */

?>
<div class="wizard-elements-container credits-topup">
    <?php echo Yii::$app->controller->renderPartial('___service-wizard', [
        'steps' => $steps,
    ]); ?>
</div>
<div class="services-container clearfix">
    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="service-item-wrapper col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="service-item">
                    <div class="service-title PS-C-1a">
                        <?php echo $service->label; ?><br/>
                        <span class="service-credits<?php echo $service->name == Service::TITLE_GOLD_CREDITS_200 ? 
                            ' golden-credits' : ''; ?>"> (<?php 
                            echo $service->credits . ' ' . Yii::t('app', 'CREDITS');
                        ?>) <?php $price = number_format(round($service->price / $service->credits, 2), 2, '.', ''); 
                        ?><span class="service-euro-credits <?php echo $service->name == Service::TITLE_GOLD_CREDITS_200 ? 
                            ' golden-price' : ''; ?>"><?php
                            echo $price . ' ' . Yii::t('app', 'EURO_PER_CREDIT');
                        ?></span></span>
                    </div>
                    <div class="service-price PS-C-1b">
                        <?php echo Yii::t('app', 'SERVICE_PRICE_TAG', [
                            'price' => $service->price,
                            'vat' => '',
                        ]); ?>
                    </div>
                    <div class="service-info">
                        <?php if ($applyVAT) : ?>
                            <div class="service-info-line"><?php echo Yii::t('app', 'PRICING_INFO_VAT'); ?></div>
                        <?php endif; ?>
                    </div>
                    <button
                            class="service select-service primary-button"
                            data-service-type="<?php echo \frontend\controllers\SubscriptionController::CREDIT_TOPUP_ORDER_PJAX_ID; ?>"
                            data-service-id="<?php echo $service->id; ?>"
                    >
                        <?php echo Yii::t('app', 'SERVICE_SELECT'); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>