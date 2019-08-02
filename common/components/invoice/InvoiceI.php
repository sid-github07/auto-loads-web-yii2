<?php

namespace common\components\invoice;

/**
 * Interface InvoiceI
 *
 * @package common\components\invoice
 */
interface InvoiceI
{
    /**
     * Sets number
     */
    public function setNumber();

    /**
     * Sets path
     */
    public function setPath();

    /**
     * Sets file name
     */
    public function setFileName();

    /**
     * Sets file extension
     */
    public function setFileExtension();

    /**
     * Sets invoice type
     */
    public function setType();
}