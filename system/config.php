<?php
/*
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * PayPal System - Config
 */

// Here we can define if we want to work on the sandbox or live
// Here you can find the Paypal ipn simulator https://developer.paypal.com/webapps/developer/applications/ipn_simulator

// Enabled sandbox for test system.
// NOTICE: IPN simulator will give invalid response back if this is set to false
// Set "true" for test and "false" for live.
// Default: true
$use_sandbox = true;

// Test Sandbox account
$SandboxPayPalEmail = 'YourSandboxTestPaypal@Account.com';
$SandboxpayPalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr"; // Only touch this line if you know what your doing.

// Fill your PayPal email below
// This is where you will receive the donations
$myPayPalEmail = 'YourPaypal@Account.com';
$payPalURL = "https://www.paypal.com/cgi-bin/webscr"; // Only touch this line if you know what your doing.

// Donation center folder location
// Default: https://YourWebsiteName.com/donation_center
$donation_center_folder_loc = 'https://YourWebsiteName/donation_center';

// Define the currency you want to use for paypal
// You can find them here https://developer.paypal.com/docs/classic/api/currency_codes/
$currency_code = 'EUR';

//change the currency on the donation centre page. Visible html only
$currency_code_html = '&euro;';

// Define the donation item_id
// Default item goldbar
$item_id = '3470';

// Turn donation options on or off (true=on | false=off)
// Reward coins
$coins_enabled = true;
// Remove karma
$karma_enabled = true;
// Remove PK points
$pkpoints_enabled = true;
// Enchant equipped items
$enchant_item_enabled = true;

// Enable or disable the creation of an admin account.
// NOTE: Use the extra password defined here.
// NOTE: Disable this when you are done.
$admin_account = false;
$admin_reg_password = '12345678';

// Here you need to configure the phpmailer settings.
// This will be used for forgot password and the verify e-mail.
// NOTE: You need to create a speacial app password on your gmail account.
$smtp_debug		= 0;
$smtp_auth		= true;
$smtp_secure	= "ssl";
$mail_host		= "smtp.gmail.com";
$mail_port		= 465;
$mail_username	= "YourEmail@gmail.com";
$mail_password	= "YourSpecialAppPassword";
$mail_setfrom	= "YourEmail@gmail.com";
$mail_addreplyto= "YourEmail@gmail.com";
$mail_subject	= "Donation system";

/**
 * COIN OPTIONS
 * IMPORTANT: Always use a different $donatecoinamount price amount on eatch option!!!.
 */
 
// Define coin option 1 (true=on | false=off)
$coins1_enabled = true;
// price of the donation.
$donatecoinamount1 = 1;
// The reward coins amount.
$donatecoinreward1 = 1;

// Define coin option 2 (true=on | false=off)
$coins2_enabled = true;
// Price of the donation.
$donatecoinamount2 = 5;
//  The reward coins amount.
$donatecoinreward2 = 6;

// Define coin option 3 (true=on | false=off)
$coins3_enabled = true;
// Price of the donation.
$donatecoinamount3 = 10;
// The reward coins amount.
$donatecoinreward3 = 15;

// Define coin option 4 (true=on | false=off)
$coins4_enabled = true;
// Price of the donation.
$donatecoinamount4 = 20;
// The reward coins amount.
$donatecoinreward4 = 40;

/**
 * KARMA OPTIONS
 * IMPORTANT: Always use a different $donatekarmaamount price on eatch option!!!.
 */
 
// Define karma option 1 (true=on | false=off)
$karma1_enabled = true;
// Price of the donation.
$donatekarmaamount1 = 2;
// The amount of karma that needs to be removed.
$donateremovekarma1 = 2000;

// Define karma option 2 (true=on | false=off)
$karma2_enabled = true;
// Price of the donation.
$donatekarmaamount2 = 3;
// The amount of karma that needs to be removed.
$donateremovekarma2 = 4000;

// Define karma option 3 (true=on | false=off)
$karma3_enabled = true;
// Price of the donation.
$donatekarmaamount3 = 5;
// The amount of karma that needs to be removed.
$donateremovekarma3 = 6000;

// Define karma option 4 (true=on | false=off)
$karma4_enabled = true;
// Price of the donation. (All karma gets removed)
$donatekarmaallamount = 10;

/**
 * PK POINTS OPTIONS
 * IMPORTANT: Always use a different $donatepkamount price on eatch option!!!.
 */

// Define PK points option 1 (true=on | false=off)
$pkpoints1_enabled = true;
// Price of the donation.
$donatepkamount1 = 1;
// The amount of PK points that needs to be removed.
$donateremovepk1 = 5;

// Define PK points option 2 (true=on | false=off)
$pkpoints2_enabled = true;
// Price of the donation.
$donatepkamount2 = 2;
// The amount of PK points that needs to be removed.
$donateremovepk2 = 11;

// Define PK points option 3 (true=on | false=off)
$pkpoints3_enabled = true;
// Price of the donation.
$donatepkamount3 = 5;
// The amount of PK points that needs to be removed.
$donateremovepk3 = 25;

// Define PK points option 4 (true=on | false=off)
$pkpoints4_enabled = true;
// Price of the donation. (All pk points gets removed)
$donatepkallamount = 15;

/**
 * ENCHANT ITEM OPTIONS
 */

