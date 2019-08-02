<?php

namespace common\components\audit;

use yii\helpers\Json;

/**
 * Class Read
 *
 * @package common\components\audit
 */
class Read extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'READ';

    /** @const string Log message placeholder when user reviews load information */
    const PLACEHOLDER_USER_REVIEWED_LOAD_INFO = 'USER_REVIEWED_LOAD_INFO';

    /** @const string Log message placeholder when user reviews car transporter information */
    const PLACEHOLDER_USER_REVIEWED_CAR_TRANSPORTER_INFO = 'USER_REVIEWED_CAR_TRANSPORTER_INFO';

    const PLACEHOLDER_USER_PREVIEWED_TRANSPORTER_VIEW_DATA = 'USER_PREVIEWED_TRANSPORTER_VIEW_DATA';
    const PLACEHOLDER_USER_PREVIEWED_LOAD_VIEW_DATA = 'USER_PREVIEWED_LOAD_VIEW_DATA';

    /**
     * Read constructor.
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
            case self::PLACEHOLDER_USER_REVIEWED_LOAD_INFO:
            case self::PLACEHOLDER_USER_REVIEWED_CAR_TRANSPORTER_INFO:
                parent::setData($this->getPreviewData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user reviews load or car transporter information
     *
     * @return string
     */
    private function getPreviewData()
    {
        $data = [];
        foreach ($this->getModels() as $model) {
            $data = [
                't' => 'log',
                'message' => $this->getPlaceholder(),
                'params' => [
                    'id' => $model->id,
                ],
            ];
        }

        return Json::encode($data);
    }
}