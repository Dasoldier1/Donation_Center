<?php

/* 
------------------
Language: Netherlands
@author Dasoldier
------------------
*/

$lang = array();

// Warnings.
$lang['warning_user_empty']				= 'Gebruikersnaam is niet ingevuld.';
$lang['warning_email_empty']			= 'Email address is niet ingevuld.';
$lang['warning_email_user_empty']		= 'Email/gebruiker is niet ingevuld.';
$lang['warning_email_not_valid']		= 'Ongeldig e-mailadres.';
$lang['warning_email_exists']			= 'E-mailadres is al in gebruik. Probeer een ander E-mailadres';
$lang['warning_username_exists']		= 'Gebruikersnaam bestaat al.';
$lang['warning_email_not_found']		= 'E-mailadres niet gevonden.';
$lang['warning_pass_empty']				= 'Wachtwoord is niet ingevuld.';
$lang['warning_confirm_pass_empty']		= 'Tweede wachtwoord voor controle is niet ingevuld.';
$lang['warning_pass_not_match']			= 'De door u ingevulde wachtwoorden komen niet overeen.';
$lang['warning_pass_min_chars']			= 'Wachtwoord minimum van 6 karakters is verplicht.';
$lang['warning_user_min_chars']			= 'Username minimum van 4 karakters is verplicht.';
$lang['warning_pass_confirm_field']		= 'wachtwoorden komen niet overheen.';
$lang['warning_captcha_wrong']			= 'Captcha is niet correct.';
$lang['warning_admin_creation_on']		= 'ADMIN ACCOUNT CREATIE STAAT AAN U KUNT DEZE WEER UIT ZETTEN ZODRA U KLAAR BENT.';
$lang['warning_account_not_activated']	= 'Account is niet geactiveerd, ga naar uw inbox om deze te activeren.';
$lang['warning_no_account']				= 'Er is geen account gevonden. Probeer nogmaals.';
$lang['warning_wrong_details']			= 'Verkeerde gegevens!';
$lang['warning_wrong_admin_pass']		= 'Admin wachtwoord klopt niet.';
$lang['warning_query_not_executed']		= 'Sorrie, query kan niet uitgevoerd worden. Neem contact op met de administratie.';
$lang['warning_account_activated']		= 'U account is al geactiveerd:';
$lang['warning_account_not_found']		= 'Er is geen account gevonden:';
$lang['warning_not_allowed']			= 'U heeft geen toegang tot deze pagina !!!';
$lang['warning_no_login_connection']	= 'Er kan geen verbinding worden gemaakt naar de login server.';
$lang['warning_charname_empty']			= 'Karakter is niet ingevuld';
$lang['warning_character_not_set']		= 'Er is geen karkter geselecteerd';
$lang['warning_character_set']			= 'Karakter geselecteerd:';
$lang['warning_logout_character']		= 'U moet de karakter uitloggen. U kunt weer inloggen als de donatie klaar is.';
$lang['warning_keep_logged_off']		= 'Laat u karakter uitgelogd staan totdat de donatie afgehandeld is.';
$lang['warning_disabled']				= 'Deze functie is uitgeschakeld.';
$lang['login_attempts_error']			= 'Te vaak het verkeerde wachtwoord gebruikt probeer nogmaals in';
$lang['login_attempts_error_2']			= 'minuten.';
$lang['sandbox_mode']					= 'SANDBOX MODE';

// General text when not logged in.
$lang['dc_message_1']					= 'Login';
$lang['dc_message_2']					= 'Login';
$lang['dc_message_3']					= 'Registreer';
$lang['dc_message_4']					= 'Wachtwoord vergeten ?';
$lang['dc_message_5']					= 'Er kan geen verbinding worden gemaakt.';
$lang['dc_message_6']					= 'Probeer het later nogmaals.';
$lang['dc_message_7']					= 'Captcha Veranderen';
$lang['dc_message_8']					= 'Wij hebben een e-mail verzonden naar';
$lang['dc_message_9']					= 'Klik op de password reset link in uw email om een nieuw wachtwoord te genereren.';
$lang['dc_message_10']					= 'Wachtwoord vergeten';
$lang['dc_message_11']					= 'Vul uw e-mailadres in. Hierin kunt u een link vinden om uw wachtwoord te veranderen.';
$lang['dc_message_12']					= 'Genereer nieuw wachtwoord';
$lang['dc_message_13']					= 'Terug';
$lang['dc_message_14']					= 'Wachtwoord veranderd.';
$lang['dc_message_15']					= 'U bent hier om uw vergeten wachtwoord te resetten.';
$lang['dc_message_16']					= 'Wachtwoord Reset.';
$lang['dc_message_17']					= 'Reset uw wachtwoord.';
$lang['dc_message_18']					= 'Success!';
$lang['dc_message_19']					= 'Wij hebben een e-mail gezonden naar';
$lang['dc_message_20']					= 'Klik op de confirmatie link in uw e-mail om uw account te activeren.';
$lang['dc_message_21']					= 'Uw account is nu geactiveerd:';
$lang['dc_message_22']					= 'Login';
$lang['dc_message_23']					= 'Registeer hier';
$lang['dc_message_24']					= 'Bevestig';

