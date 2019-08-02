<?php

/* @var $this View */
/* @var $content string */

use backend\assets\AppAsset;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\View;

Icon::map($this, Icon::FA);
AppAsset::register($this);
$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title) . Yii::$app->params['titleEnding']; ?></title>
    <link rel="icon" href="<?php echo Yii::$app->request->baseUrl . '/images/favicon.ico?v=2'; ?>" type="image/x-icon">
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>

<main class="container-fluid wrap-login">
    <?php echo $content; ?>
</main>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>