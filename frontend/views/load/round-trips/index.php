<?php

use common\models\LoadCity;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var View $this */
/** @var LoadCity $loadCity */

?>

<div class="load-round-trips-index">
    <h1 id="R-T-1">
        <?php echo Yii::t('element', 'R-T-1'); ?>
    </h1>
    
    <div class="required-fields-text">
        <?php echo Yii::t('app', 'FIELDS_WITH_STAR_ARE_REQUIRED'); ?>
    </div>
    
    <?php $form = ActiveForm::begin(['id' => 'load-round-trips-form']); ?>
        <div class="roundtrip-city-search">
            <?php echo Yii::$app->controller->renderPartial('/load/__load-city', [
                'form' => $form,
                'cities' => null,
                'loadCity' => $loadCity,
                'multiple' => false,
                'attribute' => 'city_id',
                'label' => Yii::t('element', 'R-T-2'),
                'id' => 'R-T-2',
                'placeholder' => null,
                'url' => Url::to([
                    'site/city-list',
                    'lang' => Yii::$app->language,
                    'unload' => true,
                ]),
            ]) ?>

            <?php echo Html::submitButton(Icon::show('search', '', Icon::FA) . Yii::t('element', 'R-T-3'), [
                'id' => 'R-T-3',
                'class' => 'primary-button search-btn',
                'name' => 'load-round-trips-submit',
            ]); ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search.js', ['depends' => [JqueryAsset::className()]]);