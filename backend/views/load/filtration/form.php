<?php

use common\models\Load;
use common\models\LoadCity;
use dosamigos\datepicker\DateRangePicker;
use dosamigos\selectize\SelectizeDropDownList;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var array $countries */
/** @var array $loadTypes */
/** @var ActiveForm $form */

$form = ActiveForm::begin([
    'id' => 'load-index-form',
    'method' => 'GET',
    'action' => $action,
]); ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($load, 'dateFrom', [
                'options' => [
                    'id' => 'A-C-322a',
                ],
                'labelOptions' => [
                    'id' => 'A-C-321a',
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
            ])->label(Yii::t('element', 'A-C-321a')); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 select">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo $form->field($load, 'type', [
                'options' => [
                    'id' => 'A-C-322f',
                ],
                'labelOptions' => [
                    'id' => 'A-C-321f',
                    'class' => 'control-label',
                ],
            ])->dropDownList($loadTypes, [
                'prompt' => Yii::t('app', 'NOT_SELECTED'),
            ])->label(Yii::t('element', 'A-C-321f')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($loadCity, 'loadCountry', [
                'options' => [
                    'id' => 'A-C-322d',
                ],
                'labelOptions' => [
                    'id' => 'A-C-321d',
                    'class' => 'control-label',
                ],
            ])->widget(SelectizeDropDownList::className(), [
                'items' => array_merge([null => ''], $countries),
                'options' => [
                    'placeholder' => Yii::t('app', 'NOT_SELECTED'),
                    'class' => 'load-country',
                ],
                'clientOptions' => [
                    'sortField' => 'text',
                ],
            ])->label(Yii::t('element', 'A-C-321d')); ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo Yii::$app->controller->renderPartial('/load/partial/city', [
                'form' => $form,
                'loadCity' => $loadCity,
                'attribute' => 'loadCityId',
                'label' => Yii::t('element', 'A-C-321b'),
                'id' => 'A-C-322b',
                'multiple' => false,
                'placeholder' => '',
                'isLoad' => true,
                'containerClass' => '',
                'url' => Url::to([
                    'city/simple-search',
                    'lang' => Yii::$app->language,
                ])
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($loadCity, 'unloadCountry', [
                'options' => [
                    'id' => 'A-C-322e',
                ],
                'labelOptions' => [
                    'id' => 'A-C-321e',
                    'class' => 'control-label',
                ],
            ])->widget(SelectizeDropDownList::className(), [
                'items' => array_merge([null => ''], $countries),
                'options' => [
                    'placeholder' => Yii::t('app', 'NOT_SELECTED'),
                    'class' => 'unload-country',
                ],
                'clientOptions' => [
                    'sortField' => 'text',
                ],
            ])->label(Yii::t('element', 'A-C-321e')); ?>
        </div>
        
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <?php echo Yii::$app->controller->renderPartial('/load/partial/city', [
                'form' => $form,
                'loadCity' => $loadCity,
                'attribute' => 'unloadCityId',
                'label' => Yii::t('element', 'A-C-321c'),
                'id' => 'A-C-322c',
                'multiple' => false,
                'placeholder' => '',
                'isLoad' => false,
                'containerClass' => '',
                'url' => Url::to([
                    'city/simple-search',
                    'lang' => Yii::$app->language,
                ])
            ]); ?>
        </div>
    </div>

    <div class="text-right">
        <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'A-C-321g'), [
            'id' => 'A-C-322g',
            'class' => 'primary-button',
        ]); ?>
        
        <a id="A-C-322f" href="<?php echo $action; ?>" 
           class="secondary-button reset-filtration"
        >
            <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'A-C-382'); ?>
        </a>
    </div>

<?php ActiveForm::end();