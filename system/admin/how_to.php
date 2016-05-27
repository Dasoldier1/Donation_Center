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
				<center><h4><p><?php echo $lang['admin_message_how_to']; ?></p></h4></center>
					<hr />
						<blockquote>
							<b>Configure config files</b><br><br>
							1. Edit system/connect.php<br>
							2. Go to system/config.php and change this file according to YOUR preferred settings<br>
							2. Go to system/admin/admin_config.php and change this file according to YOUR preferred settings<br>
						</blockquote>
						<hr />
						<blockquote>
							<b>Testing the ipn file trough the ipn simulator</b><br><br>
							Login:  <a href="https://developer.paypal.com/webapps/developer/applications/ipn_simulator" target='_blank'>Visit paypal's ipn simulator</a><br>
							Step1: choose your ipn file and select web accept:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/12985371/8546e594-d0f2-11e5-9338-b30f01f1b338.png"><br><br>
							Step2: check if its instant, confirmed and verified:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/12985033/e00a54fe-d0f0-11e5-8946-cc45eae5b663.png"><br><br>
							step3: you can select every donation option just change mc_gross and custom field:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/12985160/66eaeb78-d0f1-11e5-842e-f07d08eb4f25.png"><br><br>
							// Note for selecting the correct option (coins karma pk) use the correct mc_gross amount same as your config.<br>
							Charactername|Coins<br>
							Charactername|Karma<br>
							Charactername|Pkpoints<br><br>
							// These ones are for the enchant option.<br>
							charname|Enchitems|Shirt<br>
							charname|Enchitems|Helmet<br>
							charname|Enchitems|Necklace<br>
							charname|Enchitems|Weapon<br>
							charname|Enchitems|FullarmorBreastplate<br>
							charname|Enchitems|Shield<br>
							charname|Enchitems|Ring1<br>
							charname|Enchitems|Ring2<br>
							charname|Enchitems|Earring1<br>
							charname|Enchitems|Earring2<br>
							charname|Enchitems|Gloves<br>
							charname|Enchitems|Leggings<br>
							charname|Enchitems|Boots<br>
							charname|Enchitems|Belt<br><br>
							Change  MC_gross = donation amount. Same as your config otherwise the donations wont work.<br>
							It will get logged into admin/donationoverview because the donation amount is not correct.<br>
							And turn sandbox mode on in config if you are testing trough the ipn simulator. Otherwise it will also fail.
						</blockquote>
						<hr />
						<blockquote>
						<b>Adding the ipn file to your paypal account</b><br><br>
							Login:  <a href="https://paypal.com" target='_blank'>Login into paypal.com</a><br>
							Step1: Go to selling tools:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449431/ff3f29e0-1f7d-11e6-80bf-8d87bdba1bdc.jpg"><br><br>
							Step2: Then go to Instant payment notifications:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449432/01549dd2-1f7e-11e6-82fc-fe05356f2c23.jpg"><br><br>
							Step3: Now enable ipn and add your ipn file (it is located in your assets folder):<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449433/03118874-1f7e-11e6-9566-1ee518fb61bc.jpg"><br>
						</blockquote>
						<hr />
						<blockquote>
						<b>Adding a app password on your gmail account</b><br><br>
							Login:  <a href="https://gmail.com" target='_blank'>Login into gmail.com</a><br>
							Step1: Go to my account and press on sign in and security:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449533/c8384064-1f80-11e6-9b0d-f1588f4ebb2d.jpg"><br><br>
							Step2: Now go to Password & sign-in method:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449536/c9b4dac4-1f80-11e6-8137-67e409da422b.jpg"><br><br>
							Step3: Here you want to generate a app password:<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449537/cb37a340-1f80-11e6-9b9d-4ae78c5c9959.jpg"><br><br>
							Step4: Now you have a app password you can use for the donation system: Press done.<br>
							<img src="https://cloud.githubusercontent.com/assets/12301312/15449539/cf14928e-1f80-11e6-9e64-20e41dc94912.jpg"><br><br>
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
			<center><table><tr><td><a href="?lang=en" title="English"><img src="../assets/images/flag/en.png" alt="English"></a> <a href="?lang=es" title="Spanish"><img src="../assets/images/flag/es.png" alt="Spanish"></a> <a href="?lang=nl" title="Netherlands"><img src="../assets/images/flag/nl.png" alt="Netherlands"></a></td></tr></table></center>
			<!--/.fluid-container-->
			<script src="../assets/bootstrap/js/jquery-1.9.1.min.js"></script>
			<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
			<script src="../assets/js/scripts.js"></script>
	</body>
</html>