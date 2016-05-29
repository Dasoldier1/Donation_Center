<?php
session_start();
require_once '../../class.user.php';
include '../assets/languages/common.php';
require '../config.php';

// Reporting to end users.
if ($use_reporting == false)
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

$user_home = new USER();
$database_class = new Database();

// Checks the connection.
$con_check = $database_class->dbConnection();

// XML full names on items.
// Load xml file.
$xml_file = "../assets/xml/item_names.xml";
$xmlload = simplexml_load_file($xml_file);

// Full item name based on id and xml. Always use item id minus 1.
$item_id_min1 = $item_id - 1;
$item_name = $xmlload->item[$item_id_min1]['name'];

// used for showing item icons
$icon_path = "../assets/images/icons/5-" . $item_id . ".jpg";


if(!$user_home->is_logged_in())
	{
		$user_home->redirect('../../index.php');
	}

// Redirect to logout if no connection to the database can be made.
if ($con_check == false)
	{
		$user_home->redirect('../../logout.php');
	}

// Select userID.
$select_user = $user_home->runQuery("SELECT * FROM paypal_donation_users WHERE userID=:uid");
$select_user->execute(array(":uid"=>$_SESSION['userSession']));
$row = $select_user->fetch(PDO::FETCH_ASSOC);

$user_role = 'USER';
?>
<!DOCTYPE html>
	<head>
		<title><?php echo $site_title, ' ', $row['userName']; ?></title>
		<!-- Bootstrap -->
		<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="../assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
		<link href="../assets/css/styles.css" rel="stylesheet" media="screen">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
						<a class="brand" href="../../index.php"><?php echo $lang['menu_brand']; ?></a>
						<div class="nav-collapse collapse">
							<ul class="nav pull-right">
								<li class="dropdown">
									<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> 
										<?php echo $row['userEmail']; ?> <i class="caret"></i>
									</a>
									<ul class="dropdown-menu">
										<li>
											<a tabindex="-1" href="../../logout.php"><?php echo $lang['menu_logout']; ?></a>
										</li>
									</ul>
								</li>
							</ul>
							<ul class="nav">
								<li class="inactive">
									<a href="../../index.php"><?php echo $lang['menu_home']; ?></a>
								</li>
									<?php
									if ($user_role != $row['userRole'])
										{
									?>
										<li class="dropdown">
											<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_admin_info']; ?><b class="caret"></b></a>
											<ul class="dropdown-menu" id="menu1">
												<li><a href="../admin/database_log.php"><?php echo $lang['menu_admin_db_log']; ?></a></li>
												<li><a href="../admin/web_ipn_error_log.php"><?php echo $lang['menu_admin_web_ipn_log']; ?></a></li>
												<li><a href="../admin/paypal_response_log.php"><?php echo $lang['menu_admin_paypal_response']; ?></a></li>
												<li><a href="../admin/how_to.php"><?php echo $lang['menu_admin_how_to']; ?></a></li>
												<li><a href="../admin/support_links.php"><?php echo $lang['menu_admin_support_links']; ?></a></li>
											</ul>
										</li>
									<?php
										}	// End admin menu content.
									?>
								<li class="inactive">
									<a href="select_char.php"><?php echo $lang['menu_select_character']; ?></a>
								</li>
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_donation_options']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<?php
											// Checks if get coins/item is enabled in config.
											if ($coins_enabled == true)
												{
													?>
													<li><a href="get_item.php"><?php echo $lang['menu_get_item'], $item_name, '\'s'; ?></a></li>
													<?php
												}
											// Checks if remove karma is enabled in config.
											if ($karma_enabled == true)
												{
													?>
													<li><a href="remove_karma.php"><?php echo $lang['menu_remove_karma']; ?></a></li>
													<?php
												}
											// Checks if remove pk points is enabled in config.
											if ($pkpoints_enabled == true)
												{
													?>
													<li><a href="remove_pk.php"><?php echo $lang['menu_remove_pk']; ?></a></li>
													<?php
												}
											// Checks if enchant is enabled in config.
											if ($enchant_item_enabled == true)
												{
													?>
													<li><a href="enchant_items.php"><?php echo $lang['menu_enchant_equip_itmes']; ?></a></li>
													<?php
												}
												?>
										</ul>
									</li>
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_user_info']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<li><a href="credits.php"><?php echo $lang['menu_user_credits']; ?></a></li>
										</ul>
									</li>
							</ul>
						</div>
					<!--/.nav-collapse -->
					</div>
				</div>
				<div class="navbar-inner2">
				<?php
				if ($user_role == $row['userRole'])
					{
						echo $lang['menu_user'];
					}
				else
					{
						echo $lang['menu_admin'];
					}
				print($row['userName']);
				?>
				</div>
			</div>
				<center>
				<h4><p><?php echo $lang['get_item'], $item_name, '\'s'; ?></p></h4>
				<?php if ($icons_enabled == true)
					{
						?>
						<img src="<?php echo $icon_path;?>">
						<?php
					}
				?>
				</center>
					<hr />
					<?php 
			if ($coins_enabled == true)
				{
				// Gives a message if sandbox is enabled.
				if ($use_sandbox == false)
					{
					// Checks if the user has a character connected to his account otherwise dont show the page content
					if (strlen($row['characterName']) != '')
						{
							// TODO: create a telnet connection check. player needs to logout if telnet connection is false.
							//	include'system/ipn/l2j_telnet.php';
							//	// Telnet host, port, pass, timeout.
							//	$telnet = new telnet("".$telnet_host."", "".$telnet_port."", "".$telnet_pass."", 2);
							//	$telnet_connect = $telnet->init();
							
							// Gets the online status from the gameserver database according to charname.
							$account_char_check = $user_home->runQuery("SELECT online FROM characters WHERE char_name =:charname LIMIT 1");
							$account_char_check->execute(array(":charname"=>$row['characterName']));
							$row_online_check = $account_char_check->fetch(PDO::FETCH_ASSOC);
							
							// Checks if the character is offline
							if ($row_online_check['online'] == 0)
							{
						?>
								<blockquote>
									<div id="loginsuccess">
									<!-- Oke now lets show the donation options -->
									<!-- The PayPal coins Donation option list -->
									<center>
										<form action="<?php if ($use_sandbox == true){ echo $SandboxpayPalURL;} else { echo $payPalURL; } ?>" method="post" class="payPalForm">
											<input type="hidden" name="cmd" value="_donations" />
											<input type="hidden" name="item_name" value="<?php echo $item_name?>'s" />

											<!-- Custom field that will be passed to paypal -->
											<input type="hidden" name="custom" value="<?php echo $row['characterName']?>|Coins">

											<!-- Your PayPal email -->
											<input type="hidden" name="business" value="<?php if ($use_sandbox == true){ echo $SandboxPayPalEmail;} else { echo $myPayPalEmail; } ?>" />
											<!-- PayPal will send an IPN notification to this URL -->
											<input type="hidden" name="notify_url" value="<?php echo $donation_center_folder_loc ?>system/assets/ipn/ipn_donations.php" />

											<!-- The return page to which the user is navigated after the donations is complete -->
											<input type="hidden" name="return" value="<?php echo $donation_center_folder_loc?>system/user/done.php" />

											<!-- Signifies that the transaction data will be passed to the return page by POST -->
											<input type="hidden" name="rm" value="2" />

											<!-- General configuration variables for the paypal landing page. Consult -->
											<!-- http://www.paypal.com/IntegrationCenter/ic_std-variable-ref-donate.html for more info -->
											<input type="hidden" name="no_note" value="1" />
											<input type="hidden" name="cbt" value="Go Back To The Site" />
											<input type="hidden" name="no_shipping" value="1" />
											<input type="hidden" name="lc" value="US" />
											<input type="hidden" name="currency_code" value="<?php echo $currency_code?>" />

													<!-- The amount of the transaction: -->
													<select name="amount" style="width: 190px">
														<?php 
															if ($coins1_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount1?>"><?php echo $donatecoinreward1, ' X ', $item_name, ' ', $currency_code_html, $donatecoinamount1;?>.00 </option>
																	<?php
																}
															if ($coins2_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount2?>"><?php echo $donatecoinreward2, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount2;?>.00</option>
																	<?php
																}
															if ($coins3_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount3?>"><?php echo $donatecoinreward3, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount3;?>.00</option>
																	<?php
																}
															if ($coins4_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount4?>"><?php echo $donatecoinreward4, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount4;?>.00</option>
																	<?php
																} 
														?>
														</select><br>
											<!-- Here you can change the image of the coins donation button  -->
											<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
											<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
											<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest" />
										</form>
									</center>
										<br>
									</div>
								</blockquote>
								<?php
								// Give a message that the character needs to be logged out when telnet is turned off.
								if ($use_telnet == false)
									{
										?>
										<div class='alert alert-success'>
											<center><strong><?php echo $lang['warning_keep_logged_off']; ?></strong></center>
										</div>
										<?php
									}
							}
						// Character is online.
						else
							{
								// Show the page if telnet is true otherwise give character logout message.
								if ($use_telnet == true)
									{
								?>
									<blockquote>
										<div id="loginsuccess">
											<!-- Oke now lets show the donation options -->
											<!-- The PayPal coins Donation option list -->
											<center>
												<form action="<?php if ($use_sandbox == true){ echo $SandboxpayPalURL;} else { echo $payPalURL; } ?>" method="post" class="payPalForm">
													<input type="hidden" name="cmd" value="_donations" />
													<input type="hidden" name="item_name" value="<?php echo $item_name?>'s" />

													<!-- Custom field that will be passed to paypal -->
													<input type="hidden" name="custom" value="<?php echo $row['characterName']?>|Coins">

													<!-- Your PayPal email -->
													<input type="hidden" name="business" value="<?php if ($use_sandbox == true){ echo $SandboxPayPalEmail;} else { echo $myPayPalEmail; } ?>" />
													
													<!-- PayPal will send an IPN notification to this URL -->
													<input type="hidden" name="notify_url" value="<?php echo $donation_center_folder_loc ?>system/assets/ipn/ipn_donations.php" />

													<!-- The return page to which the user is navigated after the donations is complete -->
													<input type="hidden" name="return" value="<?php echo $donation_center_folder_loc?>system/user/done.php" />

													<!-- Signifies that the transaction data will be passed to the return page by POST -->
													<input type="hidden" name="rm" value="2" />

													<!-- General configuration variables for the paypal landing page. Consult -->
													<!-- http://www.paypal.com/IntegrationCenter/ic_std-variable-ref-donate.html for more info -->
													<input type="hidden" name="no_note" value="1" />
													<input type="hidden" name="cbt" value="Go Back To The Site" />
													<input type="hidden" name="no_shipping" value="1" />
													<input type="hidden" name="lc" value="US" />
													<input type="hidden" name="currency_code" value="<?php echo $currency_code?>" />
													<!-- The amount of the transaction: -->
													<select name="amount" style="width: 190px">
														<?php 
															if ($coins1_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount1?>"><?php echo $donatecoinreward1, ' Xxx ', $item_name, ' ', $currency_code_html, $donatecoinamount1;?>.00 </option>
																	<?php
																}
															if ($coins2_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount2?>"><?php echo $donatecoinreward2, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount2;?>.00</option>
																	<?php
																}
															if ($coins3_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount3?>"><?php echo $donatecoinreward3, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount3;?>.00</option>
																	<?php
																}
															if ($coins4_enabled == true)
																{
																	?>
																	<option value="<?php echo $donatecoinamount4?>"><?php echo $donatecoinreward4, ' X ', $item_name, '\'s ', $currency_code_html, $donatecoinamount4;?>.00</option>
																	<?php
																} 
														?>
													</select><br>
													<!-- Here you can change the image of the coins donation button  -->
													<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
													<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
													<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest" />
												</form>
											</center>
											<br>
										</div>
									</blockquote>
								<?php
									}
									// Telnet is disabled show message that the character needs to logout.
									else
										{
										echo "
												<div class='alert alert-error'>
													<center><strong>" . $lang['warning_logout_character'] . "</strong></center>
												</div>
											";
										}
							}
							// This message is always shown if a character exists.
							echo "
									<div class='alert alert-success'>
										<center><strong>" . $lang['warning_character_set'], ' ', $row['characterName'] . "</strong></center>
									</div>
								";
						}
					// Dont show the page because character is not set. 
					else
						{
							echo "
									<div class='alert alert-error'>
										<center><strong>" . $lang['warning_character_not_set'] . "</strong></center>
									</div>
								";
						}
					}
				// Dont show the page and gives a message if sandbox is enabled.
				else
					{
						echo "
								<div class='alert alert-error'>
									<center><strong>" . $lang['sandbox_mode'] . "</strong></center>
								</div>
							";
					}
				}
			// Dont show the page because its disabled in the config
			else
				{
					echo "
							<div class='alert alert-error'>
								<center><strong>" . $lang['warning_disabled'] . "</strong></center>
							</div>
						";
				}
					?>
					<br>
				<hr />
			<center><?php echo $lang['made_by']; ?> Dasoldier</center>
			<center><table><tr><td><a href="?lang=en" title="English"><img src="../assets/images/flag/en.png" alt="English"></a> <a href="?lang=es" title="Spanish"><img src="../assets/images/flag/es.png" alt="Spanish"></a> <a href="?lang=nl" title="Netherlands"><img src="../assets/images/flag/nl.png" alt="Netherlands"></a></td></tr></table></center>
			<!--/.fluid-container-->
			<script src="../assets/bootstrap/js/jquery-1.9.1.min.js"></script>
			<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
			<script src="../assets/js/scripts.js"></script>
	</body>
</html>