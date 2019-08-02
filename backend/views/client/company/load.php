<?php

use backend\controllers\ClientController;
use common\models\Load;
use common\models\LoadCity;
use kartik\icons\Icon;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var View $this */
/** @var Load $load */
/** @var LoadCity $loadCity */
/** @var array $countries */
/** @var array $loadTypes */
/** @var ActiveDataProvider $dataProvider */

Icon::map($this, Icon::FI);
?>


<?php echo Yii::$app->controller->renderPartial('/load/filtration/form', [
    'load' => $load,
    'loadCity' => $loadCity,
    'loadTypes' => $loadTypes,
    'countries' => $countries,
    'action' => Url::to([
        'client/company',
        'lang' => Yii::$app->language,
        'id' => $company->id,
        'tab' => ClientController::TAB_COMPANY_LOADS,
    ]),
]); ?>

<div class="text-right page-size-dropdown select">
    <span class="posts-per-page-select">
        <label id="L-T-14" class="page-size-filter-label">
            <?php echo Yii::t('element', 'A-C-324'); ?>
        </label>
        <?php echo Html::dropDownList('load-per-page', Yii::$app->request->get('per-load-page'), Load::getPageSizes(), [
            'id' => 'A-C-325',
            'onchange' => 'changeLoadPageNumber(event, this);',
        ]); ?>
        <span class="select-arrow"><i class="fa fa-caret-down"></i></span>
    </span>
</div>


<?php Pjax::begin(['id' => 'pjax-load-container']); ?>

<?php echo Yii::$app->controller->renderPartial('/client/company/preview/load-list', [
    'dataProvider' => $dataProvider,
]);

Pjax::end();