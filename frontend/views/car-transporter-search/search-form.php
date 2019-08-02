<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */

?>
<div class="car-transporter-search">
    <h1 id="C-T-68">
        <?php echo Yii::t('element', 'C-T-68'); ?>
    </h1>

    <?php $form = ActiveForm::begin([
        'id' => 'car-transporter-search-form',
        'method' => 'GET',
        'validationUrl' => ['car-transporter-search/validate-available-from-date'],
        'action' => ['car-transporter-search/search', 'lang' => Yii::$app->language],
    ]); ?>

        <?php echo $form->field($carTransporter, 'radius', [
            'options' => [
                'class' => 'text-center',
            ],
        ])
            ->inline()
            ->radioList(CarTransporter::getRadius(), [
                'itemOptions' => [
                    'class' => 'C-T-69a C-T-69b C-T-69c',
                ],
            ])
            ->label(Yii::t('element', 'C-T-69')); ?>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($carTransporter, 'quantity')->dropDownList(CarTransporter::getQuantities(), [
                    'id' => 'C-T-71',
                ])->label(Yii::t('element', 'C-T-70'), ['id' => 'C-T-70']); ?>
                <span class="select-addon"><i class="fa fa-caret-down"></i></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo $form->field($carTransporter, 'available_from', [
                    'enableAjaxValidation' => true,
                ])->widget(DatePicker::className(), [
                    'options' => [
                        'id' => 'C-T-73',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'startDate' => date('Y-m-d'),
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', 'C-T-72'), ['id' => 'C-T-72']); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('/site/partial/single-location', [
                    'form' => $form,
                    'model' => $carTransporterCity,
                    'attribute' => 'loadLocation',
                    'label' => Yii::t('element', 'C-T-74'),
                    'labelOptions' => ['id' => 'C-T-74'],
                    'id' => 'C-T-75',
                    'url' => Url::to([
                        'site/search-for-location',
                        'lang' => Yii::$app->language,
                        'showDirections' => false,
                    ]),
                ]); ?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('/site/partial/single-location', [
                    'form' => $form,
                    'model' => $carTransporterCity,
                    'attribute' => 'unloadLocation',
                    'label' => Yii::t('element', 'C-T-76'),
                    'labelOptions' => ['id' => 'C-T-76'],
                    'id' => 'C-T-77',
                    'url' => Url::to([
                        'site/search-for-location',
                        'lang' => Yii::$app->language,
                        'showDirections' => false,
                    ]),
                ]); ?>
            </div>
        </div>

        <div class="text-center">
            <?php echo Html::submitButton(Icon::show('search', '', Icon::FA) . Yii::t('element', 'C-T-78'), [
                'id' => 'C-T-78',
                'class' => 'primary-button search-btn',
            ]) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);