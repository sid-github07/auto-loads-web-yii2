<?php

namespace common\widgets;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\RequiredValidator;

/**
 * Class Base
 *
 * @package common\widgets
 */
class Base
{
    /** @var Model Current widget model */
    private $model;

    /** @var string Current widget model attribute */
    private $attribute;

    /** @var array $containerOptions List of attributes for widget container */
    private $containerOptions = [];

    /** @var array $defaultContainerOptions Default attributes and values for widget container */
    private $defaultContainerOptions = [
        'class' => 'form-group',
    ];

    /** @var array $labelOptions List of attributes for widget label */
    private $labelOptions = [];

    /** @var array $defaultLabelOptions Default attributes and values for widget label */
    private $defaultLabelOptions = [
        'class' => 'control-label',
    ];

    /** @var null|string $inputId Input field ID value */
    private $inputId = null;

    /** @var array $inputOptions List of attributes for widget input */
    private $inputOptions = [];

    /** @var array $defaultInputOptions Default attributes and values for widget input */
    private $defaultInputOptions = [
        'class' => 'form-control',
    ];

    /**
     * Base constructor.
     *
     * @param $widget
     */
    public function __construct($widget)
    {
        $this->setModel($widget->model);
        $this->setAttribute($widget->attribute);
        $this->setContainerOptions($widget->containerOptions);
        $this->setLabelOptions($widget->labelOptions);
        $this->setInputId($widget->getInputId());
        $this->setInputOptions($widget->inputOptions);
    }

    /**
     * Returns current widget model
     *
     * @return Model
     */
    private function getModel()
    {
        return $this->model;
    }

    /**
     * Sets current widget model
     *
     * @param Model $model Current widget model
     */
    private function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Returns current widget model attribute
     *
     * @return string
     */
    private function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Sets current widget model attribute
     *
     * @param string $attribute Current widget model attribute
     */
    private function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Returns current widget container options
     *
     * @return array
     */
    private function getContainerOptions()
    {
        return $this->containerOptions;
    }

    /**
     * Sets current widget container options
     *
     * @param array $containerOptions Current widget container options
     */
    private function setContainerOptions($containerOptions)
    {
        $this->containerOptions = $containerOptions;
    }

    /**
     * Returns current widget label options
     *
     * @return array
     */
    private function getLabelOptions()
    {
        return $this->labelOptions;
    }

    /**
     * Sets current widget label options
     *
     * @param array $labelOptions Current widget label options
     */
    private function setLabelOptions($labelOptions)
    {
        $this->labelOptions = $labelOptions;
    }

    /**
     * Returns current widget input ID
     *
     * @return null|string
     */
    private function getInputId()
    {
        return $this->inputId;
    }

    /**
     * Sets current widget input ID
     *
     * @param $inputId
     */
    private function setInputId($inputId)
    {
        $this->inputId = $inputId;
    }

    /**
     * Returns current widget input options
     *
     * @return array
     */
    private function getInputOptions()
    {
        return $this->inputOptions;
    }

    /**
     * Sets current widget input options
     *
     * @param array $inputOptions Current widget input options
     */
    private function setInputOptions($inputOptions)
    {
        $this->inputOptions = $inputOptions;
    }

    /**
     * Validates current widget model
     *
     * @throws ErrorException If model is not set or empty or model type is invalid
     */
    public function validateModel()
    {
        if (is_null($this->getModel()) || empty($this->getModel())) {
            throw new ErrorException(Yii::t('app', 'WIDGET_MODEL_NOT_FOUND'));
        }

        if (!$this->getModel() instanceof Model) {
            throw new ErrorException(Yii::t('app', 'INVALID_WIDGET_MODEL_TYPE'));
        }
    }

