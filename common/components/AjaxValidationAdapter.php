<?php

namespace common\components;

use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;

/**
 * Class AjaxValidationAdapter
 *
 * @package common\components
 */
class AjaxValidationAdapter
{
    /** @var Model $model Model that is used in current form and needs to be validated */
    private $model;

    /**
     * AjaxValidationAdapter constructor.
     *
     * @param Model $model Model that is used in current form
     * @param string $scenario Current model scenario
     */
    function __construct(Model $model, $scenario = Model::SCENARIO_DEFAULT)
    {
        $this->setModel($model);
        $this->setScenario($scenario);
    }

    /**
     * Sets given model to current model
     *
     * @param Model $model
     */
    private function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Returns current model
     *
     * @return Model
     */
    private function getModel()
    {
        return $this->model;
    }

    /**
     * Sets scenario to current model
     *
     * @param string $scenario Current model scenario
     */
    private function setScenario($scenario)
    {
        $model = $this->getModel();
        $model->scenario = $scenario;
    }

    /**
     * Validates current model attributes
     *
     * @return string The error message array indexed by the attribute IDs in JSON format
     */
    public function validate()
    {
        if (Yii::$app->request->isPost) {
            $this->model->load(Yii::$app->request->post());
        } elseif (Yii::$app->request->isGet) {
            $this->model->load(Yii::$app->request->get());
        } else {
            return json_encode([]);
        }

        return json_encode(ActiveForm::validate($this->model));
    }
}