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

$user_login = new USER();
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

// Checks if user is already logged in.
if($user_login->is_logged_in()!="")
{
	$user_login->redirect('home.php');
}

// If submit button is used.
if(isset($_POST['btn-login']))
{
	// Wait for x seconds.
	if ($loading_delay == true)
		{
			sleep($delaytime);
		}

		// Checks if captcha is correct.
		if ($securimage->check($_POST['captcha_code']) == true) 
		{
			$login_attempts = $user_login->runQuery("SELECT COUNT(*) FROM paypal_donation_login_attempts WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address");
			$login_attempts->execute(array(":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip));
			$row_login_attempts = $login_attempts->fetchColumn();
			
			$uname = $user_login->sanitize($_POST['txt_name_email']);
			$email = $user_login->sanitize($_POST['txt_name_email']);
			$upass = $user_login->sanitize($_POST['txtupass']);
			// Checks if to many login attempts are tried give a message
			if ($row_login_attempts <= $allowed_login_attempts)
			{
				// If email/user is empty give a message.
				if (strlen($email) == 0)
				{
					$user_error = "	<div class='alert alert-error'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_email_user_empty'] . "</strong></center>
									</div>";
				}
				// If password is empty give a message.
				else if (strlen($upass) == 0)
					{
						$user_error =	"<div class='alert alert-error'>
												<button class='close' data-dismiss='alert'>&times;</button>
												<center><strong>" . $lang['warning_pass_empty'] . "</strong></center>
											</div>";
					}
					// suc6 redirect to home page.
					else
						{
						if($user_login->login($uname,$email,$upass,$get_ip))
							{
								$user_login->redirect('home.php');
							}
					}
			}
			// To many failed login attempts.
			else
			{
				// If to many failed login attempts are tried give a message.
				$user_error = "	<div class='alert alert-error'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['login_attempts_error'] . ' ' . $login_attempt_interval_timeout . ' ' . $lang['login_attempts_error_2'] . "</strong></center>
									</div>";

				// Used for passing a value.
				$set_usermail = 1;

				$mail_notice_check = $user_login->runQuery("SELECT sum(usermail) FROM paypal_donation_login_attempts WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address AND usermail=:umail");
				$mail_notice_check->execute(array(":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip, ":umail"=>$set_usermail));
				$row_mail_notice_check = $mail_notice_check->fetchColumn();
			// send mail if more then ? failed login attempts.
			if ($row_mail_notice_check >= $send_warning_mail_user)
				{

				// Checks if its a valid e-mail address.
				if (preg_match('/^(?:[\w\d-]+\.?)+@(?:(?:[\w\d]\-?)+\.)+\w{2,63}$/i', $email))
					{
						$final_mail = $email;
					}
				// Find e-mail adress based on username.
				else
					{
						// Gets the correct email based on username.
						$correct_mail =  $user_login->runQuery("SELECT userEmail FROM paypal_donation_users WHERE userName=:username");
						$correct_mail->execute(array(":username"=>$uname));
						$row_final_mail = $correct_mail->fetch(PDO::FETCH_ASSOC);
						$final_mail = $row_final_mail['userEmail'];
					}

					// Write to log file.
					//logging: Timestamp
					$local_log = '['.date('m/d/Y g:i A').'] - '; 

					//logging: response from the server
					$local_log .= "INDEX TO MANY FAILED LOGIN ATTEMPTS<br>";
					$local_log .= "ACCOUNT: ". $uname ."<br>";
					$local_log .= "IP-ADDRESS: ". $get_ip ."<br>";
					$local_log .= '<hr />';

					// Write to log
					$fp=fopen('system/log/website_error_log.php','a');
					fwrite($fp, $local_log . ""); 

					fclose($fp);  // close file

					// Send a e-mail message with a warning with to many failed login attempts.
					$message = "
								" . $lang['mail_failed_login_1'] . "
								<br /><br />
								" . $lang['mail_failed_login_2'] . " $uname,<br/>
								" . $lang['mail_failed_login_3'] . " $get_ip,<br/>
								<br /><br />
								" . $lang['mail_failed_login_4'] . "
								<br />";
					// Lets send the e-mail.
					$subject = $lang['mail_failed_login_5'];
					
					$user_login->send_mail($final_mail,$message,$subject);
					
					// used for passing a value.
					$reset_mail = 0;

					// Sets usermail to 0 to prevent mail and log spamming.
					$reset_mail_login_attempts = $user_login->runQuery("UPDATE paypal_donation_login_attempts SET usermail=:user_mail WHERE (dt > now() - INTERVAL :inverval MINUTE) AND address=:address");
					$reset_mail_login_attempts->execute(array(":user_mail"=>$reset_mail, ":inverval"=>$login_attempt_interval_timeout, ":address"=>$get_ip));
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
<html>
	<head>
		<title><?php echo $site_title ?></title>
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
			<center><h2 class="form-signin-heading"><?php echo $lang['dc_message_1']; ?></h2></center><hr />
<?php
			// Check if a connection could be made if not dont show the form.
		if ($con_check == true)
			{
				// Show warning when admin account creation is enabled.
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
					// Show errors for empty fields.
					if(isset($user_error))
						{
							echo $user_error;
						}
					// Show warning when captcha is wrong.
					if(isset($captcha_error))
						{
							echo $captcha_error;
						}
					// Show warning when account is not yet activated.
					if(isset($_GET['inactive']))
						{
							echo "
								<div class='alert alert-error'>
									<button class='close' data-dismiss='alert'>&times;</button>
									<center><strong>" . $lang['warning_account_not_activated'] . "</strong></center>
								</div>
							";
						}
						// If the wrong details are entered give a message.
						if(isset($_GET['error']))
							{
								echo "
									<div class='alert alert-error'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_wrong_details'] . "</strong></center>
									</div>
								";
							}
								?>
								<input type="text" class="input-block-level" placeholder="Username or E-mail" name="txt_name_email" required />
								<input type="password" class="input-block-level" placeholder="Password" name="txtupass" required />
								<input type="text" class="input-block-level" placeholder="Captcha code" name="captcha_code" maxlength="6" required/>
								<center><img id="captcha" src="system/assets/securimage/securimage_show.php" alt="CAPTCHA Image" /><br>
								<!--Change captcha image -->
								<a href="#" class="btn btn-small" onclick="document.getElementById('captcha').src = 'system/assets/securimage/securimage_show.php?' + Math.random(); return false"><?php echo $lang['dc_message_7']; ?></a></center>
								<hr />
								<button class="btn btn-large btn-primary" type="submit" name="btn-login"><?php echo $lang['dc_message_2']; ?></button>
								<a href="signup.php" style="float:right;" class="btn btn-large"><?php echo $lang['dc_message_3']; ?></a><hr />
								<center><a href="fpass.php"><?php echo $lang['dc_message_4']; ?></a></center>
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
				<hr /><center><table><tr><td><a href="?lang=en" title="English"><img src="system/assets/images/flag/en.png" alt="English"></a> <a href="?lang=es" title="Spanish"><img src="system/assets/images/flag/es.png" alt="Spanish"></a> <a href="?lang=nl" title="Netherlands"><img src="system/assets/images/flag/nl.png" alt="Netherlands"></a></td></tr></table></center>
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