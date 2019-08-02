<?php

return [
    'FAQ_FEEDBACK_ADMIN_SUBJECT' => 'Ajutor #[{serialNumber}]: {question}',
    'FAQ_FEEDBACK_ADMIN_BODY' => '<div>Întrebare din  FAQ</div>' .'<div> ID-ul căutării: {serialNumber}</div>' .'<div>Denumire: {question}</div>' .'<div>Adresa de e-mail: {clientEmail}</div>' .'<div>Ați primit răspunsul la întrebarea dvs.?: {isSolved}</div>' .'<div>Comentariu: {comment}</div>',
    'FAQ_FEEDBACK_ADMIN_FAQ_QUESTION' => 'FAQ question',
    'FAQ_FEEDBACK_ADMIN_REQUEST_ID' => 'Request ID',
    'FAQ_FEEDBACK_ADMIN_REQUEST_NUMBER' => '{serialNumber}',
    'FAQ_FEEDBACK_ADMIN_QUESTION_NAME' => 'Question name',
    'FAQ_FEEDBACK_ADMIN_QUESTION' => '{question}',
    'FAQ_FEEDBACK_ADMIN_EMAIL_NAME' => 'Email',
    'FAQ_FEEDBACK_ADMIN_EMAIL' => '{clientEmail}',
    'FAQ_FEEDBACK_ADMIN_IS_SOLVED_NAME' => 'Has your question been answered',
    'FAQ_FEEDBACK_ADMIN_IS_SOLVED' => '{isSolved}',
    'FAQ_FEEDBACK_ADMIN_COMMENT_NAME' => 'Comment',
    'FAQ_FEEDBACK_ADMIN_COMMENT' => '{comment}',
    'FAQ_FEEDBACK_CLIENT_SUBJECT' => 'Ajutor #[{serialNumber}]: {question}',
    'FAQ_FEEDBACK_CLIENT_BODY_1' => '<p>Bună ziua,</p>' .
        '<div>Cererea dvs. a fost înregistrată cu succes.</div>' .'<div> Veți primi un răspuns în termen de o zi.</div>' .'<div> ID-ul cererii: <b>{serialNumber}</b>.</div>' .'<div>Denumire: <b>{question}</b></div>' .
        '<p>Cu stimă, {companyName}</p>',
    'FAQ_FEEDBACK_CLIENT_REQUEST_ID' => 'Request ID',
    'FAQ_FEEDBACK_CLIENT_REQUEST_NUMBER' => '{serialNumber}',
    'FAQ_FEEDBACK_CLIENT_QUESTION_NAME' => 'Question',
    'FAQ_FEEDBACK_CLIENT_QUESTION' => '{question}',
    'FAQ_FEEDBACK_CLIENT_COMMENT_NAME' => 'Comment',
    'FAQ_FEEDBACK_CLIENT_COMMENT' => '{comment}',
    'FAQ_FEEDBACK_CLIENT_BODY_2' => 'Respectfully, {companyName}',
    'USER_CARRIER_DOCUMENTS_REQUEST_SUBJECT' => ' Înregistrare Auto-loads ',
    'USER_CARRIER_DOCUMENTS_REQUEST_BODY' => '<p>Bună ziua,</p>' .'<div>pe site-ul {companyName} v-ați înregistrat ca transportator. ' .'Vă rugăm să trimiteți următoarele documente, pentru că categoria ' .' înregistrării dvs. să rămână cea a transportatorului:</div>' .'<ul>' .
        '<li>copie scanată a asigurării CMR;</li>' .
        '<li>copie scanată documente de comunitatea UE;</li>' .
        '<li> copie scanată a înregistrării Companiei;</li>' .'</ul>' .
        '<p>Cu stimă, {companyName}</p>',
    'USER_SIGN_UP_CONFIRMATION_SUBJECT' => 'Înregistrare {companyName}',
    'USER_SIGN_UP_CONFIRMATION_BODY_1' => '<p>Bună ziua,</p>' .'<div>După apăsarea <a href="{url}">acestui link</a> ' .'veți finaliza înregistrarea cu succes.</div>',
    'USER_SIGN_UP_CONFIRMATION_BODY_2' => '<p>Cu stimă, {companyName}</p>',
    'USER_SIGN_UP_CONFIRMATION_BUTTON' => 'Confirm registration',
    'USER_PASSWORD_RESET_REQUEST_SUBJECT' => 'recuperarea parolei de auto-loads ',
    'USER_PASSWORD_RESET_REQUEST_BODY_1' => '<p>Bună ziua,</p>' .'<div>După ce ați apăsat <a href="{url}">acest link</a> ' . 'veți fi condus la o nouă fereastră de creare a parolei, ' .
    'unde puteți crea o nouă parolă.</div>',
    'USER_PASSWORD_RESET_REQUEST_BODY_2' => '<p>Cu stimă, {companyName}</p>',
    'COMPANY_CONTACTS' => '<p>{companyName}  •  Zanavyku st. 46A, Kaunas, Lithuania</p>',
    'USER_PASSWORD_RESET_REQUEST_BUTTON' => 'Reset password',
    'USER_REQUEST_EMAIL_CHANGE_SUBJECT' => 'Schimbarea emailului de auto-loads',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_NAME' => 'User email',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL' => '{userEmail}',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT_NAME' => 'Email content',
    'USER_REQUEST_EMAIL_CHANGE_EMAIL_CONTENT' => '{content}',
    'USER_REQUEST_EMAIL_CHANGE_BODY' => '<p> Emailul utilizatorului: <b>{userEmail}</b></p>' .'<div>Conținutul mesajului:</div><div>{content}</div>' .'<p>Cu stimă, {companyName}</p>',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_SUBJECT' => 'Schimbarea codului TVA de auto-loads',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_NAME' => 'User email',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL' => '{userEmail}',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_CONTENT_NAME' => 'Email content',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_EMAIL_CONTENT' => '{content}',
    'COMPANY_REQUEST_VAT_CODE_CHANGE_BODY' => '<p>Emailul utilizatorului: <b>{userEmail}</b></p>' .'<div>Conținutul mesajului:</div><div>{content}</div>' .
    '<p>Cu stimă, {companyName}</p>',
    'COMPANY_INVITATION_SEND_SUBJECT' => 'Invitație de înregistrare pe site-ul {companyName}',
    'COMPANY_INVITATION_SEND_BODY_1' => '<p>Bună ziua,</p>' .
    '<div>Ați primit invitația de înregistrare pe site-ul {companyName}.</div>' .'<div>Apăsați  <a href="{url}">acest link</a>, ' . 'pentru a accesa pagina de înregistrare.</div>' .
    '<p>Cu stimă, {companyName}</p>',
    'COMPANY_INVITATION_SEND_BUTTON' => 'Register',
    'COMPANY_INVITATION_SEND_BODY_2' => '<p>Respectfully, {companyName}</p>',
    'SUBSCRIPTION_REMINDER_SUBJECT' => '{companyName}  reamintire pentru reînnoirea abonamentului',
    'SUBSCRIPTION_REMINDER_BODY' => '<p>Bună ziua,</p>' .
    '<div> Suntem încântați să vă reamintim pe site-ul {companyName} ' .
    ' expiră abonamentul dvs.</div>' .
    '<p>Cu stimă, {companyName}</p>',
    'USER_SUCCESSFUL_PAYMENT_SUBJECT' => '{companyName} plătit cu succes',
    'USER_SUCCESSFUL_PAYMENT_BODY' => '<p>Bună ziua,</p>' .
    '<div>Vă informăm că pe site-ul {companyName} ' . 'ați finalizat cu succes plata și aveți activat un serviciu de abonament.</div>' .
    '<p>Cu stimă, {companyName}</p>',
    'LOAD_MY_LOADS_SUBJECT' => 'Postarea unei încărcături pe site-ul {companyName}',
    'LOAD_MY_LOADS_BODY_1' => '<p>Bună ziua</p>' .'<div>Vă informăm că pe site-ul {companyName} ați postat cu succes o încărcătură, ' .'dar deocamdată încărcătura nu este activată. ' .'Puteți activa încărcătura făcând clic pe <a href="{url}">acest link</a>.</div>' .
    '<p>Cu stimă, {companyName}</p>',
    'LOAD_SUGGESTIONS_SUBJECT' => 'New loads suggestions',
    'LOAD_SUGGESTIONS_BODY_1' => '<p>Hello,</p>' .
                                'According to your searches ' .
                                'and registered location where are new load suggestions for you',
    'LOAD_SUGGESTIONS_BUTTON' => 'Preview',
    'LOAD_SUGGESTIONS_BODY_2' => '<p>Respectfully, {companyName}</p>',
    'LOAD_MY_LOADS_BUTTON' => 'Activate',
    'LOAD_MY_LOADS_BODY_2' => '<p>Respectfully, {companyName}</p>',
    'INFORM_USER_ABOUT_UNPAID_SUBSCRIPTION_SUBJECT' => 'Aducere aminte a unei facturi neplătite pe pagină de internet a {companyName}',
    'INFORM_USER_ABOUT_UNPAID_SUBSCRIPTION_BODY' => '<p>Bună ziua,</p>'
                                                  . '<p>vă informam că pe pagina de internet a {companyName} aveți o factura neplătită. O atașam la această scrisoare.</p>'
                                                  . '<p>Cu stima, {companyName}</p>',
    'INFORM_USER_ABOUT_EXPIRED_SUBSCRIPTION_SUBJECT' => 'expirarea abonamentului a {companyName} ',
    'INFORM_USER_ABOUT_EXPIRED_SUBSCRIPTION_BODY' => '<p>Bună ziua,</p>'
                                                   . '<p>vă informam că abonamentul dvs. pe pagina de internet a {companyName} a expirat.</p>'
                                                   . '<p>Cu stima, {companyName}</p>',
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