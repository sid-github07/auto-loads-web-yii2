<?php

namespace common\components\audit;

use yii\helpers\Json;
use Yii;

/**
 * Class Pay
 *
 * @package common\components\audit
 */
class Pay extends ActionAbstractFactory implements ActionInterface
{
    /** @const string Action name */
    const ACTION = 'PAY';

    const MESSAGE = 'PAY FOR ...';
    const PAYFOR_LOAD_PROMO                 = 'PAYFOR_LOAD_PROMO';
    const PAYFOR_LOAD_PROMO_SUBSCR          = 'PAYFOR_LOAD_PROMO_SUBSCR';
    const PAYFOR_TRUCK_PROMO                = 'PAYFOR_TRUCK_PROMO';
    const PAYFOR_TRUCK_PROMO_SUBSCR         = 'PAYFOR_TRUCK_PROMO_SUBSCR';
    const PAYFOR_LOAD_OPEN_CONTACTS         = 'PAYFOR_LOAD_OPEN_CONTACTS';
    const PAYFOR_LOAD_OPEN_CONTACTS_SUBSCR  = 'PAYFOR_LOAD_OPEN_CONTACTS_SUBSCR';
    const PAYFOR_TRUCK_OPEN_CONTACTS        = 'PAYFOR_TRUCK_OPEN_CONTACTS';
    const PAYFOR_TRUCK_OPEN_CONTACTS_SUBSCR = 'PAYFOR_TRUCK_OPEN_CONTACTS_SUBSCR';

    const PAYFOR_PREVIEWS                   = 'PAYFOR_PREVIEWS';
    const PAYFOR_POTENTIAL_HAULERS          = 'PAYFOR_POTENTIAL_HAULERS';
    const PAYFOR_SIMILAR_SEARCHES           = 'PAYFOR_SIMILAR_SEARCHES';
    const PAYFOR_WHO_OFFERS                 = 'PAYFOR_WHO_OFFERS';
    const PAYFOR_TRUCK_CONTACTS             = 'PAYFOR_TRUCK_CONTACTS';
    const PAYFOR_PREVIEWS_SUBSCR            = 'PAYFOR_PREVIEWS_SUBSCR';
    const PAYFOR_POTENTIAL_HAULERS_SUBSCR   = 'PAYFOR_POTENTIAL_HAULERS_SUBSCR';
    const PAYFOR_SIMILAR_SEARCHES_SUBSCR    = 'PAYFOR_SIMILAR_SEARCHES_SUBSCR';
    const PAYFOR_WHO_OFFERS_SUBSCR          = 'PAYFOR_WHO_OFFERS_SUBSCR';
    const PAYFOR_TRUCK_CONTACTS_SUBSCR      = 'PAYFOR_TRUCK_CONTACTS_SUBSCR';

    public static function getSearchCreditFilter()
    {
        return [
            static::PAYFOR_LOAD_PROMO                 => Yii::t('text', 'Load promotion'),
            static::PAYFOR_LOAD_PROMO_SUBSCR          => Yii::t('text', 'Load promotion using subscription'),
            static::PAYFOR_TRUCK_PROMO                => Yii::t('text', 'Truck promotion'),
            static::PAYFOR_TRUCK_PROMO_SUBSCR         => Yii::t('text', 'Truck promotion using subscription'),
            static::PAYFOR_LOAD_OPEN_CONTACTS         => Yii::t('text', 'Load open contacts'),
            static::PAYFOR_LOAD_OPEN_CONTACTS_SUBSCR  => Yii::t('text', 'Load open contacts using subscription'),
            static::PAYFOR_TRUCK_OPEN_CONTACTS        => Yii::t('text', 'Truck open contacts'),
            static::PAYFOR_TRUCK_OPEN_CONTACTS_SUBSCR => Yii::t('text', 'Truck open contacts using subscription'),
            static::PAYFOR_PREVIEWS                   => Yii::t('text', 'Previews'),
            static::PAYFOR_PREVIEWS_SUBSCR            => Yii::t('text', 'Previews using subscription'),
            static::PAYFOR_POTENTIAL_HAULERS          => Yii::t('text', 'Potential haulers'),
            static::PAYFOR_POTENTIAL_HAULERS_SUBSCR   => Yii::t('text', 'Potential haulers using subscription'),
            static::PAYFOR_SIMILAR_SEARCHES           => Yii::t('text', 'Similar searches'),
            static::PAYFOR_SIMILAR_SEARCHES_SUBSCR    => Yii::t('text', 'Similar searches using subscription'),
            static::PAYFOR_WHO_OFFERS                 => Yii::t('text', 'Who offers'),
            static::PAYFOR_WHO_OFFERS_SUBSCR          => Yii::t('text', 'Who offers using subscription'),
            static::PAYFOR_TRUCK_CONTACTS             => Yii::t('text', 'Truck contacts'),
            static::PAYFOR_TRUCK_CONTACTS_SUBSCR      => Yii::t('text', 'Truck contacts using subscription'),
        ];
    }

    /**
     * Pay constructor.
     *
     * @param string $placeholder User action message placeholder
     * @param array|string $actionData User action data
     * @param null|integer $userId User ID, who performs the action
     */
    public function __construct($placeholder, $actionData, $userId)
    {
        // action parameter in constructor is placeholder (not self::ACTION) for direct search in table
        parent::__construct($placeholder, $placeholder, self::MESSAGE, $userId);
        $this->setData($actionData);
    }

    /**
     * @inheritdoc
     */
    public function action()
    {
        if (empty($this->getPlaceholder())) {
            parent::setData(0);
        }
        return $this;
    }

}
