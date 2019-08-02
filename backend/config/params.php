<?php
return [
    'adminEmail' => 'admin@auto-loads.com',
    'companyName' => 'Auto-loads',
    'titleEnding' => ' - Auto-loads',
    'VATService' => 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
    'CMRPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                 'documents' . DIRECTORY_SEPARATOR .
                 'cmr' . DIRECTORY_SEPARATOR,
    'CMR' => 'CMR',
    'CMROriginal' => 'CMR_original',
    'EUPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                'documents' . DIRECTORY_SEPARATOR .
                'eu' . DIRECTORY_SEPARATOR,
    'EU' => 'EU',
    'EUOriginal' => 'EU_original',
    'IMPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                'documents' . DIRECTORY_SEPARATOR .
                'im' . DIRECTORY_SEPARATOR,
    'IM' => 'IM',
    'IMOriginal' => 'IM_original',
    'watermarkPath' => Yii::getAlias('@frontend') . DIRECTORY_SEPARATOR .
                       'web' . DIRECTORY_SEPARATOR .
                       'images' . DIRECTORY_SEPARATOR .
                       'watermark.png',
    'FPDFPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                  '..' . DIRECTORY_SEPARATOR .
                  'vendor' . DIRECTORY_SEPARATOR .
                  'binarystash' . DIRECTORY_SEPARATOR .
                  'pdf-watermarker' . DIRECTORY_SEPARATOR .
                  'fpdf' . DIRECTORY_SEPARATOR .
                  'fpdf.php',
    'FPDIPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                  '..' . DIRECTORY_SEPARATOR .
                  'vendor' . DIRECTORY_SEPARATOR .
                  'binarystash' . DIRECTORY_SEPARATOR .
                  'pdf-watermarker' . DIRECTORY_SEPARATOR .
                  'fpdi' . DIRECTORY_SEPARATOR .
                  'fpdi.php',
    'PDFWatermarkPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                          '..' . DIRECTORY_SEPARATOR .
                          'vendor' . DIRECTORY_SEPARATOR .
                          'binarystash' . DIRECTORY_SEPARATOR .
                          'pdf-watermarker' . DIRECTORY_SEPARATOR .
                          'pdfwatermarker' . DIRECTORY_SEPARATOR .
                          'pdfwatermark.php',
    'PDFWaterMarkerPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                            '..' . DIRECTORY_SEPARATOR .
                            'vendor' . DIRECTORY_SEPARATOR .
                            'binarystash' . DIRECTORY_SEPARATOR .
                            'pdf-watermarker' . DIRECTORY_SEPARATOR .
                            'pdfwatermarker' . DIRECTORY_SEPARATOR .
                            'pdfwatermarker.php',
    'preInvoicePath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                        'documents' . DIRECTORY_SEPARATOR .
                        'pre-invoice' . DIRECTORY_SEPARATOR,
    'invoicePath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                     'documents' . DIRECTORY_SEPARATOR .
                     'invoice' . DIRECTORY_SEPARATOR,
    'preInvoiceNumber' => 'ALI',
    'invoiceNumber' => 'AL',
    'sellerCompanyName' => 'MB "auto-loads"',
    'sellerCompanyCode' => '302565898',
    'sellerVatCode' => 'LT100008002619',
    'sellerAddress' => 'Zanavykų g. 46A, Kaunas, Lietuva',
    'sellerBankName' => 'AB SEB BANKAS',
    'sellerBankCode' => '70440',
    'sellerSwift' => 'CBVI LT 2X',
    'sellerBankAccount' => 'LT 55 7044 0600 0766 1720',
    'serviceDiscount' => '0.00',
    'daysToPayPreInvoice' => 14,
    'invoicedByPosition' => 'Direktorius',
    'invoicedByNameSurname' => 'S. Niedvaras',
    'preInvoiceFileName' => 'pre-invoice_',
    'preInvoiceFileExtension' => 'pdf',
    'invoiceFileName' => 'invoice_',
    'invoiceFileExtension' => 'pdf',
    'oldSystemUrl' => 'https://b70730.auto-loads.lt/',
    'frontendHost' => 'http://lt.auto-loads.com',
    'xmlInvoices' => [
        'sellerAddress' => 'Zanavykų g. 46A',
        'sellerCity' => 'Kaunas',
        'sellerEmail' => 'admin@auto-loads.com',
        'sellerContractId' => 'OODFYH-00000',
        'sellerRegNumber' => '302565898',
    ],
];
