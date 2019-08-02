<?php
    use yii\helpers\Url;
?>

<div class="whats-new-message">
    <?php echo Yii::t('app', 'WHATS_NEW_CONTENT_1'); ?>
    <div class="text-center" style="margin: 32px 0;">
        <a href="<?php echo Url::to(['site/request-password-reset', 'lang' => Yii::$app->language]); ?>"
           class="button primary-button"
           style="padding: 8px 24px;"
           onclick="dismissModal()"
        >
            <?php echo Yii::t('app', 'RESET_PASSWORD'); ?>
        </a>
    </div>
    <?php echo Yii::t('app', 'WHATS_NEW_CONTENT_2'); ?>
</div>