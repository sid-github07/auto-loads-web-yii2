<?php

use common\models\Load;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use kartik\icons\Icon;

/**
 * @var null|Load $load
 */

?>

<div class="row" style="margin: 0">
    <?php echo Tabs::widget([
        'id' => 'previews-nav',
        'navType' => 'nav-tabs nav-justified tabs-navigation',
        'encodeLabels' => false,
        'items' => [
            [
                'label' => Icon::show('eye', [], Icon::FA) .
                    Html::tag('span', Yii::t('element', 'load_preview_count'), ['class' => 'tab-label-text']),
                'content' => Yii::$app->controller->renderPartial('partial/previews-components/previews',
                    ['load' => $load]),
                'options' => ['id' => 'title']
            ],
            [
                'label' => Icon::show('truck', [], Icon::FA) .
                    Html::tag('span', Yii::t('element',
                        'Potential hauliers'), ['class' => 'tab-label-text']),
                'content' => Yii::$app->controller->renderPartial('partial/previews-components/potential-hauliers',
                    ['load' => $load]),
                'options' => ['id' => 'description']
            ]
        ]
    ]); ?>
</div>
