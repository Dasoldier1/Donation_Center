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

// Checks if user is logged in, otherwise redirect to index.php.
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
							// Start admin menu content.
							if ($user_role != $row['userRole'])
								{
							?>
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_admin_info']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<li><a href="database_log.php"><?php echo $lang['menu_admin_db_log']; ?></a></li>
											<li><a href="web_ipn_error_log.php"><?php echo $lang['menu_admin_web_ipn_log']; ?></a></li>
											<li><a href="paypal_response_log.php"><?php echo $lang['menu_admin_paypal_response']; ?></a></li>
											<li><a href="how_to.php"><?php echo $lang['menu_admin_how_to']; ?></a></li>
											<li><a href="support_links.php"><?php echo $lang['menu_admin_support_links']; ?></a></li>
										</ul>
									</li>
								<?php
								}	// End admin menu content.
								?>
							<li class="inactive">
								<a href="../user/select_char.php"><?php echo $lang['menu_select_character']; ?></a>
							</li>
								<li class="dropdown">
									<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_donation_options']; ?><b class="caret"></b></a>
									<ul class="dropdown-menu" id="menu1">
										<?php
										// Checks if get coins/item is enabled in config.
										if ($coins_enabled == true)
											{
												?>
												<li><a href="../user/get_item.php"><?php echo $lang['menu_get_item'], $item_name, '\'s'; ?></a></li>
												<?php
											}
										// Checks if remove karma is enabled in config.
										if ($karma_enabled == true)
											{
												?>
												<li><a href="../user/remove_karma.php"><?php echo $lang['menu_remove_karma']; ?></a></li>
												<?php
											}
										// Checks if remove pk points is enabled in config.
										if ($pkpoints_enabled == true)
											{
												?>
												<li><a href="../user/remove_pk.php"><?php echo $lang['menu_remove_pk']; ?></a></li>
												<?php
											}
										// Checks if enchant is enabled in config.
										if ($enchant_item_enabled == true)
											{
												?>
												<li><a href="../user/enchant_items.php"><?php echo $lang['menu_enchant_equip_itmes']; ?></a></li>
												<?php
											}
											?>
									</ul>
								</li>
								<li class="dropdown">
									<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_user_info']; ?><b class="caret"></b></a>
									<ul class="dropdown-menu" id="menu1">
										<li><a href="../user/credits.php"><?php echo $lang['menu_user_credits']; ?></a></li>
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
			<?php 
			// Start admin menu content.
			if ($user_role != $row['userRole'])
				{
			?>
				<center><h4><p><?php echo $lang['admin_message_response_log']; ?></p></h4></center>
					<hr />
						<blockquote>
							<?php
							include '../log/ipn_log.php';
							?>
						</blockquote>
				<?php 
				} // End admin menu content 
				else
					{
						echo "
								<div class='alert alert-error'>
									<center><strong>" . $lang['warning_not_allowed'] . "</strong></center>
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