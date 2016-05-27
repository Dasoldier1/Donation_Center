<?php
require_once 'class.user.php';
include 'system/assets/languages/common.php';
require 'system/config.php';

// Reporting to end users.
if ($use_reporting == false)
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

$user = new USER();
$database_class = new Database();

// Checks the connection.
$con_check = $database_class->dbConnection();

// Checks if id and token code is empty if so redirect user to index.php.
if(empty($_GET['id']) && empty($_GET['code']))
{
	$user->redirect('index.php');
}

// Checks if id and token code is setted.
if(isset($_GET['id']) && isset($_GET['code']))
{
	$id = base64_decode($_GET['id']);
	$code = $_GET['code'];
	
	$statusY = "Y";
	$statusN = "N";
	
	// Select userID and userStatus based on uid and token.
	$select_uid_ustatus = $user->runQuery("SELECT userID,userStatus FROM paypal_donation_users WHERE userID=:uID AND tokenCode=:code LIMIT 1");
	$select_uid_ustatus->execute(array(":uID"=>$id,":code"=>$code));
	$row=$select_uid_ustatus->fetch(PDO::FETCH_ASSOC);
	
	// Checks if the user got the correct id and token.
	if($select_uid_ustatus->rowCount() > 0)
	{
		// Checks if the account is activated.
		if($row['userStatus']==$statusN)
			{
				// Activate the account.
				$activate_account = $user->runQuery("UPDATE paypal_donation_users SET userStatus=:status WHERE userID=:uID");
				$activate_account->bindparam(":status",$statusY);
				$activate_account->bindparam(":uID",$id);
				$activate_account->execute();
				
				// Sends a success message to the user.
				$msg = "
						<div class='alert alert-success'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center>" . $lang['dc_message_21'] . " <strong><a href='index.php'>" . $lang['dc_message_22'] . "</a></strong></center>
						</div>
						";
			}
		// Account is already activated, Send message.
		else
			{
				$msg = "
						<div class='alert alert-error'>
							<button class='close' data-dismiss='alert'>&times;</button>
							<center>" . $lang['warning_account_activated'] . " <strong><a href='index.php'>" . $lang['dc_message_22'] . "</a></strong></center>
						</div>
						";
			}
		}
	// No account found, Send message.
	else
		{
			$msg = "
					<div class='alert alert-error'>
						<button class='close' data-dismiss='alert'>&times;</button>
						<center>" . $lang['warning_account_not_found'] . " <strong><a href='signup.php'>" . $lang['dc_message_23'] . "</a></strong></center>
					</div>
					";
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
				<center><h2 class="form-signin-heading"><?php echo $lang['dc_message_24']; ?></h2></center><hr />
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
				// Give some info to the user.
				if(isset($msg))
					{
						echo $msg;
					}
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