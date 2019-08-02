<?php

use yii\helpers\Url;
use common\components\helpers\Html;
use frontend\controllers\SubscriptionController;
use common\models\Service;
use common\models\User;

/**
 * @var Service $service
 * @var int $needCredits
 * @var User $user
 */

?>

<?php if ($user->service_credits < $service->credit_cost): ?>
    <?php if ($user->hasSubscription()) : ?>
        <?php echo Yii::t('element', 'not_enough_total_credits', [
            'creditTopupLink' => Html::a(
                Yii::t('element', 'adv_credits_topup'),
                Url::to([
                        'subscription/',
                        'tab' => SubscriptionController::TAB_CREDIT_TOP_UP_ORDER
                    ]
                ), [
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'class' => 'credit-topup-link',
                ]
            ),
            'subscriptionEndTime' => $user->getSubscriptionEndTime(),
            'subscriptionCredits' => $user->getSubscriptionCredits(),
        ]); ?>
    <?php else : ?>
        <?php echo Yii::t('element', 'preview_not_enough_credits_short'); ?>
    <?php endif; ?>
<?php endif; ?>
<?php echo Html::tag('div',
    Yii::t('element', 'adv_open_contacts_service_cost', ['credits' => $service->credit_cost])); ?>
