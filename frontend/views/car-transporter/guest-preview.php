<?php

use common\models\CarTransporter;
use common\models\CreditCode;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\web\View;

/** @var CarTransporter $carTransporter */
/** @var boolean $showInfo */
?>
<div class="cartransport-preview-container text-center">
    <div class="row text-left" style="width:100%; margin: 0px 10px 0px 10px;">
        <span><b><?php echo Yii::t('element', 'C-T-105') ?></b></span>
    </div>
    <div class="row">
        <div class="search-load-guest-preview-col">
            <!--<?php //Pjax::begin(['id' => 'car-transporter-link-creditcost-pjax']); ?>-->
            <?= Html::beginForm(['car-transporter/preview', 'lang' => Yii::$app->language], 'post', ['id' => 'car-transporter-creditcode-form', 'data-pjax' => '', 'class' => 'form-inline']); ?>
                <div class="form-group search-load-guest-preview-form-group <?php echo ($hasError ? 'has-error' : ''); ?>">
                    <?php echo Html::input('hidden', 'id', Yii::$app->request->post('id')); ?>
                    <?php echo Html::input('hidden', 'showInfo', Yii::$app->request->post('showInfo')); ?>
                    <?php echo Html::input('text', 'creditCode', 
                        $sCreditCode, 
                        [   
                            'class' => 'form-control form-control-plaintext',
                            'placeholder' => Yii::t('element', 'C-T-100'),
                            'maxlength' => CreditCode::CREDIT_CODE_LENGTH,
                            'onKeydown' => 'if (event.keyCode == 13) { return false;}',
                        ]); 
                    ?>
                    <?= Html::button('<i class="fa fa-sign-in" aria-hidden="true"></i>', [
                        'id' => 'car-transporter-link-creditcost-button',
                        'class' => 'btn btn-lg primary-button search-load-check-credit-btn',
                        'onClick' => 'refreshCarTransporterPreview(event, ' . Yii::$app->request->post('id') . ');',
                        'name' => 'credit-button'
                        ]) ?>
                    <?php if (!empty($creditsCost)) { ?>
                        <p class="help-block search-load-guest-preview-help-block">
                            <?php echo Yii::t('element', 'C-T-101', ['price' => $creditsCost]); ?>
                        </p>
                    <?php } ?>
                </div>
            <?= Html::endForm() ?>
            <!--<?php //Pjax::end(); ?>-->
        </div>
        <div class="search-load-guest-preview-col">
            <span class="search-load-guest-preview-text"><?php echo Yii::t('element', 'C-T-103'); ?></span>
            <a href="<?php echo Url::to(['creditcode/buy-credits', 'lang' => Yii::$app->language]); ?>"
               class="btn btn-sm buy-creditcodes-btn search-load-guest-preview-buy-credits-btn"
            ><?php echo Yii::t('element', 'C-T-102'); ?></a>
        </div>

        <div class="search-load-guest-preview-col">
            <span class="search-load-guest-preview-text"><?php echo Yii::t('element', 'C-T-103'); ?></span>
            <a href="<?php echo Url::to(['site/login', 'lang' => Yii::$app->language]); ?>"
                class="btn btn-sm primary-button search-load-guest-preview-sign-in-btn"><i class="fa fa-sign-in"></i><?php echo Yii::t('element', 'C-T-8d'); ?></a>
            <span class="search-load-guest-preview-text"><?php echo Yii::t('element', 'C-T-104'); ?></span>
        </div>

    </div>
</div>

<div  class="search-results-load-code">
    <?php echo '#' . $carTransporter->code; ?>
</div>

<?php if ($showInfo === 'true'): ?>
    <div class="load-info"><?php echo Yii::t('element', 'C-T-8b') . ': ' . $carTransporter->quantity; ?></div>
<?php endif; ?>