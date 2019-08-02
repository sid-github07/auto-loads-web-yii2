<?php

use yii\helpers\Url;

?>

<section class="subscription-reminder" style="margin-top: 0">
                    <span class="reminder-icon">
                        <i id="NP-FC-1" class="fa fa-exclamation-triangle"></i>
                    </span>

    <span id="NP-FC-2" class="reminder-message">
                        <?php echo Yii::t('element', 'NP-FC-4'); ?>
                    </span>

    <span class="reminder-btn-wrapper">
                        <a href="<?php echo Url::to(['subscription/index', 'lang' => Yii::$app->language]); ?>"
                           id="NP-FC-3"
                           class="reminder-action-btn">
                            <?php echo Yii::t('element', 'NP-FC-3'); ?>
                        </a>
                    </span>

    <a href="#" class="close-subscription-reminder"
       onclick="hideSubscriptionAlert(event, <?php echo Yii::$app->user->isGuest ? 'true' : 'false' ?>)">✕</a>
</section>
