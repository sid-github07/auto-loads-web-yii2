<?php

use yii\web\JqueryAsset;
use yii\web\View;
use common\models\Seo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\icons\Icon;
use yii\bootstrap\Tabs;

/**
 * @var View $this
 * @var string $domain
 * @var string $page
 * @var array $language
 * @var Seo $seoModel
 */

$this->title = Yii::t('seo', 'SEO');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(\yii\helpers\Url::base() . '/dist/js/seo/main.js', ['depends' => [JqueryAsset::class]]);

?>

<div class="admin-index clearfix">
    <section class="widget widget-admin-list-edit">
        <div class="widget-heading">
            <?php echo Html::beginForm('/' . Yii::$app->getRequest()->getPathInfo(), 'GET') ?>
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
                    <?php echo Html::label(Yii::t('element', 'Domain'), 'domain', ['class' => 'control-label']) ?>
                    <?php echo Html::dropDownList('domain', Yii::$app->getRequest()->get('domain', Yii::$app->language),
                        $languages,
                        ['class' => 'form-control', 'onchange' => 'this.form.submit()']) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12">
                    <?php echo Html::label(Yii::t('element', 'Page'), 'page', ['class' => 'control-label']) ?>
                    <div>
                        <?php echo sprintf('/%s', preg_replace('!^\/+!', '', $seoModel->page)) ?>
                    </div>
                    <?php echo Html::hiddenInput('route', $seoModel->route) ?>
                </div>
            </div>
            <?php echo Html::endForm() ?>
        </div>
        <div class="widget-content">
            <?php $form = ActiveForm::begin(['method' => 'POST',]); ?>

            <div class="row" style="margin: 0">
                <?php echo Tabs::widget([
                    'id' => 'seo-nav',
                    'navType' => 'nav-tabs nav-justified tabs-navigation',
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => Icon::show('pencil', [], Icon::FA) .
                                Html::tag('span', Yii::t('element', 'SEO Title'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('components/title',
                                ['seoModel' => $seoModel, 'form' => $form]),
                            'options' => ['id' => 'title']
                        ],
                        [
                            'label' => Icon::show('key', [], Icon::FA) .
                                Html::tag('span', Yii::t('element',
                                    'SEO Keywords'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('components/keywords',
                                ['seoModel' => $seoModel, 'form' => $form]),
                            'options' => ['id' => 'keywords']
                        ],
                        [
                            'label' => Icon::show('align-left', [], Icon::FA) .
                                Html::tag('span', Yii::t('element',
                                    'SEO Description'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('components/description',
                                ['seoModel' => $seoModel, 'form' => $form]),
                            'options' => ['id' => 'description']
                        ]
                    ]
                ]); ?>
            </div>

            <div class="hidden">
                <?php echo $form->field($seoModel, 'domain')->hiddenInput() ?>
                <?php echo $form->field($seoModel, 'page')->hiddenInput() ?>
            </div>

            <?php echo Html::submitButton(sprintf('<i class="fa fa-save"></i>%s', Yii::t('element', 'Save')),
                ['class' => 'primary-button']) ?>

            <?php ActiveForm::end() ?>
        </div>

    </section>

</div>




