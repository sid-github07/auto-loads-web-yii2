<?php

namespace common\components\document;

use common\models\CompanyDocument;
use Exception;
use PDFWatermark;
use PDFWatermarker;
use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
// NOTE: PDF widget does not have namespace, that's why "require" is used
require (Yii::$app->params['FPDFPath']);
require (Yii::$app->params['FPDIPath']);
require (Yii::$app->params['PDFWatermarkPath']);
require (Yii::$app->params['PDFWaterMarkerPath']);

/**
 * Class Document
 *
 * @package common\components\document
 */
class Document
{
    /**
     * Uploads document
     *
     * @param string $date Document date of expiry
     * @return CompanyDocument|string CompanyDocument model or error message
     */
    public function upload($date)
    {
        /** @var DocumentCMR|DocumentEU|DocumentIM $this */
        $fileAttribute = $this->getFileAttribute();
        $dateAttribute = $this->getDateAttribute();
        $document = new CompanyDocument(['scenario' => $this->getScenario()]);
        $document->$fileAttribute = UploadedFile::getInstanceByName($this->getInputName());
        $document->$dateAttribute = $date;
        if (!$document->validate()) {
            return $document; // NOTE: document file and date MUST be valid before saving to catalog and database
        }

        $directory = $this->getDirectory() . $this->getCompany()->id . DIRECTORY_SEPARATOR;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true); // FIXME
        }

        $path = $directory . $this->getOriginalName() . '.' . $document->$fileAttribute->extension;
        if ($document->$fileAttribute->saveAs($path)) {
            return $document;
        }
        return Yii::t('alert', 'DOCUMENT_UPLOAD_CANNOT_SAVE');
    }

    /**
     * Returns document extension by provided document
     *
     * @param CompanyDocument $document Company document model
     * @return string
     */
    public function getExtension(CompanyDocument $document)
    {
        /** @var DocumentCMR|DocumentEU|DocumentIM $this */
        $fileAttribute = $this->getFileAttribute();
        return $document->$fileAttribute->extension;
    }

    /**
     * Adds watermark in document
     *
     * @param CompanyDocument $document Current company document model
     */
    public function addWatermark(CompanyDocument $document)
    {
        /** @var DocumentCMR|DocumentEU|DocumentIM $this */
        $directory = $this->getDirectory() . $this->getCompany()->id . DIRECTORY_SEPARATOR;
        $input = $directory . $this->getOriginalName() . '.' . $this->getExtension($document);
        $output = $directory . $this->getName() . '.' . $this->getExtension($document);
        $watermark = new PDFWatermark(Yii::$app->params['watermarkPath']);
        $waterMarker = new PDFWatermarker($input, $output, $watermark);
        $waterMarker->watermarkPdf();
    }

    /**
     * Returns full path to document
     *
     * @param CompanyDocument $document Current company document model
     * @return string
     */
    public function getFullPath(CompanyDocument $document)
    {
        /** @var DocumentCMR|DocumentEU|DocumentIM $this */
        $directory = $this->getDirectory() . $this->getCompany()->id . DIRECTORY_SEPARATOR;
        return $directory . $this->getName() . '.' . $document->extension;
    }

    /**
     * Removes document directory
     *
     * @return boolean Whether document directory was removed successfully
     */
    public function remove()
    {
        /** @var DocumentCMR|DocumentEU|DocumentIM $this */
        $directory = $this->getDirectory() . $this->getCompany()->id . DIRECTORY_SEPARATOR;
        try {
            FileHelper::removeDirectory($directory);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}