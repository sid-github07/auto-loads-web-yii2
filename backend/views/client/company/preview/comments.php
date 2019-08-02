<?php

use backend\controllers\ClientController;
use common\models\Company;
use common\models\CompanyComment;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var CompanyComment[] $comments */
/** @var Company $company */

$this->title = Yii::t('seo', 'TITLE_COMPANY_COMMENTS');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('seo', 'TITLE_ADMIN_CLIENTS'),
    'url' => ['client/index', 'lang' => Yii::$app->language],
];
$this->params['breadcrumbs'][] = [
    'label' => Html::encode($company->getTitleByType()),
    'url' => [
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $company->id,
        'tab' => ClientController::TAB_COMPANY_INFO,
    ],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="widget widget-comments-list">
    <div class="widget-heading">
        <?php echo Yii::t('element', 'A-C-360e'); ?>
    </div>
    
    <div class="widget-content">
        <?php echo Yii::$app->controller->renderPartial('/client/company/preview/comments-table', [
            'comments' => $comments,
            'company' => $company,
            'toIndex' => 0,
        ]); ?>
    </div>
</section>