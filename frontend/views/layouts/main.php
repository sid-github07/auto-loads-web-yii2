<?php

use cinghie\cookieconsent\widgets\CookieWidget;
use common\components\ControllerTrait;
use common\components\Languages;
use common\models\UserServiceActive;
use frontend\assets\AppAsset;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use common\components\ElasticSearch\Suggestions;
use common\components\helpers\Html as AutoLoadsHtml;
use common\models\User;

/**
 * @var View $this
 * @var string $content
 * @var array $languages
 * @var User|null $userIdentity
 * @var bool $isGuest
 */

//header("X-Frame-Options: deny");
//header("X-XSS-Protection: 1");
//header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com; img-src * blob: data:");
//header("X-Content-Type-Options: nosniff");

AppAsset::register($this);
Icon::map($this, Icon::FA); // FA - Font Awesome icons
Icon::map($this, Icon::FI); // FI - flag icons
$languages = Languages::getLanguages();
$shortCode = Yii::$app->language === 'en' ? 'us' : Yii::$app->language;
$language = \common\models\Language::find()->where('country_code LIKE "%' . $shortCode. '%"')->one();
$unseenSuggestionsCount = Yii::$app->getUser()->getIsGuest() ? 0 : count(Suggestions::findNotSeenUserSuggestions(Yii::$app->getUser()->id));
$isGuest = Yii::$app->getUser()->getIsGuest();
$userIdentity = $isGuest ? null : Yii::$app->getUser()->getIdentity();

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <title><?php echo Html::encode($this->title) . Yii::$app->params['titleEnding']; ?></title>
    <link rel="icon" href="<?php echo Yii::$app->request->baseUrl . '/images/favicon.ico?v=2'; ?>" type="image/x-icon">
    <script>
        var timezone = "<?php echo Yii::$app->session->has('timezone') ? Yii::$app->session->get('timezone') : null; ?>";
        var actionSetTimezone = "<?php echo Url::to(['site/set-timezone', 'lang' => Yii::$app->language]); ?>";
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                                                          j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5497XWR');</script>
    <!-- End Google Tag Manager -->

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyChxKg2LsVN-h4Uy3iHCXgpKA63vKYtlB8"></script>
    <script type="text/javascript">
        var actionDisableSubscriptionAlert = "<?php echo Url::to([
            'site/disable-subscription-alert',
            'lang' => Yii::$app->language,
        ]) ?>";
        var actionDisableAnnouncementAlert = "<?php echo Url::to([
            'site/disable-announcement-alert',
            'lang' => Yii::$app->language,
        ]) ?>";
    </script>
    <?php $this->head(); ?>
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5497XWR"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php $this->beginBody(); ?>

<?php $siteLanguageSelect = [
    'label' => $languages[Yii::$app->language],
    'options' => [
        'id' => 'HP-H-1',
        'class' => 'language-dropdown-menu menu-item'
    ],
    'items' => [],
] ?>

<?php foreach ($languages as $shortName => $name) {
    if ($shortName != Yii::$app->language) {
        $siteLanguageSelect['items'][] = [
            'label' => $name,
            'url' => [Yii::$app->requestedRoute, 'lang' => $shortName],
            'tabindex' => '-1',
            'options' => [
                'onclick' => 'clearSessionStorage()',
            ],
        ];
    }
} ?>

<?php
    $userMenu = [];
    $menu = [
    'label' => AutoLoadsHtml::getBadge(Yii::t('element', 'HP-H-1a'), $unseenSuggestionsCount),
    'options' => ['class' => 'language-dropdown-menu menu-item'],
    'items' => [
        //Services and accounts
        [
            'label' => Icon::show('briefcase', '', Icon::FA) . Yii::t('element', 'HP-H-5'),
            'url' => ['subscription/index', 'lang' => Yii::$app->language],
            'options' => ['id' => 'HP-H-5'],
        ],
        // My loads
        [
            'label' => Icon::show('file-text-o', '', Icon::FA) . Yii::t('element', 'SB-P-1'),
            'url' => ['/my-announcement/index', 'lang' => Yii::$app->language],
            'options' => ['id' => 'SB-P-1a'],
        ],
        //Load suggestions
        [
            'label' => AutoLoadsHtml::getBadge(Icon::show('car', '', Icon::FA) . Yii::t('element', 'SB-P-2'), $unseenSuggestionsCount, false),
            'url' => ['/load/suggestions', 'lang' => Yii::$app->language],
            'options' => ['id' => 'SB-P-2a'],
        ],
        //Settings
        [
            'label' => Icon::show('cog', '', Icon::FA) . Yii::t('element', 'HP-H-6'),
            'url' => ['settings/index', 'lang' => Yii::$app->language],
            'options' => ['id' => 'HP-H-6'],
        ],
        //Log Out
        [
            'label' => Icon::show('sign-out', '', Icon::FA) . Yii::t('element', 'HP-H-9'),
            'url' => ['site/logout', 'lang' => Yii::$app->language],
            'linkOptions' => [
                'data-method' => 'post'
            ],
            'options' => [
                'class' => 'menu-item'
            ]
        ],
    ],
];

