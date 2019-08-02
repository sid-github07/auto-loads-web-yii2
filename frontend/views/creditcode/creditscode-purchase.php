<?php

use common\models\UserService;
use common\models\Service;
use yii\helpers\Url;

/** @var integer $userServiceId */
/** @var Service $service */

$this->title = Yii::t('seo', 'TITLE_BUY_CREDIT');
?>

<div class="purchase-container clearfix">
    <h2 id="PS-C-12" class="order-payment text-center">
        <?php echo Yii::t('element', 'PS-C-12'); ?>
    </h2>

    <div class="payment-selection-buttons center-block">
        <div class="col-lg-3 col-md-3 col-sm-3 col-md-offset-3 col-xs-12">
            <a id="PS-C-16" class="paysera payment-selection" data-pjax="0" href="<?php
                echo Url::to([
                    'subscription/service-payment-method',
                    'lang' => Yii::$app->language,
                    'id' => $userServiceId,
                    'method' => UserService::PAYSERA,
                ]);
            ?>">
                <img src="<?php echo Yii::getAlias('@web') . '/images/paysera-logo.png'; ?>"
                     alt="<?php echo Yii::t('element', 'PS-C-16'); ?>"
                />
            </a>
            
            <a id="PS-C-16a" class="primary-button pay-btn" data-pjax="0" href="<?php
                echo Url::to([
                    'subscription/service-payment-method',
                    'lang' => Yii::$app->language,
                    'id' => $userServiceId,
                    'method' => UserService::PAYSERA,
                ]);
            ?>">
                <?php echo Yii::t('element', 'PS-C-16a'); ?>
            </a>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <a id="PS-C-17" class="paypal payment-selection" data-pjax="0" href="<?php
                echo Url::to([
                    'subscription/service-payment-method',
                    'lang' => Yii::$app->language,
                    'id' => $userServiceId,
                    'method' => UserService::PAYPAL,
                ]);
            ?>">
                <img src="<?php echo Yii::getAlias('@web') . '/images/paypal-logo.png'; ?>"
                     alt="<?php echo Yii::t('element', 'PS-C-17'); ?>"
                />
            </a>
            
            <a id="PS-C-16a" class="primary-button pay-btn" data-pjax="0" href="<?php
                echo Url::to([
                    'subscription/service-payment-method',
                    'lang' => Yii::$app->language,
                    'id' => $userServiceId,
                    'method' => UserService::PAYPAL,
                ]);
            ?>">
                <?php echo Yii::t('element', 'PS-C-17a'); ?>
            </a>
        </div>
    </div>
</div>

<a id="PS-C-11a" class="primary-button bt-lg ack-to-confirmation" href="<?php echo (empty($returnUrl) ? Url::to(['car-transporter-search/search-form']) : $returnUrl) ?>">
    <i class="fa fa-arrow-left"></i>
    <?php echo Yii::t('element', 'PS-C-11a'); ?>
</a>

<!-- Facebook Pixel Code -->
<script> !function (f, b, e, v, n, t, s) {
    if (f.fbq) return;
    n = f.fbq = function () {
        n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = !0;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = !0;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s)
}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '794770557232331');
fbq('track', 'PageView'); </script>
<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=794770557232331&ev=PageView &noscript=1"/>
</noscript> <!-- End Facebook Pixel Code -->

<!-- Google Code for U&#382;sakymas Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 949194720;
    var google_conversion_label = "CBxBCPT2730Q4J_OxAM";
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt=""
             src="//www.googleadservices.com/pagead/conversion/949194720/?label=CBxBCPT2730Q4J_OxAM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
