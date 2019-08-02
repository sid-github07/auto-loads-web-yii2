<?php

namespace common\components\audit;

use yii\helpers\Json;

/**
 * Class Create
 *
 * @package common\components\audit
 */
class Create extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'CREATE';

    /** @const string Log message placeholder when user signs-up to system */
    const PLACEHOLDER_USER_SIGNED_UP = 'USER_SIGNED_UP';

    /** @const string Log message placeholder when user registers new company */
    const PLACEHOLDER_USER_REGISTERED_COMPANY = 'USER_REGISTERED_COMPANY';

    /** @const string Log message placeholder when user uploads company document */
    const PLACEHOLDER_USER_UPLOADED_DOCUMENT = 'USER_UPLOADED_DOCUMENT';

    /** @const string Log message placeholder when user invites other user to join the company */
    const PLACEHOLDER_USER_INVITES_TO_JOIN_COMPANY = 'USER_INVITES_TO_JOIN_COMPANY';

    /** @const string Log message placeholder when user joins to the company */
    const PLACEHOLDER_USER_JOINS_TO_COMPANY = 'USER_JOINS_TO_COMPANY';

    /** @const string Log message placeholder when user announces new load */
    const PLACEHOLDER_USER_ANNOUNCED_LOAD = 'USER_ANNOUNCED_LOAD';

    /** @const string Log message placeholder when user buys subscription */
    const PLACEHOLDER_USER_BOUGHT_SUBSCRIPTION = 'USER_BOUGHT_SUBSCRIPTION';

    /** @const string Log message placeholder when guest buys creditcocde */
    const PLACEHOLDER_GUEST_BOUGHT_CREDITCODE = 'GUEST_BOUGHT_CREDITCODE';

    /** @const string Log message placeholder when user announces new car transporter */
    const PLACEHOLDER_USER_ANNOUNCED_CAR_TRANSPORTER = 'USER_ANNOUNCED_CAR_TRANSPORTER';

    const PLACEHOLDER_USER_ADVERTISED_LOAD = 'USER_ADVERTISED_LOAD';
    const PLACEHOLDER_USER_ADVERTISED_TRANSPORTER = 'USER_ADVERTISED_TRANSPORTER';
    const PLACEHOLDER_USER_BOUGHT_VIEW_LOAD_PREVIEW_SERVICE = 'VIEW_LOAD_PREVIEW_DATA_SERVICE_PURCHASE';
    const PLACEHOLDER_USER_BOUGHT_VIEW_TRANSPORTER_PREVIEW_SERVICE = 'VIEW_TRANSPORTER_PREVIEW_DATA_SERVICE_PURCHASE';
    const PLACEHOLDER_USER_SET_LOAD_OPEN_CONTACTS = 'USER_SET_LOAD_OPEN_CONTACTS';
    const PLACEHOLDER_USER_SET_TRANSPORTER_OPEN_CONTACTS = 'USER_SET_TRANSPORTER_OPEN_CONTACTS';

    /**
     * Create constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param array $models User action models
     * @param null|integer $userId User ID, who performs the action
     */
    public function __construct($placeholder, $models, $userId)
    {
        parent::__construct(self::ACTION, $placeholder, $models, $userId);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        switch ($this->getPlaceholder()) {
            case self::PLACEHOLDER_USER_UPLOADED_DOCUMENT:
                parent::setData($this->getUserCompanyDocumentData());
                break;
            case self::PLACEHOLDER_USER_INVITES_TO_JOIN_COMPANY:
                parent::setData($this->getUserInvitationData());
                break;
            case self::PLACEHOLDER_USER_SIGNED_UP:
            case self::PLACEHOLDER_USER_REGISTERED_COMPANY:
            case self::PLACEHOLDER_USER_JOINS_TO_COMPANY:
            case self::PLACEHOLDER_USER_ANNOUNCED_LOAD:
            case self::PLACEHOLDER_USER_ANNOUNCED_CAR_TRANSPORTER:
            case self::PLACEHOLDER_USER_SET_LOAD_OPEN_CONTACTS:
            case self::PLACEHOLDER_USER_SET_TRANSPORTER_OPEN_CONTACTS:
                parent::setData($this->getUserActionData());
                break;
            case self::PLACEHOLDER_USER_BOUGHT_SUBSCRIPTION:
                parent::setData($this->getUserSubscriptionData());
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

    /**
     * Returns formatted user action data when user invites other user to join to the company
     *
     * @return string
     */
    private function getUserInvitationData()
    {
        $data = [];
        foreach ($this->getModels() as $model) {
            $data = [
                't' => 'log',
                'message' => $this->getPlaceholder(),
                'params' => [
                    'id' => $model->id,
                    'company_id' => $model->company_id,
                    'email' => $model->email,
                ],
            ];
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user uploads company document
     *
     * @return string
     */
    private function getUserCompanyDocumentData()
    {
        $data = [];
        foreach ($this->getModels() as $model) {
            $data = [
                't' => 'log',
                'message' => $this->getPlaceholder(),
                'params' => [
                    'company_id' => $model->company_id,
                    'type' => $model->type,
                ],
            ];
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user buys subscription
     *
     * @return string
     */
    private function getUserSubscriptionData()
    {
        $data = [];
        foreach ($this->getModels() as $model) {
            $data = [
                't' => 'log',
                'message' => $this->getPlaceholder(),
                'params' => [
                    'name' => $model->name,
                ],
            ];
        }

        return Json::encode($data);
    }
}