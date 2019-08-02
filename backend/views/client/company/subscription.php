<?php

use backend\controllers\ClientController;
use common\components\Model;
use common\models\UserService;
use common\models\UserServiceActive;
use dosamigos\datepicker\DatePicker;
use kartik\icons\Icon;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var null|integer $id Company ID */
/** @var ActiveDataProvider $dataProvider */
/** @var null|integer $year */
?>
<div class="company-subscription">
    <?php Pjax::begin(['id' => 'company-subscriptions-pjax']); ?>
        <div class="select year-select-wrapper">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            
            <?php echo Html::dropDownList('year', $year, Model::getYearsRange(), [
                'id' => 'A-C-185',
                'onchange' => 'changeYear();',
                'class' => 'subscription-year year-select',
            ]); ?>
        </div>
    
        <div class="responsive-table-wrapper custom-gridview subscription-list-gridview">
			<h2>
				<?php echo Yii::t('text', 'SUBSCRIPTION_ACTIVE'); ?>
			</h2>
            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered responsive-table'
                ],
                'rowOptions' => function ($userServiceActive) {
                    $hasSubscription = count($userServiceActive) > 0;
                    return ['class' => $hasSubscription ? 'paid' : 'not-paid'];
                },
                'afterRow' => function ($userServiceActive) {
                    $currentCredits = $userServiceActive->user->current_credits;
                    $credits = is_null($currentCredits) ? Yii::t('yii', '(not set)') : $currentCredits;
                    $span = Html::tag('span', $credits, ['class' => 'A-C-204']);
                    $text = Yii::t('element', 'A-C-201', ['quantity' => $span]);
                    $span = Html::tag('span', $text, ['class' => 'A-C-201']);
                    $td = Html::tag('td', $span, ['colspan' => 6]);
                    $hasSubscription = count($userServiceActive) > 0;
                    $subscriptionClass = 'credit-content-row ' . $hasSubscription ? 'paid' : 'not-paid';
                    $tr = Html::tag('tr', $td, ['class' => $subscriptionClass]);
                    return $tr;
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('element', 'A-C-186'),
                        'headerOptions' => [
                            'id' => 'A-C-186',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-186'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            $service = Html::tag('div', $userServiceActive->service->getTitle(), ['class' => 'A-C-192']);
                            $name = Html::tag('div', $userServiceActive->user->getNameAndSurname(), ['class' => 'A-C-193']);
                            $email = Html::tag('div', $userServiceActive->user->email, ['class' => 'A-C-194']);
                            return $service . $name . $email;
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => Yii::t('element', 'A-C-187'),
                        'headerOptions' => [
                            'id' => 'A-C-187',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-187'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            $dateTimeStamp = Html::tag('div', date('Y-m-d', $userServiceActive->created_at));
                            $hoursTimeStamp = Html::tag('div', date('H:i', $userServiceActive->created_at));
                            return Html::tag('span', $dateTimeStamp . $hoursTimeStamp, ['class' => 'A-C-195']);
                        },
                    ],
                    [
                        'attribute' => 'date_of_purchase',
                        'label' => Yii::t('element', 'A-C-188'),
                        'headerOptions' => [
                            'id' => 'A-C-188',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-188'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            return Html::tag('span', date('Y-m-d', $userServiceActive->date_of_purchase), ['class' => 'A-C-196']);
                        }
                    ],
                    [
                        'attribute' => 'end_date',
                        'label' => Yii::t('element', 'A-C-189'),
                        'headerOptions' => [
                            'id' => 'A-C-189',
                        ],
                        'contentOptions' => [
                            'class' => 'clearfix',
                            'data-title' => Yii::t('element', 'A-C-189'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            $datePicker = DatePicker::widget([
                                'name' => 'end_date',
                                'value' => date('Y-m-d', $userServiceActive->end_date),
                                'template' => '{input}{addon}',
                                'options' => [
                                    'disabled' => Yii::$app->admin->identity->isModerator(),
                                    'class' => 'A-C-197 end-date-' . $userServiceActive->id,
                                ],
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'startDate' => date('Y-m-d'),
                                ],
                            ]);
                            $button = '';
                            if (!Yii::$app->admin->identity->isModerator()) {
                                $button = Html::button(Icon::show('floppy-o', [], Icon::FA), [
                                    'class' => 'A-C-198 primary-button end-date-save-button',
                                    'onclick' => 'changeSubscriptionEndDate(' . $userServiceActive->id . ');'
                                ]);
                            }
                            $container = Html::tag('div', $datePicker . $button, [
                                'class' => 'end-date-datepicker-wrapper'
                            ]);
                            return $container;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'label' => Yii::t('element', 'A-C-190'),
                        'headerOptions' => [
                            'id' => 'A-C-190',
                        ],
                        'contentOptions' => [
                            'class' => 'clearfix',
                            'data-title' => Yii::t('element', 'A-C-190'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            $checkbox = Html::checkbox('status', $userServiceActive->isActive(), [
                                'class' => 'A-C-199 status-' . $userServiceActive->id,
                                'disabled' => Yii::$app->admin->identity->isModerator(),
                                'onchange' => 'changeSubscriptionActivity(' . $userServiceActive->id. ', ' . $userServiceActive->user_id  . ')',
                            ]);
                            $label = Html::tag('label', $checkbox, ['class' => 'custom-checkbox subscription-status']);
                            return $label;
                        }
                    ],
                    [
                        'attribute' => 'trans_id',
                        'label' => Yii::t('element', 'A-C-191'),
                        'headerOptions' => [
                            'id' => 'A-C-191',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-191'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserServiceActive $userServiceActive) {
                            // TODO: čia turi būti user_service id bet mes jog negauname
                            return Html::tag('span', $userServiceActive->id, [
                                'class' => 'A-C-200',
                            ]);
                        }
                    ],
                ],
                'pager' => [
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                ],
            ]); ?>
        </div>
		<div class="responsive-table-wrapper custom-gridview subscription-list-gridview">
            <h2>
                <?php echo Yii::t('text', 'SUBSCRIPTION_HISTORY'); ?>
            </h2>
            <?php echo GridView::widget([
                'dataProvider' => $historyDataProvider,
                'summary' => false,
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered responsive-table'
                ],
                'rowOptions' => function (UserService $userService) {
                    $hasSubscription = count($userService) > 0;
                    return ['class' => $userService->findActiveSubscription() ? 'paid' : 'not-paid'];
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('element', 'A-C-186'),
                        'headerOptions' => [
                            'id' => 'A-C-186',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-186'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $service = Html::tag('div', $userService->service->getTitle(), ['class' => 'A-C-192']);
                            $name = Html::tag('div', $userService->user->getNameAndSurname(), ['class' => 'A-C-193']);
                            $email = Html::tag('div', $userService->user->email, ['class' => 'A-C-194']);
                            return $service . $name . $email;
                        }
                    ],
                    [
                        'attribute' => 'createdAt',
                        'label' => Yii::t('element', 'A-C-187'),
                        'headerOptions' => [
                            'id' => 'A-C-187',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-187'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            $dateTimeStamp = Html::tag('div', date('Y-m-d', $userService->created_at));
                            $hoursTimeStamp = Html::tag('div', date('H:i', $userService->created_at));
                            return Html::tag('span', $dateTimeStamp . $hoursTimeStamp, ['class' => 'A-C-195']);
                        },
                    ],
                    [
                        'attribute' => 'startDate',
                        'label' => Yii::t('element', 'A-C-188'),
                        'headerOptions' => [
                            'id' => 'A-C-188',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-188'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            return Html::tag('span', date('Y-m-d', $userService->start_date), ['class' => 'A-C-196']);
                        }
                    ],
                    [
                        'attribute' => 'endDate',
                        'label' => Yii::t('element', 'A-C-189'),
                        'headerOptions' => [
                            'id' => 'A-C-189',
                        ],
                        'contentOptions' => [
                            'class' => 'clearfix',
                            'data-title' => Yii::t('element', 'A-C-189'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            return Html::tag('span', date('Y-m-d', $userService->end_date), ['class' => 'A-C-198']);
                        }
                    ],
                    [
                        'attribute' => 'trans_id',
                        'label' => Yii::t('element', 'A-C-191'),
                        'headerOptions' => [
                            'id' => 'A-C-191',
                        ],
                        'contentOptions' => [
                            'data-title' => Yii::t('element', 'A-C-191'),
                        ],
                        'format' => 'raw',
                        'value' => function (UserService $userService) {
                            // TODO: čia turi būti user_service id bet mes jog negauname
                            return Html::tag('span', $userService->id, [
                                'class' => 'A-C-200',
                            ]);
                        }
                    ],
                ],
                'pager' => [
                    'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                    'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                ],
            ]); ?>
        </div>
    <?php Pjax::end(); ?>

    <?php if (!Yii::$app->admin->identity->isModerator()): ?>
    <button id="A-C-205" class="primary-button" onclick="createNewSubscription();">
        <?php echo Yii::t('element', 'A-C-205'); ?>
    </button>
    <?php endif; ?>
</div>

<?php
Modal::begin([
    'id' => 'new-subscription-modal',
    'header' => Yii::t('element', 'A-C-205'),
]);
    Pjax::begin(['id' => 'new-subscription-pjax']);
    Pjax::end();
Modal::end();

$this->registerJs(
    'var actionCompanySubscriptions = "' . Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $id,
        'tab' => ClientController::TAB_COMPANY_SUBSCRIPTIONS,
    ]) . '"; ' .
    'var actionRenderNewSubscriptionForm = "' . Url::to([
        'client/render-new-subscription-form',
        'lang' => Yii::$app->language,
        'id' => $id,
        'tab' => ClientController::TAB_COMPANY_SUBSCRIPTIONS,
    ]) . '"; ' .
    'var actionGetSubscriptionRange = "' . Url::to(['client/get-subscription-range']) . '";' .
    'var actionChangeSubscriptionEndDate = "' . Url::to([
        'client/change-subscription-end-date',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionChangeSubscriptionActivity = "' . Url::to([
        'client/change-subscription-activity',
        'lang' => Yii::$app->language,
    ]) . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/subscription.js', ['depends' => [JqueryAsset::className()]]);