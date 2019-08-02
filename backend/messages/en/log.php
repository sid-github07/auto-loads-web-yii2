<?php

use yii\helpers\Html;

return [
    // Create
    'USER_SIGNED_UP' => 'Vartotojas užsiregistravo į sistemą',
    'USER_REGISTERED_COMPANY' => 'Vartotojas užregistravo įmonę (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{id}', 'imones-redagavimas?id={id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPLOADED_DOCUMENT' => 'Vartotojas įkėlė ' . Html::tag('b', '{type}') . ' dokumentą (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{company_id}', 'imones-redagavimas?id={company_id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_INVITES_TO_JOIN_COMPANY' => 'Vartotojas pakvietė ' . Html::tag('b', '{email}') . ' prisijungti prie įmonės (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{company_id}', 'imones-redagavimas?id={company_id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_JOINS_TO_COMPANY' => 'Vartotojas prisijungė prie įmonės (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{id}', 'imones-redagavimas?id={id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_ANNOUNCED_LOAD' => 'Vartotojas paskelbė krovinį (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_BOUGHT_SUBSCRIPTION' => 'Vartotojas įsigijo prenumeratą ' . Html::tag('b', '{name}'),
    'USER_ANNOUNCED_CAR_TRANSPORTER' => 'Vartotojas paskelbė autovežį (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162a',
        ]) . ': ' .
        Html::a('{id}', 'autovezio-perziura/{id}', [
            'class' => 'A-C-163a',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_SET_LOAD_OPEN_CONTACTS' => 'Vartotojas nustatė atvirus kontaktus kroviniui (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162a',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163a',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_SET_TRANSPORTER_OPEN_CONTACTS' => 'Vartotojas nustatė atvirus kontaktus autovežiui (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162a',
        ]) . ': ' .
        Html::a('{id}', 'autovezio-perziura/{id}', [
            'class' => 'A-C-163a',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',

    // Read
    'USER_REVIEWED_LOAD_INFO' => 'Vartotojas peržiūrėjo krovinio informaciją (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_REVIEWED_CAR_TRANSPORTER_INFO' => 'Vartotojas peržiūrėjo autovežio informaciją (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'autovezio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',

    // Update
    'UPDATED_FIELD' =>
        Html::beginTag('li') .
            'Laukelį ' . Html::tag('b', '"{label}"') .
            ' pakeitė iš ' . Html::tag('b', '{oldValue}') .
            ' į ' . Html::tag('b', '{newValue}') .
        Html::endTag('li'),
    'UPDATED_MULTIPLE_FIELDS' =>
        Html::beginTag('li') .
            '{object} (' .
            Html::tag('span', 'ID', [
                'class' => 'A-C-162',
            ]) . ': ' .
            Html::a('{id}', 'krovinio-perziura/{id}', [
                'class' => 'A-C-163',
                'data-pjax' => 0,
                'target' => '_blank',
            ]) . ')' .
            ' laukelį ' . Html::tag('b', '"{label}"') .
            ' pakeitė iš ' . Html::tag('b', '{oldValue}') .
            ' į ' . Html::tag('b', '{newValue}') .
        Html::endTag('li'),
    'USER_UPDATED_COMPANY_INFO' => 'Vartotojas atnaujino įmonės duomenis (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{id}', 'imones-redagavimas?id={id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPDATED_COMPANY_DOCUMENT' => 'Vartotojas atnaujino įmonės ' . Html::tag('b', '{type}') . ' dokumentą (' .
        Html::tag('span', 'Įmonė', [
            'class' => 'A-C-160',
        ]) . ': ' .
        Html::a('{company_id}', 'imones-redagavimas?id={company_id}&tab=company-info', [
            'class' => 'A-C-161',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPDATED_LOAD_INFO' => 'Vartotojas atnaujino krovinio informaciją (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPDATED_LOAD_ACTIVE_STATUS' => 'Vartotojas pakeitė krovinio rodymą (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPDATED_MULTIPLE_LOADS_ACTIVE_STATUS' => 'Vartotojas pakeitė kelių krovinių rodymą:',
    'USER_UPDATED_LOAD_CARS' => 'Vartotojas atnaujino krovinio automobilių informaciją (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_UPDATED_PROFILE_INFO' => 'Vartotojas atnaujino savo informaciją:',
    'USER_UPDATED_LANGUAGES' => 'Vartotojas atnaujino kalbas, kuriomis kalba',
    'USER_UPDATED_PASSWORD' => 'Vartotojas pasikeitė slaptažodį',
    'USER_UPDATED_CAR_TRANSPORTER_INFO' => 'Vartotojas atnaujino autovežio informaciją (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'autovežio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',

    // Delete
    'USER_REMOVED_LOAD' => 'Vartotojas ištrynė krovinį (' .
        Html::tag('span', 'ID', [
            'class' => 'A-C-162',
        ]) . ': ' .
        Html::a('{id}', 'krovinio-perziura/{id}', [
            'class' => 'A-C-163',
            'data-pjax' => 0,
            'target' => '_blank',
        ]) . ')',
    'USER_REMOVED_MULTIPLE_LOADS' => 'Vartotojas ištrynė kelis krovinius:',
    'USER_REMOVED_MULTIPLE_CAR_TRANSPORTERS' => 'Vartotojas ištrynė kelis autovežius:',

    // Search
    'USER_SEARCHED_FOR_LOAD' => 'Searched for load:' .
        Html::beginTag('ul') .
            Html::tag('li', 'search radius: ' . Html::tag('b', '"{radius} km"')) .
            Html::tag('li', 'load point: ' . Html::tag('b', '"{loadArea}"')) .
            Html::tag('li', 'unload point: ' . Html::tag('b', '"{unloadArea}"')) .
            Html::tag('li', 'type: ' . Html::tag('b', '"{type}"')) .
        Html::endTag('ul'),
    'USER_SEARCHED_FOR_CAR_TRANSPORTER' => 'Searched for car transporter:' .
        Html::beginTag('ul') .
            Html::tag('li', 'search radius: ' . Html::tag('b', '"{radius} km"')) .
            Html::tag('li', 'load point: ' . Html::tag('b', '"{loadLocation}"')) .
            Html::tag('li', 'unload point: ' . Html::tag('b', '"{unloadLocation}"')) .
        Html::endTag('ul'),

    // Login
    'USER_LOGGED_IN' => 'Vartotojas prisijungė į sistemą',

    // System message
    'USER_REQUESTED_VAT_CODE_CHANGE' => 'Vartotojas išsiuntė el. laišką administratoriui dėl PVM kodo keitimo',
    'USER_RECEIVED_EMAIL_FOR_ANNOUNCING_LOAD' => 'Vartotojui buvo išsiųstas el. laiškas dėl krovinio aktyvavimo',
    'USER_RECEIVED_SUBSCRIPTION_REMINDER_EMAIL' => 'Vartotojui buvo išsiųstas priminimo el. laiškas ' .
                                                   'apie besibaigiančią prenumeratą',
    'USER_RECEIVED_CARRIER_DOCUMENTS_REQUEST' => 'Vartotojui buvo išsiųstas el. laiškas ' .
                                                 'su prašymu atsiųsti vežėjo dokumentus',
    'USER_REQUESTED_EMAIL_CHANGE' => 'Vartotojas išsiuntė el. laišką administratoriui dėl el. pašto keitimo',
    'USER_RECEIVED_SING_UP_EMAIL' => 'Vartotojui buvo išsiųstas el. laiškas ' .
                                     'informuojantis apie naujai sukurtą paskyrą jo el. pašto adresu',
    'USER_REQUESTED_PASSWORD_RESET' => 'Vartotojui buvo išsiųstas el. laiškas ' .
                                                   'su slaptažodžio priminimo nuoroda',
    'USER_RECEIVED_SIGN_UP_CONFIRMATION' => 'Vartotojui buvo išsiųstas el. laiškas su registracijos patvirtinimo nuoroda',
    'USER_RECEIVED_SUCCESSFUL_PAYMENT_EMAIL' => 'Vartotojui buvo išsiųstas el. laiškas ' .
                                                'informuojantis apie sėkmingai įsigytą prenumeratą',
    'USER_RECEIVED_EXPIRED_SUBSCRIPTION_EMAIL' => 'Vartotojui buvo išsiųstas el. laiškas ' .
                                                  'informuojantis apie pasibaigusią prenumeratą',

    // Failed action
    'USER_GOT_ERROR_MESSAGE' => 'Vartotojas gavo klaidos pranešimą: {message}',
];
