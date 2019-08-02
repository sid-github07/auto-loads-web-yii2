<?php

use common\models\Company;
use common\models\UserInvoice;
use common\models\UserService;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var UserService $userService */
/** @var Company $company */
/** @var UserInvoice $userInvoice */
?>

<?php $form = ActiveForm::begin([
    'id' => 'bill-planned-income-filtration-form',
    'action' => ['bill/planned-income'],
    'method' => 'GET',
]); ?>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($company, 'title', [
                'labelOptions' => [
                    'id' => 'A-C-447a',
                    'class' => 'control-label'
                ],
                'inputOptions' => [
                    'id' => 'A-C-448a',
                ],
            ])->label(Yii::t('element', 'A-C-447a')); ?>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($userInvoice, 'number', [
                'labelOptions' => [
                    'id' => 'A-C-447b',
                    'class' => 'control-label'
                ],
                'inputOptions' => [
                    'id' => 'A-C-448b',
                ],
            ])->label(Yii::t('element', 'A-C-447b')); ?>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($userService, 'price', [
                'labelOptions' => [
                    'id' => 'A-C-447c',
                    'class' => 'control-label'
                ],
                'inputOptions' => [
                    'id' => 'A-C-448c',
                ],
            ])->label(Yii::t('element', 'A-C-447c')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($userService, 'end_date', [
                'labelOptions' => [
                    'id' => 'A-C-447d',
                    'class' => 'control-label'
                ],
            ])->widget(DatePicker::className(), [
                'options' => [
                    'id' => 'A-C-448d',
                    'class' => 'form-control end_date',
                    'onchange' => 'clearPeriod(); clearDateRange();',
                ],
                'language' => Yii::$app->language,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label(Yii::t('element', 'A-C-447d')); ?>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 select">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo $form->field($userService, 'period', [
                'labelOptions' => [
                    'id' => 'A-C-452',
                    'class' => 'control-label'
                ],
                'inputOptions' => [
                    'id' => 'A-C-453',
                    'class' => 'form-control period',
                    'onchange' => 'clearEndDate(); clearDateRange();',
                ],
            ])->dropDownList(UserInvoice::getTranslatedPeriods(), [
                'prompt' => Yii::t('app', 'NOT_SELECTED'),
            ])->label(Yii::t('element', 'A-C-452')); ?>
        </div>
        
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <?php echo $form->field($userService, 'dateFrom', [
                'labelOptions' => [
                    'id' => 'A-C-454',
                    'class' => 'control-label'
                ],
            ])->widget(DateRangePicker::className(), [
                'attributeTo' => 'dateTo',
                'form' => $form,
                'language' => Yii::$app->language,
                'labelTo' => Yii::t('element', 'A-C-456'),
                'options' => [
                    'id' => 'A-C-455',
                    'class' => 'form-control dateFrom',
                    'onchange' => 'clearEndDate(); clearPeriod();',
                ],
                'optionsTo' => [
                    'id' => 'A-C-457',
                    'class' => 'form-control dateTo',
                    'onchange' => 'clearEndDate(); clearPeriod();',
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label(Yii::t('element', 'A-C-454')); ?>
        </div>
    </div>

    <div class="text-right">
        <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'A-C-458'), [
            'id' => 'A-C-458',
            'class' => 'primary-button',
            'name' => 'bill-planned-income-submit',
        ]); ?>

        <a href="<?php echo Url::to(['bill/planned-income', 'lang' => Yii::$app->language]) ?>"
           id="A-C-449"
           class="secondary-button reset-filtration"
        >
            <?php echo Icon::show('times', null, Icon::FA) . Yii::t('element', 'A-C-449'); ?>
        </a>
    </div>
<?php ActiveForm::end();

$this->registerJsFile(Url::base() . '/dist/js/bill/filtration.js', ['depends' => [JqueryAsset::className()]]);