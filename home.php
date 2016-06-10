<?php
session_start();
require_once 'class.user.php';
include 'system/assets/languages/common.php';
require 'system/config.php';

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
$xml_file = "system/assets/xml/item_names.xml";
$xmlload = simplexml_load_file($xml_file);

// Full item name based on id and xml. Always use item id minus 1.
$item_id_min1 = $item_id - 1;
$item_name = $xmlload->item[$item_id_min1]['name'];

// Checks if user is logged in, otherwise redirect to index.php.
if(!$user_home->is_logged_in())
	{
		$user_home->redirect('index.php');
	}
// Redirect to logout if no connection to the database can be made.
if ($con_check == false)
	{
		$user_home->redirect('logout.php');
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
		<link href="system/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="system/assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
		<link href="system/assets/css/styles.css" rel="stylesheet" media="screen">
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
						<a class="brand" href="index.php"><?php echo $lang['menu_brand']; ?></a>
						<div class="nav-collapse collapse">
							<ul class="nav pull-right">
								<li class="dropdown">
									<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"> <i class="icon-user"></i> 
										<?php echo $row['userEmail']; ?> <i class="caret"></i>
									</a>
									<ul class="dropdown-menu">
										<li>
											<a tabindex="-1" href="logout.php"><?php echo $lang['menu_logout']; ?></a>
										</li>
									</ul>
								</li>
							</ul>
							<ul class="nav">
								<li class="active">
									<a href="index.php"><?php echo $lang['menu_home']; ?></a>
								</li>
							<?php
							// Start admin menu content.
							if ($user_role != $row['userRole'])
								{
							?>
							
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_admin_info']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<li><a href="system/admin/database_log.php"><?php echo $lang['menu_admin_db_log']; ?></a></li>
											<li><a href="system/admin/web_ipn_error_log.php"><?php echo $lang['menu_admin_web_ipn_log']; ?></a></li>
											<li><a href="system/admin/paypal_response_log.php"><?php echo $lang['menu_admin_paypal_response']; ?></a></li>
											<li><a href="system/admin/how_to.php"><?php echo $lang['menu_admin_how_to']; ?></a></li>
											<li><a href="system/admin/support_links.php"><?php echo $lang['menu_admin_support_links']; ?></a></li>
										</ul>
									</li>
						<?php } // End admin menu content ?>
								<li class="inactive">
									<a href="system/user/select_char.php"><?php echo $lang['menu_select_character']; ?></a>
								</li>
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_donation_options']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<?php
											// Checks if get coins/item is enabled in config.
											if ($coins_enabled == true)
												{
													?>
													<li><a href="system/user/get_item.php"><?php echo $lang['menu_get_item'], $item_name, '\'s'; ?></a></li>
													<?php
												}
											// Checks if remove karma is enabled in config.
											if ($karma_enabled == true)
												{
													?>
													<li><a href="system/user/remove_karma.php"><?php echo $lang['menu_remove_karma']; ?></a></li>
													<?php
												}
											// Checks if remove pk points is enabled in config.
											if ($pkpoints_enabled == true)
												{
													?>
													<li><a href="system/user/remove_pk.php"><?php echo $lang['menu_remove_pk']; ?></a></li>
													<?php
												}
											// Checks if enchant is enabled in config.
											if ($enchant_item_enabled == true)
												{
													?>
													<li><a href="system/user/enchant_items.php"><?php echo $lang['menu_enchant_equip_itmes']; ?></a></li>
													<?php
												}
												?>
										</ul>
									</li>
									<li class="dropdown">
										<a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $lang['menu_user_info']; ?><b class="caret"></b></a>
										<ul class="dropdown-menu" id="menu1">
											<li><a href="system/user/credits.php"><?php echo $lang['menu_user_credits']; ?></a></li>
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
				<center><h4><p><?php echo $lang['dc_home_page']; ?></p></h4></center>
					<hr />
					<?php
				// Start admin content.
				if ($user_role != $row['userRole'])
					{
					try {
							$result_amount_count = $database_class->dbConnection()->query("SELECT count(amount) FROM paypal_donation_log");
							$result_amount_count_fetch = $result_amount_count->fetchColumn();

							$result_online_count = $database_class->dbConnection()->query("SELECT online FROM characters WHERE online = 1");
							$result_online_count_fetch = $result_online_count->rowCount();
						
							$result_amount = $database_class->dbConnection()->query("SELECT sum(amount) FROM paypal_donation_log");
							$result_amount_fetch = $result_amount->fetchColumn();
							
							$result_amount_fee = $database_class->dbConnection()->query("SELECT sum(amountminfee) FROM paypal_donation_log");
							$result_amount_fee_fetch = $result_amount_fee->fetchColumn();
							$result_amount_fee_format = number_format($result_amount_fee_fetch, 2, '.', ',');
						}
					catch(PDOException $e) 
						{
							// Visible end user reporting.
							if ($use_reporting == true)
							{
								echo 'ERROR: ' . $e->getMessage();
							}
							//logging: Timestamp
							$local_log = '['.date('m/d/Y g:i A').'] - '; 

							//logging: response from the server
							$local_log .= "ADMIN HOME.PHP ERROR: ". $e->getMessage();
							$local_log .= '<hr />';

							// Write to log
							$fp=fopen('system/log/website_error_log.php','a');
							fwrite($fp, $local_log . ""); 

							fclose($fp);  // close file
						}
					?>
					<center><blockquote>
							<table cellpadding="0" cellspacing="0" border="2" width="240px">
								<tr><td>
									<?php
										echo $lang['dc_home_on_players'], ' ', $result_online_count_fetch, '<br>';
										echo $lang['dc_home_times_donated'], ' ', $result_amount_count_fetch, '<br>';
										echo $lang['dc_home_total_donated'], ' ', $currency_code_html, ' ', $result_amount_fetch, '<br>';
										echo $lang['dc_home_donated_fee'], ' ', $currency_code_html, ' ', $result_amount_fee_format, '<br>';
									?>
								</td></tr>
							</table>
						</blockquote>
						<blockquote>
							<p>
							<?php
								echo $lang['dc_home_admin_message_1'], '<br>';
								echo $lang['dc_home_admin_message_2'];
							?>
							</p>
						</blockquote></center>
				<?php 
					}
					// End admin content.

				// Start user content
				if ($user_role == $row['userRole'])
					{
				?>
						<center><blockquote>
							<p>
							<?php
								echo $lang['dc_home_user_message_1'], '<br>';
								echo $lang['dc_home_user_message_2'];
							?>
							</p>
						</blockquote></center>
				<?php
					} // End user content.
					// Checks if the user has a character connected to his account
					if (strlen($row['characterName']) == '')
						{
							echo "
								<div class='alert alert-error'>
									<center><strong>" . $lang['warning_character_not_set'] . "</strong></center>
								</div>
							";
						}
					else
						{
							echo "
								<div class='alert alert-success'>
									<center><strong>" . $lang['warning_character_set'], ' ', $row['characterName'] . "</strong></center>
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
			<!--/.fluid-container-->
			<script src="system/assets/bootstrap/js/jquery-1.9.1.min.js"></script>
			<script src="system/assets/bootstrap/js/bootstrap.min.js"></script>
			<script src="system/assets/js/scripts.js"></script>
	</body>
</html>