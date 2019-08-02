<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

$this->title = Yii::t('app', 'SITE_DASHBOARD');
?>
<div class="site-index">
    <h4>
        <?php echo Yii::t('text', 'WELCOME_ADMIN_MESSAGE'); ?>
    </h4>
</div>

<?php
$this->registerJs(
    'var timezoneOffset = "' . Yii::$app->session->get('timezone-offset') . '";' .
    'var actionSetTimezoneOffset = "' . Url::to(['site/set-timezone-offset', 'lang' => Yii::$app->language]) . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/index.js', ['depends' => [JqueryAsset::className()]]);