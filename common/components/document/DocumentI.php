<?php

namespace common\components\document;

/**
 * Interface DocumentI
 *
 * @package common\components\document
 */
interface DocumentI
{
    /**
     * Sets current user company
     *
     * @return mixed
     */
    public function setCompany();

    /**
     * Returns current user company
     *
     * @return mixed
     */
    public function getCompany();

    /**
     * Sets document scenario
     *
     * @return mixed
     */
    public function setScenario();

    /**
     * Returns document scenario
     *
     * @return mixed
     */
    public function getScenario();

    /**
     * Sets input name
     *
     * @return mixed
     */
    public function setInputName();

    /**
     * Returns input name
     *
     * @return mixed
     */
    public function getInputName();

    /**
     * Sets directory path
     *
     * @return mixed
     */
    public function setDirectory();

    /**
     * Returns directory path
     *
     * @return mixed
     */
    public function getDirectory();

    /**
     * Sets name
     *
     * @return mixed
     */
    public function setName();

    /**
     * Returns name
     *
     * @return mixed
     */
    public function getName();

    /**
     * Sets original name
     *
     * @return mixed
     */
    public function setOriginalName();

    /**
     * Returns original name
     *
     * @return mixed
     */
    public function getOriginalName();

    /**
     * Sets file attribute
     *
     * @return mixed
     */
    public function setFileAttribute();

    /**
     * Returns file attribute
     *
     * @return mixed
     */
    public function getFileAttribute();

    /**
     * Sets date attribute
     *
     * @return mixed
     */
    public function setDateAttribute();

    /**
     * Returns date attribute
     *
     * @return mixed
     */
    public function getDateAttribute();
}