// If the user have subscription, display
if ($userIdentity instanceof User && $userIdentity->hasSubscription() && $userIdentity->getSubscriptionEndTime() !== '') {
    array_unshift($menu['items'],
        [
            'label' => sprintf('%s %s', Yii::t('element', 'Subscription'), Html::tag('span', substr($userIdentity->getSubscriptionEndTime(), 0, 10), ['class' => 'highlight-orange', 'id' => 'subscription_end_date'])),
            'url' => ['subscription/index', 'lang' => Yii::$app->language],
        ]
    );
}

// If the user have service credits
if ($userIdentity instanceof User && $userIdentity->hasEnoughServiceCredits(1)) {
    $creditsMenu = [
        'label' => sprintf('%s %s', Yii::t('element', 'Credits'), Html::tag('span', $userIdentity->service_credits, ['class' => 'highlight-orange', 'id' => 'credits-amount'])),
        'url' => ['subscription/index', 'lang' => Yii::$app->language],
    ];
    $userMenu[] = $creditsMenu;
}

$userMenu[] = $menu;
$userMenu[] = $siteLanguageSelect;

?>

<?php $guestUserMenu = [
    //Login
    [
        'label' => Yii::t('element', 'HP-H-2'),
        'url' => ['/site/login', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-H-2'
        ]
    ],
    //Sign In
    [
        'label' => Yii::t('element', 'HP-H-3'),
        'url' => ['/site/sign-up', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-H-3'
        ]
    ],
    $siteLanguageSelect
]; ?>

<?php $services = [
    // Loads
    [
        'label' => Yii::t('element', 'L-T-1'),
        'url' => ['/load/loads', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'L-T-1',
            'class' => 'highlighted orange-text',
        ],
    ],
    // Car transporters
    [
        'label' => Yii::t('element', 'C-T-1'),
        'url' => ['car-transporter/index', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'C-T-1',
            'class' => 'blue-text',
        ],
    ],
    // Drivers
    [
        'label' => Yii::t('element', 'D-T-1'),
        'options' => [
            'id' => 'D-T-1',
            'class' => 'disabled'
        ],
    ],
    // Load Suggestions
    [
        'label' => Yii::t('element', 'SB-P-2'),
        'url' => ['/load-suggestions', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'SB-P-2'
        ],
    ]
]; ?>

<?php $footerLeftButtons = [
    //Roundtrip
    [
        'label' => Yii::t('element', 'HP-F-1'),
        'url' => ['/load/round-trips', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-F-1',
            'class' => 'highlighted'
        ]
    ],
]; ?>

<?php $pages = [
    //Imprint
    [
        'label' => Yii::t('element', 'HP-F-2'),
        'url' => ['/site/imprint', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-F-2',
        ]
    ],
    //Guidelines
    [
        'label' => Yii::t('element', 'HP-F-4'),
        'url' => ['/site/guidelines', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-F-4',
        ]
    ],
    //Advertisement
    [
        'label' => Yii::t('element', 'HP-F-5'),
        'url' => '#',
        'options' => [
            'id' => 'HP-F-5',
            'class' => 'disabled'
        ]
    ],
    //Help
    [
        'label' => Yii::t('element', 'HP-F-7'),
        'url' => ['/site/help', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'HP-F-7',
            'class' => 'highlighted'
        ]
    ]
]; ?>

