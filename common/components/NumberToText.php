<?php

namespace common\components;

use Yii;

/**
 * Class NumberToText
 * @package common\components
 */
class NumberToText
{
    /** @var array List of words that are converted from number */
    private $words;

    /** @var boolean Attribute, whether queue was missed */
    private $memory;

    /** @var array List of translated units */
    public $units;

    /** @var array List of translated dozens from eleven to nineteen */
    public $elevenToNineteen;

    /** @var array List of translated dozens */
    public $dozens;

    /**
     * NumberToText constructor
     */
    public function __construct()
    {
        $this->addWord();
        $this->setMemory();
        $this->setUnits();
        $this->setElevenToNineteen();
        $this->setDozens();
    }

    /**
     * Adds word to words list
     *
     * @param string $word The word that needs to be added to words list
     */
    public function addWord($word = '')
    {
        if (empty($word)) {
            $this->words = [];
        } else {
            array_push($this->words, $word);
        }
    }

    /**
     * Returns words
     *
     * @return array
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * Sets memory value
     *
     * @param boolean $value Memory value
     */
    public function setMemory($value = false)
    {
        $this->memory = $value;
    }

    /**
     * Returns memory value
     *
     * @return boolean
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Sets units
     */
    public function setUnits()
    {
        $this->units = [
            Yii::t('document', 'ZERO'),
            Yii::t('document', 'ONE'),
            Yii::t('document', 'TWO'),
            Yii::t('document', 'THREE'),
            Yii::t('document', 'FOUR'),
            Yii::t('document', 'FIVE'),
            Yii::t('document', 'SIX'),
            Yii::t('document', 'SEVEN'),
            Yii::t('document', 'EIGHT'),
            Yii::t('document', 'NINE'),
        ];
    }

    /**
     * Sets eleven to nineteen
     */
    public function setElevenToNineteen()
    {
        $this->elevenToNineteen = [
            Yii::t('document', 'ZERO'),
            Yii::t('document', 'ELEVEN'),
            Yii::t('document', 'TWELVE'),
            Yii::t('document', 'THIRTEEN'),
            Yii::t('document', 'FOURTEEN'),
            Yii::t('document', 'FIFTEEN'),
            Yii::t('document', 'SIXTEEN'),
            Yii::t('document', 'SEVENTEEN'),
            Yii::t('document', 'EIGHTEEN'),
            Yii::t('document', 'NINETEEN'),
        ];
    }

    /**
     * Sets dozens
     */
    public function setDozens()
    {
        $this->dozens = [
            Yii::t('document', 'ZERO'),
            Yii::t('document', 'TEN'),
            Yii::t('document', 'TWENTY'),
            Yii::t('document', 'THIRTY'),
            Yii::t('document', 'FORTY'),
            Yii::t('document', 'FIFTY'),
            Yii::t('document', 'SIXTY'),
            Yii::t('document', 'SEVENTY'),
            Yii::t('document', 'EIGHTY'),
            Yii::t('document', 'NINETY'),
        ];
    }

    /**
     * Returns translated price to words
     *
     * @param null|float $price Price number
     * @return boolean|string
     */
    public static function getPrice($price = null)
    {
        if (is_null($price) || empty($price)) {
            return false;
        }

        $model = new self();
        $euros = $model->getEuros($price);
        $currency = $model->getCurrency($price);
        $cents = $model->getEuroCents($price);
        return $euros . ' ' . $currency . ' ' . Yii::t('document', 'INVOICE_AND') . ' ' . $cents;
    }

    /**
     * Returns converted price in euros to words
     *
     * @param null|float $price Price number
     * @return string
     */
    private function getEuros($price)
    {
        $number = intval($price);
        $digits = array_reverse(str_split($number));
        foreach ($digits as $key => $digit) {
            $next = isset($digits[$key + 1]);
            $previous = isset($digits[$key - 1]) ? $digits[$key - 1] : null;
            switch ($key) {
                case 0:
                    $this->units($next, $digit);
                    break;
                case 1:
                    $this->dozens($previous, $digit);
                    break;
                case 2:
                    $this->hundreds($digit);
                    break;
                case 3:
                    $this->thousands($next, $digit);
                    break;
                case 4:
                    $this->tensOfThousands($previous, $digit);
                    break;
                default:
                    break;
            }
        }

        return implode(' ', array_reverse($this->getWords()));
    }

