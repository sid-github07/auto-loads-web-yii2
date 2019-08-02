<?php

namespace backend\controllers\migration;

/**
 * Trait MigrationTrait
 *
 * This trait contains common methods that shares migration controllers
 *
 * @package backend\controllers\migration
 */
trait MigrationTrait
{
    /**
     * Checks whether old system user had set raw password
     *
     * In old system some users passwords were saved in raw format (not encrypted).
     * Therefore, this method checks whether that password is available.
     *
     * @param string $password Old system user raw password
     * @return boolean
     */
    public function hadOldPassword($password)
    {
        return !empty($password);
    }
}