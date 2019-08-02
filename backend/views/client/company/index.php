<?php

use backend\controllers\ClientController;
use common\models\CarTransporter;
use common\models\CarTransporterCity;
use common\models\Company;
use common\models\CompanyDocument;
use common\models\Load;
use common\models\LoadCity;
use common\models\UserService;
use kartik\icons\Icon;
use odaialali\yii2toastr\ToastrAsset;
use odaialali\yii2toastr\ToastrFlash;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var string $activeVatRateLegalCountryCode */
/** @var array $vatRateCountries */
/** @var string $cityLegal */
/** @var array $phoneNumbers */
/** @var string $activePhoneNumber */
/** @var Company $company */
/** @var CompanyDocument $companyDocument */
/** @var ActiveDataProvider $subscriptionDataProvider */
/** @var ActiveDataProvider $invoiceDataProvider */
/** @var ActiveDataProvider $preInvoiceDataProvider */
/** @var array|UserService[] $paymentDataProvider */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var array $countries */
/** @var ActiveDataProvider $loadDataProvider */
/** @var CarTransporter $carTransporter */
/** @var CarTransporterCity $carTransporterCity */
/** @var ActiveDataProvider $carTransporterDataProvider */
/** @var null|integer $year */
/** @var null|integer $id Company ID */
/** @var string $tab */

$this->title = Yii::t('seo', 'TITLE_ADMIN_COMPANY_INFO');
$this->params['breadcrumbs'][] = ['label' => Yii::t('seo', 'TITLE_ADMIN_CLIENTS'), 'url' => ['client/index'],];
$this->params['breadcrumbs'][] = $this->title;
ToastrAsset::register($this);
?>

<?php Pjax::begin(['id' => 'company-index-pjax']); ?>
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
                '$(".main-alert").append($("#toast-container"));' .
            '}',
        ]
    ]); ?>
<?php Pjax::end(); ?>