    /**
     * Adds translated unit to words
     *
     * @param boolean $isNextExist Attribute, whether next digit exists
     * @param integer $digit Current digit
     */
    private function units($isNextExist, $digit)
    {
        if ($isNextExist) {
            $this->setMemory(true);
        } else {
            $this->addWord($this->units[$digit]);
            $this->setMemory(false);
        }
    }

    /**
     * Adds translated dozen to words
     *
     * @param null|integer $previous Previous digit
     * @param integer $digit Current digit
     */
    private function dozens($previous, $digit)
    {
        if (!$this->getMemory()) {
            return;
        }

        if ($digit == 0 && $previous != 0) {
            $this->addWord($this->units[$previous]);
        } elseif ($digit == 1) {
            $this->addWord(($previous == 0) ? $this->dozens[$digit] : $this->elevenToNineteen[$previous]);
        } else {
            $this->addWord($this->dozens[$digit] . (($previous != 0) ? ' ' . $this->units[$previous] : ''));
        }

        $this->setMemory(false);
    }

    /**
     * Adds translated hundreds to words
     *
     * @param integer $digit Current digit
     */
    private function hundreds($digit)
    {
        $this->addWord(($digit == 1) ? Yii::t('document', 'HUNDRED') : Yii::t('document', 'HUNDREDS'));
        $this->addWord($this->units[$digit]);
        $this->setMemory(false);
    }

    /**
     * Adds thousands to words
     *
     * @param boolean $isNextExist Attribute, whether next digit exists
     * @param integer $digit Current digit
     */
    private function thousands($isNextExist, $digit)
    {
        if (!$isNextExist) {
            $this->addWord(($digit == 1) ? Yii::t('document', 'THOUSAND') : Yii::t('document', 'THOUSANDS'));
            $this->addWord($this->units[$digit]);
        }
        $this->setMemory(false);
    }

    /**
     * Adds tens of thousands to words
     *
     * @param null|integer $previous Previous digit
     * @param integer $digit Current digit
     */
    private function tensOfThousands($previous, $digit)
    {
        if ($digit == 1) {
            $this->addWord($this->elevenToNineteen[$previous] . Yii::t('document', 'FOR_THOUSANDS'));
        } elseif ($digit == 0) {
            $this->addWord($this->units[$previous]);
            $this->addWord(($previous == 1) ? Yii::t('document', 'THOUSAND') : Yii::t('document', 'THOUSANDS'));
            $this->addWord($this->elevenToNineteen[$digit] . ' ' . $this->units[$previous]);
        }
    }

    /**
     * Returns translated currency by given price
     *
     * @param null|float $price Price number
     * @return string
     */
    private function getCurrency($price)
    {
        $currency = Yii::t('document', 'EUROS');
        $number = intval($price);
        if (strlen($number) <= 0) {
            return $currency;
        }

        $lastNumber = substr($number, -1, 1);
        if (substr($number, -2, 1) == 1) {
            return $currency;
        }

        switch ($lastNumber) {
            case 0:
                return Yii::t('document', 'EUROS');
            case 1:
                return Yii::t('document', 'EURO');
            default:
                return $currency;
        }
    }

    /**
     * Returns euro cents by given price
     *
     * @param null|float $price Price number
     * @return string
     */
    private function getEuroCents($price)
    {
        $number = str_replace('.', ',', $price);
        $position = strrpos($number, ',');
        if ($position) {
            return number_format(intval(substr($price, $position + 1)), 0) . ' ct';
        }
        return '00 ct';
    }
}