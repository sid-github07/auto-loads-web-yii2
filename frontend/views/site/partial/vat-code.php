<?php

use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/** @var View $this */
/** @var ActiveForm $form */
/** @var User $model */
/** @var string $attribute */
/** @var string $id */
/** @var string $containerClass */
/** @var string $activeCountryCode */
/** @var string $disabled */
/** @var array $countries */
/** @var string $label */
/** @var string $inputClass */

$list = '';
if (!empty($countries)) {
    foreach ($countries as $code => $name) {
        $active = strtolower($activeCountryCode) == strtolower($code) ? ' active' : ''; // Is current code is active
        $list .=
            '<li class="vat-code-list-dropdown-item vat-code-item-' . $id . $active . '" ' .
                'data-code="' . strtolower($code) . '"' .
            '>' .
                '<a href="#">' .
                    '<i class="flag-icon flag-icon-' . strtolower($code) .'"></i> ' .
                    '<span class="code">' . strtoupper($code) . '</span> ' .
                    '<span class="name">(' . $name . ')</span>' .
                '</a>' .
            '</li>';
    }
}

echo $form->field($model, $attribute, [
    'enableAjaxValidation' => true,
    'options' => [
        'class' => $containerClass,
    ],
    'inputOptions' => [
        'id' => $id,
        'class' => $inputClass,
    ],
    'template' =>
        '{label}' .
        '<div class="input-group vat-code-input-group">' .
            '<div class="input-group-btn">' .
                '<button id="vat-code-' . $id . '" ' .
                        'type="button" ' .
                        'class="dropdown-toggle form-control vat-code-flag" ' .
                        'data-toggle="dropdown" ' .
                        'aria-haspopup="true" ' .
                        'aria-expanded="false" ' .
                        $disabled .
                '>' .
                    '<i class="flag-icon flag-icon-' . strtolower($activeCountryCode) . '"></i> ' .
                    '<b class="caret"></b>' .
                '</button>' .
                '<ul class="vat-code-list-' . $id . ' dropdown-menu vat-code-list-dropdown" 
                     aria-labelledby="vat-code-' . $id . '"' .
                '>' .
                    $list .
                '</ul>' .
            '</div>' .
            '{input}' .
        '</div>' .
        '{error}{hint}',
])->label($label);

$this->registerJsFile(Url::base() . '/dist/js/site/partial/vat-code.js', ['depends' => [JqueryAsset::className()]]);