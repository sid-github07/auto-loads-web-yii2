<?php

namespace common\components\audit;

use common\components\Model;
use common\models\Company;
use common\models\Load;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class Update
 *
 * @package common\components\audit
 */
class Update extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'UPDATE';

    /** @const string Log message placeholder when user updates company information */
    const PLACEHOLDER_USER_UPDATED_COMPANY_INFO = 'USER_UPDATED_COMPANY_INFO';

    /** @const string Log message placeholder when user updates company document */
    const PLACEHOLDER_USER_UPDATED_COMPANY_DOCUMENT = 'USER_UPDATED_COMPANY_DOCUMENT';

    /** @const string Log message placeholder when user updates profile information */
    const PLACEHOLDER_USER_UPDATED_PROFILE_INFO = 'USER_UPDATED_PROFILE_INFO';

    /** @const string Log message placeholder when user updates list of languages that speaks */
    const PLACEHOLDER_USER_UPDATED_LANGUAGES = 'USER_UPDATED_LANGUAGES';

    /** @const string Log message placeholder when user updates password */
    const PLACEHOLDER_USER_UPDATED_PASSWORD = 'USER_UPDATED_PASSWORD';

    /** @const string Log message placeholder when user updates load information */
    const PLACEHOLDER_USER_UPDATED_LOAD_INFO = 'USER_UPDATED_LOAD_INFO';

    /** @const string Log message placeholder when user updates load cars */
    const PLACEHOLDER_USER_UPDATED_LOAD_CARS = 'USER_UPDATED_LOAD_CARS';

    /** @const string Log message placeholder when user updates load active status */
    const PLACEHOLDER_USER_UPDATED_LOAD_ACTIVE_STATUS = 'USER_UPDATED_LOAD_ACTIVE_STATUS';

    /** @const string Log message placeholder when user updates multiple loads active status */
    const PLACEHOLDER_USER_UPDATED_MULTIPLE_LOADS_ACTIVE_STATUS = 'USER_UPDATED_MULTIPLE_LOADS_ACTIVE_STATUS';

    /** @const string Log message placeholder when user updates car transporter information */
    const PLACEHOLDER_USER_UPDATED_CAR_TRANSPORTER_INFO = 'USER_UPDATED_CAR_TRANSPORTER_INFO';

    /**
     * Update constructor.
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
            case self::PLACEHOLDER_USER_UPDATED_COMPANY_INFO:
                parent::setData($this->getCompanyData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_COMPANY_DOCUMENT:
                parent::setData($this->getCompanyDocumentData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_PROFILE_INFO:
                parent::setData($this->getUserData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_LANGUAGES:
                parent::setData($this->getUserLanguagesData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_PASSWORD:
                parent::setData($this->getUserPasswordData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_LOAD_INFO:
                parent::setData($this->getLoadData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_LOAD_CARS:
                parent::setData($this->getLoadCarData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_LOAD_ACTIVE_STATUS:
                parent::setData($this->getLoadData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_MULTIPLE_LOADS_ACTIVE_STATUS:
                parent::setData($this->getLoadsActiveStatusesData());
                break;
            case self::PLACEHOLDER_USER_UPDATED_CAR_TRANSPORTER_INFO:
                parent::setData($this->getLoadData());
                break;
            default:
                parent::setData(Json::encode([]));
                break;
        }

        return $this;
    }

    /**
     * Returns formatted user action data when user updates company information
     *
     * @return string
     */
    private function getCompanyData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        /** @var ActiveRecord $model */
        foreach ($this->getModels() as $model) {
            $data['params']['id'] = $model->id;
            $changes = Model::getAttributeChanges($model);
            foreach ($changes as $attribute => $oldValue) {
                $data['fields'][$attribute] = [
                    'label' => $model->getAttributeLabel($attribute),
                    'oldValue' => $oldValue,
                    'newValue' => $model->$attribute,
                ];
            }
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user updates company document
     *
     * @return string
     */
    private function getCompanyDocumentData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        foreach ($this->getModels() as $model) {
            $data['params'] = [
                'company_id' => $model->company_id,
                'type' => $model->type,
            ];
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user updates profile information
     *
     * @return string
     */
    private function getUserData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        /** @var User $model */
        foreach ($this->getModels() as $model) {
            $company = Company::findUserCompany($model->id);
            $data['params']['id'] = $company->id;
            $changes = Model::getAttributeChanges($model);
            foreach ($changes as $attribute => $oldValue) {
                $data['fields'][$attribute] = [
                    'label' => $model->getAttributeLabel($attribute),
                    'oldValue' => $oldValue,
                    'newValue' => $model->$attribute,
                ];
            }
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user updates languages that speaks
     *
     * @return string
     */
    private function getUserLanguagesData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user updates password
     *
     * @return string
     */
    private function getUserPasswordData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        return Json::encode($data);
    }

    /**
     * Returns formatted user action data when user updates load information
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

        /** @var Load $model */
        foreach ($this->getModels() as $model) {
            $data['params']['id'] = $model->id;
            $changes = Model::getAttributeChanges($model);
            foreach ($changes as $attribute => $oldValue) {
                $data['fields'][$attribute] = [
                    'label' => $model->getAttributeLabel($attribute),
                    'oldValue' => $this->getFormattedLoadAttribute($attribute, $oldValue),
                    'newValue' => $this->getFormattedLoadAttribute($attribute, $model->$attribute),
                ];
            }
        }

        return Json::encode($data);
    }

    /**
     * Returns formatted load attribute value depending on attribute
     *
     * @param string $attribute Load attribute name
     * @param mixed $value Load attribute value
     * @return mixed
     */
    private function getFormattedLoadAttribute($attribute, $value)
    {
        switch ($attribute) {
            case 'date':
                return $this->formatLoadDate($value);
            case 'payment_method':
                return $this->formatLoadPaymentMethod($value);
            case 'price':
                return $this->formatLoadPrice($value);
            case 'active':
                return $this->formatLoadActiveStatus($value);
            default:
                return Yii::t('yii', '(not set)');
        }
    }

    /**
     * Converts load date from timestamp to human readable date
     *
     * @param integer $value Load date
     * @return false|string
     */
    private function formatLoadDate($value)
    {
        return is_numeric($value) ? date('Y-m-d', $value) : Yii::t('yii', '(not set)');
    }

    /**
     * Translates load payment method value
     *
     * @param integer $value Load payment method
     * @return string
     */
    private function formatLoadPaymentMethod($value)
    {
        $paymentMethods = Load::getTranslatedPaymentMethods();
        if (array_key_exists($value, $paymentMethods)) {
            return $paymentMethods[$value];
        }

        return Yii::t('yii', '(not set)');
    }

    /**
     * Adds euro sign to load price
     *
     * @param double|integer $value Load price
     * @return string
     */
    private function formatLoadPrice($value)
    {
        return is_null($value) ? Yii::t('yii', '(not set)') : $value . ' €';
    }

    /**
     * Translates load active status value
     *
     * @param integer $value Load active status
     * @return string
     */
    private function formatLoadActiveStatus($value)
    {
        $activeStatuses = Load::getTranslatedActiveStatus();
        if (array_key_exists($value, $activeStatuses)) {
            return $activeStatuses[$value];
        }

        return Yii::t('yii', '(not set)');
    }

    /**
     * Returns formatted user action data when user updates load cars information
     *
     * @return string
     */
    private function getLoadCarData()
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
     * Returns formatted user action data when user updates multiple loads active statuses
     *
     * @return string
     */
    private function getLoadsActiveStatusesData()
    {
        $data = [
            't' => 'log',
            'message' => $this->getPlaceholder(),
            'params' => [],
        ];

        $activated = $this->formatLoadActiveStatus(Load::ACTIVATED);
        $notActivated = $this->formatLoadActiveStatus(Load::NOT_ACTIVATED);

        /** @var Load $model */
        foreach ($this->getModels() as $model) {
            $data['multiple'][$model->id] = [
                'id' => $model->id,
                'object' => Yii::t('app', 'LOAD'),
                'label' => $model->getAttributeLabel('active'),
                'oldValue' => $model->isActivated() ? $notActivated : $activated,
                'newValue' => $model->isActivated() ? $activated : $notActivated,
            ];
        }

        return Json::encode($data);
    }
}