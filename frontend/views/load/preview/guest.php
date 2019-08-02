<?php

use yii\helpers\Url;

/** @var string $code  */
/** @var string $params */
?>
<div>
    <span id="IK-C-28" class="search-results-load-code">
        <?php echo "#$code"; ?>
    </span>
</div>

<span id="IA-C-90" class="text-center not-logged-in-preview">
    <?php echo Yii::t('element', 'IA-C-90'); ?>
</span>

<div class="text-center">
    <a id="IA-C-91"
       class="primary-button search-load-sign-in-btn"
       href="<?php echo Url::to([
           'site/login',
           'lang' => Yii::$app->language,
           'params' => $params,
       ]); ?>"
    >
        <i class="fa fa-sign-in" aria-hidden="true"></i> <?php echo Yii::t('element', 'IA-C-91'); ?>
    </a>
</div>