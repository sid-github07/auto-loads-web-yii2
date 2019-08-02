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
    'preInvoiceNumber' => 'ALI',
    'preInvoicePath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                        'documents' . DIRECTORY_SEPARATOR .
                        'pre-invoice' . DIRECTORY_SEPARATOR,
    'preInvoiceFileName' => 'pre-invoice_',
    'preInvoiceFileExtension' => 'pdf',
    'invoiceNumber' => 'AL',
    'invoicePath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                     'documents' . DIRECTORY_SEPARATOR .
                     'invoice' . DIRECTORY_SEPARATOR,
    'invoiceFileName' => 'invoice_',
    'invoiceFileExtension' => 'pdf',
    'defaultCurrency' => 'EUR',
    // Production PayPal settings
    'paypalIdentifier' => 'admin@auto-loads.com',
    'paypalTestMode' => false,
    'paypalParser' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                      'components' . DIRECTORY_SEPARATOR .
                      'Rbs' . DIRECTORY_SEPARATOR .
                      'Payment' . DIRECTORY_SEPARATOR .
                      'Template' . DIRECTORY_SEPARATOR .
                      'Parser.php',
    'paypalLoaderInterface' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                               'components' . DIRECTORY_SEPARATOR .
                               'Rbs' . DIRECTORY_SEPARATOR .
                               'Payment' . DIRECTORY_SEPARATOR .
                               'Template' . DIRECTORY_SEPARATOR .
                               'LoaderInterface.php',
    'paypalFilesystem' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                    'components' . DIRECTORY_SEPARATOR .
                    'Rbs' . DIRECTORY_SEPARATOR .
                    'Payment' . DIRECTORY_SEPARATOR .
                    'Template' . DIRECTORY_SEPARATOR .
                    'Loader' . DIRECTORY_SEPARATOR .
                    'Filesystem.php',
    'paypalFields' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                      'components' . DIRECTORY_SEPARATOR .
                      'Rbs' . DIRECTORY_SEPARATOR .
                      'Payment' . DIRECTORY_SEPARATOR .
                      'Fields.php',
    'paypalAbstractGateway' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                               'components' . DIRECTORY_SEPARATOR .
                               'Rbs' . DIRECTORY_SEPARATOR .
                               'Payment' . DIRECTORY_SEPARATOR .
                               'Gateway' . DIRECTORY_SEPARATOR .
                               'AbstractGateway.php',
    'paypalPaypal' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                      'components' . DIRECTORY_SEPARATOR .
                      'Rbs' . DIRECTORY_SEPARATOR .
                      'Payment' . DIRECTORY_SEPARATOR .
                      'Gateway' . DIRECTORY_SEPARATOR .
                      'Paypal.php',
    'paypalClient' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                      'components' . DIRECTORY_SEPARATOR .
                      'Rbs' . DIRECTORY_SEPARATOR .
                      'Payment' . DIRECTORY_SEPARATOR .
                      'Http' . DIRECTORY_SEPARATOR .
                      'Client.php',
    'paypalFactory' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                       'components' . DIRECTORY_SEPARATOR .
                       'Rbs' . DIRECTORY_SEPARATOR .
                       'Payment' . DIRECTORY_SEPARATOR .
                       'Factory.php',
    'webToPatPath' => Yii::getAlias('@common') . DIRECTORY_SEPARATOR .
                      'components' . DIRECTORY_SEPARATOR .
                      'libwebtopay' . DIRECTORY_SEPARATOR .
                      'WebToPay.php',
    'paySeraProjectId' => 7922,
    'paySeraSignPassword' => 'c2c31114743da09d2075f076a5780421',
    'paySeraTestPayment' => 0,

    // Google Maps API key
    'GOOGLE_API_KEY' => 'AIzaSyDvGQSmRBmC0accLZeBbpA0gLFuy5ASIR8',
    'googleMapsApiKey' => 'AIzaSyDvGQSmRBmC0accLZeBbpA0gLFuy5ASIR8',
    'gmapsZoom' => 5,
    'gmapsEuropeCenter' => [
        52.519325, 13.392709
    ],
];
