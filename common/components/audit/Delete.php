<?php

namespace common\components\audit;

use common\models\CarTransporter;
use common\models\Load;
use Yii;
use yii\helpers\Json;

/**
 * Class Delete
 *
 * @package common\components\audit
 */
class Delete extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'DELETE';

    /** @const string Log message placeholder when user removes load */
    const PLACEHOLDER_USER_REMOVED_LOAD = 'USER_REMOVED_LOAD';

    /** @const string Log message placeholder when user removes multiple loads */
    const PLACEHOLDER_USER_REMOVED_MULTIPLE_LOADS = 'USER_REMOVED_MULTIPLE_LOADS';

    /** @const string Log message placeholder when user removes multiple car transporters */
    const PLACEHOLDER_USER_REMOVED_MULTIPLE_CAR_TRANSPORTERS = 'USER_REMOVED_MULTIPLE_CAR_TRANSPORTERS';

    /**
     * Delete constructor.
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
            case self::PLACEHOLDER_USER_REMOVED_LOAD:
                parent::setData($this->getLoadData());
                break;
            case self::PLACEHOLDER_USER_REMOVED_MULTIPLE_LOADS:
                parent::setData($this->getMultipleLoadsData());
                break;
            case self::PLACEHOLDER_USER_REMOVED_MULTIPLE_CAR_TRANSPORTERS:
                parent::setData($this->getMultipleCarTransportersData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user removes load
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
            $data['params']['id'] = $model->id;
        }

        return Json::encode($data);
    }

    /**
     * Translates load status value
     *
     * @param $value
     * @return string
     */
    private function translateLoadStatus($value)
    {
        $statuses = Load::getTranslatedStatuses();
        if (array_key_exists($value, $statuses)) {
            return $statuses[$value];
        }

        return Yii::t('yii', '(not set)');
    }

    /**
     * Returns formatted user action data when user deletes multiple loads
     *
     * @return string
     */
    private function getMultipleLoadsData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        /** @var Load $model */
        foreach ($this->getModels() as $model) {
            $data['multiple'][$model->id] = [
                'id' => $model->id,
                'object' => Yii::t('app', 'LOAD'),
                'label' => $model->getAttributeLabel('status'),
                'oldValue' => $this->translateLoadStatus(Load::ACTIVE),
                'newValue' => $this->translateLoadStatus(Load::INACTIVE),
            ];
        }

        return Json::encode($data);
    }

    /**
     * Translates car transporter archivation status
     *
     * @param integer $value Car transporter archivation status
     * @return string
     */
    private function translateCarTransporterArchived($value)
    {
        $archivation = CarTransporter::getTranslatedArchivation();
        if (array_key_exists($value, $archivation)) {
            return $archivation[$value];
        }

        return Yii::t('yii', '(not set)');
    }

    /**
     * Returns formatted user action data when user removes multiple car transporters
     *
     * @return string
     */
    private function getMultipleCarTransportersData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        /** @var CarTransporter $model */
        foreach ($this->getModels() as $model) {
            $data['multiple'][$model->id] = [
                'id' => $model->id,
                'object' => Yii::t('app', 'CAR_TRANSPORTER'),
                'label' => $model->getAttributeLabel('archived'),
                'oldValue' => $this->translateCarTransporterArchived(CarTransporter::NOT_ARCHIVED),
                'newValue' => $this->translateCarTransporterArchived(CarTransporter::ARCHIVED),
            ];
        }

        return Json::encode($data);
    }
}