    /**
     * Validates current widget model attribute
     *
     * @throws ErrorException If attribute is not set or empty or not string or not between model attributes
     */
    public function validateAttribute()
    {
        if (is_null($this->getAttribute()) || empty($this->getAttribute())) {
            throw new ErrorException(Yii::t('app', 'WIDGET_ATTRIBUTE_NOT_FOUND'));
        }

        if (!is_string($this->getAttribute())) {
            throw new ErrorException(Yii::t('app', 'INVALID_WIDGET_ATTRIBUTE_TYPE'));
        }

        if (!in_array($this->getAttribute(), $this->getModel()->attributes()) &&
            !property_exists($this->getModel(), $this->getAttribute())) {
            throw new ErrorException(Yii::t('app', 'INVALID_WIDGET_ATTRIBUTE'));
        }
    }

    /**
     * Returns final input ID
     *
     * Whether input ID is set and not empty - it is returned instantly.
     * Otherwise, it is created from model class name and model attribute name.
     *
     * @return string
     */
    public function getFinalInputId()
    {
        if (isset($this->getInputOptions()['id']) && !empty($this->getInputOptions()['id'])) {
            $this->setInputId($this->getInputOptions()['id']);
            return $this->getInputId();
        }

        $modelName = $this->getFormattedModelName();
        $this->setInputId($modelName . '-' . strtolower($this->getAttribute()));
        return $this->getInputId();
    }

    /**
     * Returns formatted model name
     *
     * @return string
     */
    private function getFormattedModelName()
    {
        $className = $this->getModelClassName();
        return $this->formatModelClassName($className);
    }

    /**
     * Returns model class name
     *
     * @return string
     */
    private function getModelClassName()
    {
        $classNamespace = new \ReflectionClass($this->getModel());
        return $classNamespace->getShortName();
    }

    /**
     * Formats model class name
     *
     * @param string $className Model class name that needs to be formatted
     * @return string
     */
    private function formatModelClassName($className = '')
    {
        $string = '';
        $pieces = preg_split('/(?=[A-Z])/', $className);
        foreach ($pieces as $piece) {
            if (!empty($piece)) { // NOTE: first array element is empty, because class name starts with uppercase letter
                $string .= strtolower($piece) . '-';
            }
        }
        return rtrim($string, '-');
    }

    /**
     * Returns final input options
     *
     * @return array
     */
    public function getFinalInputOptions()
    {
        $this->setInputOptions(ArrayHelper::merge($this->defaultInputOptions, $this->getInputOptions()));
        return $this->getInputOptions();
    }

    /**
     * Returns final container options
     *
     * @return array
     */
    public function getFinalContainerOptions()
    {
        $this->addFieldClass();
        $this->setContainerOptions(ArrayHelper::merge($this->defaultContainerOptions, $this->getContainerOptions()));
        $this->addRequiredClass();
        return $this->getContainerOptions();
    }

    /**
     * Adds field-{input-id} class to default container options
     */
    private function addFieldClass()
    {
        $defaultContainerClass = $this->defaultContainerOptions['class'];
        $this->defaultContainerOptions['class'] = "{$defaultContainerClass} field-{$this->getInputId()}";
    }

    /**
     * Adds required class to container options class if model attribute is required
     */
    private function addRequiredClass()
    {
        $containerClass = $this->getContainerOptions()['class'];
        foreach ($this->getModel()->validators as $validator) {
            foreach ($validator->attributes as $attribute) {
                if ($attribute == $this->getAttribute() && $validator instanceof RequiredValidator) {
                    $containerOptions = $this->getContainerOptions();
                    $containerOptions['class'] = "{$containerClass} required";
                    $this->setContainerOptions($containerOptions);
                }
            }
        }
    }

    /**
     * Returns final label options
     *
     * @return array
     */
    public function getFinalLabelOptions()
    {
        $this->addForAttribute();
        $this->setLabelOptions(ArrayHelper::merge($this->defaultLabelOptions, $this->getLabelOptions()));
        return $this->getLabelOptions();
    }

    /**
     * Adds for attribute to label options
     */
    private function addForAttribute()
    {
        $this->defaultLabelOptions['for'] = $this->getInputId();
    }
}