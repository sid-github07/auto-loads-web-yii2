<?php

use backend\controllers\ClientController;
use common\models\Company;
use common\models\CameFrom;
use common\models\CompanyDocument;
use common\models\User;
use common\models\UserService;
use common\models\UserServiceActive;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/** @var View $this */
/** @var integer $companiesCount */
/** @var Company[] $companies */
/** @var string $searchText */
/** @var string $title */
/** @var User[] $user */
/** @var CompanyDocument[] $companyDocument */
/** @var UserServiceActive[] $userServiceActive */
/** @var UserService[] $userService */
/** @var array $comments */
/** @var ActiveDataProvider $dataProvider */

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $companyUsersDataColumns = [
    [
        'class' => '\kartik\grid\SerialColumn'
    ],
    [
        'attribute' => 'id',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_ID_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerIdAsArray[] = $company->id;
                $companyUserIds = [];
                foreach ($company->companyUsers as $companyUser) {
                    array_push($companyUserIds, $companyUser->user['id']);
                }
                $ids = array_merge($companyOwnerIdAsArray, $companyUserIds);
                return implode(' | ', $ids);
            }

            return $company->id;
        },
    ],
    [
        'attribute' => 'title',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_TITLE_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyTitleAsArray[] = (!empty($company->title) ? $company->title : $company->name . ' ' . $company->surname);
                $companyUserTitles = [];
                foreach ($company->companyUsers as $companyUser) {
                    $userNameSurname = $companyUser->user['name'] . ' ' . $companyUser->user['surname'];
                    array_push($companyUserTitles, $userNameSurname);
                }
                $titles = array_merge($companyTitleAsArray, $companyUserTitles);
                return implode(' | ', $titles);
            }

            return (!empty($company->title) ? $company->title : $company->name . ' ' . $company->surname);
        },
    ],
    [
        'attribute' => 'email',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_EMAIL_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerEmailAsArray[] = $company->email;
                $companyUserEmails = [];
                foreach ($company->companyUsers as $companyUser) {
                    array_push($companyUserEmails, $companyUser->user['email']);
                }
                $emails = array_merge($companyOwnerEmailAsArray, $companyUserEmails);
                return implode(' | ', $emails);
            }

            return $company->email;
        },
    ],
    [
        'attribute' => 'phone',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_PHONE_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerPhoneAsArray[] = $company->phone;
                $companyUserPhones = [];
                foreach ($company->companyUsers as $companyUser) {
                    array_push($companyUserPhones, $companyUser->user['phone']);
                }
                $phones = array_merge($companyOwnerPhoneAsArray, $companyUserPhones);
                return implode(' | ', $phones);
            }

            return $company->phone;
        },
    ],
    [
        'attribute' => 'code',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_CODE_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            return $company->code;
        },
    ],
    [
        'attribute' => 'class',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_CLASS_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if ($company->ownerList['class'] == User::SUPPLIER) {
                return Yii::t('text', 'CLIENT_EXPORT_COMPANY_CLASS_SUPPLIER');
            } else {
                if ($company->ownerList['class'] == User::CARRIER) {
                    return Yii::t('text', 'CLIENT_EXPORT_COMPANY_CLASS_CARRIER');
                } else {
                    return Yii::t('text', 'CLIENT_EXPORT_COMPANY_CLASS_MINI_CARRIER');
                }
            }
        },
    ],
    [
        'attribute' => 'activity',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_ACTIVITY_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerActivityAsArray[] = Company::getCompanyUserActivityStatus($company->ownerList['archive'],
                    $company->ownerList['active']);
                $companyUserActivityStatuses = [];

                foreach ($company->companyUsers as $companyUser) {
                    array_push($companyUserActivityStatuses,
                        Company::getCompanyUserActivityStatus($companyUser->user['archive'],
                            $companyUser->user['active']));
                }

                $activityStatuses = array_merge($companyOwnerActivityAsArray, $companyUserActivityStatuses);
                return implode(' | ', $activityStatuses);
            }

            return Company::getCompanyUserActivityStatus($company->ownerList['archive'], $company->ownerList['active']);
        },
    ],
    [
        'attribute' => 'last_login',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_LAST_LOGIN_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerLastLogin[] = date('Y-m-d', $company->ownerList['last_login']);
                $companyUserLastLogins = [];

                foreach ($company->companyUsers as $companyUser) {
                    array_push($companyUserLastLogins, date('Y-m-d', $companyUser->user['last_login']));
                }

                $lastLogins = array_merge($companyOwnerLastLogin, $companyUserLastLogins);
                return implode(' | ', $lastLogins);
            }

            return date('Y-m-d', $company->ownerList['last_login']);
        },
    ],
    [
        'attribute' => 'subscription_end_date',
        'label' => Yii::t('text', 'CLIENT_EXPORT_COMPANY_SUBSCRIPTION_END_DATE_LABEL'),
        'format' => 'raw',
        'value' => function (Company $company) {
            if (!empty($company->companyUsers)) {
                $companyOwnerLastSubscription[] = '—';
                if (!empty($company->ownerList->userServiceActive)) {
                    $ownersLastSubscription = array_slice($company->ownerList->userServiceActive, -1);
                    $companyOwnerLastSubscription[] = date('Y-m-d', $ownersLastSubscription[0]['end_date']);
                }
                $companyUserLastSubscriptions = [];

                foreach ($company->companyUsers as $companyUser) {
                    $companyUserLastSubscription = '—';
                    if (!empty($companyUser->user->userServiceActive)) {
                        $usersLastSubscription = array_slice($companyUser->user->userServiceActive, -1);
                        $companyUserLastSubscription = date('Y-m-d', $usersLastSubscription[0]['end_date']);
                    }
                    array_push($companyUserLastSubscriptions, $companyUserLastSubscription);
                }
                $lastSubscriptions = array_merge($companyOwnerLastSubscription, $companyUserLastSubscriptions);
                return implode(' | ', $lastSubscriptions);
            }

            if (!empty($company->ownerList->userServiceActive)) {
                $lastSubscription = array_slice($company->ownerList->userServiceActive, -1);
                return date('Y-m-d', $lastSubscription[0]['end_date']);
            }
        },
    ],
]; ?>

    <div class="client-index clearfix">
        <section class="widget widget-client-list">
            <div class="widget-heading">
                <span id="A-C-1"><?php echo Yii::t('element', 'A-C-1'); ?></span>
            </div>

            <div class="widget-content">
                <?php echo Yii::$app->controller->renderPartial('_extendedSearch', [
                    'user' => $user,
                    'searchText' => $searchText,
                    'company' => $company,
                    'userServiceActive' => $userServiceActive,
                    'userService' => $userService,
                    'companyDocument' => $companyDocument,
                ]); ?>

                <div class="export-csv-btn-wrapper client">
                    <?php echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $companyUsersDataColumns,
                        'emptyText' => Yii::t('element', 'A-C-10a'),
                        'target' => ExportMenu::TARGET_SELF,
                        'showConfirmAlert' => false,
                        'asDropdown' => false,
                        'showColumnSelector' => false,
                        'exportConfig' => [
                            ExportMenu::FORMAT_HTML => false,
                            ExportMenu::FORMAT_CSV => [
                                'label' => Yii::t('element', 'A-C-10b'),
                                'icon' => false,
                                'alertMsg' => Yii::t('element', 'A-C-10c'),
                                'mime' => 'application/csv',
                                'extension' => 'csv',
                                'writer' => ExportMenu::FORMAT_CSV,
                            ],
                            ExportMenu::FORMAT_TEXT => false,
                            ExportMenu::FORMAT_PDF => false,
                            ExportMenu::FORMAT_EXCEL => false,
                            ExportMenu::FORMAT_EXCEL_X => false,
                        ],
                        'filename' => Yii::t('element', 'A-C-10d'),
                    ]); ?>
                </div>

                <div class="clearfix"></div>

                <div class="client-search-results-container">
                <span id="A-C-6" class="client-search-results-label">
                    <?php echo Yii::t('element', 'A-C-11'); ?>
                </span>

                    <span id="A-C-12" class="client-search-results-value">
                    <?php echo ' ' . $companiesCount ?>
                </span>
                </div>

                <div class="responsive-table-wrapper custom-table clients-table">
                    <table class="table table-striped table-bordered responsive-table">
                        <thead>
                        <tr>
                            <th>
                                <?php echo Yii::t('element', 'A-C-13'); ?>
                            </th>
                            <th>
                                <?php echo Yii::t('element', 'A-C-14'); ?>
                            </th>
                        </tr>
                        </thead>
                        <?php if (!empty($companies)) : ?>
                            <tbody>
                            <?php foreach ($companies as $count => $company) : ?>
                                <tr class="companies-row">
                                    <td data-title="<?php echo Yii::t('element', 'A-C-13'); ?>">
                                        <h5>
                                            <span class="A-C-15"><?php echo $pages->offset + ($count + 1) . '. ' ?></span>
                                            <?php echo Html::a($company->getTitleByType(), [
                                                '/client/company',
                                                'lang' => Yii::$app->language,
                                                'id' => $company->id,
                                                'tab' => ClientController::TAB_COMPANY_INFO,
                                            ],
                                                [
                                                    'class' => 'A-C-17'
                                                ]); ?>
                                        </h5>

                                        <div class="client-info">
                                            <div class="users-usernames">
                                                <?php echo Yii::t('text', 'CLIENT_INDEX_USERS_USERNAMES'); ?>
                                            </div>
                                            <ul>
                                                <li>
                                                    <?php $ownerHasSubscription = count($company->ownerList->userServiceActives) > 0; ?>
                                                    <?php echo !Yii::$app->admin->identity->isModerator() ? Html::a($company->getClientNameString(),
                                                        '#', [
                                                            'class' => $ownerHasSubscription ? 'A-C-19 user-has-subscription' : 'A-C-19 user-has-no-subscription',
                                                            'onclick' => 'editUser(event, ' . $company->ownerList->id . ');',
                                                        ]) : Html::tag('span', $company->getClientNameString(), [
                                                        'class' => $ownerHasSubscription ? 'A-C-19 user-has-subscription' : 'A-C-19 user-has-no-subscription',
                                                    ]); ?>
                                                    <span class="A-C-20">
                                                        <?php echo $company->ownerList->convertLastLoginToString(); ?>
                                                    </span>
                                                    <?php echo
                                                    Html::a(Html::tag('i', null,
                                                        ['class' => 'fa fa-caret-down', 'aria-hidden' => true]), '#', [
                                                        'class' => 'preview-icon closed',
                                                        'onclick' => 'loadUserRoutes(event, ' . $company->ownerList->id . ')',
                                                        'data-placement' => 'top',
                                                        'data-toggle' => 'tooltip',
                                                        'style' => 'font-size: 20px',
                                                        'title' => Yii::t('element', 'A-C-339a'),
                                                    ]);
                                                    ?>
                                                </li>
                                            </ul>
                                            <?php foreach ($company->companyUsers as $companyUser) : ?>
                                                <div class="company-users-container">
                                                    <?php $hasSubscription = count($companyUser->user->userServiceActives) > 0; ?>
                                                    <?php echo Html::a($companyUser->user->getNameAndSurname(), '#', [
                                                        'class' => $hasSubscription ? 'A-C-19 user-has-subscription' : 'A-C-19 user-has-no-subscription',
                                                        'onclick' => 'editUser(event, ' . $companyUser->user->id . ');',
                                                    ]); ?>
                                                    <span class="A-C-20">
                                                    <?php echo $companyUser->user->convertLastLoginToString(); ?>
                                                </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="client-contact-info">
                                            <div class="user-imprint">
                                                <?php echo Yii::t('text', 'CLIENT_INDEX_USER_IMPRINT'); ?>
                                            </div>

                                            <div class="A-C-21">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-21'); ?>
                                                </span>
                                                <?php echo Html::a($company->email, 'mailto:' . $company->email, [
                                                    'class' => 'client-email'
                                                ]); ?>
                                            </div>

                                            <div class="A-C-22">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-22c'); ?>
                                                </span>

                                                <span><?php echo $company->getCompanyClassType(); ?></span>
                                            </div>

                                            <div class="A-C-23">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-23'); ?>
                                                </span>

                                                <span><?php echo $company->code; ?></span>
                                            </div>

                                            <div class="A-C-24">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-24'); ?>
                                                </span>

                                                <span><?php echo $company->vat_code; ?></span>
                                            </div>

                                            <div class="A-C-25">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-25'); ?>
                                                </span>

                                                <span><?php echo $company->address; ?></span>
                                            </div>

                                            <div class="A-C-26">
                                                <span class="client-contact-info-label">
                                                    <?php echo Yii::t('element', 'A-C-26'); ?>
                                                </span>

                                                <span><?php echo $company->ownerList->phone; ?></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-title="<?php echo Yii::t('element', 'A-C-14'); ?>">
                                        <div class="text-right">
                                            <label class="custom-checkbox">
                                                <?php echo
                                                    Yii::t('app', 'COMPANY_POTENTIAL_LABEL') . ' ' .
                                                    Html::checkbox('potential', $company->potential, [
                                                        'id' => 'potential-' . $company->id,
                                                        'onchange' => 'changeCompanyPotentiality(' . $company->id . ')'
                                                    ]); ?>
                                            </label>
                                        </div>
                                        <div class="registration-date-status text-center">
                                            <div class="A-C-28">
                                                <?php echo date('Y-m-d', $company->created_at); ?>
                                            </div>

                                            <div class="A-C-29">
                                                <?php echo $company->getCompanyActivityType(); ?>
                                            </div>
                                            <div class="A-C-29">
                                                <?php echo Html::encode($company->ownerList->came_from_referer); ?>
                                            </div>
                                            <div class="A-C-31">
                                                <?php if (!is_null($company->ownerList->came_from_id) && $company->ownerList->cameFrom->type == CameFrom::REASON_TO_REGISTER) : ?>
                                                    <?php echo $company->ownerList->cameFrom->source_name; ?>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <?php if (!Yii::$app->admin->identity->isModerator()): ?>
                                    <tr class="companies-row">
                                        <td colspan="2">
                                            <div class="company-comments">
                                                <a href="#"
                                                   class="A-C-27"
                                                   onclick="addComment(event, <?php echo $company->id; ?>, 1);"
                                                >
                                                    <?php echo Yii::t('element', 'A-C-27'); ?>
                                                </a>
                                                <?php Pjax::begin(['id' => 'company-comment-pjax-' . $company->id]); ?>
                                                <?php Pjax::end(); ?>
                                                <?php echo Yii::$app->controller->renderPartial('/client/company/preview/comments-table',
                                                    [
                                                        'comments' => isset($comments[$company->id]) ? $comments[$company->id] : [],
                                                        'company' => $company,
                                                        'toIndex' => 1,
                                                    ]); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>
                    <div class="text-center">
                        <?php echo LinkPager::widget([
                            'pagination' => $pages,
                            'firstPageLabel' => Yii::t('app', 'FIRST_PAGE'),
                            'lastPageLabel' => Yii::t('app', 'LAST_PAGE'),
                        ]); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php Modal::begin([
    'id' => 'company-email-modal'
]); ?>
<?php echo Yii::$app->controller->renderPartial('_usersEmailModalView', [
    'companies' => $companies,
]); ?>
<?php Modal::end(); ?>

