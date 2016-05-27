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

$reg_user = new USER();
$securimage = new Securimage();
$database_class = new Database();

// Checks the connection.
$con_check = $database_class->dbConnection();

if($reg_user->is_logged_in()!="")
{
	$reg_user->redirect('home.php');
}

// If submit button is used.
if(isset($_POST['btn-signup']))
	{
	// Wait for x seconds.
	if ($loading_delay == true)
		{
			sleep($delaytime);
		}
	// Checks if captcha is correct.
	if ($securimage->check($_POST['captcha_code']) == true) 
		{
			$uname = $reg_user->sanitize($_POST['txtuname']);
			$email = $reg_user->sanitize($_POST['txtemail']);
			$upass = $reg_user->sanitize($_POST['txtpass']);
			$upass2 = $reg_user->sanitize($_POST['txtpass2']);
			if ($admin_account == false)
				{
					$userrole = 'USER';
				}
			else
				{
					$adminpass = $reg_user->sanitize($_POST['txtadminpass']);
					$userrole = 'ADMIN';
				}
			$code = md5(uniqid(rand()));
			
			// Select userEmail.
			$user_email = $reg_user->runQuery("SELECT * FROM paypal_donation_users WHERE userEmail=:email_id");
			$user_email->execute(array(":email_id"=>$email));
			$row = $user_email->fetch(PDO::FETCH_ASSOC);
			
			// Select userName.
			$user_name = $reg_user->runQuery("SELECT * FROM paypal_donation_users WHERE userName=:username");
			$user_name->execute(array(":username"=>$uname));
			$row_user_name = $user_name->fetch(PDO::FETCH_ASSOC);
			
			
			// Checks if username field is empty.
			if (strlen($uname) == 0)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_user_empty'] . "</strong></center>
						</div>
						";
				}
			// Checks if username field is minimal 4 characters.
			else if (strlen($uname) <= 3)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_user_min_chars'] . "</strong></center>
						</div>
						";
				}
			// Checks if email is empty
			else if (strlen($email) == 0)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_email_empty'] . "</strong></center>
						</div>
						";
				}
			// Checks if its truely a e-mail address
			else if ( !preg_match('/^(?:[\w\d-]+\.?)+@(?:(?:[\w\d]\-?)+\.)+\w{2,63}$/i', $email))
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_email_not_valid'] . "</strong></center>
						</div>
						";
				}
			// E-mail already exists
			else if($user_email->rowCount() > 0)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_email_exists'] . "</strong></center>
						</div>
						";
				}
			// Checks if password is empty
			else if (strlen($upass) == 0)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_pass_empty'] . "</strong></center>
						</div>
						";
				}
			// Checks if password is minimal 6 characters
			else if (strlen($upass) <= 5)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_pass_min_chars'] . "</strong></center>
						</div>
						";
				}
			// Checks if confirm password field is the same as password field.
			else if ($upass != $upass2)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center><strong>" . $lang['warning_pass_confirm_field'] . "</strong></center>
						</div>
						";
				}
			// Username already exists
			else if($user_name->rowCount() > 0)
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
								<center><strong>" . $lang['warning_username_exists'] . "</strong></center>
						</div>
						";
				}
			// Checks the admin creation password
			else if (($admin_account == true) && ($adminpass != $admin_reg_password))
				{
					$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
								<center><strong>" . $lang['warning_wrong_admin_pass'] . "</strong></center>
						</div>
						";
				}
			// Suc6 lets create a account and send a e-mail with confimation code 
			else
			{
				// Create the new user.
				if($reg_user->register($uname,$email,$upass,$userrole,$code))
				{
					$id = $reg_user->lasdID();
					$key = base64_encode($id);
					$id = $key;
					
					// Here we put the e-mail content.
					$message = "
								" . $lang['mail_signup_message_1'] . " $uname,
								<br /><br />
								" . $lang['mail_signup_message_2'] . "<br/>
								<br /><br />
								<a href='" . $donation_center_folder_loc . "verify.php?id=$id&code=$code'>" . $lang['mail_signup_message_3'] . "</a>
								<br />";

					$subject = $lang['mail_signup_message_4'];
					
					// Lets send the e-mail.
					$reg_user->send_mail($email,$message,$subject);
					
					// Give some info to the user. suc6
					$msg = "
							<div class='alert alert-success'>
								<button class='close' data-dismiss='alert'>&times;</button>
								<center><strong>" . $lang['dc_message_18'] . "</strong> " . $lang['dc_message_19'] . " $email.
								" . $lang['dc_message_20'] . "</center>
							</div>
							";
				}
			// Query could not be executed.
			else
				{
					$msg = "
							<div class='alert alert-error'>
								<button class='close' data-dismiss='alert'>&times;</button>
								<center><strong>" . $lang['warning_query_not_executed'] . "</strong></center>
							</div>
							";
				}
			}
		}
		// Captcha is wrong.
		else
			{
			 $captcha_error = "
								<div class='alert alert-error'>
									<button class='close' data-dismiss='alert'>&times;</button>
									<center><strong>" . $lang['warning_captcha_wrong'] . "</strong></center>
								</div>";
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
				<center><h2 class="form-signin-heading"><?php echo $lang['dc_message_3']; ?></h2></center><hr />
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
					<input type="text" class="input-block-level" placeholder="Username" name="txtuname" required />
					<input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
					<input type="password" class="input-block-level" placeholder="Password" name="txtpass" required />
					<input type="password" class="input-block-level" placeholder="Confirm Password" name="txtpass2" required />
					<input type="text" class="input-block-level" placeholder="Captcha code" name="captcha_code" maxlength="6" required/>
					<?php
					// When admin account is enabled we will show the extra password field.
					if ($admin_account == true)
						{
					?>
						<input type="password" class="input-block-level" placeholder="Admin password" name="txtadminpass" required />
					<?php
						}
					?>
					<center><img id="captcha" src="system/assets/securimage/securimage_show.php" alt="CAPTCHA Image" /><br>
					<a href="#" class="btn btn-small" onclick="document.getElementById('captcha').src = 'system/assets/securimage/securimage_show.php?' + Math.random(); return false"><?php echo $lang['dc_message_7']; ?></a></center>
					<hr />
					<button class="btn btn-large btn-primary" type="submit" name="btn-signup"><?php echo $lang['dc_message_3']; ?></button>
					<a href="index.php" style="float:right;" class="btn btn-large"><?php echo $lang['dc_message_2']; ?></a>
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
	</body>
</html>