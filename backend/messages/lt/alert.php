<?php

return [
    'ERROR_MESSAGE_OCCURRED_ERROR' => 'Aukščiau esanti klaida atsirado, kol serveris bandė apdoroti Jūsų užklausą.',
    'ERROR_MESSAGE_CONTACT_US' => 'Prašome susisiekti su mumis, jeigu manote jog tai serverio klaida.',
    'ERROR_ACTION_NEEDS_RIGHTS' => 'Nėra veiksmui reikalingų teisių',
    
    // Admin
    'ADMIN_HACK_MESSAGE' => 'Redagavimo klaida',
    
    // Admin login'
    'LOGIN_INCORRECT_EMAIL_OR_PASSWORD' => 'Neteisingas elektroninis paštas arba slaptažodis',
    
    // Admin index user edit
    'ADMIN_USER_EDIT_SUCCESS' => 'Informacija atnaujinta sėkmingai',
    'ADMIN_USER_EDIT_ERROR' => 'Informacijos atnaujinti nepavyko',
    
    // Admin index user password edit
    'ADMIN_USER_PASSWORD_EDIT_SUCCESS' => 'Slaptažodis pakeistas sėkmingai',
    'ADMIN_USER_PASSWORD_EDIT_ERROR' => 'Slaptažodžio pakeisti nepavyko',
    
    // Admin index user delete
    'ADMIN_USER_DELETE_SUCCESS' => 'Vartotojas pašalintas sėkmingai',
    'ADMIN_USER_DELETE_ERROR' => 'Vartotojaus pašalinti nepavyko',
    
    // Admin create
    'ADMIN_CREATE_SUCCESS' => 'Vartotojas sukurtas sėkmingai',
    'ADMIN_CREATE_ERROR' => 'Vartotojo sukurti nepavyko',
    
    // Company edit
    'COMPANY_NOT_FOUND_BY_ID' => 'Tokios įmonės nėra',
    'INVALID_ARCHIVE_STATUS' => 'Neteisingai pasirinktas archyvavimo statusas',
    'COMPANY_ARCHIVE_STATUS_CHANGED_SUCCESSFULLY' => 'Įmonės archyvavimo statusas pakeistas sėkmingai.',
    'COMPANY_INFO_CHANGE_ERROR' => 'Įmonės rekvizitų keitimas nepavyko',
    'COMPANY_INFO_CHANGE_SUCCESSFUL' => 'Įmonės rekvizitai sėkmingai pakeisti',

    'CLIENT_COMPANY_NOT_FOUND' => 'Tokios įmonės rasti nepavyko',

    // Load
    'LOAD_PREVIEW_LOAD_NOT_FOUND' => 'Krovinio rasti nepavyko. Bandykite dar kartą.',
    'CANNOT_REMOVE_LOAD' => 'Krovinio pašalinti nepavyko',
    'LOAD_REMOVED_SUCCESSFULLY' => 'Krovinys buvo sėkmingai pašalintas',
    
    //Company document
    'ADD_DOCUMENT_INVALID_TYPE' => 'Neteisingai nurodytas dokumento tipas',
    'ADD_DOCUMENT_CREATED_SUCCESSFULLY' => 'Dokumentas išsaugotas sėkmingai',
    'ADD_DOCUMENT_CANNOT_CREATE' => 'Nepavyko išsaugoti dokumento. Bandykite dar kartą',
    'DOWNLOAD_DOCUMENT_INVALID_TYPE' => 'Neteisingai nurodytas dokumento tipas',
    'DOWNLOAD_DOCUMENT_FILE_NOT_EXISTS' => 'Toks failas neegzistuoja',
    'DOCUMENT_UPLOAD_CANNOT_SAVE' => 'Nepavyko išsaugoti dokumento. Bandykite dar kartą',
    'REMOVE_DOCUMENT_INVALID_TYPE' => 'Neteisingai nurodytas dokumento tipas',
    'REMOVE_DOCUMENT_REMOVED_SUCCESSFULLY' => 'Dokumentas pašalintas sėkmingai',
    'REMOVE_DOCUMENT_CANNOT_REMOVE' => 'Dokumento pašalinti nepavyko. Bandykite dar kartą',
    'NOT_FOUND_COMPANY_DOCUMENT_BY_TYPE' => 'Dokumento, pagal nurodytą tipą, rasti nepavyko',

    // Bill
    'BILL_DOWNLOAD_ID_NOT_DEFINED' => 'Neteisingai nurodytas sąskaitos identifikacinis kodas',
    'BILL_DOWNLOAD_INVOICE_NOT_FOUND' => 'Tokios sąskaitos rasti nepavyko',
    'BILL_DOWNLOAD_FILE_NOT_FOUND' => 'Tokios sąskaitos failo rasti nepavyko',
    'BILL_REGENERATE_INVALID_USER_INVOICE_ID' => 'Neteisingai nurodytas vartotojo sąskaitos identifikacinis kodas',
    'BILL_REGENERATE_USER_INVOICE_NOT_FOUND' => 'Tokios sąskaitos faktūros rasti nepavyko',
    'BILL_REGENERATE_USER_COMPANY_NOT_FOUND' => 'Nepavyko rasti vartotojo įmonės',
    'BILL_REGENERATE_INVALID_USER_INVOICE_DATA' => 'Neteisingi sąskaitos-faktūros duomenys',
    'BILL_REGENERATE_INVALID_USER_SERVICE_DATA' => 'Neteisingi vartotojo privilegijos duomenys',
    'BILL_REGENERATE_CANNOT_UPDATE_USER_INVOICE' => 'Nepavyko atnaujinti vartotojo sąskaitos-faktūros duomenų. ' .
        'Vidinė serverio klaida',
    'BILL_REGENERATE_REGENERATED_SUCCESSFULLY' => 'Vartotojo sąskaita-faktūra sėkmingai sugeneruota iš naujo.',
    'BILL_MARK_AS_PAID_USER_PRE_INVOICE_NOT_FOUND' => 'Tokios išankstinės sąskaitos-faktūros rasti nepavyko',
    'BILL_MARK_USER_SERVICE_AS_PAID_INVALID_USER_SERVICE' => 'Neteisingi vartotojo paslaugos duomenys',
    'BILL_ACTIVATE_USER_SERVICE_INVALID_ACTIVE_USER_SERVICE' => 'Neteisingi aktyvios vartotojo paslaugos duomenys',
    'BILL_ACTIVATE_USER_SERVICE_INVALID_USER_SERVICE_DATA' => 'Neteisingi vartotojo paslaugos duomenys',
    'BILL_CREATE_USER_INVOICE_USER_COMPANY_NOT_FOUND' => 'Nepavyko rasti vartotojo įmonės',
    'BILL_CREATE_USER_INVOICE_INVALID_USER_INVOICE_DATA' => 'Neteisingi sąskaitos-faktūros duomenys',
    'BILL_CREATE_USER_INVOICE_CANNOT_GENERATE_USER_INVOICE' => 'Nepavyko sugeneruoti sąskaitos-faktūros failo',
    'BILL_MARK_AS_PAID_CANNOT_SEND_EMAIL' => 'Nepavyko išsiųsti informacinio laiško vartotojui',
    'BILL_MARK_AS_PAID_SUCCESSFULLY_MARKED_AS_PAID' => 'Vartotojo sąskaita-faktūra sėkmingai pažymėta kaip apmokėta',
    'SEND_PRE_INVOICE_DOCUMENT_TO_USER_PRE_INVOICE_NOT_FOUND' => 'Tokios išankstinės sąskaitos-faktūros rasti nepavyko',
    'SEND_PRE_INVOICE_DOCUMENT_TO_USER_NOT_PRE_INVOICE' => 'Neteisingas išankstinės sąskaitos-faktūros tipas',
    'SEND_PRE_INVOICE_DOCUMENT_TO_USER_DOCUMENT_NOT_EXIST' => 'Toks išankstinės sąskaitos-faktūros dokumentas neegzistuoja',
    'SEND_PRE_INVOICE_DOCUMENT_TO_USER_CANNOT_SEND_MAIL' => 'Nepavyko išsiųsti išansktinės sąskaitos-faktūros dokumento',
    'SEND_PRE_INVOICE_DOCUMENT_TO_USER_SEND_SUCCESSFULLY' => 'Išankstinės sąskaitos-faktūros dokumentas išsiųstas sėkmingai.',
    'INVOICE_CREATE_BY_ADMIN_COMPANY_NOT_FOUND' => 'Nepavyko rasti įmonės sąskaitai-faktūrai',

    // User
    'CLIENT_COMPANY_USER_EDIT_FORM_USER_NOT_FOUND' => 'Tokio vartotojo rasti nepavyko',
    'CLIENT_COMPANY_USER_EDIT_FORM_COMPANY_NOT_FOUND' => 'Vartotojo įmonės rasti nepavyko',
    'CLIENT_EDIT_COMPANY_USER_USER_NOT_FOUND' => 'Tokio vartotojo rasti nepavyko',
    'CLIENT_EDIT_COMPANY_USER_CANNOT_LOAD_USER_DATA' => 'Nepavyko užkrauti vartotojo duomenų',
    'CLIENT_EDIT_COMPANY_USER_INVALID_USER_DATA' => 'Neteisingi vartotojo duomenys',
    'CLIENT_EDIT_COMPANY_USER_CANNOT_SAVE_USER_DATA' => 'Nepavyko išsaugoti vartotojo duomenų',
    'CLIENT_EDIT_COMPANY_USER_CANNOT_UPDATE_USER_LANGUAGES' => 'Nepavyko išsaugoti kalbų, kuriomis kalbas vartotojas',
    'CLIENT_EDIT_COMPANY_USER_SAVED_SUCCESSFULLY' => 'Vartotojo duomenys išsaugoti sėkmingai',
    'CLIENT_ADD_COMPANY_USER_CANNOT_LOAD_USER_DATA' => 'Nepavyko užkrauti vartotojo duomenų',
    'CLIENT_ADD_COMPANY_USER_INVALID_USER_DATA' => 'Neteisingi vartotojo duomenys',
    'CLIENT_ADD_COMPANY_USER_CANNOT_SAVE_USER_DATA' => 'Nepavyko išsaugoti vartotojo duomenų',
    'CLIENT_ADD_COMPANY_USER_CANNOT_SAVE_USER_LANGUAGES' => 'Nepavyko išsaugoti kalbų, kuriomis kalba vartotojas',
    'CLIENT_ADD_COMPANY_USER_CANNOT_ADD_USER_TO_COMPANY' => 'Nepavyko priskirti vartotojo įmonei',
    'CLIENT_ADD_COMPANY_USER_CANNOT_SEND_EMAIL' => 'Nepavyko išsiųsti vartotojui informacinio laiško ' .
                                                   'informuojant apie naujai sukurtą vartotojo paskyrą',
    'CLIENT_ADD_COMPANY_USER_CREATED_SUCCESSFULLY' => 'Įmonės vartotojas sukurtas sėkmingai.',

    // Client controller
    'CHANGE_SUBSCRIPTION_END_DATE_NOT_FOUND_USER_ACTIVE_SERVICE' => 'Tokios vartotojo privilegijos rasti nepavyko.',
    'INVALID_SUBSCRIPTION_END_DATE' => 'Neteisinga privilegijos pabaigos galiojimo data.',
    'SUBSCRIPTION_END_DATE_WAS_NOT_CHANGED' => 'Nepavyko išsaugoti vartotojo privilegijos galiojimo datos.',
    'SUBSCRIPTION_END_DATE_CHANGED_SUCCESSFULLY' => 'Vartotojo privilegijos galiojimo data išsaugota sėkmingai.',
    'CHANGE_SUBSCRIPTION_STATUS_NOT_FOUND_USER_ACTIVE_SERVICE' => 'Tokios vartotojo privilegijos rasti nepavyko.',
    'INVALID_SUBSCRIPTION_STATUS' => 'Neteisingas privilegijos aktyvumo statusas.',
    'SUBSCRIPTION_STATUS_WAS_NOT_CHANGED' => 'Nepavyko pakeisti vartotojo privilegijos aktyvumo statuso.',
    'SUBSCRIPTION_STATUS_CHANGED_SUCCESSFULLY' => 'Vartotojo privilegijos aktyvumo statusas pakeistas sėkmingai.',
    'CREATE_USER_SERVICE_ACTIVE_SERVICE_NOT_FOUND' => 'Tokios privilegijos rasti nepavyko.',
    'CREATE_USER_SERVICE_ACTIVE_INVALID_USER_SERVICE_ACTIVE_DATA' => 'Neteisingi privilegijos duomenys.',
    'CREATE_USER_SERVICE_ACTIVE_CANNOT_SAVE_USER_SERVICE_ACTIVE_DATA' => 'Nepavyko sukurti naujos privilegijos.',
    'CREATE_USER_SERVICE_INVALID_USER_SERVICE_DATA' => 'Neteisingi privilegijos duomenys.',
    'CREATE_USER_SERVICE_CANNOT_SAVE_USER_SERVICE_DATA' => 'Nepavyko sukurti naujos privilegijos.',
    'UPDATE_USER_CREDITS_USER_NOT_FOUND' => 'Tokio vartotojo rasti nepavyko.',
    'UPDATE_USER_CREDITS_INVALID_USER_DATA' => 'Neteisingi vartotojo duomenys.',
    'UPDATE_USER_CREDITS_CANNOT_SAVE_USER_DATA' => 'Nepavyko išsaugoti vartotojo duomenų.',
    'CREATE_NEW_SUBSCRIPTION_SAVED_SUCCESSFULLY' => 'Nauja privilegija sukurta sėkmingai.',
    'ADD_COMPANY_COMMENT_INVALID_DATA' => 'Neteisingi įmonės komentaro duomenys.',
    'ADD_COMPANY_COMMENT_CANNOT_SAVE' => 'Nepavyko išsaugoti įmonės komentaro.',
    'ADD_COMPANY_COMMENT_SAVED_SUCCESSFULLY' => 'Įmonės komentaras išsaugotas sėkmingai.',
    'SHOW_COMPANY_COMMENTS_COMPANY_NOT_FOUND' => 'Tokios įmonės rasti nepavyko',
    'REMOVE_COMPANY_COMMENT_NONE_COMMENT_REMOVED' => 'Nepavyko ištrinti komentaro. ' .
                                                     'Toks komentaras neegzistuoja arba jau ištrintas',
    'REMOVE_COMPANY_COMMENT_REMOVED_SUCCESSFULLY' => 'Komentaras ištrintas sėkmingai.',
    'CREATE_PRE_INVOICE_INVALID_DATA' => 'Neteisingi išankstinės sąskaitos duomenys.',
    'CREATE_PRE_INVOICE_INVALID_USER_SERVICE_DATA' => 'Neteisingi vartotojo privilegijos duomenys.',
    'CREATE_PRE_INVOICE_CANNOT_SAVE_USER_SERVICE' => 'Nepavyko išsaugoti vartotojo privilegijos.',
    'CREATE_PRE_INVOICE_CANNOT_SAVE_USER_INVOICE' => 'Nepavyko išsaugoti vartotojo išankstinės sąskaitos.',
    'CREATE_PRE_INVOICE_CREATED_SUCCESSFULLY' => 'Išankstinė sąskaita sukurta sėkmingai.',
	'SUBSCRIPTION_ALREADY_EXIST' => 'Vartotojas jau turi aktyvia prenumeratą',

    'COMPANY_NOT_FOUND' => 'Tokios įmonės rasti nepavyko',
    'INVALID_COMPANY_DATA' => 'Neteisingi įmonės duomenys',
    'COMPANY_NAME_CHANGED_SUCCESSFULLY' => 'Įmonės pavadinimas pakeistas sėkmingai.',

    // AdminController
    'INVALID_DATA' => 'Neteisingi duomenys',
    'NEW_ADMINISTRATOR_ADDED_SUCCESSFULLY' => 'Naujas administratorius sukurtas sėkmingai.',
    'NEW_MODERATOR_ADDED_SUCCESSFULLY' => 'Naujas moderatorius sukurtas sėkmingai.',
    'ADMIN_NOT_FOUND' => 'Tokio administratoriaus rasti nepavyko.',
    'ADMIN_DATA_SAVED_SUCCESSFULLY' => 'Duomenys išsaugoti sėkmingai.',
    'PASSWORD_CHANGED_SUCCESSFULLY' => 'Slaptažodis pakeistas sėkmingai.',
    'INVALID_ADMIN_ID' => 'Neteisingai nurodytas administratoriaus ID',
    'CANNOT_REMOVE_ITSELF_ACCOUNT' => 'Savo paskyros pašalinti negalima',
    'ADMINISTRATOR_REMOVED_SUCCESSFULLY' => 'Pašalinta sėkmingai',
    'INVALID_ADMIN_CURRENT_PASSWORD' => 'Neteisingas senas slaptažodis',

    // SiteController
    'INVALID_ADMIN_AS_USER_CONNECTION' => 'Nepavyko atlikti prisijungimo prie vartotojo. Neteisingi duomenys',

    // CarTransporterController
    'CAR_TRANSPORTER_NOT_FOUND' => 'Tokio autovežio rasti nepavyko',
    'announcement_create_success' => 'Pranešimas sukurtas sėkmingai',
    'announcement_not_found' => 'Pranešimas nerastas',
    'announcement_edit_success' => 'Pranešimas pakeistas sėkmingai',
    'announcement_hide_success' => 'Pranešimas paslėptas sėkmingai',
    'announcement_removed_successfully' => 'Pranešimas pašalintas sėkmingai',
    'invalid_announcement_id' => 'Netinkamas pranešimo identifikavimo numeris',
    'service_create_success' => 'Paslauga sukūrta sėkmingai',
    'service_removed_successfully' => 'Paslauga pašalinta sėkmingai',
    'credit_service_not_found' => 'Kreditų ribojimo paslauga nerasta',
    'credit_service_edit_success' => 'Kredito ribojimas pakoreguotas sėkmingai',

    'LOAD_VISIBILITY_CHANGE_SUCCESS' => 'Krovinio matomumas pakeistas sėkmingai',
    'LOAD_FAILED_TOGGLE_VISIBILITY' => 'Nepavyko pakeisti krovinio matomumo nustatymų',
    'LOAD_ADVERTISE_SUCCESS' => 'Krovinys iškeltas sėkmingai',
    'LOAD_FAILED_TO_SAVE' => 'Nepavyko išsaugoti krovinio',
    'LOAD_OPEN_CONTACTS_SUCCESS' => 'Atviri kontaktai sėkmingai nustatyti',
    'LOAD_OPEN_CONTACTS_FAILURE' => 'Atvirų kontaktų nustatyti nepavyko',
    'TRANSPORTER_ALREADY_OPEN_CONTACTS' => 'Autovežis jau turi atvirus kontaktus iki {date}',
    'TRANSPORTER_OPEN_CONTACTS_SUCCESS' => 'Atviri kontaktai sėkmingai nustatyti',
    'TRANSPORTER_OPEN_CONTACTS_FAILURE' => 'Atvirų kontaktų nustatyti nepavyko',
    'TRANSPORTER_OPEN_CONTACTS_ERROR' => 'Klaida nustatant atvirus kontaktus autovežiui',
    'INCORRECT_OPEN_CONTACTS_DATA' => 'Incorrect open contacts data',

    'TRANSPORTER_NOT_FOUND' => 'Šis autovežis neegzistuoja mūsų sistemoje',
    'CANNOT_REMOVE_TRANSPORTER' => 'Nepavyko pašalinti autovežio',
    'TRANSPORTER_REMOVED_SUCCESSFULLY' => 'Autovežis pašalintas sėkmingai',
    'TRANSPORTER_VISIBILITY_CHANGE_SUCCESS' => 'Autovežio matomumas pakeistas sėkmingai',
    'TRANSPORTER_FAILED_TOGGLE_VISIBILITY' => 'Nepavyko pakeisti autovežio matomumo',
    'TRANSPORTER_ADVERTISE_SUCCESS' => 'Autovežio skelbimas iškeltas sėkmingai',
    'TRANSPORTER_FAILED_TO_SAVE' => 'Nepavyko išsaugoti autovežio',
    
    'BASIC_CREDITS_20_SERVICE_NOT_FOUND' => 'Paslauga BASICCREDITS20 nerasta',
    'USER_SERVICE_ACTIVE_NOT_FOUND' => 'Vartotojas neturi aktyvių paslaugų',

    'SUCCESSFULLY_SAVED' => 'Successfully saved',
    'UNKNOWN_ERROR_OCCURED' => 'Unknown error occured',
    
    'XML_INVOICES_DATE_RANGE_REQUIRED' => 'XML sąskaitų eksportui reikia įvesti datos rėžius',
];
