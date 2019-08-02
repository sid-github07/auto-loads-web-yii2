<?php

use common\models\Service;

/** @var Service $service */
/** @var array $steps */
/** @var boolean $applyVAT */

?>
<div class="wizard-elements-container">
    <?php echo Yii::$app->controller->renderPartial('___service-wizard', [
        'steps' => $steps,
    ]); ?>
</div>

<div class="cart-container clearfix">
    <h3 id="PS-C-4" class="your-cart-title">
        <?php echo Yii::t('element', 'PS-C-4'); ?>
    </h3>

    <div class="selected-service-list-container clearfix">
        <span id="PS-C-6" class="selected-service-title">
            <?php echo $service->getTitle(); ?>
        </span>
        <div class="service-price-and-remove">
            <span id="PS-C-7" class="selected-service-price">
                <?php echo Yii::t('element', 'PS-C-7', [
                    'price' => Yii::t('app', 'SERVICE_PRICE_TAG', [
                        'price' => $service->price,
                        'vat' => $applyVAT ? Yii::t('app', 'PLUS_VAT') : '',
                    ]),
                ]); ?>
            </span>

            <div class="remove-from-cart">
                <a href="#" id="PS-C-8" class="link secondary-button remove-from-cart-btn">
                    <i class="fa fa-trash-o"></i>
                    <?php echo Yii::t('element', 'PS-C-8'); ?>
                </a>
            </div>
        </div>
    </div>

    <div id="PS-C-10" class="selected-service-full-price">
        <?php echo Yii::t('element', 'PS-C-10', [
            'price' => Yii::t('app', 'SERVICE_PRICE_TAG', [
                'price' => $service->price,
                'vat' => $applyVAT ? Yii::t('app', 'PLUS_VAT') : '',
            ]),
        ]); ?>
    </div>

    <button id="PS-C-11a" class="primary-button cancel-purchase" data-service-type-id="<?php echo $service->service_type_id ?>">
        <i class="fa fa-arrow-left"></i>
        <?php echo Yii::t('element', 'PS-C-11a'); ?>
    </button>

    <button id="PS-C-11" class="primary-button purchase" data-service-id="<?php echo $service->id; ?>">
        <?php echo Yii::t('element', 'PS-C-11'); ?>
    </button>
</div>
