<?php

use common\models\UserInvoice;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var UserInvoice $userInvoice */
/** @var boolean $returnFromXmlExport */
/** @var ActiveForm $form */
?>

<?php $form = ActiveForm::begin([
    'id' => 'bill-list-filtration-form',
    'action' => ['bill/list'],
    'method' => 'GET',
]); ?>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 select">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo $form->field($userInvoice, 'type', [
                'inputOptions' => [
                    'id' => 'A-C-381a',
                ],
            ])->dropDownList(UserInvoice::getTranslatedTypes(), [
                'prompt' => Yii::t('app', 'ALL_INVOICES'),
            ])->label(Yii::t('element', 'A-C-381a')); ?>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($userInvoice, 'number', [
                'inputOptions' => [
                    'id' => 'A-C-381b',
                ],
            ])->label(Yii::t('element', 'A-C-381b')); ?>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <?php echo $form->field($userInvoice, 'companyName', [
                'inputOptions' => [
                    'id' => 'A-C-381c',
                ],
            ])->label(Yii::t('element', 'A-C-381c')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
            <?php echo $form->field($userInvoice, 'created_at')->widget(DatePicker::className(), [
                'options' => [
                    'id' => 'A-C-381d',
                    'class' => 'form-control created_at',
                    'onchange' => 'clearPeriod(); clearDateRange();',
                ],
                'language' => Yii::$app->language,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label(Yii::t('element', 'A-C-381d')); ?>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 select">
            <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
            <?php echo $form->field($userInvoice, 'period', [
                'inputOptions' => [
                    'id' => 'A-C-386',
                    'class' => 'form-control period',
                    'onchange' => 'clearCreatedAt(); clearDateRange();',
                ],
                'labelOptions' => [
                    'id' => 'A-C-385',
                    'class' => 'control-label'
                ],
            ])->dropDownList(UserInvoice::getTranslatedPeriods(), [
                'prompt' => Yii::t('app', 'NOT_SELECTED'),
            ])->label(Yii::t('element', 'A-C-385')); ?>
        </div>
        
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <?php $errorClass = $returnFromXmlExport ? ' has-error' : ''; ?>
            <?php echo $form->field($userInvoice, 'dateFrom', [
                'options' => [
                    'class' => 'form-group' . $errorClass,
                ],
                'labelOptions' => [
                    'id' => 'A-C-387',
                    'class' => 'control-label'
                ],
            ])->widget(DateRangePicker::className(), [
                'attributeTo' => 'dateTo',
                'form' => $form,
                'language' => Yii::$app->language,
                'labelTo' => Yii::t('element', 'A-C-389'),
                'options' => [
                    'id' => 'A-C-388',
                    'class' => 'form-control dateFrom',
                    'onchange' => 'clearCreatedAt(); clearPeriod();',
                ],
                'optionsTo' => [
                    'id' => 'A-C-390',
                    'class' => 'form-control dateTo',
                    'onchange' => 'clearCreatedAt(); clearPeriod();',
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ])->label(Yii::t('element', 'A-C-387')); ?>
        </div>
    </div>

    <div class="text-right">
        <?php echo Html::submitButton(Icon::show('filter', '', Icon::FA) . Yii::t('element', 'A-C-391'), [
            'id' => 'A-C-391',
            'class' => 'primary-button',
            'name' => 'bill-list-filtration-submit',
        ]); ?>
        
        <a id="A-C-382" href="<?php echo Url::to(['bill/list', 'lang' => Yii::$app->language]); ?>" 
           class="secondary-button reset-filtration"
        >
            <?php echo Icon::show('times', '', Icon::FA) . Yii::t('element', 'A-C-382'); ?>
        </a>
    </div>
<?php ActiveForm::end();

$this->registerJsFile(Url::base() . '/dist/js/bill/filtration.js', ['depends' => [JqueryAsset::className()]]);