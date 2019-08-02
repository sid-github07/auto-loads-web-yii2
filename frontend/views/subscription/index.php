<?php

use frontend\controllers\SubscriptionController;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var string $title */
/** @var string $tab */
/** @var array $services */
/** @var array $steps */
/** @var null|boolean $isPaid */
/** @var array $preInvoices */
/** @var array $invoices */
/** @var array $activeServices */
/** @var null|integer $currentCredits */
/** @var boolean $applyVAT */
$this->title = $title . Yii::$app->params['titleEnding'];
?>
<div class="subscription-index">
    <h1 id="PS-M-1">
        <?php echo Yii::t('element', 'PS-M-1'); ?>
    </h1>

    <div class="subscription-tabs-container">
        <?php echo Tabs::widget([
            'navType' => 'nav-tabs nav-justified tabs-navigation',
            'encodeLabels' => false,
            'items' => [[
                'label' => '<span class="tab-label-text">' .
                Yii::t('element', 'PS-M-2'). '</span>',
                'content' => (is_null($isPaid)) ? Yii::$app->controller->renderPartial('_new-service-order', [
                    'subscriptionServices' => $subscriptionServices,
                    'steps' => $steps,
                    'applyVAT' => $applyVAT,
                ]) : (($isPaid) ? Yii::$app->controller->renderPartial('_service-paid', [
                    'steps' => $steps
                ]) : Yii::$app->controller->renderPartial('_service-not-paid', [
                    'steps' => $steps
                ])),
                'active' => $tab === SubscriptionController::TAB_NEW_SERVICE_ORDER,
                'linkOptions' => [
                    'id' => 'PS-M-2',
                ],
            ], [
                'label' => '<span class="tab-label-text">' .
                    Yii::t('element', 'top-up-credits'). '</span>',
                'content' => Yii::$app->controller->renderPartial('_credit-topup-order', [
                    'creditServices' => $adCredits,
                    'steps' => $steps,
                    'applyVAT' => $applyVAT,
                ]),
                'active' => $tab === SubscriptionController::TAB_CREDIT_TOP_UP_ORDER,
                'linkOptions' => [
                    'id' => 'top-up-credits',
                ],
            ],
            [
               'label' => '<span class="tab-label-text">' .
                Yii::t('element', 'PS-M-3') . ' (' . count($activeServices) . ')' . '</span>',
                'content' => Yii::$app->controller->renderPartial('_active-services', [
                    'activeServices' => $activeServices,
                    'currentCredits' => $currentCredits,
                    'advCredits' => $advCredits,
                ]),
                'active' => $tab === SubscriptionController::TAB_ACTIVE_SERVICES,
                'linkOptions' => [
                    'id' => 'PS-M-3',
                ],
            ], [
                'label' => '<span class="tab-label-text">' .
                Yii::t('element', 'PS-M-4') . ' (' . count($invoices) . ')' . '</span>',
                'content' => Yii::$app->controller->renderPartial('_paid-accounts', [
                    'preInvoices' => $preInvoices,
                    'invoices' => $invoices,
                ]),
                'active' => $tab === SubscriptionController::TAB_PAID_ACCOUNTS,
                'linkOptions' => [
                    'id' => 'PS-M-4',
                ],
            ]]
        ]); ?>
    </div>
</div>
<?php
$this->registerJs(
    'var isGuest = "' . Yii::$app->user->isGuest . '"; ' .
    'var serviceSelectionUrl = "' . Url::to([
        'subscription/service-selection',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var serviceConfirmationUrl = "' . Url::to([
        'subscription/service-confirmation',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var servicePurchaseUrl = "' . Url::to([
        'subscription/service-purchase',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var backToServicePurchaseUrl = "' . Url::to([
        'subscription/back-to-service-purchase',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var serviceActivationUrl = "' . Url::to([
        'subscription/service-activation',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var pjaxContainerId = "' . SubscriptionController::NEW_SERVICE_ORDER_PJAX_ID . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/subscription/index.js', ['depends' => [JqueryAsset::className()]]);