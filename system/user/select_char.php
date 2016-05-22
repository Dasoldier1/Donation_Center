<?php
session_start();
require_once '../../class.user.php';
include_once '../assets/securimage/securimage.php';
include '../assets/languages/common.php';
require '../config.php';

// Reporting to end users.
if ($use_reporting == false)
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

$user_home = new USER();
$database_class = new Database();
$securimage = new Securimage();

// Checks the connection.
$con_check = $database_class->dbConnection();

// Gets the correct address
//Test if it is a shared client
if (!empty($_SERVER['HTTP_CLIENT_IP']))
	{
		$get_ip=$_SERVER['HTTP_CLIENT_IP'];
	}
// Checks if its a proxy address
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$get_ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
// Use normal remote address
else
	{
		$get_ip=$_SERVER['REMOTE_ADDR'];
	}

// XML full names on items.
// Load xml file.
$xml_file = "../assets/xml/item_names.xml";
$xmlload = simplexml_load_file($xml_file);

// Full item name based on id and xml. Always use item id minus 1.
$item_id_min1 = $item_id - 1;
$item_name = $xmlload->item[$item_id_min1]['name'];

if(!$user_home->is_logged_in())
	{
		$user_home->redirect('../../index.php');
	}

// Redirect to logout if no connection to the game database can be made.
if ($con_check == false)
	{
		$user_home->redirect('../../logout.php');
	}

// Select userID.
$select_user = $user_home->runQuery("SELECT * FROM paypal_donation_users WHERE userID=:uid");
$select_user->execute(array(":uid"=>$_SESSION['userSession']));
$row = $select_user->fetch(PDO::FETCH_ASSOC);

