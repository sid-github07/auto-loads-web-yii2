<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gridview' => ['class' => 'kartik\grid\Module'],
    ],
    'language' => 'lt',
    'timeZone' => 'Europe/Vilnius',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\Admin',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendAdmin',
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['potential-hauliers'],
                    'logFile' => '@app/runtime/logs/potential-hauliers.log',
                    'logVars' => [],
                ],
            ],
        ],
		
		'geoip' => [
            'class' => 'dpodium\yii2\geoip\components\CGeoIP',
            'mode' => 'STANDARD',  // Choose MEMORY_CACHE or STANDARD mode
        ],
		
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                'alert*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'country*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'document*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'element*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'log*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'mail*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'seo*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'text*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                ],
                'yii*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en',
                    'forceTranslation' => true,
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r = routes
            'enablePrettyUrl' => true,
            'rules' => [
            // Lithuanian
                
                //SiteController
                '<lang:(lt)>/valdymo-skydas' => 'site/index',
                'admin' => 'site/login',
                '<lang:(lt)>/prisijungimas' => 'site/login',
                '<lang:(lt)>/nustatyti-laiko-zona' => 'site/set-timezone-offset',
                '<lang:(lt)>/prisijungti-prie-vartotojo/<id:\d+>' => 'site/login-to-user',
                '<lang:(lt)>/prisijungti-prie-vartotojo' => 'site/login-to-user',

                // AdminController
                '<lang:(lt)>/vartotojai' => 'admin/index',
                '<lang:(lt)>/profilio-redagavimas' => 'admin/edit-my-profile',
                '<lang:(lt)>/slaptazodzio-keitimas' => 'admin/change-my-password',
                '<lang:(lt)>/vartotojo-kurimas' => 'admin/add-new',
                '<lang:(lt)>/vartotojo-redagavimo-forma' => 'admin/render-edit-form',
                '<lang:(lt)>/vartotojo-redagavimas/<id:\d+>' => 'admin/edit',
                '<lang:(lt)>/vartotojo-redagavimas' => 'admin/edit',
                '<lang:(lt)>/vartotojo-slaptazodzio-keitimo-forma' => 'admin/render-change-password-form',
                '<lang:(lt)>/keisti-vartotojo-slaptazodi/<id:\d+>' => 'admin/change-password',
                '<lang:(lt)>/keisti-vartotojo-slaptazodi' => 'admin/change-password',
                '<lang:(lt)>/pasalinti-vartotoja' => 'admin/remove',

                //ClientController
                '<lang:(lt)>/uzsakovai' => 'client/index',
                '<lang:(lt)>/isplestine-paieska' => 'client/render-extended-search',
//                '<lang:(lt)>/imones-redagavimas/<id:\d+>/<tab:[0-9a-zA-Z\-\_]+>/<load-page:\d+>/<per-load-page:\d+>' => 'client/company',
//                '<lang:(lt)>/imones-redagavimas/<id:\d+>/<tab:[0-9a-zA-Z\-\_]+>' => 'client/company',
                '<lang:(lt)>/imones-redagavimas' => 'client/company',
                '<lang:(lt)>/prideti-dokumenta/<type:[A-Z]+>/<date:[0-9\-]+>/<companyId:\d+>' => 'client/add-document',
                '<lang:(lt)>/prideti-dokumenta/<type:[A-Z]+>' => 'client/add-document',
                '<lang:(lt)>/prideti-dokumenta/<type:[A-Z]+>/<companyId:\d+>' => 'client/add-document',
                '<lang:(lt)>/prideti-dokumenta/<type:[A-Z]+>/<companyId:\d+>/<selectedTab:\w+(-\w+)*>' => 'client/add-document',
                '<lang:(lt)>/prideti-dokumenta/<type:[A-Z]+>/<date:[0-9\-]+>/<companyId:\d+>/<selectedTab:\w+(-\w+)*>' => 'client/add-document',
                '<lang:(lt)>/parsisiusti-dokumenta/<type:[A-Z]+>/<companyId:\d+>' => 'client/download-document',
                '<lang:(lt)>/parsisiusti-dokumenta/<type:[A-Z]+>' => 'client/download-document',
                '<lang:(lt)>/imones-komentarai/<id:\d+>' => 'client/show-company-comments',
                '<lang:(lt)>/imones-komentarai' => 'client/show-company-comments',
                '<lang:(lt)>/istrinti-komentara/<id:\d+>' => 'client/remove-company-comment',
                '<lang:(lt)>/istrinti-komentara' => 'client/remove-company-comment',
                '<lang:(lt)>/pakeisti-privilegijos-galiojimo-data' => 'client/change-subscription-end-date',
                '<lang:(lt)>/pakeisti-privilegijos-aktyvuma' => 'client/change-subscription-activity',

                // LoadController
                '<lang:(lt)>/kroviniai' => 'load/index',
                '<lang:(lt)>/krovinio-perziura/<id:\d+>' => 'load/show-load',
                '<lang:(lt)>/krovinio-perziura' => 'load/show-load',
                '<lang:(lt)>/krovinio-informacija/<id:\d+>' => 'load/load-preview',
                '<lang:(lt)>/krovinio-informacija' => 'load/load-preview',
                '<lang:(lt)>/perziuros/<id:\d+>' => 'load/previews',
                '<lang:(lt)>/perziuros' => 'load/previews',
                '<lang:(lt)>/istrinti-krovini/<id:\d+>' => 'load/remove-load',
                '<lang:(lt)>/istrinti-krovini' => 'load/remove-load',

                // CarTransporter
                '<lang:(lt)>/autoveziai' => 'car-transporter/index',
                '<lang:(lt)>/autovezio-perziura/<id:\d+>' => 'car-transporter/show-load',

                // CityController
                '<lang:(lt)>/paprasta-miesto-paieska' => 'city/simple-search',

                // BillController
                '<lang:(lt)>/saskaitu-sarasas' => 'bill/list',
                '<lang:(lt)>/parsisiusti-saskaita/<id:\d+>/<preview:\d+>' => 'bill/download',
                '<lang:(lt)>/parsisiusti-saskaita/<id:\d+>' => 'bill/download',
                '<lang:(lt)>/parsisiusti-saskaita' => 'bill/download',
                '<lang:(lt)>/sugeneruoti-is-naujo/<id:\d+>' => 'bill/regenerate',
                '<lang:(lt)>/sugeneruoti-is-naujo' => 'bill/regenerate',
                '<lang:(lt)>/pazymeti-kaip-apmoketa/<id:\d+>' => 'bill/mark-as-paid',
                '<lang:(lt)>/pazymeti-kaip-apmoketa' => 'bill/mark-as-paid',
                '<lang:(lt)>/planuojamos-pajamos' => 'bill/planned-income',

            // Common
                
                '<controller:\w+>/<action:[\w\-]+>/lang/<lang:\w+(-\w+)*>' => '<controller>/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                
            ],
        ],
        'urlManagerFrontend' => [
            'baseUrl' => $params['devEnvironment'] ? 'http://dev.auto-loads.com' : 'http://auto-loads.lt', // FIXME
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r = routes
            'enablePrettyUrl' => true,
            'rules' => [
                // SiteController

                '<lang:(lt)>/administratoriaus-prisijungimas/<token:[0-9a-zA-Z\-\_]+>' => 'site/login-for-admin',
                '<lang:(lt)>/administratoriaus-prisijungimas' => 'site/login-for-admin',

                // Common

                '<controller:\w+>/<action:[\w\-]+>/lang/<lang:\w+(-\w+)*>' => '<controller>/<action>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        /* TODO: pakeisti mailer prisijungimus */
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'e-smtp.itpc.lt',
                'localDomain' => 'aidasPC',
                'username' => 'no-reply@itpc.lt',
                'password' => 'toq9mSCWmRn',
                'port' => '465',
                'encryption' => 'ssl',
                // https://stackoverflow.com/a/38026352/5747867
				'streamOptions' => [ 
					'ssl' => [
						'allow_self_signed' => true,
						'verify_peer' => false,
						'verify_peer_name' => false,
					],
				],
            ],
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '195.181.244.35:9200'],
            ],
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => sys_get_temp_dir(),
        ],
        'request' => [
            'cookieValidationKey' => '6pjIMH6yL5kCW6TULOzIaw9cXPjQJvCu',
            'csrfParam' => '_backendCSRF',
        ],
        'datetime' => [
            'class' => 'common\components\DateTime',
        ],
    ],
    'as beforeRequest' => [
        'class' => 'common\components\Languages',
    ],
    'params' => $params,
];
