<?php

/* 
------------------
Language: Spanish
@author swarlog
------------------
*/

$lang = array();

// Warnings.
$lang['warning_user_empty']				= 'Username field is empty.';
$lang['warning_email_empty']			= 'Email address field is empty.';
$lang['warning_email_user_empty']		= 'Email/User field is empty.';
$lang['warning_email_not_valid']		= 'This is not a valid email address.';
$lang['warning_email_exists']			= 'Email already exists, please try another one.';
$lang['warning_username_exists']		= 'Username already exists.';
$lang['warning_email_not_found']		= 'Email address not found.';
$lang['warning_pass_empty']				= 'Password field is empty.';
$lang['warning_confirm_pass_empty']		= 'Confirm password field is empty.';
$lang['warning_pass_not_match']			= 'Passwords are not matching.';
$lang['warning_pass_min_chars']			= 'Password must contain a minimum of 6 characters.';
$lang['warning_user_min_chars']			= 'Username must contain a minimum of 4 characters.';
$lang['warning_pass_confirm_field']		= 'Confirm password is not the same.';
$lang['warning_captcha_wrong']			= 'Captcha is wrong.';
$lang['warning_admin_creation_on']		= 'ADMIN ACCOUNT CREATION IS ENABLED TURN IT OFF IN CONFIG WHEN READY.';
$lang['warning_account_not_activated']	= 'This Account is not Activated Go to your Inbox and Activate it.';
$lang['warning_no_account']				= 'No account Found, Try again.';
$lang['warning_wrong_details']			= 'Wrong details!';
$lang['warning_wrong_admin_pass']		= 'Wrong admin password.';
$lang['warning_query_not_executed']		= 'Sorry, query could not be executed please contact the aministration.';
$lang['warning_account_activated']		= 'Your Account is allready Activated:';
$lang['warning_account_not_found']		= 'No Account Found:';
$lang['warning_not_allowed']			= 'You are not allowed to view this page !!!';
$lang['warning_no_login_connection']	= 'No connection could be made to the login server.';
$lang['warning_charname_empty']			= 'Character field is empty';
$lang['warning_character_not_set']		= 'You have no character selected';
$lang['warning_character_set']			= 'Character selected:';
$lang['warning_logout_character']		= 'Please logout your character. You can login again when the donation is finished.';
$lang['warning_keep_logged_off']		= 'Keep your character logged out untill the donation is finished.';
$lang['warning_disabled']				= 'This function is disabled.';
$lang['login_attempts_error']			= 'To many failed login attempts try again in';
$lang['login_attempts_error_2']			= 'minutes.';
$lang['sandbox_mode']					= 'SANDBOX MODE';

// General text when not logged in.
$lang['dc_message_1']					= 'Login';
$lang['dc_message_2']					= 'Sign in';
$lang['dc_message_3']					= 'Sign Up';
$lang['dc_message_4']					= 'Lost your Password ?';
$lang['dc_message_5']					= 'No connection could be made.';
$lang['dc_message_6']					= 'Please try again later.';
$lang['dc_message_7']					= 'Change Image';
$lang['dc_message_8']					= 'We have sent an email to';
$lang['dc_message_9']					= 'Please click on the password reset link in the email to generate new password.';
$lang['dc_message_10']					= 'Forgot password';
$lang['dc_message_11']					= 'Please enter your email address. You will receive a link to create a new password via email.';
$lang['dc_message_12']					= 'Generate new Password';
$lang['dc_message_13']					= 'Back';
$lang['dc_message_14']					= 'Password Changed.';
$lang['dc_message_15']					= 'you are here to reset your forgotton password of your account.';
$lang['dc_message_16']					= 'Password Reset.';
$lang['dc_message_17']					= 'Reset Your Password.';
$lang['dc_message_18']					= 'Success!';
$lang['dc_message_19']					= 'We have sent an email to';
$lang['dc_message_20']					= 'Please click on the confirmation link in the email to create your account.';
$lang['dc_message_21']					= 'Your Account is Now Activated:';
$lang['dc_message_22']					= 'Login here';
$lang['dc_message_23']					= 'Signup here';
$lang['dc_message_24']					= 'Verify';

// Change password e-mail content.
$lang['mail_pass_message_1']			= 'Hello ,';
$lang['mail_pass_message_2']			= 'We got requested to reset your password, if you do this then just click the following link to reset your password, if not just ignore this email,';
$lang['mail_pass_message_3']			= 'Click Following Link To Reset Your Password.';
$lang['mail_pass_message_4']			= 'Click here to reset your password';
$lang['mail_pass_message_5']			= 'Password Reset';

// Change signup e-mail content.
$lang['mail_signup_message_1']			= 'Welcome';
$lang['mail_signup_message_2']			= 'To complete your registration  please, just click following link';
$lang['mail_signup_message_3']			= 'Click HERE to Activate';
$lang['mail_signup_message_4']			= 'Confirm Registration';

// To many failed login attempts e-mail content.
$lang['mail_failed_login_1']			= 'To many failed login attempts.';
$lang['mail_failed_login_2']			= 'Username or email: ';
$lang['mail_failed_login_3']			= 'Ip-Address: ';
$lang['mail_failed_login_4']			= 'If this was not done by you please contact the administration.';
$lang['mail_failed_login_5']			= 'USER NOTICE: To many failed login attempts.';

