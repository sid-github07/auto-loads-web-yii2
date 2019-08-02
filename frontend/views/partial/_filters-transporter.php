<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\City;
use kartik\icons\Icon;
use common\models\Load;

/**
 * @var City $loadCity
 * @var City $unloadCity
 */

?>

<div class="row" style="border: 2px grey solid">
    <div class="col-xs-12 col-lg-10">
        <div class="form-group">
            <label id="C-T-9" class="control-label">
                <?php echo Yii::t('element', 'C-T-9'); ?>
            </label>

            <div class="row">
                <div class="col-xs-12 col-sm-3 col-lg-3">
                    <div class="control-label">
                        <?php echo Yii::t('element', 'IK-C-2') ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-9 col-lg-9">
                    <?php echo Html::radioList('searchRadius',
                        Yii::$app->getRequest()->get('searchRadius', Load::FIRST_RADIUS), Load::getSearchRadius(),
                        ['itemOptions' => ['class' => 'IK-C-3 IK-C-4 IK-C-5']]) ?>
                </div>
            </div>

            <div class="control-label col-xs-1" style="text-align: right; margin-top: 5px">
                <?php echo Yii::t('element', 'C-T-10aa'); ?>
            </div>
            <div class="col-xs-11 col-sm-5" style="padding: 0">
                <div class="form-group">
                    <?php echo Yii::$app->controller->renderPartial('/site/partial/filter-location', [
                        'name' => 'loadCityId',
                        'city' => $loadCity,
                        'id' => 'C-T-10a',
                        'placeholder' => Yii::t('element', 'C-T-10'),
                        'url' => Url::to([
                            'site/search-for-location',
                            'lang' => Yii::$app->language,
                            'showDirections' => false,
                        ]),
                    ]); ?>
                </div>
                <div class="form-group car-transporter-load-country-filtration-container">
                    <?php echo Html::dropDownList('loadCountryId', Yii::$app->getRequest()->get('loadCountryId'), $countries, [
                        'prompt' => Yii::t('element', 'C-T-10aaa'),
                        'class' => 'form-control'
                    ]); ?>
                </div>
            </div>

            <div class="control-label col-xs-1" style="text-align: right; margin-top: 5px">
                <?php echo Yii::t('element', 'C-T-10bb'); ?>
            </div>
            <div class="col-xs-11 col-sm-5" style="padding: 0;">
                <div class="form-group">
                    <?php echo Yii::$app->controller->renderPartial('/site/partial/filter-location', [
                        'name' => 'unloadCityId',
                        'city' => $unloadCity,
                        'id' => 'C-T-10b',
                        'placeholder' => Yii::t('element', 'C-T-10'),
                        'url' => Url::to([
                            'site/search-for-location',
                            'lang' => Yii::$app->language,
                            'showDirections' => false,
                        ]),
                    ]); ?>
                </div>
                <div class="form-group car-transporter-unload-country-filtration-container">
                    <?php echo Html::dropDownList('unloadCountryId', Yii::$app->getRequest()->get('unloadCountryId'), $countries, [
                        'prompt' => Yii::t('element', 'C-T-10bbb'),
                        'class' => 'form-control'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-lg-2">
        <div class="visible-lg" style="height: 122px">
            <?php echo Html::hiddenInput('isNewSearch', true) ?>
        </div>
        <div>
            <?php echo Html::submitButton(Icon::show('search', '', Icon::FA) . Yii::t('element', 'IK-C-16'), [
                'id' => 'IK-C-16',
                'class' => 'primary-button search-btn',
                'style' => 'float: right; margin-bottom: 10px'
            ]); ?>
        </div>
    </div>
</div>