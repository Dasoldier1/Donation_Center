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

// Checks if user is already logged in
if($user->is_logged_in()!="")
	{
		$user->redirect('home.php');
	}

// If submit button is used.
if(isset($_POST['btn-submit']))
{
	// Wait for X seconds.
	if ($loading_delay == true)
		{
			sleep($delaytime);
		}
		// Checks if captcha is correct.
		if ($securimage->check($_POST['captcha_code']) == true) 
		{
				$email = $user->sanitize($_POST['txtemail']);
				
		// Checks if its truely a valid e-mail address.
		if ( !preg_match('/^(?:[\w\d-]+\.?)+@(?:(?:[\w\d]\-?)+\.)+\w{2,63}$/i', $email))
			{
				$msg = "
					<div class='alert alert-error'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<center><strong>" . $lang['warning_email_not_valid'] . "</strong></center>
					</div>
					";
			}
		// E-mail address is valid.
		else
			{
			try
				{
					// Select userEmail.
					$select_user = $user->runQuery("SELECT userID FROM paypal_donation_users WHERE userEmail=:email LIMIT 1");
					$select_user->execute(array(":email"=>$email));
					$row = $select_user->fetch(PDO::FETCH_ASSOC);

				// Checks if e-mail address exsists.
				if($select_user->rowCount() == 1)
					{
						$id = base64_encode($row['userID']);
						$code = md5(uniqid(rand()));
						
						$update_token = $user->runQuery("UPDATE paypal_donation_users SET tokenCode=:token WHERE userEmail=:email");
						$update_token->execute(array(":token"=>$code,":email"=>$email));
						
						// Here we put the e-mail content for changing the password
						$message= "
									" . $lang['mail_pass_message_1'] . " $email
									<br /><br />
									" . $lang['mail_pass_message_2'] . "
									<br /><br />
									" . $lang['mail_pass_message_3'] . "
									<br /><br />
									<a href='" . $donation_center_folder_loc . "resetpass.php?id=$id&code=$code'>" . $lang['mail_pass_message_4'] . "</a>
									<br />";

						$subject = $lang['mail_pass_message_5'];
						
						// Lets send the e-mail.
						$user->send_mail($email,$message,$subject);
						
						// Give some info to the user.
						$msg = "<div class='alert alert-success'>
									<button class='close' data-dismiss='alert'>&times;</button>
									<center><strong>" . $lang['dc_message_8'] . " $email.
									" . $lang['dc_message_9'] . "</strong></center>
								</div>";
					}
					// E-mail adress not found 
					else
						{
							$msg = "<div class='alert alert-danger'>
										<button class='close' data-dismiss='alert'>&times;</button>
										<center><strong>" . $lang['warning_email_not_found'] . "</strong></center>
									</div>";
						}
				}
				catch(PDOException $e)
					{
						require('system/config.php');
						
						// Visible end user reporting.
						if ($use_reporting == true)
						{
							echo 'ERROR: ' . $e->getMessage();
						}
						
						//logging: Timestamp
						$local_log = '['.date('m/d/Y g:i A').'] - '; 

						//logging: response from the server
						$local_log .= "FPASS.PHP ERROR: ". $e->getMessage();
						$local_log .= '<hr />';

						// Write to log
						$fp=fopen('system/log/website_error_log.php','a');
						fwrite($fp, $local_log . ""); 

						fclose($fp);  // close file
					}
			}
	}
	// Captcha is wrong.
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
				<center><h2 class="form-signin-heading"><?php echo $lang['dc_message_10']; ?></h2></center><hr />
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
					// Show warning when captcha is wrong
					if(isset($captcha_error))
						{
							echo $captcha_error;
						}
					// Show a message to the user on what happend.
					if(isset($msg))
						{
							echo $msg;
						}
							?>
								<div class='alert alert-info'>
								<?php echo $lang['dc_message_11']; ?>
								</div>  
								<input type="email" class="input-block-level" placeholder="Email address" name="txtemail" required />
								<input type="text" class="input-block-level" placeholder="Captcha code" name="captcha_code" maxlength="6" required/>
								<center><img id="captcha" src="system/assets/securimage/securimage_show.php" alt="CAPTCHA Image" /><br>
								<a href="#" class="btn btn-small" onclick="document.getElementById('captcha').src = 'system/assets/securimage/securimage_show.php?' + Math.random(); return false"><?php echo $lang['dc_message_7']; ?></a></center>
								<hr />
								<center><button class="btn btn-danger btn-primary" type="submit" name="btn-submit"><?php echo $lang['dc_message_12']; ?></button>
								<hr /><a href="index.php" class="btn btn-large"><?php echo $lang['dc_message_13']; ?></a></center>
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