<?php

use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Load;
use common\models\CreditService;
use common\models\User;
use yii\helpers\Html;
use common\models\City;
use common\components\ElasticSearch\Cities;

function intervalToString(DateInterval $interval)
{
    $array = [];
    foreach ([
                 'm' => 'month',
                 'd' => 'd',
                 'h' => 'h',
                 'i' => 'min',
                 's' => 'sec'
             ] as $t => $naming) {
        if (count($array) < 2 && $interval->$t) {
            $array[] = Yii::t('element', sprintf('{0} %s', $naming), [$interval->$t]);
        }
    }
    return implode(' ', $array);
}

/**
 * @var View $this
 * @var array $trucks
 * @var Load $load
 * @var CreditService $service
 * @var null|string $token
 * @var User $user
 * @var string $formName
 * @var array $opened
 * @var array $loadCities
 * @var array $unloadCities
 * @var bool $freeForMemberships
 */

?>

<?php if ($freeForMemberships !== true || !$user->hasSubscription()) : ?>
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
                    'user' => $user,
                    'freeForMemberships' => $freeForMemberships
                ]); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

<?php if (count($trucks)) : ?>
    <div class="table-responsive custom-table">
        <table class="table" id="<?php echo $formName; ?>">
            <tbody>
            <?php foreach ($trucks as $entity):
                $viewed = isset($opened[$entity['user_id']]);
                $diff = (new DateTime())->setTimestamp($entity['available_from'])->diff((new DateTime()));
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
                        <?php echo intervalToString($diff); ?>
                    </td>
                    <td>
                        <?php
                        $distance = $closest = $from = null;
                        foreach ($loadCities as $city) {
                            foreach ($entity['load_location'] as $ctc) {
                                $newDistance = Cities::getDistance($city['city_id'], $ctc['city_id']);
                                if (is_null($distance) || $newDistance < $distance) {
                                    $distance = $newDistance;
                                    $closest = $ctc;
                                    $from = $city;
                                }
                            }
                        }
                        $city = City::findOne($closest['city_id']);
                        $cityFrom = City::findOne($from['city_id']); // load city in truck ad
                        if (intval($distance) > 0) {
                            if ($cityFrom->isCountry()) {
                                $howFarAway = Yii::t('element', 'is located in {0}', [
                                    $cityFrom->name
                                ]);
                            } else {
                                $howFarAway = Yii::t('element', '{0} km from {1}', [
                                    (int)$distance,
                                    $cityFrom->name
                                ]);
                            }
                        } else {
                            $howFarAway = '';
                        }
                        $flag = Html::tag('i', '',
                                ['class' => 'flag-icon flag-icon-' . strtolower($city->country_code)]) . ', ' . $city->name;
                        echo Html::tag('div', $flag) . Html::tag('div', $howFarAway);
                        ?>
                    </td>
                    <td>

                    </td>
                    <td class="load-contacts">
                        <?php
                        $distance = $closest = $to = null;
                        foreach ($unloadCities as $city) {
                            foreach ($entity['unload_location'] as $ctc) {
                                $newDistance = Cities::getDistance($city['city_id'], $ctc['city_id']);
                                if (is_null($distance) || $newDistance < $distance) {
                                    $distance = $newDistance;
                                    $closest = $ctc;
                                    $to = $city;
                                }
                            }
                        }
                        $city = City::findOne($closest['city_id']);
                        $cityTo = City::findOne($to['city_id']); // unload city in truck ad
                        if (intval($distance) > 0) {
                            if ($cityFrom->isCountry()) {
                                $howFarAway = Yii::t('element', 'is located in {0}', [
                                    $cityTo->name
                                ]);
                            } else {
                                $howFarAway = Yii::t('element', '{0} km from {1}', [
                                    (int)$distance,
                                    $cityTo->name
                                ]);
                            }
                        } else {
                            $howFarAway = '';
                        }
                        $flag = Html::tag('i', '',
                                ['class' => 'flag-icon flag-icon-' . strtolower($city->country_code)]) . ', ' . $city->name;
                        echo Html::tag('div', $flag) . Html::tag('div', $howFarAway);
                        ?>
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
                <tr style="display: none; background-color: #fff"
                    data-user-contacts="<?php echo $entity['user_id']; ?>">
                    <td colspan="4"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <?php echo Html::tag('span', Yii::t('element', 'no_trucks_found')); ?>
<?php endif; ?>