<?php
Modal::begin([
    'id' => 'edit-user-modal',
    'header' => Yii::t('element', 'A-C-130'),
]);
Pjax::begin(['id' => 'edit-user-pjax']);
Pjax::end();
Modal::end();


//TODO add translations
Modal::begin([
    'id' => 'user-routes-modal',
    'header' => Yii::t('element', Yii::t('element', 'main_routes')),
]);
Pjax::begin(['id' => 'user-routes-pjax']);
Pjax::end();
Modal::end();

$this->registerJsFile(Url::base() . '/dist/js/client/index.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJs(
    'var actionIndex = "' . Url::to(['client/index', 'lang' => Yii::$app->language]) . '"; ' .
    'var actionRenderCompanyCommentForm = "' . Url::to([
        'client/render-company-comment-form',
        'lang' => Yii::$app->language,
    ]) . '"; ' .
    'var actionCompanyUserEditForm = "' . Url::to([
        'client/company-user-edit-form',
        'lang' => Yii::$app->language
    ]) . '";' .
    'var actionLoadUserRoutes = "' . Url::to([
        'client/load-user-routes',
        'lang' => Yii::$app->language
    ]) . '";' .
    'var actionCheckPotential = "' . Url::to(['client/save-potentiality', 'lang' => Yii::$app->language]) . '";',
    View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/comment.js', ['depends' => [JqueryAsset::className()]]);
