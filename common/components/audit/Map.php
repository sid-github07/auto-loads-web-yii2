<?php

namespace common\components\audit;

use yii\helpers\Json;

/**
 * Class Create
 *
 * @package common\components\audit
 */
class Map extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'MAP';
    
    /** @const integer view type */
    const TYPE_LOADS = 1;
    
    /** @const integer view type */
    const TYPE_TRANSPORTERS = 2;

    /** @const string Log message placeholder when user opens map */
    const PLACEHOLDER_USER_OPENED_MAP = 'USER_OPENED_MAP';

    /** @var integer Map log in loads or transporters view type */
    private $type;
            
    /**
     * Map constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param integer $type log view type id load or transporter
     */
    public function __construct($placeholder, $type)
    {
        $this->type = $type;
        parent::__construct(self::ACTION, $placeholder, [], 0);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_OPENED_MAP:
                parent::setData($this->getUserActionData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data
     *
     * @return string
     */
    private function getUserActionData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [
                'id' => $this->getUserId(),
                'type' => $this->type,
            ],
        ];

        return Json::encode($data);
    }
}