<div class="client-company-info clearfix">
    <section class="widget widget-company-info-edit">
        <div class="widget-heading">
            <span class="select status-select">
                <?php if (!Yii::$app->admin->identity->isModerator()): ?>
                    <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
                    <?php echo Html::activeDropDownList($company, 'archive', Company::getTranslatedArchives(), [
                        'id' => 'A-M-8',
                        'class' => 'company-status-select',
                        'onchange' => 'triggerCompanyArchivation(this)',
                    ]); ?>
                <?php endif; ?>
            </span>

            <span class="company-user-name">
                <span id="A-M-9">
                    <?php echo $company->getTitleByType(); ?>
                </span>
                
                <?php if (!Yii::$app->admin->identity->isModerator()): ?>
                <a href="#"
                   id="A-M-9a"
                   class="edit-company"
                   onclick="changeCompanyName(event);"
                >
                    <i class="fa fa-pencil"></i>
                </a>
                <?php endif; ?>

                <span class="company-id">
                    <span id="A-M-10">
                        <?php echo Yii::t('element', 'A-M-9'); ?>
                    </span>

                    <span id="A-M-11">
                        <?php echo $company->id; ?>
                    </span>
                </span>
            </span>
        </div>

        <div class="widget-content">
            <div class="settings-tabs-container">
                <?php echo Tabs::widget([
                    'navType' => 'nav-tabs nav-justified tabs-navigation',
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => Icon::show('building', [], Icon::FA) .
                                       Html::tag('span', Yii::t('element', 'A-M-12'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/info', [
                                'company' => $company,
                                'activeVatRateLegalCountryCode' => $activeVatRateLegalCountryCode,
                                'vatRateCountries' => $vatRateCountries,
                                'cityLegal' => $cityLegal,
                                'phoneNumbers' => $phoneNumbers,
                                'activePhoneNumber' => $activePhoneNumber,
                                'companyDocument' => $companyDocument,
                                'tab' => $tab,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_INFO ||
                                        $tab === ClientController::TAB_COMPANY_DOCUMENTS,
                            'linkOptions' => [
                                'id' => 'A-M-12',
                                'class' => 'company-info company-documents company-tab',
                                'title' => Yii::t('element', 'A-M-12'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_INFO . '")',
                            ],
                            'visible' => !Yii::$app->admin->identity->isModerator(),
                        ],
                        [
                            'label' => Icon::show('user', [], Icon::FA) .
                                       Html::tag('span', Yii::t('element', 'A-M-13'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/user', [
                                'company' => $company,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_USERS,
                            'linkOptions' => [
                                'id' => 'A-M-13',
                                'class' => 'company-users company-tab',
                                'title' => Yii::t('element', 'A-M-13'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_USERS . '")',
                            ],
                            'visible' => !Yii::$app->admin->identity->isModerator(),
                        ],
                        [
                            'label' => Icon::show('key', [], Icon::FA) .
                                       Html::tag('span', Yii::t('element', 'A-M-14'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/subscription', [
                                'id' => $id,
                                'dataProvider' => $subscriptionDataProvider,
								'historyDataProvider' => $subscriptionHistoryDataProvider,
                                'year' => $year,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_SUBSCRIPTIONS,
                            'linkOptions' => [
                                'id' => 'A-M-14',
                                'class' => 'company-subscriptions company-tab',
                                'title' => Yii::t('element', 'A-M-14'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_SUBSCRIPTIONS . '")',
                            ],
                        ],
                        [
                            'label' => Icon::show('file-text', [], Icon::FA) .
                                       Html::tag('span', Yii::t('element', 'A-M-15'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/bill', [
                                'tab' => $tab,
                                'id' => $id,
                                'year' => $year,
                                'invoiceDataProvider' => $invoiceDataProvider,
                                'preInvoiceDataProvider' => $preInvoiceDataProvider,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_INVOICES ||
                                        $tab === ClientController::TAB_COMPANY_PRE_INVOICES,
                            'linkOptions' => [
                                'id' => 'A-M-15',
                                'class' => 'company-bills company-tab',
                                'title' => Yii::t('element', 'A-M-15'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_INVOICES . '")',
                            ],
                        ],
                        [
                            'label' => Icon::show('money', [], Icon::FA) .
                                       Html::tag('span', Yii::t('element', 'A-M-16'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/payment', [
                                'id' => $id,
                                'year' => $year,
                                'paymentDataProvider' => $paymentDataProvider,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_PAYMENTS,
                            'linkOptions' => [
                                'id' => 'A-M-16',
                                'class' => 'company-payments company-tab',
                                'title' => Yii::t('element', 'A-M-16'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_PAYMENTS . '")',
                            ],
                            'visible' => !Yii::$app->admin->identity->isModerator(),
                        ],
                        [
                            'label' => Icon::show('car', [], Icon::FA) .
                                Html::tag('span', Yii::t('element', 'A-M-18'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/load', [
                                'load' => $load,
                                'company' => $company,
                                'loadCity' => $loadCity,
                                'countries' => $countries,
                                'loadTypes' => Load::getTranslatedTypes(),
                                'dataProvider' => $loadDataProvider,
                                'tab' => $tab, 
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_LOADS,
                            'linkOptions' => [
                                'id' => 'A-M-18',
                                'class' => 'company-loads company-tab',
                                'title' => Yii::t('element', 'A-M-18'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_LOADS . '")',
                            ],
                            'visible' => !Yii::$app->admin->identity->isModerator(),
                        ],
                        [
                            'label' => Icon::show('truck', [], Icon::FA) .
                                Html::tag('span', Yii::t('element', 'C-T-132'), ['class' => 'tab-label-text']),
                            'content' => Yii::$app->controller->renderPartial('/client/company/car-transporter', [
                                'company' => $company,
                                'carTransporter' => $carTransporter,
                                'carTransporterCity' => $carTransporterCity,
                                'dataProvider' => $carTransporterDataProvider,
                            ]),
                            'active' => $tab === ClientController::TAB_COMPANY_CAR_TRANSPORTERS,
                            'linkOptions' => [
                                'id' => 'C-T-132',
                                'class' => 'company-car-transporters company-tab',
                                'title' => Yii::t('element', 'C-T-132'),
                                'onclick' => 'changeTabUrl(event, "' . ClientController::TAB_COMPANY_CAR_TRANSPORTERS . '")',
                            ],
                            'visible' => !Yii::$app->admin->identity->isModerator(),
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </section>
</div>

<?php Modal::begin([
    'id' => 'change-company-archivation',
    'header' => Yii::t('element', 'CHANGE_COMPANY_STATUS_TITLE'),
]); ?>
    <h4><?php echo Yii::t('element', 'CHANGE_COMPANY_STATUS_QUESTION'); ?></h4>
    <div class="modal-form-footer-center">
        <button id="change-company-archivation-yes" class="primary-button yes-btn" onclick="changeCompanyArchivation(this)">
            <?php echo Yii::t('element', 'CLIENT_COMPANY_INFO_CONFIRM'); ?>
        </button>

        <button id="change-company-archivation-no" class="secondary-button no-btn" data-dismiss="modal">
            <?php echo Yii::t('element', 'CLIENT_COMPANY_INFO_CANCEL'); ?>
        </button>
    </div>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'change-company-name-modal',
    'header' => Yii::t('element', 'A-M-9a'),
]); ?>

    <?php Pjax::begin(['id' => 'change-company-name-pjax']); ?>
    <?php Pjax::end(); ?>

<?php Modal::end(); ?>

<?php
$this->registerJsFile(Url::base() . '/dist/js/site/url.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/client/companyEdit.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/dist/js/client/editCompanyDocuments.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJs(
    'var NOT_ARCHIVED = "' . Company::NOT_ARCHIVED . '"; ' .
    'var ARCHIVED = "' . Company::ARCHIVED . '"; ' .
    'var actionChangeCompanyArchivation = "' . Url::to([
        'client/change-company-archivation',
        'lang' => Yii::$app->language,
        'id' => $company->id,
    ]) . '"; ' .
    'var actionRenderCompanyNameChangeForm = "' . Url::to([
        'client/render-company-name-change-form',
        'lang' => Yii::$app->language,
        'id' => $company->id,
    ]) . '"; ',
View::POS_BEGIN);
