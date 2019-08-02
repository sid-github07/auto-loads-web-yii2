<?php

use yii\helpers\Url;

/** @var string $code  */
?>
<div>
    <span id="IK-C-28" class="search-results-load-code">
        <?php echo "#$code"; ?>
    </span>
</div>

<span id="IA-C-90a" class="text-center not-logged-in-preview">
    <?php echo Yii::t('element', 'IA-C-90a'); ?>
</span>

<div class="text-center">
    <a id="IA-C-91a"
       class="primary-button search-load-sign-in-btn"
       href="<?php echo Url::to([
           'subscription/index',
           'lang' => Yii::$app->language,
       ]); ?>"
    >
        <i class="fa fa-briefcase" aria-hidden="true"></i> <?php echo Yii::t('element', 'IA-C-91a'); ?>
    </a>
</div>