// Change password e-mail content.
$lang['mail_pass_message_1']			= 'Hallo ,';
$lang['mail_pass_message_2']			= 'Wij hebben een notificatie binnengekregen om uw wachtwoord te resetten, Als u dat gedaan hebt dan kunt u op de volgende link klikken. Als u dit niet gedaan hebt dan kunt u deze mail negeren,';
$lang['mail_pass_message_3']			= 'Klik op de volgende link om uw password te resetten.';
$lang['mail_pass_message_4']			= 'Klik hier om uw wachtwoord te resetten';
$lang['mail_pass_message_5']			= 'Wachtwoord resetten';

// Change signup e-mail content.
$lang['mail_signup_message_1']			= 'Welkom';
$lang['mail_signup_message_2']			= 'Om uw registratie compleet te maken kunt u op de volgende link klikken';
$lang['mail_signup_message_3']			= 'Klik hier om uw account te activeren';
$lang['mail_signup_message_4']			= 'Registratie voltooien';

// To many failed login attempts e-mail content.
$lang['mail_failed_login_1']			= 'Te veel inlog pogingen gedaan.';
$lang['mail_failed_login_2']			= 'Gebruikersnaam of email: ';
$lang['mail_failed_login_3']			= 'Ip-Address: ';
$lang['mail_failed_login_4']			= 'Als u dit niet gedaan hebt neem dan contact op met de administatie.';
$lang['mail_failed_login_5']			= 'Gebruikers notificatie: Te veel inlog pogingen.';

// Menu content of all pages
$lang['menu_brand']						= 'Donation Center';
$lang['menu_logout']					= 'Uitloggen';
$lang['menu_home']						= 'Home';
$lang['menu_select_character']			= 'Karakter selecteren';
$lang['menu_donation_options']			= 'Donatie opties';
$lang['menu_get_item']					= 'Haal ';
$lang['menu_remove_karma']				= 'Karma verwijderen';
$lang['menu_remove_pk']					= 'PK punten verwijderen';
$lang['menu_enchant_equip_itmes']		= 'Enchant aangetrokken items';
$lang['menu_admin_info']				= 'Admin Informatie';
$lang['menu_admin_db_log']				= 'Database log';
$lang['menu_admin_web_ipn_log']			= 'Website en ipn error log';
$lang['menu_admin_paypal_response']		= 'Ipn paypal response log';
$lang['menu_admin_how_to']				= 'Hoe te gebruiken';
$lang['menu_admin_support_links']		= 'Support Links';
$lang['menu_user_info']					= 'Info';
$lang['menu_user_credits']				= 'Credits';
$lang['menu_user']						= 'Gebruiker: ';
$lang['menu_admin']						= 'Admin: ';
$lang['menu_user_donate_btn']			= 'Doneren';

// Home.php ( admin and user content in one file.)
$lang['dc_home_page']					= 'Donation center home Pagina';
$lang['dc_home_on_players']				= 'Players online:';
$lang['dc_home_times_donated']			= 'Aantal keer donated:';
$lang['dc_home_total_donated']			= 'Totaal donated:';
$lang['dc_home_donated_fee']			= 'Totaal donated - paypal fee:';
$lang['dc_home_admin_message_1']		= 'Welkom op de donatie overview pagina.';
$lang['dc_home_admin_message_2']		= 'Hier kunt u zien of er iemand gedoneerd heeft of als er fouten opgetreden zijn.';
$lang['dc_home_user_message_1']			= 'Welkom op de donation center.';
$lang['dc_home_user_message_2']			= 'Hier kunt u doneren voor ingame items.';

// USER PAGE CONTENT.
// Credits.php
$lang['user_credits']					= 'Credits';

