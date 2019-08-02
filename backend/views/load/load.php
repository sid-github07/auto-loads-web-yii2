<?php

use kartik\icons\Icon;
use yii\data\ActiveDataProvider;
use yii\web\View;

/** @var View $this */
/** @var ActiveDataProvider $dataProvider */

$this->title = Yii::t('seo', 'TITLE_LOAD');
Icon::map($this, Icon::FI);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('seo', 'TITLE_ADMIN_LOADS'),
    'url' => [
        'load/index',
        'lang' => Yii::$app->language,
    ],
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="load-load">
    <div class="widget widget-loads-list">
        <div class="widget-content">
            <?php echo Yii::$app->controller->renderPartial('/load/partial/list', [
                'dataProvider' => $dataProvider,
            ]); ?>
        </div>
    </div>
</div>
