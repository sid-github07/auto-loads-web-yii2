<?php

use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var Load $load */
/** @var LoadCar $loadCar */
/** @var ActiveForm $form */
/** @var LoadCity $loadCity */

?>
<div class="load-search">
    <h1 id="IK-C-1"><?php echo Yii::t('element', 'IK-C-1'); ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'load-search-form',
        'method' => 'GET',
    ]); ?>
        
        <?php echo $form->field($load, 'searchRadius', [
            'options' => [
                'class' => 'text-center'
            ]
        ])
            ->inline()
            ->radioList(Load::getSearchRadius(), [
                'itemOptions' => [
                    'class' => 'IK-C-3 IK-C-4 IK-C-5',
                ],
            ])
            ->label(Yii::t('element', 'IK-C-2')); ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($loadCar, 'quantity')->dropDownList(LoadCar::getQuantities(), [
                    'id' => 'IK-C-7',
                ])->label(Yii::t('element', 'IK-C-6')); ?>
                <span class="select-addon"><i class="fa fa-caret-down"></i></span>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($load, 'date')->widget(DatePicker::className(), [
                    'options' => [
                        'id' => 'IA-C-15',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'startDate' => date('Y-m-d'),
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', 'IK-C-14')); ?>
            </div>
        </div>
    
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-10',
                    'loadCity' => $loadCity,
                    'attribute' => 'loadCityId',
                    'label' => Yii::t('element', 'IK-C-8'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => [],
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'load' => true,
                    ]),
                    'fillLoadCity' => true,
                ]); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-11',
                    'loadCity' => $loadCity,
                    'attribute' => 'unloadCityId',
                    'label' => Yii::t('element', 'IK-C-9'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => [],
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'unload' => true,
                    ]),
                ]); ?>
            </div>
        </div>

        <div class="text-center">
            <?php echo Html::submitButton(Icon::show('search', '', Icon::FA) . Yii::t('element', 'IK-C-16'), [
                'id' => 'IK-C-16',
                'class' => 'primary-button search-btn',
            ]); ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search.js', ['depends' => [JqueryAsset::className()]]);