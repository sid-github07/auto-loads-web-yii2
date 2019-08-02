<?php

/* @var $this View */
/* @var $content string */

use backend\assets\AppAsset;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrFlash;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

Icon::map($this, Icon::FA);
AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title) . Yii::$app->params['titleEnding']; ?></title>
    <link rel="icon" href="<?php echo Yii::$app->request->baseUrl . '/images/favicon.ico?v=2'; ?>" type="image/x-icon">
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvGQSmRBmC0accLZeBbpA0gLFuy5ASIR8">
    </script>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
    <header>
        <nav class="topbar">
            <div class="navbar-header">
                <a href="<?php echo Url::to(['site/index', 'lang' => Yii::$app->language, ]); ?>"
                   class="mobile-logo-main">
                    <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                         class="site-logo-main-img"
                         alt="<?php echo Yii::t('element', 'SITE_LOGO'); ?>"
                    />
                    <div id="HP-C1-1a" class="logo-subtext">
                        <?php echo Yii::t('element', 'LOGO_SUBTEXT'); ?>
                    </div>
                </a>
                
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#topbar">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="topbar">
                <a href="<?php echo Url::to(['site/index', 'lang' => Yii::$app->language, ]); ?>"
                   class="site-logo-main pull-left text-center">
                    <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                     class="site-logo-main-img"
                     alt="<?php echo Yii::t('element', 'SITE_LOGO'); ?>"
                    />
                    <div id="HP-C1-1a" class="logo-subtext">
                        <?php echo Yii::t('element', 'LOGO_SUBTEXT'); ?>
                    </div>
                </a>
                
                <?php echo Yii::$app->controller->renderPartial('/layouts/nav-bar-client-items'); ?>
                <?php echo Yii::$app->controller->renderPartial('/layouts/nav-bar-profile-items'); ?>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="page-heading-wrap">
            <div class="container-fluid page-heading-wrapper">
                <h1>
                    <?php echo Html::encode($this->title); ?>
                </h1>
                <?php echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'encodeLabels' => false
                ]); ?>
            </div>
        </div>
        <div class="page-content-container container-fluid wrap">
            <div class="main-alert">
                <?php  echo ToastrFlash::widget([
                    'options' => [
                        'closeButton' => true,
                        'debug' => false,
                        'newestOnTop' => true,
                        'progressBar' => false,
                        'positionClass' => 'toast-top-center',
                        'preventDuplicates' => true,
                        'showDuration' => 0, // how long it takes to show the alert in milliseconds
                        'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
                        'timeOut' => 45000, // how long the alert must be visible to user in milliseconds
                        'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
                        'onShown' => 'function() { ' .
                            '$(".main-alert").append($("#toast-container"));' .
                        '}',
                    ]
                ]); ?>
            </div>
            <?php echo $content; ?>
        </div>
    </main>

<footer class="footer navbar-fixed-bottom">
    <div class="container-fluid">
        <p class="text-center copyright">
            &copy; <?php echo date('Y') . ' ' . Yii::$app->params['companyName'] . Yii::t('element', 'COPYRIGHT'); ?>
        </p>
    </div>
</footer>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
