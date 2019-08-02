<?php

use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this View */
/* @var $model Model */
/* @var $attribute string */
/* @var $vatRateCountries array */
/* @var $activeVatRateCountryCode string */
/* @var $containerOptions array */
/* @var $label string */
/* @var $labelOptions array */
/* @var $inputId string */
/* @var $inputOptions array */
/* @var $validationUrl null|string */

?>
<div <?php foreach ($containerOptions as $key => $value) {
    echo "{$key}=\"{$value}\"";
} ?>>
    <?php if (!is_null($label) && !empty($label)): ?>
        <label <?php foreach ($labelOptions as $key => $value) {
            echo "{$key}=\"{$value}\"";
        } ?>>
            <?php echo $label; ?>
        </label>
    <?php endif; ?>

    <div class="input-group">
        <div class="input-group-btn">
            <button id="vat-code-<?php echo $inputId; ?>"
                    type="button"
                    class="dropdown-toggle form-control vat-code-flag"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    <?php echo (isset($inputOptions['disabled']) && $inputOptions['disabled']) ? 'disabled="true"' : ''; ?>
            >
                <i class="flag-icon flag-icon-<?php echo strtolower($activeVatRateCountryCode); ?>"></i>
                <b class="caret"></b>
            </button>
            <ul class="vat-code-list-<?php echo $inputId; ?> dropdown-menu
                vat-code-list-dropdown"
                aria-labelledby="vat-code-<?php echo $inputId; ?>"
            >
                <?php if (!empty($vatRateCountries)): ?>
                    <?php foreach ($vatRateCountries as $code => $name): ?>
                        <li class="vat-code-item-<?php echo $inputId .
                            (strtolower($activeVatRateCountryCode) == strtolower($code) ? ' active' : ''); ?>
                            vat-code-list-dropdown-item"
                            data-code="<?php echo strtolower($code); ?>"
                        >
                            <a href="#">
                                <i class="flag-icon flag-icon-<?php echo strtolower($code); ?>"></i>
                                <span class="code"><?php echo strtoupper($code); ?></span>
                                <span class="name">(<?php echo $name; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        <input type="hidden"
               class="active-vat-code-<?php echo $inputId; ?>"
               name="active-vat-code-<?php echo $inputId; ?>"
               value="<?php echo strtoupper($activeVatRateCountryCode); ?>"
        >
        <?php if (is_null($model->$attribute) || empty($model->$attribute)) {
            $model->$attribute = $activeVatRateCountryCode;
        } ?>
        <?php echo Html::activeTextInput($model, $attribute, $inputOptions); ?>
    </div>

    <p class="help-block help-block-error"></p>
</div>

<?php
$this->registerJsFile(Url::base() . '/dist/js/widgets/validation.js', [
    'depends' => [
        JqueryAsset::className(),
    ]
]);

if (!is_null($validationUrl) && !empty($validationUrl)) {
    $this->registerJs("validationUrls['" . $inputId . "'] = '" . $validationUrl . "';", View::POS_END);
}

$this->registerJs("vatCodeInputIds.push('" . $inputId . "');", View::POS_END);
$this->registerJsFile(Url::base() . '/dist/js/widgets/vat-code.js', [
    'depends' => [
        JqueryAsset::className(),
    ]
]);