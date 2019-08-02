<?php
use yii\helpers\Url;
?>

<div class="whats-new-message">
    <?php echo Yii::t('app', 'WHATS_NEW_CONTENT_1_DEMO'); ?>
    <?php if (Yii::$app->user->isGuest): ?>
        <?php echo Yii::t('app', 'WHATS_NEW_CONTENT_2_DEMO'); ?>
        <div class="text-center" style="margin: 32px 0;">
            <a href="<?php echo Url::to(['site/request-password-reset', 'lang' => Yii::$app->language]); ?>"
               class="button primary-button"
               style="padding: 8px 24px;"
               onclick="dismissWhatsNewInDemoModal()"
            >
                <?php echo Yii::t('app', 'RESET_PASSWORD'); ?>
            </a>
        </div>
    <?php endif; ?>
    <?php echo Yii::t('app', 'WHATS_NEW_CONTENT_3_DEMO'); ?>
</div>