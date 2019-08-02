<?php

use common\models\Company;
use yii\helpers\Html;

/** @var Company[] $companies */

if (!is_null($companies)) {
    foreach($companies as $company) {
        echo Html::tag('div', $company->email . ';');
        if (!empty($company->companyUsers)) { 
            foreach($company->companyUsers as $companyRelated) {
                echo Html::tag('div', $companyRelated->user->email . ';');
            }
        }            
    }
}