<?php $guestMainActions = [
    //Search load
    [
        'label' => Icon::show('search', '', Icon::FA) . Yii::t('element', 'SB-N-2'),
        'url' => ['/load/search', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'SB-N-2a',
            'class' => 'main-action-item'
        ]
    ],
    // Search car transporter
    [
        'label' => Icon::show('search', '', Icon::FA) . Yii::t('element', 'C-T-65'),
        'url' => ['car-transporter-search/search-form', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'C-T-65',
            'class' => 'main-action-item',
        ],
    ],
    //Announce load
    [
        'label' => Icon::show('bullhorn', '', Icon::FA) . Yii::t('element', 'SB-N-3'),
        'url' => ['/load/announce', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'SB-N-3a',
            'class' => 'main-action-item'
        ]
    ],
    // Announce car transporter
    [
        'label' => Icon::show('bullhorn', '', Icon::FA) . Yii::t('element', 'C-T-24'),
        'url' => ['car-transporter-announcement/announcement-form', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'C-T-24',
            'class' => 'main-action-item',
        ],
    ],
]; ?>

<?php $loggedInMainActions = [
    //Announce load
    [
        'label' => Icon::show('bullhorn', '', Icon::FA) . Yii::t('element', 'SB-P-4'),
        'url' => ['/load/announce', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'SB-P-4a',
            'class' => 'main-action-item'
        ]
    ],
    //Search load
    [
        'label' => Icon::show('search', '', Icon::FA) . Yii::t('element', 'SB-P-3'),
        'url' => ['/load/search', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'SB-P-3a',
            'class' => 'main-action-item'
        ]
    ],
    // Announce car transporter
    [
        'label' => Icon::show('bullhorn', '', Icon::FA) . Yii::t('element', 'C-T-26'),
        'url' => ['car-transporter-announcement/announcement-form', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'C-T-26',
            'class' => 'main-action-item',
        ],
    ],
    // Search car transporter
    [
        'label' => Icon::show('search', '', Icon::FA) . Yii::t('element', 'C-T-67'),
        'url' => ['car-transporter-search/search-form', 'lang' => Yii::$app->language],
        'options' => [
            'id' => 'C-T-67',
            'class' => 'main-action-item',
        ],
    ],
];
$announcement = is_null($language) ? null : \common\models\Announcement::queryGetActiveAnnouncements()
    ->andWhere('language_id = ' . $language->id)
    ->one();

CookieWidget::widget([
    'message' => Yii::t('app', 'ACCEPT_COOKIE_MESSAGE', [
        'acceptButton' => Yii::t('app', 'ACCEPT_COOKIE_BUTTON'),
    ]),
    'dismiss' => Yii::t('app', 'ACCEPT_COOKIE_BUTTON'),
    'learnMore' => null,
    'link' => null,
    'theme' => 'light-top',
]); ?>
<div class="page-loader">
    <div class="loader-wrap">
        <div class="loader-text">
            <?php echo Yii::t('app', 'PLEASE_WAIT'); ?>
        </div>

        <img src="<?php echo Yii::getAlias('@web') . '/images/loader.gif'; ?>" width="100px" height="100px" />
    </div>
</div>

