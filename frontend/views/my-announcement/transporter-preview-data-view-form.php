<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\components\Location;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var \common\models\CarTransporter $carTransporter
 * @var CreditService $service
*/

$user = Yii::$app->user->identity;
$serviceBought = $user->hasBoughtService($carTransporter, \common\models\CreditService::CREDIT_TYPE_CAR_TRANSPORTER_PREVIEW_VIEW);
?>

<?php if ($serviceBought === false): ?>
    <?php
    $form = ActiveForm::begin([
        'action' => [
            Url::to([
                'my-car-transporter/transporter-preview-buy',
                'lang' => Yii::$app->language,
                'id' => $carTransporter->id
            ])
       ],
    ]);
    ?>
    <div class="row">
        <div class="col-xs-12">
            <div id="alert" class="alert alert-warning">
                <?php if($service->credit_cost > $user->service_credits): ?>
                    <?php echo Yii::t('element', 'preview_not_enough_credits {0}', [$dataProvider->getTotalCount()]);?>
                <?php else: ?>
                    <?php echo Yii::t('element', 'preview_buy_service {0}', [$dataProvider->getTotalCount()]);?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xs-offset-7 col-xs-5 stats-wrap">
            <span class="adv-credits-cost pull-right"><?php echo Yii::t('element', 'adv_credits_cost');?>
                <span class="cred-val"><?php echo $service->credit_cost ?></span>
            </span>
            <span class="adv-total-credits pull-right"><?php echo Yii::t('element', 'adv_total_credits {0}', [$user->service_credits]);?></span>
            <span id="total-creds-topup" class="adv-total-credits-topup pull-right">
                <a href="<?php echo Url::to(['subscription/', 'tab' => \frontend\controllers\SubscriptionController::TAB_CREDIT_TOP_UP_ORDER])?>"><?php echo Yii::t('element', 'adv_credits_topup'); ?></a>
            </span>
        </div>
    </div>
    <?php if ($service->credit_cost <= $user->service_credits): ?>
        <div class="modal-form-footer-center text-center">
            <button type="submit" class="buy-preview-service primary-button">
                <i class="fa fa-shopping-cart"></i>
                <?php echo Yii::t('element', 'preview_view'); ?>
            </button>
        </div>
    <?php endif; ?>
<?php ActiveForm::end(); ?>
<?php else: ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'tableOptions' => ['class' => 'table responsive-table'],

            'options' => [
                'class' => 'grid-view',
            ],
            'columns' => [
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('element', 'preview_created_at'),
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'L-T-21',
                        'data-title' => Yii::t('element', 'preview_created_at')
                    ],
                    'value' => function (\common\models\CarTransporterPreview $preview) {
                        $location = Location::getGeoLocation();
                        $date = date_create(date('Y-m-d H:i:s', $preview->created_at));
                        if ($location != null) {
                            $date->setTimeZone(new DateTimeZone($location->timeZone));
                        }
                        return $date->format('Y-m-d H:i:s');
                    }
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('element', 'preview_name'),
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'L-T-21',
                        'data-title' => Yii::t('element', 'preview_name')
                    ],
                    'value' => function (\common\models\CarTransporterPreview $preview) {

                        $user = $preview->user;
                        return $user->name . ' ' . $user->surname . (is_null($user->company_name) ? '' : ' | ' . $user->company_name);
                    }
                ],
                [
                    'attribute' => 'email',
                    'label' => Yii::t('element', 'preview_email'),
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'L-T-21',
                        'data-title' => Yii::t('element', 'preview_email')
                    ],
                    'value' => function (\common\models\CarTransporterPreview $preview) {

                        $user = $preview->user;
                        return $user->email;
                    }
                ],
                [
                    'attribute' => 'phone',
                    'label' => Yii::t('element', 'preview_phone'),
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'L-T-21',
                        'data-title' => Yii::t('element', 'preview_phone')
                    ],
                    'value' => function (\common\models\CarTransporterPreview $preview) {

                        $user = $preview->user;
                        return $user->phone;
                    }
                ],
            ],
            'pager' => [
                'firstPageLabel' => Yii::t('text', 'FIRST_PAGE_LABEL'),
                'lastPageLabel' => Yii::t('text', 'LAST_PAGE_LABEL'),
            ],
        ]);  ?>

<?php endif; ?>