// Used for login attempts
$login_attempts = $user_home->runQuery("SELECT COUNT(*) FROM paypal_donation_login_attempts WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address");
$login_attempts->execute(array(":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip));
$row_login_attempts = $login_attempts->fetchColumn();

$user_role = 'USER';
$char_delete = '';

// When remove character button  is used.
if(isset($_POST['remove_character']))
	{
	$remove_char = $user_home->runQuery("UPDATE paypal_donation_users SET characterName=:char_delete  WHERE userID=:uid");
	$remove_char->execute(array(":char_delete"=>$char_delete,":uid"=>$_SESSION['userSession']));
	}

// If submit button is used.
if(isset($_POST['btn-select-char']))
{
	// Wait for x seconds.
	if ($loading_delay == true)
		{
			sleep($delaytime);
		}
		// Checks if captcha is correct.
		if ($securimage->check($_POST['captcha_code']) == true) 
		{
				// Checks the login server connection.
				$con_check_login = $database_class->dbConnection_Login();
			
				$username = $user_home->sanitize($_POST['username']);
				$password = $user_home->sanitize($_POST['password']);
				$charname = $user_home->sanitize($_POST['charname']);
			// If username is empty give a message.
			if (strlen($username) == 0)
				{
					$user_error = "	<div class='alert alert-error'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_user_empty'] . "</strong></center>
									</div>";
				}
			// If password is empty give a message.
			else if (strlen($password) == 0)
				{
					$user_error =	"<div class='alert alert-error'>
											<button class='close' data-dismiss='alert'>&times;</button>
											<center><strong>" . $lang['warning_pass_empty'] . "</strong></center>
										</div>";
				}
			// If charname is empty give a message.
			else if (strlen($charname) == 0)
				{
					$user_error =	"<div class='alert alert-error'>
											<button class='close' data-dismiss='alert'>&times;</button>
											<center><strong>" . $lang['warning_charname_empty'] . "</strong></center>
										</div>";
				}
			// if no connection can be made to the login server give a message.
			else if ($con_check_login == false)
				{
					$user_error =	"<div class='alert alert-error'>
											<button class='close' data-dismiss='alert'>&times;</button>
											<center><strong>" . $lang['warning_no_login_connection'] . "</strong></center>
										</div>";
				}
				// Completed first form checks
				else
					{
						// Gets the username from the login database.
						$getusername = $user_home->runQueryLogin("SELECT login FROM accounts WHERE login=:username");
						$getusername->execute(array(":username"=>$username));
						
						// Gets the charname from the gameserver database.
						$getcharname = $user_home->runQuery("SELECT char_name FROM characters WHERE char_name=:charname");
						$getcharname->execute(array(":charname"=>$charname));
						
						// Gets the charname from paypal_donation_users.
						$get_dc_charname = $user_home->runQuery("SELECT characterName FROM paypal_donation_users WHERE characterName=:charname AND userID=:uid");
						$get_dc_charname->execute(array(":charname"=>$charname, ":uid"=>$_SESSION['userSession']));
						
						// Gets the account_name from the gameserver database according to charname.
						$account_char_check = $user_home->runQuery("SELECT account_name FROM characters WHERE char_name=:charname");
						$account_char_check->execute(array(":charname"=>$charname));
						$row_account_char_check = $account_char_check->fetch(PDO::FETCH_ASSOC);
						
						// Gets the correct password from the login database.
						$getpass = $user_home->runQueryLogin("SELECT password FROM accounts WHERE login=:username");
						$getpass->execute(array(":username"=>$username));
						$rowpass = $getpass->fetch(PDO::FETCH_ASSOC);
						
						// Put encryption on password from form.
						$getpassencrypt = $user_home->l2j_encrypt($password);
						
						// Used for passing a value.
						$set_usermail = 1;
						
						// Adds a failed login attempt to the database.
						$failed_attempts = $user_home->runQuery("INSERT INTO paypal_donation_login_attempts(address, usermail) 
														VALUES(:address, :usermail)");
						$failed_attempts->bindparam(":address",$get_ip);
						$failed_attempts->bindparam(":usermail",$set_usermail);
						
						// If username does not exists give a message.
						if ($getusername->rowCount() == 0)
							{
								$user_error = "	<div class='alert alert-error'>
													<button class='close' data-dismiss='alert'>&times;</button>
													<center><strong>" . $lang['select_char_warning'] . "</strong></center>
												</div>";
								$failed_attempts->execute();
							}
						// If password is not correct give a message.
						else if ($getpassencrypt != $rowpass['password'])
							{
								$user_error = "	<div class='alert alert-error'>
													<button class='close' data-dismiss='alert'>&times;</button>
													<center><strong>" . $lang['select_char_warning'] . "</strong></center>
												</div>";
								$failed_attempts->execute();
							}
						// If character does not exists give a message.
						else if ($getcharname->rowCount() == 0)
							{
								$user_error = "	<div class='alert alert-error'>
													<button class='close' data-dismiss='alert'>&times;</button>
													<center><strong>" . $lang['select_char_warning'] . "</strong></center>
												</div>";
								$failed_attempts->execute();
							}
						// If character is not linked to this account give a message.
						else if ($username != $row_account_char_check['account_name'])
							{
								$user_error = "	<div class='alert alert-error'>
													<button class='close' data-dismiss='alert'>&times;</button>
													<center><strong>" . $lang['select_char_warning'] . "</strong></center>
												</div>";
								$failed_attempts->execute();
							}
						// If character is allready added.
						else if ($get_dc_charname->rowCount() == 1)
							{
								$user_error = "	<div class='alert alert-success'>
													<button class='close' data-dismiss='alert'>&times;</button>
													<center><strong>" . $charname . " " . $lang['select_char_already_added'] . "</strong></center>
												</div>";
							}
						// Suc6 connect character to the donation system.
						else
							{
								// update the character name.
								$update_charactername = $user_home->runQuery("UPDATE paypal_donation_users SET characterName=:charname WHERE userID=:uid");
								$update_charactername->execute(array(":charname"=>$charname,":uid"=>$_SESSION['userSession']));
								
								$user_complete = "	<div class='alert alert-success'>
														<button class='close' data-dismiss='alert'>&times;</button>
														<center><strong>" . $charname . " " . $lang['select_char_added'] . "</strong></center>
													</div>";
							}
					}
		}
	// Captcha is wrong give a message.
	else
	{
	 $captcha_error = "	<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_captcha_wrong'] . "</strong></center>
						</div>";
	}
}
?>
<!DOCTYPE html>
<html class="no-js">
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
								<li class="active">
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
				<center><h4><p><?php echo $lang['select_char_message_1']; ?></p></h4></center>
					<hr />
					<?php
					// Checks if to many login attempts are tried give a message
					if ($row_login_attempts <= $allowed_login_attempts)
					{
				?>
						<blockquote>
							<form class="form-signin" method="post">
								<?php
									// Show errors for empty fields.
									if(isset($user_error))
										{
											echo $user_error;
										}
									// Show messsage if everything is complete
									if(isset($user_complete))
										{
											echo $user_complete;
										}
									// Show warning when captcha is wrong.
									if(isset($captcha_error))
										{
											echo $captcha_error;
										}
								?>
								<center>
									<input type="text" class="input-block-level-custom" placeholder="Username" name="username" required /><br>
									<input type="password" class="input-block-level-custom" placeholder="Password" name="password" required /><br>
									<input type="text" class="input-block-level-custom" placeholder="Character Name" name="charname" required /><br>
									<input type="text" class="input-block-level-custom" placeholder="Captcha code" name="captcha_code" maxlength="6" required/>
									<center><img id="captcha" src="../assets/securimage/securimage_show.php" alt="CAPTCHA Image" /><br>
									<!--Change captcha image -->
									<a href="#" class="btn btn-small" onclick="document.getElementById('captcha').src = '../assets/securimage/securimage_show.php?' + Math.random(); return false"><?php echo $lang['dc_message_7']; ?></a></center>
									<hr />
									<button class="btn btn-large btn-primary" type="submit" name="btn-select-char"><?php echo $lang['select_char_message_2']; ?></button>
								</center>
							</form>
						</blockquote>
				<?php
					// Checks if the user has a character connected to his account
					if ((strlen($row['characterName']) == '') && (!isset($_POST['btn-select-char'])))
						{
							echo "
								<div class='alert alert-error'>
									<button class='close' data-dismiss='alert'>&times;</button>
									<center><strong>" . $lang['warning_character_not_set'] . "</strong></center>
								</div>
							";
						}
					// Character is connected to his account
					if ((strlen($row['characterName']) != '') && (!isset($_POST['remove_character'])))
						{
							?>
							<!-- Remove character button -->
							<form method="post" action="">
							<center>
									<button class="btn btn-small" type="submit" name="remove_character"><?php echo $lang['select_char_remove']; ?></button><br>
							</center>
							</form>
						<?php
							echo "
								<div class='alert alert-success'>
									<center><strong>" . $lang['warning_character_set'], ' ', $row['characterName'] . "</strong></center>
								</div>
							";
						}
						// Shows a message when the user removed his character from his account.
						if((strlen($row['characterName']) != '') && (isset($_POST['remove_character'])))
							{
							echo "
								<div class='alert alert-success'>
									<button class='close' data-dismiss='alert'>&times;</button>
									<center><strong>" . $lang['select_char_removed'] . "</strong></center>
								</div>
							";
						}
					}
					// To many failed login attempts
					else
					{
						echo "	<div class='alert alert-error'>
											<button class='close' data-dismiss='alert'>&times;</button>
											<center><strong>" . $lang['login_attempts_error'], ' ',  $login_attempt_interval_timeout, ' ', $lang['login_attempts_error_2'] . "</strong></center>
										</div>";
						// Used for passing a value.
						$set_usermail = 1;

						$admin_notice_check = $user_home->runQuery("SELECT sum(usermail) FROM paypal_donation_login_attempts WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address AND usermail=:umail");
						$admin_notice_check->execute(array(":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip, ":umail"=>$set_usermail));
						$row_admin_notice_check = $admin_notice_check->fetchColumn();

					// Make a admin log if more then ? failed login attempts.
					if ($row_admin_notice_check >= $login_attempt_char_log)
						{
							// Write to log file.
							//logging: Timestamp
							$local_log = '['.date('m/d/Y g:i A').'] - '; 

							// Checks if a username is present.
							if(!isset($charname))
								{
									$charname = 'NONE';
								}
							//logging: response from the server
							$local_log .= "SELECT CHARACTER TO MANY FAILED LOGIN ATTEMPTS<br>";
							$local_log .= "USED ON ACCOUNT: ". $row['userEmail'] ."<br>";
							$local_log .= "IP-ADDRESS: ". $get_ip ."<br>";
							$local_log .= "CHARACTER NAME: ". $charname ."<br>";
							$local_log .= '<hr />';

							// Write to log
							$fp=fopen('../log/website_error_log.php','a');
							fwrite($fp, $local_log . ""); 

							fclose($fp);  // close file
							
							// used for passing a value.
							$reset_mail = 0;

							// Sets usermail to 0 to prevent log spamming.
							$reset_mail_login_attempts = $user_home->runQuery("UPDATE paypal_donation_login_attempts SET usermail=:user_mail WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address");
							$reset_mail_login_attempts->execute(array(":user_mail"=>$reset_mail, ":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip));
						}
					}
				// Gives a message if sandbox is enabled.
				if ($use_sandbox == true)
					{
						echo "
								<div class='alert alert-danger'>
									<center><strong>" . $lang['sandbox_mode'] . "</strong></center>
								</div>
							";
					}
					?>
					<br>
				<hr />
			<center><?php echo $lang['made_by']; ?> Dasoldier</center>
			<center><table><tr><td><a href="?lang=en" title="English"><img src="../assets/images/flag/en.png"></a> <a href="?lang=es" title="Spanish"><img src="../assets/images/flag/es.png"></a> <a href="?lang=nl" title="Netherlands"><img src="../assets/images/flag/nl.png"></a></td></tr></table></center>
			<!--/.fluid-container-->
			<script src="../assets/bootstrap/js/jquery-1.9.1.min.js"></script>
			<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
			<script src="../assets/js/scripts.js"></script>
	</body>
</html>