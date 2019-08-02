<?php

use kartik\icons\Icon;
use yii\data\ActiveDataProvider;
use yii\web\View;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('seo', 'TITLE_CAR_TRANSPORTERS');
Icon::map($this, Icon::FI);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('seo', 'TITLE_CAR_TRANSPORTERS'),
    'url' => [
        'car-transporter/index',
        'lang' => Yii::$app->language,
    ],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="load-load">
    <div class="widget widget-loads-list">
        <div class="widget-content">
            <?php echo Yii::$app->controller->renderPartial('/car-transporter/list', [
                'dataProvider' => $dataProvider,
            ]); ?>
        </div>
    </div>
</div>

