<?php

namespace common\components\audit;

/**
 * Interface ActionInterface
 *
 * @package common\components\audit
 */
interface ActionInterface
{
    /**
     * Sets required user action data
     *
     * @return $this
     */
    public function action();
}