// Specify and disable item ids for enchantment.
// Default: Cursed Weapons: 8689, 8190 Hero Weapons: 6611, 6612, 6613, 6614, 6615, 6616, 6617, 6618, 6619, 6620, 6621, 9388, 9389, 9390
// Note: If you dont want to use this option change it to array().
$enc_item_blocked = array(8689, 8190, 6611, 6612, 6613, 6614, 6615, 6616, 6617, 6618, 6619, 6620, 6621, 9388, 9389, 9390);
 
// Define SHIRT enchant (true=on | false=off)
$shirt_enchant_enabled = true;
// Enchant amount.
$shirt_enchant_amount = 18;
// Price of the donation.
$shirt_donate_amount = 10;

// Define HELMET enchant (true=on | false=off)
$helmet_enchant_enabled = true;
// Enchant amount.
$helmet_enchant_amount = 18;
// Price of the donation.
$helmet_donate_amount = 10;

// Define NECKLACE enchant (true=on | false=off)
$necklace_enchant_enabled = true;
// Enchant amount.
$necklace_enchant_amount = 18;
// Price of the donation.
$necklace_donate_amount = 10;

// Define WEAPON enchant (true=on | false=off)
$weapon_enchant_enabled = true;
// Enchant amount.
$weapon_enchant_amount = 18;
// Price of the donation.
$weapon_donate_amount = 10;

// Define BREASTPLATE and FULL ARMOR enchant (true=on | false=off)
$breastplate_full_enchant_enabled = true;
// Enchant amount.
$breastplate_full_enchant_amount = 18;
// Price of the donation.
$breastplate_full_donate_amount = 10;

// Define SHIELD enchant (true=on | false=off)
$shield_enchant_enabled = true;
// Enchant amount.
$shield_enchant_amount = 18;
// Price of the donation.
$shield_donate_amount = 10;

// Define RINGS  enchant (true=on | false=off)
$ring_enchant_enabled = true;
// Enchant amount.
$ring_enchant_amount = 18;
// Price of the donation.
$ring_donate_amount = 10;

// Define EARRINGS enchant (true=on | false=off)
$earring_enchant_enabled = true;
// Enchant amount.
$earring_enchant_amount = 18;
// Price of the donation.
$earring_donate_amount = 10;

// Define GLOVES enchant (true=on | false=off)
$gloves_enchant_enabled = true;
// Enchant amount.
$gloves_enchant_amount = 18;
// Price of the donation.
$gloves_donate_amount = 10;

// Define LEGGINGS enchant (true=on | false=off)
$leggings_enchant_enabled = true;
// Enchant amount.
$leggings_enchant_amount = 18;
// Price of the donation.
$leggings_donate_amount = 10;

// Define BOOTS enchant (true=on | false=off)
$boots_enchant_enabled = true;
// Enchant amount.
$boots_enchant_amount = 18;
// Price of the donation.
$boots_donate_amount = 10;

// Define BELT enchant (true=on | false=off)
$belt_enchant_enabled = true;
// Enchant amount.
$belt_enchant_amount = 18;
// Price of the donation.
$belt_donate_amount = 10;

// Define ALL ITEMS enchant (true=on | false=off)
$all_enchant_enabled = true;
// Enchant amount.
$all_enchant_amount = 18;
// Price of the donation.
$all_donate_amount = 100;

// Turn error reporting on or off (true=on | false=off)
// NOTE: this option only applies to errors to end users
// Default: true
$use_reporting = true;

// Use a delay when someone submit a form
// It will give a little bit protection against a brute force attack
// Enable or Disable loading delay: (true=on | false=off)
// Default: true
$loading_delay = true;

// Total delay in seconds
// Default: 3
$delaytime = 3;

// Total failed login attempts allowed after the user gets blocked for a couple of minutes.
// Default: 3
$allowed_login_attempts = 5;

// Total timeout interval in minutes after to many login attempts.
// Default: 5
$login_attempt_interval_timeout = 5;

// After to many failed login attempts a mail with a warning will be send to the user.
// NOTE: This value should be lower or the same as $allowed_login_attempts.
// Default: 5
$send_warning_mail_user = 5;

// A admin log will be made after to many login attempts on character selection.
// Here you can put the amount after a log will be made.
// NOTE: This value should be lower then $allowed_login_attempts.
// Default: 3
$login_attempt_char_log = 3;

// Enable or Disable icons: (true=on | false=off)
// Default: false
$icons_enabled = false;

// Specify log location for the ipn file
// Default: ../../log/ipn_log.php
$log_location_ipn = '../../log/ipn_log.php';

// Enable or Disable Telnet, require config (true=on | false=off )
// This option will only allow coins option to go trough telnet
// NOTE: this is not a good solution to use for now.
// Default: false
$use_telnet = false;

// Here you can configure the connection timeout.
// NOTE: is in seconds ( somehow it is connecting 3 times )
// Default: 5
$connection_timeout = 5;

// Sets the title of all the pages.
$site_title = 'U3G | PayPal System';

// Turn languages on or off
// Default: true
$english_lang	= true;
$spanish_lang	= true;
$dutch_lang		= true;

// Enable or disable user timeout if he is inactive for to long.
// Default: true
$timeout_enabled = true;

// Sets the timeout if user is inactive for x minutes.
// Default: 15
$user_timeout	= 15;
?>