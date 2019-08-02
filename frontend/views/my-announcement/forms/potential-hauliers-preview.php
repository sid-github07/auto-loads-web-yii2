<?php

use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Load;
use common\models\CreditService;
use common\models\User;
use yii\helpers\Html;

/**
 * @var View $this
 * @var array $potentialHauliers
 * @var Load $load
 * @var CreditService $service
 * @var null|string $token
 * @var User $user
 * @var string $formName
 * @var array $opened
 */

?>

<?php
$form = ActiveForm::begin([
    'action' => [
        Url::to([
            'my-load/load-preview-buy',
            'lang' => Yii::$app->language,
            'token' => $token,
            'id' => $load->id
        ])
    ],
]);
?>
<div class="row">
    <div class="col-xs-12">
        <div id="<?php echo sprintf('alert-%s', $formName); ?>" class="alert alert-warning">
            <?php echo $this->renderAjax('/my-announcement/forms/parts/preview-alert', [
                'service' => $service,
                'user' => $user
            ]); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php if (count($potentialHauliers)) : ?>
    <div class="table-responsive custom-table">
        <table class="table" id="<?php echo $formName; ?>">
            <tbody>
            <?php foreach ($potentialHauliers as $entity):
                $viewed = isset($opened[$entity['user_id']]);
                ?>
                <tr <?php if ($viewed) {
                    echo 'style="background-color: #fff;"';
                } ?> data-user="<?php echo $entity['user_id']; ?>">
                    <td class="marker">
                        <?php if (!$viewed) : ?>
                            <?php echo Html::checkbox('marker') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo Yii::t('element', '{counter} times looked for such load',
                            ['counter' => $entity['similar_views']]) ?>
                    </td>
                    <td class="load-contacts">
                        <?php echo Html::a(Html::tag('i', null, ['class' => 'fa fa-caret-down', 'aria-hidden' => true]),
                            '#', [
                                'class' => 'preview-icon closed',
                                'onclick' => 'getUserContacts(event, "' . $formName . '", ' . $load->id . ',' . $entity['user_id'] . ')',
                                'data-placement' => 'top',
                                'data-toggle' => 'tooltip',
                                'title' => Yii::t('element', 'L-T-25'),
                            ]); ?>
                    </td>
                    <td class="viewed">
                        <?php echo Yii::t('element', $viewed ? 'viewed' : 'not_viewed'); ?>
                    </td>
                </tr>
                <tr style="display: none; background-color: #fff" data-user-contacts="<?php echo $entity['user_id']; ?>">
                    <td colspan="4"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <?php echo Html::tag('span', Yii::t('element', 'no_potentials_found'));?>
<?php endif; ?>

