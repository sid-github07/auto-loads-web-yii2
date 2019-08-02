<?php

namespace common\components\Payment;

/**
 * Interface PaymentI
 *
 * @package common\components\Payment
 */
interface PaymentI
{
    /**
     * Sets user service ID and service price with response
     *
     * @param null|integer $id User service ID
     * @param null|double $price Service price
     */
    public function setDefaultAttributes($id, $price);

    /**
     * Renders payment pay page
     */
    public function pay();

    /**
     * Validates, whether response from payment is valid
     *
     * @return boolean
     */
    public function validate();
}