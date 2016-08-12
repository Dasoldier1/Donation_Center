<?php
session_start();
require_once 'class.user.php';
include_once 'system/assets/securimage/securimage.php';
include 'system/assets/languages/common.php';
require 'system/config.php';

// Reporting to end users.
if ($use_reporting == false)
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

$user = new USER();
$securimage = new Securimage();
$database_class = new Database();

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

// Checks if id and token code is empty if so redirect user to index.php.
if(empty($_GET['id']) && empty($_GET['code']))
	{
		$user->redirect('index.php');
	}

// If submit button is used.
if(isset($_POST['btn-reset-pass']))
{
		// Wait for x seconds.
		if ($loading_delay == true)
			{
				sleep($delaytime);
			}
	// Checks if id and token code is setted.
	if(isset($_GET['id']) && isset($_GET['code']))
	{
			// Checks if captcha is correct.
			if ($securimage->check($_POST['captcha_code']) == true) 
			{
				$id = base64_decode($_GET['id']);
				$code = $_GET['code'];
				
				// Selects the user id and token code.
				$select_uid_token = $user->runQuery("SELECT * FROM paypal_donation_users WHERE userID=:uid AND tokenCode=:token");
				$select_uid_token->execute(array(":uid"=>$id,":token"=>$code));
				$rows = $select_uid_token->fetch(PDO::FETCH_ASSOC);

				// Checks if the user got the correct id and token.
				if($select_uid_token->rowCount() == 1)
				{

						$pass = $user->sanitize($_POST['pass']);
						$cpass = $user->sanitize($_POST['confirm-pass']);
						// If password is empty give a message.
						if (strlen($pass) == 0)
							{
								$msg =	"<div class='alert alert-error'>
														<button class='close' data-dismiss='alert'>&times;</button>
														<center><strong>" . $lang['warning_pass_empty'] . "</strong></center>
													</div>";
							}
						// If confirm password is empty give a message.
						else if (strlen($cpass) == 0)
							{
								$msg =	"<div class='alert alert-error'>
														<button class='close' data-dismiss='alert'>&times;</button>
														<center><strong>" . $lang['warning_confirm_pass_empty'] . "</strong></center>
													</div>";
							}
						// Checks if password is minimal 6 characters
						else if (strlen($pass) <= 5)
							{
								$msg = "
									<div class='alert alert-error'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_pass_min_chars'] . "</strong></center>
									</div>
									";
							}
						// The passwords does not match
						else if($cpass!==$pass)
							{
								$msg = "
									<div class='alert alert-block'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_pass_not_match'] . "</strong></center>
									</div>";
							}
							// The passwords are matching. 
							else
								{
									// Update the new password in the database.
									$password = password_hash($cpass, PASSWORD_DEFAULT, ['cost' => 12]);
									$update_password = $user->runQuery("UPDATE paypal_donation_users SET userPass=:upass WHERE userID=:uid");
									$update_password->execute(array(":upass"=>$password,":uid"=>$rows['userID']));
									
									// Generate a new random code
									$generate_code = md5(uniqid(rand()));
									$clear_token_code = $user->runQuery("UPDATE paypal_donation_users SET tokenCode=:tokenCode WHERE userID=:uid");
									$clear_token_code->execute(array(":tokenCode"=>$generate_code,":uid"=>$rows['userID']));
									
									$msg = "<div class='alert alert-success'>
												<button class='close' data-dismiss='alert'>&times;</button>
												<center><strong>" . $lang['dc_message_14'] . "</strong></center>
											</div>";
									// Redirect to index.php
									header("refresh:5;index.php");
								}
				}
				// No account is found
				else
				{
					$msg =
							"<div class='alert alert-danger'>
								<center><strong>" . $lang['warning_no_account'] . "</strong></center>
							</div>";
					
					// Write to log file.
					//logging: Timestamp
					$local_log = '['.date('m/d/Y g:i A').'] - '; 

					//logging: response from the server
					$local_log .= "RESETPASS.PHP SOMEONE TRIED TO CHANGE A PASSWORD WITH THE WRONG ID AND TOKEN<br>";
					$local_log .= "IP-ADDRESS: ". $get_ip ."<br>";
					$local_log .= '<hr />';

					// Write to log
					$fp=fopen('system/log/failed_login_log.php','a');
					fwrite($fp, $local_log . ""); 

					fclose($fp);  // close file

					// Redirect to index.php
					header("refresh:5;index.php");
				}
			}
			// Captcha is wrong
			else
				{
					$captcha_error = "	<div class='alert alert-error'>
											<button class='close' data-dismiss='alert'>&times;</button>
											<center><strong>" . $lang['warning_captcha_wrong'] . "</strong></center>
										</div>";
				}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $site_title; ?></title>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Lineage 2: PayPal System!">
		<meta name="keywords" content="l2, lineage, lineage2, u3games, u3g, u3, paypal, system">
		<meta name="author" content="U3games, Swarlog, Dasoldier">
		<!-- Bootstrap -->
		<link href="system/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="system/assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
		<link href="system/assets/css/styles.css" rel="stylesheet" media="screen">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
  </head>
	<body id="login">
		<div class="container">
				<form class="form-signin" method="post">
					<center><h3 class="form-signin-heading"><?php echo $lang['dc_message_16']; ?></h3></center><hr />
					<?php
			// Check if a connection could be made if not dont show the form.
			if ($con_check == true)
				{
					// Show warning when admin account is enabled.
					if ($admin_account == true)
						{
							echo "
								<div class='alert alert-danger'>
									<center><strong>" . $lang['warning_admin_creation_on'] . "</strong></center>
								</div>
							";
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
					// Show warning when captcha is wrong.
					if(isset($captcha_error))
						{
							echo $captcha_error;
						}
					// Give some info to the user.
					if(isset($msg))
						{
							echo $msg;
						}
					?>
					<div class='alert alert-success'>
						<center><?php echo $lang['dc_message_15']; ?></center>
					</div>
					<input type="password" class="input-block-level" placeholder="New Password" name="pass" required />
					<input type="password" class="input-block-level" placeholder="Confirm New Password" name="confirm-pass" required />
					<input type="text" class="input-block-level" placeholder="Captcha code" name="captcha_code" maxlength="6" required/>
					<center><img id="captcha" src="system/assets/securimage/securimage_show.php" alt="CAPTCHA Image" /><br>
					<a href="#" class="btn btn-small" onclick="document.getElementById('captcha').src = 'system/assets/securimage/securimage_show.php?' + Math.random(); return false"><?php echo $lang['dc_message_7']; ?></a></center>
					<hr />
					<center><button class="btn btn-large btn-primary" type="submit" name="btn-reset-pass"><?php echo $lang['dc_message_17']; ?></button></center>
				<?php
				}
			// Show warning when no connection could be made.
			else
				{
					?>
					<div class='alert alert-error'>
						<center><strong><?php echo $lang['dc_message_5']; ?><br><?php echo $lang['dc_message_6']; ?></strong></center>
					</div>
					<?php
				}
				?>
				<hr />
				<!--/.language bar-->
				<center>
					<table>
						<tr><td>
							<?php 
								if (($english_lang == true) or ($spanish_lang == false) && ($dutch_lang == false))
									{
										echo '<a href="?lang=en" title="English"><img src="system/assets/images/flag/en.png" alt="English"></a> ';
									}
								if ($spanish_lang == true)
									{
										echo '<a href="?lang=es" title="Spanish"><img src="system/assets/images/flag/es.png" alt="Spanish"></a> ';
									}
								if ($dutch_lang == true)
									{
										echo '<a href="?lang=nl" title="Netherlands"><img src="system/assets/images/flag/nl.png" alt="Netherlands"></a> ';
									}
							?>
						</td></tr>
					</table>
				</center>
			</form>
		</div> <!-- /container -->
		<script src="system/assets/bootstrap/js/jquery-1.9.1.min.js"></script>
		<script src="system/assets/bootstrap/js/bootstrap.min.js"></script>
		<script src="system/assets/js/scripts.js"></script>
		<script>
			$(function() {
				// Invoke the placeholder plugin
				$('input, textarea').placeholder();
			});
		</script>
	</body>
</html>