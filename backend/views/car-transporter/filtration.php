<?php

use common\models\CarTransporter;
use common\models\CarTransporterCity;
use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var string $action */
/** @var ActiveForm $form */

$form = ActiveForm::begin([
    'id' => 'car-transporter-filtration-form',
    'method' => 'GET',
    'action' => $action,
]); ?>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <?php echo Yii::$app->controller->renderPartial('/site/single-location', [
                'form' => $form,
                'model' => $carTransporterCity,
                'attribute' => 'loadLocation',
                'label' => Yii::t('element', 'C-T-96'),
                'id' => 'C-T-97',
                'labelOptions' => ['id' => 'C-T-96'],
                'url' => Url::to([
                    'site/search-for-location',
                    'lang' => Yii::$app->language,
                    'showDirections' => false,
                ]),
            ]); ?>
        </div>

        <div class="col-sm-6 col-xs-12">
            <?php echo Yii::$app->controller->renderPartial('/site/single-location', [
                'form' => $form,
                'model' => $carTransporterCity,
                'attribute' => 'unloadLocation',
                'label' => Yii::t('element', 'C-T-98'),
                'labelOptions' => ['id' => 'C-T-98'],
                'id' => 'C-T-99',
                'url' => Url::to([
                    'site/search-for-location',
                    'lang' => Yii::$app->language,
                    'showDirections' => false,
                ]),
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($carTransporter, 'dateFrom', [
                'options' => ['id' => 'C-T-101'],
                'labelOptions' => [
                    'id' => 'C-T-100',
                    'class' => 'control-label',
                ],
            ])->widget(DateRangePicker::className(), [
                'attributeTo' => 'dateTo',
                'form' => $form,
                'language' => Yii::$app->language,
                'labelTo' => Yii::t('app', 'TO'),
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label(Yii::t('element', 'C-T-100')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 text-right">
            <button type="submit" id="C-T-102" class="primary-button">
                <i class="fa fa-filter"></i> <?php echo Yii::t('element', 'C-T-102'); ?>
            </button>

            <a id="C-T-103"
               class="secondary-button reset-filtration"
               href="<?php echo $action; ?>"
            >
                <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'C-T-103'); ?>
            </a>
        </div>
    </div>

<?php ActiveForm::end();