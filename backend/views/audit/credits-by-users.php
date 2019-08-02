<?php

use common\models\UserLog;
use common\components\audit\Pay;
use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$this->title = Yii::t('app', 'Credits_logs');
$this->params['breadcrumbs'][] = $this->title;

$hereRoute = 'audit/credits-users';

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
                            'id' => 'U-L-9',
                        ],
                        'labelOptions' => [
                            'id' => 'U-L-9',
                            'class' => 'control-label',
                        ],
                    ])->dropDownList(Pay::getSearchCreditFilter(), [
                        'multiple' => 'multiple',
                        'size' => 5,
                    ])->label(Yii::t('element', 'Type of log')); ?>  
                </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <?php echo $form->field($userLog, 'startDate', [
                        'options' => [
                            'id' => 'U-L-8',
                        ],
                        'labelOptions' => [
                            'id' => 'U-L-8',
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
                                'label' => Yii::t('app', 'Log type'),
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
                                'attribute' => 'credits',
                                'label' => Yii::t('app', 'Amount_of_credits_used'),
                                'value' => function (UserLog $userLog) {
                                    return $userLog->data;
                                },
                                'headerOptions' => [
                                    'style' => 'width: 10%',
                                ],
                            ],
                            [
                                'attribute' => 'userInfo',
                                'label' => Yii::t('app', 'SEARCH_USER_LABEL'),
                                'format' => 'raw',
                                'headerOptions' => [
                                    'id' => 'userInfo',
                                    'class' => 'search-user-info-at-column text-left',
                                    'style' => 'width: 25%',
                                ],
                                'contentOptions' => [
                                    'class' => 'search-user-info-at-column-content text-left userInfo',
                                ],
                                'value' => function (UserLog $userLog) {
                                    return $userLog->user->name . ' ' .  $userLog->user->surname . ' ' . Html::mailto($userLog->user->email, 'mailto:' . $userLog->user->email);
                                }
                            ],
                            [
                                'attribute' => 'date',
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
                                'value' => function (UserLog $userLog) {
                                    return date('Y/m/d H:i:s',$userLog->created_at);
                                }
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

