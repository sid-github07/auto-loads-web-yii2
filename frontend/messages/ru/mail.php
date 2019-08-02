<?php

return [
    'FAQ_FEEDBACK_ADMIN_SUBJECT' => 'Помощь #[{serialNumber}]: {question}',
    'FAQ_FEEDBACK_ADMIN_FAQ_QUESTION' => 'Вопрос ЧЗВ',
    'FAQ_FEEDBACK_ADMIN_REQUEST_ID' => 'ID запроса',
    'FAQ_FEEDBACK_ADMIN_REQUEST_NUMBER' => '{serialNumber}',
    'FAQ_FEEDBACK_ADMIN_QUESTION_NAME' => 'Название',
    'FAQ_FEEDBACK_ADMIN_QUESTION' => '{question}',
    'FAQ_FEEDBACK_ADMIN_EMAIL_NAME' => 'Адрес эл. почты',
    'FAQ_FEEDBACK_ADMIN_EMAIL' => '{clientEmail}',
    'FAQ_FEEDBACK_ADMIN_IS_SOLVED_NAME' => 'Вы получили ответ на свой вопрос',
    'FAQ_FEEDBACK_ADMIN_IS_SOLVED' => '{isSolved}',
    'FAQ_FEEDBACK_ADMIN_COMMENT_NAME' => 'Комментарий',
    'FAQ_FEEDBACK_ADMIN_COMMENT' => '{comment}',
    'FAQ_FEEDBACK_CLIENT_SUBJECT' => 'Помощь #[{serialNumber}]: {question}',
    'FAQ_FEEDBACK_CLIENT_BODY_1' => '<p>Здравствуйте,</p>' .
                                    '<p>Ваш запрос успешно зарегистрирован.</p>' .
                                    '<p>Ответ получите в течение 1 дня.</p>',
    'FAQ_FEEDBACK_CLIENT_REQUEST_ID' => 'ID запроса',
    'FAQ_FEEDBACK_CLIENT_REQUEST_NUMBER' => '{serialNumber}',
    'FAQ_FEEDBACK_CLIENT_QUESTION_NAME' => 'Название',
    'FAQ_FEEDBACK_CLIENT_QUESTION' => '{question}',
    'FAQ_FEEDBACK_CLIENT_COMMENT_NAME' => 'Комментарий',
    'FAQ_FEEDBACK_CLIENT_COMMENT' => '{comment}',
    'FAQ_FEEDBACK_CLIENT_BODY_2' => 'С уважением, {companyName}',
    'USER_CARRIER_DOCUMENTS_REQUEST_SUBJECT' => 'Auto-loads регистрация',
    'USER_CARRIER_DOCUMENTS_REQUEST_BODY' => '<p>Здравствуйте,</p>' .
                                             '<p>Вы зарегистрировались на сайте как перевозчик. Просим прислать ' .
                                             'следующие документы, для сохранения категории Вашей регистрации как перевозчика:</p>' .
                                             '<ul>' .
                                                 '<li>Сканированную копию страхования CMR;</li>' .
                                                 '<li>Сканированную копию ЕС сообщества;</li>' .
                                                 '<li>Сканированную копию регистрации предприятия;</li>' .
                                             '</ul>' .
                                        '<p>С уважением, {companyName}</p>',
    'USER_SIGN_UP_CONFIRMATION_SUBJECT' => 'Регистрация {companyName}',
    'USER_SIGN_UP_CONFIRMATION_BODY_1' => '<p>Здравствуйте,</p>' .
                                          '<p>Путём нажатия на эту ссылку успешно завершите регистрацию.</p>',
    'USER_SIGN_UP_CONFIRMATION_BODY_2' => '<p>С уважением,</p><p>{companyName}</p>',
    'USER_SIGN_UP_CONFIRMATION_BUTTON' => 'Подтвердить регистрацию',
    'USER_PASSWORD_RESET_REQUEST_SUBJECT' => 'Восстановление пароля Auto-loads',
    'USER_PASSWORD_RESET_REQUEST_BODY_1' => '<p>Здравствуйте,</p>' .
                                            '<p>Путём нажатия на эту ссылку перейдёте к окну для создания нового пароля, ' .
                                            'в котором сможете создать новый пароль.</p>',
    'USER_PASSWORD_RESET_REQUEST_BODY_2' => '<p>С уважением,</p><p>{companyName}</p>',
    'COMPANY_CONTACTS' => '<p>{companyName}  •  Zanavyku st. 46A, Kaunas, Lithuania</p>',
    'USER_PASSWORD_RESET_REQUEST_BUTTON' => 'Сброс пароля',
    'USER_REQUEST_EMAIL_CHANGE_SUBJECT' => 'Изменение эл. почты Auto-loads',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_NAME' => 'Эл. почта пользователя',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL' => '{userEmail}',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT_NAME' => 'Содержание письма',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT' => '{content}',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_SUBJECT' => 'Изменение кода НДС Auto-loads',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_NAME' => 'Эл. почта пользователя',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL' => '{userEmail}',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_CONTENT_NAME' => 'Содержание письма',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_CONTENT' => '{content}',
    'COMPANY_INVITATION_SEND_SUBJECT' => 'Приглашение для регистрации на {companyName} сайте',
    'COMPANY_INVITATION_SEND_BODY_1' => '<p>Здравствуйте,</p>' .
                                        '<p>Вы получили приглашение для регистрации на {companyName} сайте.</p>' .
                                        'Нажмите эту ссылку для перехода на страницу регистрации.',
    'COMPANY_INVITATION_SEND_BUTTON' => 'Регистр',
    'COMPANY_INVITATION_SEND_BODY_2' => '<p>С уважением, {companyName}</p>',
    'SUBSCRIPTION_REMINDER_SUBJECT' => '{companyName} Напоминание о продлении подписки',
    'SUBSCRIPTION_REMINDER_BODY' => '<p>Здравствуйте,</p>' .
                                    '<p>Напоминаем, что на {companyName} сайте заканчивается действие Вашей подписки.</p>' .
                                    '<p>С уважением, {companyName}</p>',
    'USER_SUCCESSFUL_PAYMENT_SUBJECT' => '{companyName} успешно оплачено',
    'USER_SUCCESSFUL_PAYMENT_BODY' => '<p>Здравствуйте,</p>' .
                                      '<p>Уведомляем Вас, что на {companyName} сайте Вы успешно произвели оплату, и ' .
                                      'Вам активирована услуга на подписку.</p>' .
                                      '<p>С уважением, {companyName}</p>',
    'LOAD_MY_LOADS_SUBJECT' => 'Размещение груза на {companyName} сайте',
    'LOAD_MY_LOADS_BODY_1' => '<p>Здравствуйте,</p>' .
                            '<p>Уведомляем Вас, что на {companyName} сайте Вы успешно разместили груз, ' .
                            'но груз ещё не активирован. Активировать груз можете путём нажатия на эту ссылку:</p>',
    'LOAD_MY_LOADS_BUTTON' => 'Активировать',
    'LOAD_MY_LOADS_BODY_2' => '<p>С уважением, {companyName}</p>',
    'LOAD_SUGGESTIONS_SUBJECT' => 'New loads suggestions',
    'LOAD_SUGGESTIONS_BODY_1' => '<p>Hello,</p>' .
                                'According to your searches ' .
                                'and registered location where are new load suggestions for you',
    'LOAD_SUGGESTIONS_BUTTON' => 'Preview',
    'LOAD_SUGGESTIONS_BODY_2' => '<p>Respectfully, {companyName}</p>',
    'INFORM_USER_ABOUT_UNPAID_SUBSCRIPTION_SUBJECT' => 'Напоминание о неоплаченном счёте на сайте {companyName}',
    'INFORM_USER_ABOUT_UNPAID_SUBSCRIPTION_BODY' => '<p> Здравствуйте,</p>'
                                                  . '<p>Сообщаем Вам, что на сайте {companyName} у вас есть неоплаченный счёт. Он прикреплен к этому письму.</p>'
                                                  . '<p>С уважением, {companyName}</p>',
    'INFORM_USER_ABOUT_EXPIRED_SUBSCRIPTION_SUBJECT' => '{companyName} срок действия подписки истекает',
    'INFORM_USER_ABOUT_EXPIRED_SUBSCRIPTION_BODY' => '<p>Здравствуйте,</p>'
                                                   . '<p>Сообщаем Вам, что срок действия Вашей подписки на сайте {companyName} истёк.</p>'
                                                   . '<p>С уважением, {companyName}</p>',
    'LOAD_SUGGESTIONS_BODY_3' => '<p>If you do not want to get load suggestions to your email click button below:</p>',
    'LOAD_SUGGESTIONS_REJECT_BUTTON' => 'Reject',
    'SUBSCRIPTION_REMINDER_SUBJECT' => '{companyName} subscription extend reminder',
    'SUBSCRIPTION_REMINDER_BODY' => '<p>Hello,</p>' .
                                    '<p>We would like to remind you that in {companyName} website will your subscription is ' .
                                    'about to end in 7 days</p>' .
                                    '<p>Respectfully, {companyName}</p>',
	'SUBSCRIPTION_REMINDER_SUBJECT' => '{companyName} subscription extend reminder',
    'SUBSCRIPTION_REMINDER_BODY' => '<p>Hello,</p>' .
                                    '<p>We would like to remind you that in {companyName} website will your subscription is ' .
                                    'about to end in 7 days</p>' .
                                    '<p>Respectfully, {companyName}</p>',
    'USER_SUCCESSFULL_CREDITCODE_PURCHASE_SUBJECT' => '{companyName} successful creditcode purchase',
    'USER_SUCCESSFULL_CREDITCODE_PURCHASE_BODY' => '<p>Hello,</p>' .
                                    '<p>We are glad to inform you that you successfully purchased ' .
                                    'a credit code on the {companyName} website which can used now ' .
                                    'on our website. Your creditcode is {creditCode}.</p>' .
                                    '<p>Respectfully, {companyName}</p>',
];