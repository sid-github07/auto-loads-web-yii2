<?php

namespace common\widgets;

use yii\base\Model;
use yii\base\Widget;

/**
 * Class VatCode
 *
 * @package common\widgets
 */
class VatCode extends Widget
{
    /** @var null|Model Model object that has attribute for VAT code (required to be set) */
    public $model = null;

    /** @var null|Model Model VAT code attribute name (required to be set) */
    public $attribute = null;

    /** @var array List of available countries which have VAT rate */
    public $vatRateCountries = [];

    /** @var string Currently active VAT rate country code */
    public $activeVatRateCountryCode = '';

    /** @var array List of attributes with values for widget container */
    public $containerOptions = [];

    /** @var null|string Widget label value */
    public $label = null;

    /** @var array List of attributes with values for widget label */
    public $labelOptions = [];

    /** @var null|string Input field ID value */
    private $inputId = null;

    /** @var array List of attributes with values for widget input */
    public $inputOptions = [];

    /** @var null|string URL to attribute validation */
    public $validationUrl = null;

    /**
     * Returns input ID
     *
     * @return null|string
     */
    public function getInputId()
    {
        return $this->inputId;
    }

    /**
     * Sets input ID
     *
     * @param string $inputId Input field ID value
     */
    private function setInputId($inputId)
    {
        $this->inputId = $inputId;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $base = new Base($this);
        $base->validateModel();
        $base->validateAttribute();
        $this->setInputId($base->getFinalInputId());
        $this->inputOptions = $base->getFinalInputOptions();
        $this->containerOptions = $base->getFinalContainerOptions();
        $this->labelOptions = $base->getFinalLabelOptions();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('vat-code', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'vatRateCountries' => $this->vatRateCountries,
            'activeVatRateCountryCode' => $this->activeVatRateCountryCode,
            'containerOptions' => $this->containerOptions,
            'label' => $this->label,
            'labelOptions' => $this->labelOptions,
            'inputId' => $this->getInputId(),
            'inputOptions' => $this->inputOptions,
            'validationUrl' => $this->validationUrl,
        ]);
    }
}