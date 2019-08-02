<?php

use yii\bootstrap\Nav;

$helloUserText = Yii::t('element', 'HELLO_USER', [
    'name' => Yii::$app->admin->identity->name,
    'surname' => Yii::$app->admin->identity->surname,
]);

$editProfileText = Yii::t('element', 'TOPBAR_EDIT_USER_PROFILE');

$label = '<span class="user-greeting">' . $helloUserText . '</span>';
$label .= '<span class="topbar-profile-icon">' .
              '<i class="fa fa-user-circle-o"></i>' .
          '</span>';
$label .= '<span class="topbar-profile-text">' . $editProfileText . '</span>';

echo Nav::widget([
    'encodeLabels' => false,
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => [
        [
            'label' => $label,
            'options' => ['class' => 'topbar-profile-settings'],
            'dropdownClass' => 'dropdown-menu-right',
            'items' => [
                [
                    'label' => Yii::t('element', 'TOPBAR_EDIT_PROFILE'),
                    'url' => ['/admin/edit-my-profile', 'lang' => Yii::$app->language],
                    'options' => ['class' => 'edit-account'],
                ],
                [
                    'label' => Yii::t('element', 'TOPBAR_EDIT_PASSWORD'),
                    'url' => ['/admin/change-my-password', 'lang' => Yii::$app->language],
                    'options' => ['class' => 'edit-account-pasw'],
                ],
                [
                    'label' => Yii::t('element', 'TOPBAR_LOGOUT'),
                    'url' => ['site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                ],
            ],

        ],
    ],
]);