<div class="wrap">
    <header>
        <nav class="topbar<?php echo ($isGuest) ? '' : ' logged-in'; ?>">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle toggle-topbar" data-toggle="collapse" data-target="#menu">
                    <span class="glyphicon glyphicon-option-vertical"></span>
                </button>

                <a href="<?php echo Yii::$app->homeUrl; ?>" class="topbar-home-mobile">
                    <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                         class="mobile-site-logo"
                         alt="<?php echo Yii::t('element', 'HP-H-4'); ?>"
                    />
                    <div>
                        <?php echo Yii::t('element', 'HP-H-4a'); ?>
                    </div>
                </a>

                <a href="#sidebar" id="sidebar-toggle" class="sidebar-menu-toggle">
                    <span class="glyphicon glyphicon-menu-hamburger"></span>
                </a>
            </div>

            <div class="collapse navbar-collapse">
                <?php echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-center'],
                    'encodeLabels' => false,
                    'items' => $services,
                    ]); ?>
            </div>

            <div id="menu" class="collapse navbar-collapse">
                <ul class="navbar-nav navbar-left brand-item">
                    <li class="brand">
                        <a href="<?php echo Yii::$app->homeUrl; ?>" class="topbar-home">
                            <img src="<?php echo Yii::getAlias('@web') . '/images/logo.png'; ?>"
                                 id="HP-H-4"
                                 class="site-logo-main"
                                 alt="<?php echo Yii::t('element', 'HP-H-4'); ?>"
                            />
                            <div id="HP-H-4a">
                                <?php echo Yii::t('element', 'HP-H-4a'); ?>
                            </div>
                        </a>
                    </li>
                </ul>

                <?php echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'encodeLabels' => false,
                    'items' => ($isGuest) ? $guestUserMenu : $userMenu,
                ]); ?>
            </div>
        </nav>
    </header>

    <main class="main-content-wrap<?php echo ($isGuest) ? '' : ' logged-in'; ?> clearfix">
        <div class="main-content-sidebar">
            <div class="main-action-button-wrapper">
                <a href="<?php echo Url::to(['load/announce', 'lang' => Yii::$app->language]); ?>"
                   id="<?php echo ($isGuest) ? 'SB-N-3' : 'SB-P-4'; ?>"
                   class="action-button
                   <?php echo ControllerTrait::isActiveMenuItem('load', 'announce') ? 'active' : ''; ?>"
                >
                    <span class="action-button-icon">
                        <i class="fa fa-bullhorn"></i>
                    </span>

                    <span class="action-button-label">
                        <?php echo ($isGuest) ? Yii::t('element', 'SB-N-3') : Yii::t('element', 'SB-P-4'); ?>
                    </span>
                </a>
            </div>

            <div class="main-action-button-wrapper">
                <a href="<?php echo Url::to(['load/search', 'lang' => Yii::$app->language]); ?>"
                   id="<?php echo ($isGuest) ? 'SB-N-2' : 'SB-P-3'; ?>"
                   class="action-button
                   <?php echo ControllerTrait::isActiveMenuItem('load', 'search') ? 'active' : ''; ?>"
                >
                    <span class="action-button-icon">
                        <i class="fa fa-search"></i>
                    </span>

                    <span class="action-button-label">
                        <?php echo ($isGuest) ? Yii::t('element', 'SB-N-2') : Yii::t('element', 'SB-P-3'); ?>
                    </span>
                </a>
            </div>

            <div class="main-action-button-wrapper">
                <a href="<?php echo Url::to([
                    'car-transporter-announcement/announcement-form',
                    'lang' => Yii::$app->language,
                ]); ?>"
                   id="C-T-26"
                   class="action-button-alt
                   <?php echo ControllerTrait::isActiveMenuItem('car-transporter-announcement', 'announcement-form') ? 'active' : ''; ?>"
                >
                    <span class="action-button-icon">
                        <i class="fa fa-bullhorn"></i>
                    </span>

                    <span class="action-button-label">
                        <?php echo Yii::t('element', 'C-T-26'); ?>
                    </span>
                </a>
            </div>

            <div class="main-action-button-wrapper">
                <a href="<?php echo Url::to([
                    'car-transporter-search/search-form',
                    'lang' => Yii::$app->language]); ?>"
                   id="C-T-67"
                   class="action-button-alt
                   <?php echo ControllerTrait::isActiveMenuItem('car-transporter-search', 'search-form') ? 'active' : ''; ?>"
                >
                    <span class="action-button-icon">
                        <i class="fa fa-search"></i>
                    </span>

                    <span class="action-button-label">
                        <?php echo Yii::t('element', 'C-T-67'); ?>
                    </span>
                </a>
            </div>
        </div>

        <div class="main-content-container">
            <div class="alert-container clearfix
                <?php echo ControllerTrait::isActiveMenuItem('site', 'login') ? 'login' : ''; ?>">
                <?php echo ToastrFlash::widget([
                    'options' => [
                        'closeButton' => true,
                        'debug' => false,
                        'newestOnTop' => true,
                        'progressBar' => false,
                        'positionClass' => 'toast-top-center',
                        'preventDuplicates' => true,
                        'showDuration' => 0, // how long it takes to show the alert in milliseconds
                        'hideDuration' => 1000, // how long it takes to hide the alert in milliseconds
                        'timeOut' => 45000, // how long the alert must be visible to user in milliseconds
                        'extendedTimeOut' => 8000, // how long it takes to hide alert after user hovers in milliseconds
                        'onShown' => 'function() { ' .
                            '$(".alert-container").append($("#toast-container"));' .
                        '}',
                    ]
                ]); ?>
            </div>

            <?php if (is_null($announcement) === false && Yii::$app->session->has(\frontend\controllers\SiteController::SHOW_ANNOUNCEMENT_MESSAGE_SESSION_KEY) === false): ?>
                <section class="announcement">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php if (is_null($announcement->topic) === false && $announcement->topic != ''): ?>
                                <h4><?php echo $announcement->topic ?></h4>
                                <hr class="border-color-orange"/>
                            <?php endif; ?>
                        </div>
                        <div class="col-xs-12">
                            <span class="reminder-message">
                                <?php echo $announcement->body ?>
                            </span>
                        </div>
                        <a href="#" class="close-subscription-reminder" onclick="hideAnnouncementAlert(event)">✕</a>

                    </div>
                </section>
            <?php endif; ?>
            <?php if (Yii::$app->controller->id !== 'subscription' && UserServiceActive::showSubscriptionAlert()): ?>
                <section class="subscription-reminder">
                    <span class="reminder-icon">
                        <i id="NP-FC-1" class="fa fa-exclamation-triangle"></i>
                    </span>

                    <span id="NP-FC-2" class="reminder-message">
                        <?php echo Yii::t('element', 'NP-FC-2', [
                            'sitename' => Yii::$app->params['companyName'],
                        ]); ?>
                    </span>

                    <span class="reminder-btn-wrapper">
                        <a href="<?php echo Url::to(['subscription/index', 'lang' => Yii::$app->language]); ?>"
                           id="NP-FC-3"
                           class="reminder-action-btn">
                            <?php echo Yii::t('element', 'NP-FC-3'); ?>
                        </a>
                    </span>

                    <a href="#" class="close-subscription-reminder" onclick="hideSubscriptionAlert(event)">✕</a>
                </section>
            <?php endif; ?>

            <?php echo $content; ?>
        </div>
    </main>

    <aside id="sidebar" class="sidebar-wrapper">
        <div class="close-button-wrapper">
            <a href="#" id="close-sidebar" class="close-sidebar">✕</a>
        </div>

        <div class="sidebar-items-wrapper">
            <?php echo Nav::widget([
                'options' => [
                    'class' => 'sidebar-items'
                ],
                'encodeLabels' => false,
                'items' => ($isGuest) ? $guestMainActions : $loggedInMainActions,
            ]); ?>

            <?php echo Nav::widget([
                'options' => [
                    'class' => 'sidebar-items'
                ],
                'encodeLabels' => false,
                'items' => array_merge($footerLeftButtons, $services, $pages)
            ]); ?>
        </div>
    </aside>

    <footer>
        <nav class="footer-navbar">
            <div class="collapse navbar-collapse">
                <?php echo Nav::widget([
                    'options' => [
                        'class' => 'navbar-nav navbar-left'
                    ],
                    'encodeLabels' => false,
                    'items' => $footerLeftButtons,
                ]); ?>

                <?php echo Nav::widget([
                    'options' => [
                        'class' => 'navbar-nav navbar-right'
                    ],
                    'encodeLabels' => false,
                    'items' => $pages,
                ]); ?>
            </div>
        </nav>
    </footer>
</div>

<?php /* Modal::begin([
    'id' => 'bug-report-modal',
    'header' => Yii::t('app', 'REPORT_A_BUG'),
]); ?>
    <?php echo Yii::$app->controller->renderPartial('/site/partial/_bug-report-modal'); ?>
<?php Modal::end(); */ ?>

<?php Modal::begin([
    'id' => 'whats-new-modal',
    'header' => Yii::t('app', 'WHATS_NEW_TITLE'),
    'size' => 'modal-lg'
]); ?>
    <?php echo Yii::$app->controller->renderPartial('/site/partial/_whats-new-modal'); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'whats-new-in-demo-modal',
    'header' => Yii::t('app', 'WHATS_NEW_TITLE_DEMO'),
    'size' => 'modal-lg'
]); ?>
    <?php echo Yii::$app->controller->renderPartial('/site/partial/_whats-new-in-demo-modal'); ?>
<?php Modal::end(); ?>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();
