<?php

use common\models\Service;
use frontend\controllers\SubscriptionController;

/** @var Service[] $services */
/** @var Service $service */
/** @var array $steps */
/** @var boolean $applyVAT */

?>
<div class="wizard-elements-container">
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
                        <?php echo $service->getTitle(); ?>
                    </div>

                    <div class="service-price PS-C-1b">
                        <?php echo Yii::t('app', 'SERVICE_PRICE_TAG', [
                            'price' => $service->price,
                            'vat' => '',
                        ]); ?>
                        <div class="<?php echo $service->name != Service::TITLE_MEMBER_12 ? 
                                'service-info' : 'service-info-golden'; ?>">
                            <?php if ($applyVAT) : ?>
                                <div class="service-info-line"><?php echo Yii::t('app', 'PRICING_INFO_VAT'); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($service->getMonthsByDays() > 1): ?>
                            <span class="service-price-per-month<?php echo $service->name == Service::TITLE_MEMBER_12 ? 
                                ' golden-price' : ''; ?>">
                                <?php echo Yii::t('app', 'SERVICE_PRICE_PER_MONTH_TAG', [
                                    'perMonth' => $service->calculatePricePerMonth(),
                                    'vat' => '',
                                ]); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <button id="<?php echo $service->getButtonId(); ?>"
                            class="service select-service primary-button"
                            data-service-type="<?php SubscriptionController::TAB_NEW_SERVICE_ORDER ?>"
                            data-service-id="<?php echo $service->id; ?>"
                    >
                        <?php echo Yii::t('app', 'SERVICE_SELECT'); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>