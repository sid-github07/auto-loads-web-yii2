<?php

use common\models\Company;
use common\models\User;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/** @var User $user */
/** @var Company $company */
/** @var array $vatRateCountries */
/** @var string $activeVatRate */
/** @var string $city */

switch ($company->ownerList->account_type) {
    case User::NATURAL:
        echo Yii::$app->controller->renderPartial('___contact-info-natural', [
            'company' => $company,
            'city' => $city,
        ]);
        break;
    case User::LEGAL:
        echo Yii::$app->controller->renderPartial('___contact-info-legal', [
            'company' => $company,
            'vatRateCountries' => $vatRateCountries,
            'activeVatRate' => $activeVatRate,
            'city' => $city,
        ]);
        break;
    default:
        echo 'Invalid account type'; // FIXME
}

$this->registerJsFile(Url::base() . '/dist/js/site/map.js', ['depends' => [JqueryAsset::className()]]);