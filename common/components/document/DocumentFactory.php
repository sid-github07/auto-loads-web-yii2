<?php

namespace common\components\document;

/**
 * Class DocumentFactory
 *
 * @package common\components\document
 */
class DocumentFactory
{
    /** @const string Document type is CMR */
    const CMR = 'CMR';

    /** @const string Document type is EU */
    const EU = 'EU';

    /** @const string Document type is IM */
    const IM = 'IM';

    /**
     * Creates and returns specific document object by give document type
     *
     * @param string $type Document type
     * @return DocumentCMR|DocumentEU|DocumentIM|null
     */
    public static function create($type = '', $companyId = null)
    {
        switch ($type) {
            case self::CMR:
                return new DocumentCMR($companyId);
            case self::EU:
                return new DocumentEU($companyId);
            case self::IM:
                return new DocumentIM($companyId);
            default:
                return null;
        }
    }
}