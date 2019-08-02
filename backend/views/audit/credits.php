<?php

use common\models\UserLog;
use common\components\audit\Pay;

use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use kartik\select2\Select2;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;


$this->title = Yii::t('app', 'Credits_logs');
$this->params['breadcrumbs'][] = $this->title;

$hereRoute = 'audit/credits';

?>

<div class="log-index">
    <div class="widget widget-searches-list">
        <div class="widget-heading">
            <span id="U-L-1"><?php echo Yii::t('app', 'Credits_logs'); ?></span>
        </div>
        
        <div class="widget-content">
            <?php $form = ActiveForm::begin([
                'id' => 'log-search-index-form',
                'method' => 'GET',
                'action' => Url::to([
                    $hereRoute,
                    'lang' => Yii::$app->language,
                ]),
            ]); ?>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php echo $form->field($userLog, 'searchCredit', [
                        'options' => [
                            'id' => 'search-sredit',
                        ],
                    ])->widget(Select2::className(), [
                        'data' => Pay::getSearchCreditFilter(),
                        'options' => [
                            'multiple' => true,
                            'placeholder' => '',
                        ],
                        'showToggleAll' => false,
                        'pluginOptions' => [
                            'escapeMarkup' => new JsExpression('function(m) { return m; }'),
                            'allowClear' => false,
                        ],
                    ])->label(Yii::t('element', 'Type of log') . ':'); ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php echo $form->field($userLog, 'startDate', [
                        'options' => [
                            'id' => 'start-date',
                        ],
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ])->widget(DateRangePicker::className(), [
                        'attributeTo' => 'endDate',
                        'form' => $form,
                        'language' => Yii::$app->language,
                        'labelTo' => Yii::t('app', 'TO'),
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ],
                    ])->label(Yii::t('element', 'U-L-8')); ?>
                </div>
            </div>
            
            <div class="text-left">
                <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'U-L-7'), [
                    'id' => 'U-L-7',
                    'class' => 'primary-button',
                ]); ?>
                    <a id="U-L-10" href="
                    <?php echo Url::to([
                        $hereRoute,
                        'lang' => Yii::$app->language,
                    ]); ?>" 
                    class="secondary-button reset-filtration"
                 >
                     <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'U-L-10'); ?>
                 </a>
            </div>

            <?php ActiveForm::end(); ?>
            
            <div class="responsive-table-wrapper">
                <?php
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => [
                            'class' => 'custom-gridview searches-list-gridview text-center',
                        ],
                        'tableOptions' => [
                            'class' => 'table table-striped responsive-table'
                        ],
                        'columns' => [
                            [
                                'label' => Yii::t('app', 'Log_type'),
                                'attribute' => 'action',
                                'value' => function (UserLog $userLog) {
                                    $lst = Pay::getSearchCreditFilter();
                                    $value = isset($lst[$userLog->action]) ? $lst[$userLog->action] : $userLog->action;
                                    return $value;
                                },
                                'headerOptions' => [
                                    'style' => 'width: 40%',
                                ],
                            ],
                            [
                                'attribute' => 'sumcredits',
                                'label' => Yii::t('app', 'Amount_of_credits_used'),
                                'headerOptions' => [
                                    'style' => 'width: 10%',
                                ],
                            ],
                            [
                                'attribute' => 'day',
                                'label' => Yii::t('element', 'U-L-5'),
                                'format' => 'raw',
                                'headerOptions' => [
                                    'id' => 'U-L-5',
                                    'class' => 'search-created-at-column text-left',
                                    'style' => 'width: 20%',
                                ],
                                'contentOptions' => [
                                    'class' => 'load-created-at-column-content text-left U-L-5',
                                ],
                            ]        
                        ],
						'pager' => [
                            'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                            'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                        ],
                    ])
                ?>
            </div>
        </div>
    </div>
</div>    
