<?php

use common\models\Company;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var Company $company */

?>
<div class="client-company-users">
    <div class="text-right">
        <a href="#" 
           id="A-C-129"
           onclick="addCompanyUser(event, <?php echo $company->id; ?>);"
           class="primary-button company-create-new-user-btn"
        >
            <i class="fa fa-plus"></i><?php echo Yii::t('element', 'A-C-129'); ?>
        </a>
    </div>
    <div class="responsive-table-wrapper custom-gridview">
        <table class="table table-striped table-bordered responsive-table">
            <thead>
                <tr>
                    <th id="A-C-116">
                        <?php echo Yii::t('element', 'A-C-116'); ?>
                    </th>
                    
                    <th id="A-C-117">
                        <?php echo Yii::t('element', 'A-C-117'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="A-C-128"
                        data-title="<?php echo Yii::t('element', 'A-C-116'); ?>"
                    >
                        <?php echo $company->ownerList->id; ?>
                    </td>

                    <td class="A-C-117a"
                        data-title="<?php echo Yii::t('element', 'A-C-117'); ?>"
                    >
                        <i class="A-C-118 fa fa-user"></i>

                        <a href="#" class="A-C-119" onclick="editCompanyUser(event, <?php echo $company->ownerList->id; ?>);">
                            <?php echo $company->ownerList->name . ' ' . $company->ownerList->surname; ?>
                        </a>

                        <div>
                            <span class="A-C-120">
                                <?php echo Yii::t('element', 'A-C-120') . ': '; ?>
                            </span>

                            <span class="A-C-121">
                                <?php echo $company->ownerList->phone; ?>
                            </span>
                        </div>

                        <div>
                            <span class="A-C-122">
                                <?php echo Yii::t('element', 'A-C-122') . ': '; ?>
                            </span>

                            <span class="A-C-123">
                                <?php echo $company->ownerList->email; ?>
                            </span>
                        </div>

                        <div>
                            <span class="A-C-124">
                                <?php echo Yii::t('element', 'A-C-124') . ': '; ?>
                            </span>

                            <span class="A-C-125">
                                <?php echo $company->ownerList->getLanguagesString(); ?>
                            </span>
                        </div>

                        <div class="clearfix user-edit-actions-row">
                            <a href="<?php echo Url::to([
                                'site/login-to-user',
                                'lang' => Yii::$app->language,
                                'id' => $company->ownerList->id,
                            ]); ?>"
                               class="A-C-126 pull-left"
                               target="_blank"
                            >
                                <?php echo Yii::t('element', 'A-C-126'); ?>
                            </a>

                            <a href="#"
                               class="A-C-127 pull-right company-user-activity"
                               onclick="previewUserActivity(event, <?php echo $company->ownerList->id; ?>);"
                            >
                                <?php echo Yii::t('element', 'A-C-127'); ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php if (empty(!$company->companyUsers)): ?>
                <?php foreach ($company->companyUsers as $companyUser): ?>
                    <tr>
                        <td class="A-C-128"
                            data-title="<?php echo Yii::t('element', 'A-C-116'); ?>"
                        >
                            <?php echo $companyUser->user->id; ?>
                        </td>
                        
                        <td class="A-C-117a"
                            data-title="<?php echo Yii::t('element', 'A-C-117'); ?>"
                        >
                            <i class="A-C-118 fa fa-user"></i>
                            
                            <a href="#" class="A-C-119" onclick="editCompanyUser(event, <?php echo $companyUser->user->id; ?>);">
                                <?php echo $companyUser->user->getNameAndSurname(); ?>
                            </a>
                            
                            <div>
                                <span class="A-C-120">
                                    <?php echo Yii::t('element', 'A-C-120') . ': '; ?>
                                </span>
                                
                                <span class="A-C-121">
                                    <?php echo $companyUser->user->phone; ?>
                                </span>
                            </div>
                            
                            <div>
                                <span class="A-C-122">
                                    <?php echo Yii::t('element', 'A-C-122') . ': '; ?>
                                </span>
                                
                                <span class="A-C-123">
                                    <?php echo $companyUser->user->email; ?>
                                </span>
                            </div>
                            
                            <div>
                                <span class="A-C-124">
                                    <?php echo Yii::t('element', 'A-C-124') . ': '; ?>
                                </span>
                                
                                <span class="A-C-125">
                                    <?php echo $companyUser->user->getLanguagesString(); ?>
                                </span>
                            </div>
                            
                            <div class="clearfix user-edit-actions-row">
                                <a href="<?php echo Url::to([
                                    'site/login-to-user',
                                    'lang' => Yii::$app->language,
                                    'id' => $company->ownerList->id,
                                ]); ?>"
                                   class="A-C-126 pull-left"
                                   target="_blank"
                                >
                                    <?php echo Yii::t('element', 'A-C-126'); ?>
                                </a>
                                
                                <a href="#"
                                   class="A-C-127 pull-right company-user-activity"
                                   onclick="previewUserActivity(event, <?php echo $companyUser->user->id; ?>);"
                                >
                                    <?php echo Yii::t('element', 'A-C-127'); ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                    <tr>
                        <td colspan="3">
                            <?php echo Yii::t('element', 'A-C-117a'); ?>
                        </td>
                    </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
Modal::begin([
    'id' => 'edit-company-user-modal',
    'header' => Yii::t('element', 'A-C-130'),
    'size' => 'modal-lg',
]);

    Pjax::begin(['id' => 'edit-company-user-pjax']);
    Pjax::end();

Modal::end();

Modal::begin([
    'id' => 'add-company-user-modal',
    'header' => Yii::t('element', 'A-C-165'),
    'size' => 'modal-lg',
]);

    Pjax::begin(['id' => 'add-company-user-pjax']);
    Pjax::end();

Modal::end();

Modal::begin([
    'id' => 'preview-company-user-activity-modal',
    'header' => Yii::t('element', 'A-C-155'),
    'size' => 'modal-lg',
]);

    Pjax::begin(['id' => 'preview-company-user-activity-pjax']);
    Pjax::end();

Modal::end();

$this->registerJs(
    'var actionCompanyUserEditForm = "' . Url::to(['client/company-user-edit-form']) . '"; ' .
    'var actionCompanyUserAddForm = "' . Url::to(['client/company-user-add-form']) . '"; ' .
    'var actionCompanyUserActivityPreview = "' . Url::to(['client/company-user-activity-preview']) . '";',
View::POS_BEGIN);
$this->registerJsFile(Url::base() . '/dist/js/client/company/user.js', ['depends' => [JqueryAsset::className()]]);