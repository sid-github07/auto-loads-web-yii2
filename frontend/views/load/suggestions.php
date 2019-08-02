<?php

use common\models\Load;
use common\models\LoadCity;
use common\models\User;
use common\models\LoggingActivatedEmailServices;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use yii\grid\GridView;

/** @var View $this */
/** @var array $day */
/** @var array $previous */
/** @var array $signUpCityLoads */
/** @var Load[] $loads */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var boolean $showHideButton */
/** @var array $pages */

$this->title = Yii::t('seo', 'TITLE_LOAD_SUGGESTIONS');
?>
<div class="load-suggestions">
    <h1 id="KP-C-1">
        <?php echo Yii::t('element', 'my_searches'); ?>
    </h1>

    <?php $form = ActiveForm::begin([
        'id' => 'search-results-filter-form',
        'validationUrl' => ['load/validate-suggestions-filter', 'lang' => Yii::$app->language],
        'method' => 'GET',
    ]); ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo $form->field($load, 'date', [
                    'enableAjaxValidation' => true,
                ])->widget(DatePicker::className(), [
                    'options' => [
                        'id' => 'KP-C-1a',
                        'class' => 'form-control',
                    ],
                    'language' => Yii::$app->language,
                    'clientOptions' => [
                        'startDate' => date('Y-m-d'),
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ])->label(Yii::t('element', 'KP-C-1a')); ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-10',
                    'loadCity' => $loadCity,
                    'attribute' => 'loadCityId',
                    'label' => Yii::t('element', 'KP-C-1b'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => null,
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'load' => true,
                    ]),
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo Yii::$app->controller->renderPartial('__load-city', [
                    'form' => $form,
                    'id' => 'IK-C-11',
                    'loadCity' => $loadCity,
                    'attribute' => 'unloadCityId',
                    'label' => Yii::t('element', 'KP-C-1c'),
                    'placeholder' => null,
                    'multiple' => false,
                    'cities' => null,
                    'url' => Url::to([
                        'site/city-list',
                        'lang' => Yii::$app->language,
                        'unload' => true,
                    ]),
                ]) ?>
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <?php echo $form->field($load, 'suggestionsType', [
                    'options' => [
                        'id' => 'KP-C-1d',
                    ],
                ])
                    ->dropDownList(Load::getSuggestionsTypes())
                    ->label(Yii::t('element', 'KP-C-1d')); ?>
                
                <span class="select-addon"><i class="fa fa-caret-down"></i></span>
            </div>
        </div>

        <div class="text-right">
            <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'KP-C-1e'), [
                'id' => 'KP-C-1e',
                'class' => 'primary-button filter-loads-btn',
            ]); ?>
            <a href="<?php echo Url::to(['load/suggestions', 'lang' => Yii::$app->language]); ?>" 
               class="secondary-button clear-suggestions-filter"
            >
                <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'KP-C-1f'); ?>
            </a>
        </div>

    <?php ActiveForm::end(); ?>
    <div id="email-updated" class="hidden alert alert-info">
        <p><?php echo Yii::t('element', Yii::t('element', 'email_update_success')); ?></p>
    </div>
    <div id="load-removed" class="hidden alert alert-info">
        <p><?php echo Yii::t('element', Yii::t('element', 'load_removed')); ?></p>
    </div>
    <div id="log-service-activated" class="hidden alert alert-info">
        <p><?php echo Yii::t('element', Yii::t('element', 'log_service_activated')); ?></p>
    </div>
    <div class="paginator-options">
        <span class="posts-per-page-select">
            <label id="L-T-14" class="page-size-filter-label">
                <?php echo Yii::t('element', 'L-T-14'); ?>
            </label>
            <?php echo Html::dropDownList('per-page', $pageSize, Load::getPageSizes(), [
                'id' => 'L-T-15',
                'onchange' => 'changePageSize(this);',
            ]); ?>
        </span>
    </div>
    <section class="day-searches">
        <div class="responsive-table-wrapper roundtrips-table-wrapper">
        <?php echo GridView::widget([
            'dataProvider' => $loads,
            'tableOptions' => [
                'class' => 'table responsive-table table--striped'
            ],
            'options' => [
                'class' => 'grid-view',
            ],
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'loadCity',
                    'label' => Yii::t('element', 'L-T-17'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-17'],
                    'contentOptions' => [
                        'class' => 'L-T-22 load-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-17')
                    ],
                    'value' => function (Load $model) {
                        $cities = [];
                        foreach ($model->loadCities as $loadCity) {
                            if ($loadCity->isLoadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($model->loadCities[0]->load_postal_code)) {
                            $postalCode = ' (' . $model->loadCities[0]->load_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'unloadCity',
                    'label' => Yii::t('element', 'L-T-18'),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-18'],
                    'contentOptions' => [
                        'class' => 'L-T-23 unload-city-collumn-content',
                        'data-title' => Yii::t('element', 'L-T-18')
                    ],
                    'value' => function (Load $model) {
                        $cities = [];
                        foreach ($model->loadCities as $loadCity) {
                            if ($loadCity->isUnloadingCity()) {
                                $loadCity->addCitiesToCountryList($cities);
                            }
                        }
                        $postalCode = '';
                        if (!empty($model->loadCities[0]->unload_postal_code)) {
                            $postalCode = ' (' . $model->loadCities[0]->unload_postal_code . ')';
                        }
                        return LoadCity::getFormattedCities($cities) . $postalCode;
                    }
                ],
                [
                    'attribute' => 'hasFound',
                    'label' => yii::t('element', 'Services'),
                    'format' => 'raw',
                    'headerOptions' => [
                        'id' => 'send-email-th'
                    ],
                    'contentOptions' => [
                        'class' => 'send-email-td',
                    ],
                    'value' => function (Load $load) use($log_service_details) {
                        $load_id = $load->id;
                        $load = Load::findOne($load->id);
                        $user_id = $load['user_id'];
                        $user = User::findOne($load->user_id);
                        $email = $load->user['email'];
                        $send_mail_function = "send_mail_userlog('$email');";
                        $enevelop_color = 'black';
                        if (!empty($log_service_details)) {
                            foreach ($log_service_details as $log_service) {
                                if ($log_service->load_id == $load_id && $log_service->user_id == $user_id) {
                                    if ($log_service->log_activated == 1) {
                                        $enevelop_color = 'orange';
                                    }
                                }
                            }
                        }
                        $notify_by_email = Html::tag('span', Yii::t('element', Yii::t('element', 'notify_by_email')) , [
                            'id' => $load_id."_".$user_id,
                            'class'=>'send-mail-text fa fa-envelope-o',
                            'data-placement'=>'left',
                            'data-toggle'=>'popover',
                            'data-title'=>Yii::t('element', Yii::t('element', 'notify_by_email')),
                            'data-container'=>'body',
                            'data-html'=>'true',
                            'data-placement'=>'left',
                            'style' => "color:$enevelop_color"
                        ]);

                        $credit_1_per_notification = Html::tag('p', Yii::t('element', Yii::t('element', 'credit_1_per_notification')) , []);
                        $activate_btn = Html::tag('button', Yii::t('element', Yii::t('element', 'activate')) , [
                            'class' => 'btn btn-warning',
                            'onclick' => "activate_load($load_id,$user_id)"
                        ]);
                        $cancel_btn = Html::tag('button', Yii::t('element', Yii::t('element', 'cancel')) , [
                            'class' => 'btn btn-default close-popover'
                        ]);
                        $small_email = Html::tag('small', $email , [
                            'id' => $user_id,
                            'class' => 'pull-right text-primary load-user-email',
                            'onclick' => "openEmailModal('$email',$user_id)"
                        ]);
                        $popover_content = Html::tag('div', $credit_1_per_notification . $activate_btn . $cancel_btn . $small_email , [
                            'class'=>'hide send_sms_div'
                        ]);

                        return $notify_by_email . $popover_content;
                    }
                ],
                [
                    'attribute' => 'loadInfo',
                    'label' => Yii::t('element', ''),
                    'format' => 'raw',
                    'headerOptions' => ['id' => 'L-T-19'],
                    'contentOptions' => [
                        'class' => 'search-created-at-column text-left remove-row-loads'
                    ],
                    'value' => function (Load $load) {
                        $load_id = $load->id;
                        return Html::tag('span','&times;', ['class'=>'text-muted hide_current_row','onclick'=>"remove_load($load_id)"]);
                    }
                ]
            ],
            'pager' => [
                'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
            ],
        ]); ?>
    </div>
    </section>
</div>

<?php
Modal::begin([
    'id' => 'load-preview-modal',
    'header' => Yii::t('element', 'IA-C-56a'),
    'size' => 'modal-lg',
]);

    echo Html::tag('div', null, [
        'id' => 'load-preview-modal-content',
    ]);

Modal::end();

Modal::begin([
    'id' => 'load-link-preview-modal',
    'header' => Yii::t('element', 'IA-C-55a'),
]);

    Pjax::begin(['id' => 'load-link-preview-pjax']);
    Pjax::end();

Modal::end();
Modal::begin([
    'id' => 'change-email-address-modal',
    'header' => Yii::t('element', 'change_email_address'),
    'size' => 'modal-sm',
]);
echo "<form method='POST'>
    <div class='modal-body'>
        <div class='form-group'>
            <input type='text' value='' style='display:none;' class='load_user_id'>
            <input type='text' value='' class='form-control load-user-email-txt-box'>
        </div>
    </div>
    <div class='modal-footer'>
        <button type='button' class='btn btn-success' onclick=change_email_address('modal_id')>". Yii::t('element', Yii::t('element', 'save')) ."</button>
    </div>
    </form>";
Modal::end();

$this->registerJs(
    'var actionLoadPreview = "' . Url::to(['load/preview', 'lang' => Yii::$app->language]) . '"; '.
    'var actionRemoveLoad = "' . Url::to(['load/remove-load', 'lang' => Yii::$app->language]) . '"; '.
    'var actionChangeEmailAddress = "' . Url::to(['load/change-email-address', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionActivateLoad = "' . Url::to(['load/activate-load', 'lang' => Yii::$app->language]) . '"; ' .
	'var actionPreviewLoadLink = "' . Url::to(['load/preview-load-link', 'lang' => Yii::$app->language]) . '"; ', 
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/load/search-results.js', ['depends' => [JqueryAsset::className()]]);