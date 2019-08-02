<?php

namespace common\components\document;

use common\components\Model;
use common\models\Company;
use common\models\CompanyDocument;
use Yii;

/**
 * Class DocumentIM
 *
 * @package common\components\document
 */
class DocumentIM extends Document implements DocumentI
{
    /** @var null|Company Current user company */
    private $company;

    /** @var string Document scenario for upload */
    private $scenario;

    /** @var string Document upload input name */
    private $inputName;

    /** @var string Path to common IM documents folder/catalog */
    private $directory;

    /** @var string Name for IM document */
    private $name;

    /** @var string Name for original IM document */
    private $originalName;

    /** @var string Model attribute name that has document file */
    private $fileAttribute;

    /** @var string Model attribute name that has document date */
    private $dateAttribute;

    /**
     * DocumentIM constructor
     */
    public function __construct($companyId = null)
    {
        $this->setCompany($companyId);
        $this->setScenario();
        $this->setInputName();
        $this->setDirectory();
        $this->setName();
        $this->setOriginalName();
        $this->setFileAttribute();
        $this->setDateAttribute();
    }

    /**
     * @inheritdoc
     */
    public function setCompany($companyId = null)
    {
        $this->company = Company::getCompany($companyId);
    }

    /**
     * @inheritdoc
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @inheritdoc
     */
    public function setScenario()
    {
        $this->scenario = CompanyDocument::SCENARIO_CLIENT_IM;
    }

    /**
     * @inheritdoc
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * @inheritdoc
     */
    public function setInputName()
    {
        $this->inputName = 'CompanyDocument[im]';
        if (property_exists(new CompanyDocument(), 'im')) {
            $class = Model::getClassName(new CompanyDocument());
            $this->inputName = $class . '[im]';
        }
    }

    /**
     * @inheritdoc
     */
    public function getInputName()
    {
        return $this->inputName;
    }

    /**
     * @inheritdoc
     */
    public function setDirectory()
    {
        $this->directory = Yii::$app->params['IMPath'];
    }

    /**
     * @inheritdoc
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @inheritdoc
     */
    public function setName()
    {
        $this->name = Yii::$app->params['IM'];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setOriginalName()
    {
        $this->originalName = Yii::$app->params['IMOriginal'];
    }

    /**
     * @inheritdoc
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @inheritdoc
     */
    public function setFileAttribute()
    {
        $this->fileAttribute = 'im';
    }

    /**
     * @inheritdoc
     */
    public function getFileAttribute()
    {
        return $this->fileAttribute;
    }

    /**
     * @inheritdoc
     */
    public function setDateAttribute()
    {
        $this->dateAttribute = 'dateIM';
    }

    /**
     * @inheritdoc
     */
    public function getDateAttribute()
    {
        return $this->dateAttribute;
    }
}