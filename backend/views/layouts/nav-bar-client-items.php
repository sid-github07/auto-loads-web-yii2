<?php

use yii\bootstrap\Nav;
use app\models\Seo;

$controls = [
    'label' => Yii::t('element', 'controls'),
    'items' => [
        [
            'label' => Yii::t('element', 'A-M-0'),
            'url' => ['/admin/index', 'lang' => Yii::$app->language]
        ],
        [
            'label' => Yii::t('element', 'Services'),
            'url' => ['/service/index', 'lang' => Yii::$app->language]
        ],
        [
            'label' => Yii::t('element', 'credit_services'),
            'url' => ['/credit-service/index', 'lang' => Yii::$app->language]
        ],
        [
            'label' => Yii::t('element', 'announcements'),
            'url' => ['/announcement/index', 'lang' => Yii::$app->language]
        ],
    ]
];

$clients = [
    'label' => Yii::t('element', 'A-M-1'),
    'url' => ['/client/index', 'lang' => Yii::$app->language],
];

$ads = [
    'label' => Yii::t('element', 'ads'),
    'items' => [
        [
            'label' => Yii::t('element', 'A-M-2'),
            'url' => ['/load/index', 'lang' => Yii::$app->language],
        ],
        [
            'options' => ['id' => 'C-T-94'],
            'label' => Yii::t('element', 'C-T-94'),
            'url' => ['/car-transporter/index', 'lang' => Yii::$app->language],
        ],
    ]
];
$audits = [
    'options' => ['id' => 'audit-tab'],
    'label' => Yii::t('app', 'AUDIT_TAB'),
    'items' => [
        [
            'label' => Yii::t('app', 'SEARCHES'),
            'url' => ['/audit/index', 'lang' => Yii::$app->language],
        ],
        [
            'label' => Yii::t('app', 'OPEN_MAP_ACTIONS'),
            'url' => ['/audit/map', 'lang' => Yii::$app->language],
        ],
        [
            'label' => Yii::t('app', 'Credits_logs'),
            'url' => ['/audit/credits', 'lang' => Yii::$app->language],
        ],
    ]
];

$bills = [
    'label' => Yii::t('element', 'A-M-4'),
    'items' => [
        [
            'label' => Yii::t('element', 'A-C-364'),
            'url' => ['/bill/list', 'lang' => Yii::$app->language],
            'options' => ['class' => 'bill-list'],
        ],
        [
            'label' => Yii::t('element', 'A-C-366'),
            'url' => ['/bill/planned-income', 'lang' => Yii::$app->language],
        ],
    ],
];

$other = [
    'label' => Yii::t('element', 'Other'),
    'items' => [
        [
            'label' => Yii::t('element', 'A-M-5'),
            'url' => '#',
            'options' => ['class' => 'disabled'],
        ],
        [
            'label' => Yii::t('app', 'OLD_SYSTEM_VERSION'),
            'url' => Yii::$app->params['oldSystemUrl'],
            'linkOptions' => ['target' => '_blank'],
        ]
    ]
];

$seoItems = \common\models\Seo::find()->where(['domain' => 'en'])->select('page')->indexBy('route')->column();
asort($seoItems);
array_walk($seoItems, function (&$item, $route) use ($seoItems) {
    $item = [
        'label' => sprintf('%d. https://en.auto-loads.com/%s', (array_search($route, array_keys($seoItems)) + 1), $item),
        'url' => ['/seo/edit', 'lang' => Yii::$app->language, 'route' => $route]
    ];
});

$seo = [
    'label' => Yii::t('element', 'SEO'),
    'items' => array_values($seoItems)
];

$items = [];

if (Yii::$app->admin->identity->isAdmin()) {
    array_push($items, $controls, $clients, $ads, $audits);
}

array_push($items, $bills);

if (Yii::$app->admin->identity->isAdmin()) {
    array_push($items, $other, $seo);
}

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-left'],
    'items' => $items,
]);