// select_char.php
$lang['select_char_message_1']			= 'Selecteer karakter';
$lang['select_char_message_2']			= 'Selecteer Karakter';
$lang['select_char_warning']			= 'De waarden die u ingevoerd hebt zijn niet correct';
$lang['select_char_already_added']		= 'is al toegevoegd tot uw account.';
$lang['select_char_added']				= 'has been selected to your account.';
$lang['select_char_remove']				= 'Karakter Verwijderen';
$lang['select_char_removed']			= 'Karakter verwijderd';

// Get_coins.php
$lang['get_item']						= 'Haal ';

// Remove_karma.php
$lang['remove_karma']					= 'Karma verwijderen';
$lang['remove_karma_remove']			= 'Verwijder';
$lang['remove_karma_karma']				= 'karma';
$lang['remove_karma_all']				= 'Verwijder alle karma';

// Remove_pk.php
$lang['remove_pk']						= 'Verwijder pk punten';
$lang['remove_pk_remove']				= 'Verwijder';
$lang['remove_pk_pk']					= 'PK punten';
$lang['remove_pk_all']					= 'Verwijder alle PK punten';

// Enchant_items.php / Enchant_items_finish.php
$lang['enchant_items']					= 'Enchant items';
$lang['enchant_2']						= 'Geselecteerde item:';
$lang['enchant_3']						= 'kan niet hoger worden geenchant.';
$lang['enchant_4']						= 'U moet een shirt aantrekken.';
$lang['enchant_5']						= 'U moet een helm aantrekken.';
$lang['enchant_6']						= 'U moet een ketting aantrekken.';
$lang['enchant_7']						= 'U moet een wapen vast houden.';
$lang['enchant_8']						= 'U moet een breastplate of full armor aantrekken.';
$lang['enchant_9']						= 'U moet een schild vasthouden.';
$lang['enchant_10']						= 'U moet een ring aandoen.';
$lang['enchant_11']						= 'U moet een oorbel aandoen.';
$lang['enchant_12']						= 'U moet handschoenen aantrekken.';
$lang['enchant_13']						= 'U moet een broek aantrekken.';
$lang['enchant_14']						= 'U moet een paar schoenen aantrekken.';
$lang['enchant_15']						= 'U moet een riem omdoen.';
$lang['enchant_16']						= 'Shirt optie is uitgeschakeld.';
$lang['enchant_17']						= 'Helm optie is uitgeschakeld.';
$lang['enchant_18']						= 'Ketting optie is uitgeschakeld.';
$lang['enchant_19']						= 'Wapen optie is uitgeschakeld.';
$lang['enchant_20']						= 'Breastplate en full armor optie is uitgeschakeld.';
$lang['enchant_21']						= 'Schild optie is uitgeschakeld.';
$lang['enchant_22']						= 'Ring optie is uitgeschakeld.';
$lang['enchant_23']						= 'Oorbel optie is uitgeschakeld.';
$lang['enchant_24']						= 'Handschoennen optie is uitgeschakeld.';
$lang['enchant_25']						= 'Broek optie is uitgeschakeld.';
$lang['enchant_26']						= 'Schoenen optie is uitgeschakeld.';
$lang['enchant_27']						= 'Riem optie is uitgeschakeld.';
$lang['enchant_28']						= 'U moet nog een aantal items aantrekken.';
$lang['enchant_29']						= 'Alle items enchant is uitgeschakeld.';
$lang['enchant_30']						= 'Alle aangetrokken items';
$lang['enchant_31']						= 'Somige items zijn al maximum geenchant.';
$lang['enchant_32']						= 'is niet toegelaten om te enchanten.';
$lang['enchant_33']						= 'Somige items zijn niet toegelaten om te enchanten.';
$lang['enchant_submit_button']			= 'Kies Optie';
$lang['enchant_finish_1']				= 'Enchant';
$lang['enchant_finish_2']				= 'Enchant alle items';

// ADMIN PAGE CONTENT.
// Database_log.php
$lang['admin_message_dblog']			= 'Database log pagina';

// How_to.php
$lang['admin_message_how_to']			= 'Uitleg';

// Paypal_response_log.php
$lang['admin_message_response_log']		= 'IPN paypal response log';

// Support_links.php
$lang['admin_support_links']			= 'Support links';

// Web_ipn_error_log.php
$lang['admin_web_ipn_log']				= 'Website en ipn error log';

// Done.php
$lang['done_completed']					= 'Donatie compleet';
$lang['done_1']							= 'Dank u voor het doneren, Nu kunt u de danatie controleren op uw karakter.';
$lang['done_2']							= 'Als de item niet is toegevoegd, neem dan contact op met de administratie.';

// Extra
$lang['made_by']						= 'Gemaakt door';
?>