// Menu content of all pages
$lang['menu_brand']						= 'Donation Center';
$lang['menu_logout']					= 'Logout';
$lang['menu_home']						= 'Home';
$lang['menu_select_character']			= 'Select Character';
$lang['menu_donation_options']			= 'Donation Options';
$lang['menu_get_item']					= 'Get ';
$lang['menu_remove_karma']				= 'Remove karma';
$lang['menu_remove_pk']					= 'Remove pk points';
$lang['menu_enchant_equip_itmes']		= 'Enchant equipped items';
$lang['menu_admin_info']				= 'Admin Information';
$lang['menu_admin_db_log']				= 'Database log';
$lang['menu_admin_web_ipn_log']			= 'Website and ipn error log';
$lang['menu_admin_paypal_response']		= 'Ipn paypal response log';
$lang['menu_admin_how_to']				= 'How to use';
$lang['menu_admin_support_links']		= 'Support Links';
$lang['menu_user_info']					= 'Info';
$lang['menu_user_credits']				= 'Credits';
$lang['menu_user']						= 'User: ';
$lang['menu_admin']						= 'Admin: ';
$lang['menu_user_donate_btn']			= 'Donate';

// Home.php ( admin and user content in one file.)
$lang['dc_home_page']					= 'Donation center home Page';
$lang['dc_home_on_players']				= 'Players online:';
$lang['dc_home_times_donated']			= 'Times donated:';
$lang['dc_home_total_donated']			= 'Total donated:';
$lang['dc_home_donated_fee']			= 'Total donated - paypal fee:';
$lang['dc_home_admin_message_1']		= 'Welcome to the donation overview home page.';
$lang['dc_home_admin_message_2']		= 'Here you can see whenever someone donated or if a error occured in the donation system.';
$lang['dc_home_user_message_1']			= 'Welcome to the donation center.';
$lang['dc_home_user_message_2']			= 'Here you can donate for ingame items.';

// USER PAGE CONTENT.
// Credits.php
$lang['user_credits']					= 'Credits';

// select_char.php
$lang['select_char_message_1']			= 'Select character';
$lang['select_char_message_2']			= 'Select Character';
$lang['select_char_warning']			= 'The credentials you supplied were not correct';
$lang['select_char_already_added']		= 'is already been added to your account.';
$lang['select_char_added']				= 'has been selected to your account.';
$lang['select_char_remove']				= 'Remove Character';
$lang['select_char_removed']			= 'Character removed';

// Get_coins.php
$lang['get_item']						= 'Get ';

// Remove_karma.php
$lang['remove_karma']					= 'Remove karma';
$lang['remove_karma_remove']			= 'Remove';
$lang['remove_karma_karma']				= 'karma';
$lang['remove_karma_all']				= 'Remove all karma';

// Remove_pk.php
$lang['remove_pk']						= 'Remove pk points';
$lang['remove_pk_remove']				= 'Remove';
$lang['remove_pk_pk']					= 'PK points';
$lang['remove_pk_all']					= 'Remove all PK points';

// Enchant_items.php / Enchant_items_finish.php
$lang['enchant_items']					= 'Enchant items';
$lang['enchant_2']						= 'Selected item:';
$lang['enchant_3']						= 'cannot be enchanted higher.';
$lang['enchant_4']						= 'You need to equip a shirt.';
$lang['enchant_5']						= 'You need to equip a helmet.';
$lang['enchant_6']						= 'You need to equip a necklace.';
$lang['enchant_7']						= 'You need to equip a weapon.';
$lang['enchant_8']						= 'You need to equip a breastplate or full armor.';
$lang['enchant_9']						= 'You need to equip a shield.';
$lang['enchant_10']						= 'You need to equip a ring.';
$lang['enchant_11']						= 'You need to equip a earring.';
$lang['enchant_12']						= 'You need to equip some gloves.';
$lang['enchant_13']						= 'You need to equip some leggings.';
$lang['enchant_14']						= 'You need to equip some boots.';
$lang['enchant_15']						= 'You need to equip a belt.';
$lang['enchant_16']						= 'Shirt option is disabled.';
$lang['enchant_17']						= 'Helmet option is disabled.';
$lang['enchant_18']						= 'Necklace option is disabled.';
$lang['enchant_19']						= 'Weapon option is disabled.';
$lang['enchant_20']						= 'Breastplate and full armor option is disabled.';
$lang['enchant_21']						= 'Shield option is disabled.';
$lang['enchant_22']						= 'Rings option is disabled.';
$lang['enchant_23']						= 'Earrings option is disabled.';
$lang['enchant_24']						= 'Gloves option is disabled.';
$lang['enchant_25']						= 'Leggings option is disabled.';
$lang['enchant_26']						= 'Boots option is disabled.';
$lang['enchant_27']						= 'Belt option is disabled.';
$lang['enchant_28']						= 'You need to equip some items.';
$lang['enchant_29']						= 'All items enchant is disabled.';
$lang['enchant_30']						= 'All equipped items';
$lang['enchant_31']						= 'Some equipped items are at max enchant.';
$lang['enchant_32']						= 'is not allowed to be enchanted.';
$lang['enchant_33']						= 'Some items are not allowed to be enchanted.';
$lang['enchant_submit_button']			= 'Choose Option';
$lang['enchant_finish_1']				= 'Enchant';
$lang['enchant_finish_2']				= 'Enchant all items';

// ADMIN PAGE CONTENT.
// Database_log.php
$lang['admin_message_dblog']			= 'Database log page';

// How_to.php
$lang['admin_message_how_to']			= 'How to';

// Paypal_response_log.php
$lang['admin_message_response_log']		= 'IPN paypal response log';

// Support_links.php
$lang['admin_support_links']			= 'Support links';

// Web_ipn_error_log.php
$lang['admin_web_ipn_log']				= 'Website and ipn error log';

// Done.php
$lang['done_completed']							= 'Donation Complete';
$lang['done_1']							= 'Thank you for donating, now check donation in your character.';
$lang['done_2']							= 'If the item has not been sent to your character, contact the administration.';

// Extra
$lang['made_by']						= 'Made by';
?>