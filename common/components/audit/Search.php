<?php

namespace common\components\audit;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\City;
use common\models\Load;
use common\models\LoadCar;
use common\models\LoadCity;
use Yii;
use yii\helpers\Json;

/**
 * Class Search
 *
 * @package common\components\audit
 */
class Search extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'SEARCH';

    /** @const string Log message placeholder when user searches for load */
    const PLACEHOLDER_USER_SEARCHED_FOR_LOAD = 'USER_SEARCHED_FOR_LOAD';

    /** @const string Log message placeholder when user searches for car transporter */
    const PLACEHOLDER_USER_SEARCHED_FOR_CAR_TRANSPORTER = 'USER_SEARCHED_FOR_CAR_TRANSPORTER';

    /**
     * Search constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param array $models User action models
     */
    public function __construct($placeholder, $models)
    {
        parent::__construct(self::ACTION, $placeholder, $models, null);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_SEARCHED_FOR_LOAD:
                parent::setData($this->getLoadData());
                break;
            case self::PLACEHOLDER_USER_SEARCHED_FOR_CAR_TRANSPORTER:
                parent::setData($this->getCarTransporterData());
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user searches for load
     *
     * @return string
     */
    private function getLoadData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        foreach ($this->getModels() as $model) {
            if ($model instanceof Load) {
                $data['params']['radius'] = $model->searchRadius;
				$data['params']['haveResults'] = $model->haveResults;
				$data['params']['type'] = is_null($model->type) ? 'All' : ($model->isTypeFull() ? 'Full' : 'Partial');
                continue;
            }

            if ($model instanceof LoadCar) {
                $data['params']['carsAmount'] = $model->quantity;
                continue;
            }

            if ($model instanceof LoadCity) {
                $data['params']['loadArea'] = City::getNameById($model->loadCityId);
                $data['params']['unloadArea'] = City::getNameById($model->unloadCityId);
                continue;
            }
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user searches for car transporter
     *
     * @return string
     */
    private function getCarTransporterData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        foreach ($this->getModels() as $model) {
            if ($model instanceof CarTransporter) {
                $data['params']['radius'] = $model->radius;
                $data['params']['quantity'] = $model->quantity;
				$data['params']['haveResults'] = $model->haveResults;
                if (empty($model->available_from)) {
                    $data['params']['available_from'] = Yii::t('yii', '(not set)');
                } else {
                    $data['params']['available_from'] = date('Y-m-d', $model->available_from);
                }
                continue;
            }

            if ($model instanceof CarTransporterCity) {
                $data['params']['loadLocation'] = City::getNameById($model->loadLocation);
                $data['params']['unloadLocation'] = City::getNameById($model->unloadLocation);
                continue;
            }
        }

        return Json::encode($data);
    }
}