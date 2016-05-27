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
				<center><h4><p><?php echo $lang['enchant_items']; ?></p></h4></center>
					<hr />
					<?php 
				// Checks if enchant is enabled in config.
				if ($enchant_item_enabled == true)
					{
					// Checks if the user has a character connected to his account otherwise dont show the page content
					if (strlen($row['characterName']) != '')
						{
							// Gets the online status from the gameserver database according to charname.
							$account_char_check = $user_home->runQuery("SELECT online FROM characters WHERE char_name =:charname LIMIT 1");
							$account_char_check->execute(array(":charname"=>$row['characterName']));
							$row_online_check = $account_char_check->fetch(PDO::FETCH_ASSOC);
							
							// Checks if the character is offline
							if ($row_online_check['online'] == 0)
								{
									?>
									<blockquote>
									<?php
														try {
																// Gets the charid from the charname.
																$character_id = $user_home->runQuery('SELECT charId FROM characters WHERE char_name = ? LIMIT 1');
																$character_id->bindValue(1, $row['characterName'], PDO::PARAM_STR);
																$character_id->execute();
																$row_character_id = $character_id->fetch(PDO::FETCH_ASSOC);

																// Select item id FROM items WHERE owner id = char id AND loc = PAPERDOLL ( means its equipped )
																$loc_paper = 'PAPERDOLL';
																// Loc_data locations
																// Shirt
																$locdata0 = 0;
																// Helmet
																$locdata1 = 1;
																// Necklace
																$locdata4 = 4;
																// Weapon
																$locdata5 = 5;
																// Breastplate and full armor
																$locdata6 = 6;
																// Shield
																$locdata7 = 7;
																// Lower earring
																$locdata8 = 8;
																// Upper earring
																$locdata9 = 9;
																// Gloves
																$locdata10 = 10;
																// Leggings
																$locdata11 = 11;
																// Boots
																$locdata12 = 12;
																// Lower ring
																$locdata13 = 13;
																// Upper ring
																$locdata14 = 14;
																// Belt
																$locdata24 = 24;

																// Querys for ENCHANT donate section.
																// Here we select the shirt item id.
																$char_shirt_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_shirt_select->bindValue(1, $locdata0, PDO::PARAM_INT);
																$char_shirt_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_shirt_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_shirt_select->execute();
																$row_shirt_select = $char_shirt_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_shirt_id_min1 = $row_shirt_select['item_id'] - 1;
																$char_shirt_name = $xmlload->item[$char_shirt_id_min1]['name'];

																// Here we select the current shirt enchant level.
																$char_shirt_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_shirt_enchant_select->bindValue(1, $locdata0, PDO::PARAM_INT);
																$char_shirt_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_shirt_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_shirt_enchant_select->execute();
																$row_shirt_enchant_select = $char_shirt_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the current helmet item id.
																$char_helmet_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_helmet_select->bindValue(1, $locdata1, PDO::PARAM_INT);
																$char_helmet_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_helmet_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_helmet_select->execute();
																$row_helmet_select = $char_helmet_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_helmet_id_min1 = $row_helmet_select['item_id'] - 1;
																$char_helmet_name = $xmlload->item[$char_helmet_id_min1]['name'];

																// Here we select the helmet enchant level.
																$char_helmet_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_helmet_enchant_select->bindValue(1, $locdata1, PDO::PARAM_INT);
																$char_helmet_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_helmet_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_helmet_enchant_select->execute();
																$row_helmet_enchant_select = $char_helmet_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the necklace item id.
																$char_necklace_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_necklace_select->bindValue(1, $locdata4, PDO::PARAM_INT);
																$char_necklace_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_necklace_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_necklace_select->execute();
																$row_necklace_select = $char_necklace_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_necklace_id_min1 = $row_necklace_select['item_id'] - 1;
																$char_necklace_name = $xmlload->item[$char_necklace_id_min1]['name'];

																// Here we select the necklace enchant level.
																$char_necklace_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_necklace_enchant_select->bindValue(1, $locdata4, PDO::PARAM_INT);
																$char_necklace_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_necklace_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_necklace_enchant_select->execute();
																$row_necklace_enchant_select = $char_necklace_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the weapon item id.
																$char_weapon_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_weapon_select->bindValue(1, $locdata5, PDO::PARAM_INT);
																$char_weapon_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_weapon_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_weapon_select->execute();
																$row_weapon_select = $char_weapon_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_weapon_id_min1 = $row_weapon_select['item_id'] - 1;
																$char_weapon_name = $xmlload->item[$char_weapon_id_min1]['name'];

																// Here we select the weapon enchant level.
																$char_weapon_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_weapon_enchant_select->bindValue(1, $locdata5, PDO::PARAM_INT);
																$char_weapon_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_weapon_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_weapon_enchant_select->execute();
																$row_weapon_enchant_select = $char_weapon_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the breastplate and full armor item id.
																$char_breastplate_full_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_breastplate_full_select->bindValue(1, $locdata6, PDO::PARAM_INT);
																$char_breastplate_full_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_breastplate_full_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_breastplate_full_select->execute();
																$row_breastplate_full_select = $char_breastplate_full_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_breastplate_full_id_min1 = $row_breastplate_full_select['item_id'] - 1;
																$char_breastplate_full_name = $xmlload->item[$char_breastplate_full_id_min1]['name'];

																// Here we select the breastplate and full armor enchant level.
																$char_breastplate_full_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_breastplate_full_enchant_select->bindValue(1, $locdata6, PDO::PARAM_INT);
																$char_breastplate_full_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_breastplate_full_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_breastplate_full_enchant_select->execute();
																$row_breastplate_full_enchant_select = $char_breastplate_full_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the shield item id.
																$char_shield_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_shield_select->bindValue(1, $locdata7, PDO::PARAM_INT);
																$char_shield_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_shield_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_shield_select->execute();
																$row_shield_select = $char_shield_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_shield_id_min1 = $row_shield_select['item_id'] - 1;
																$char_shield_name = $xmlload->item[$char_shield_id_min1]['name'];

																// Here we select the shield enchant level.
																$char_shield_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_shield_enchant_select->bindValue(1, $locdata7, PDO::PARAM_INT);
																$char_shield_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_shield_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_shield_enchant_select->execute();
																$row_shield_enchant_select = $char_shield_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the earring1 item id.
																$char_lowearring_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_lowearring_select->bindValue(1, $locdata8, PDO::PARAM_INT);
																$char_lowearring_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_lowearring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_lowearring_select->execute();
																$row_lowearring_select = $char_lowearring_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_lowearring_id_min1 = $row_lowearring_select['item_id'] - 1;
																$char_lowearring_name = $xmlload->item[$char_lowearring_id_min1]['name'];

																// Here we select the earring1 enchant level.
																$char_lowearring_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_lowearring_enchant_select->bindValue(1, $locdata8, PDO::PARAM_INT);
																$char_lowearring_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_lowearring_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_lowearring_enchant_select->execute();
																$row_lowearring_enchant_select = $char_lowearring_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the earring2 item id.
																$char_upearring_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_upearring_select->bindValue(1, $locdata9, PDO::PARAM_INT);
																$char_upearring_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_upearring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_upearring_select->execute();
																$row_upearring_select = $char_upearring_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_upearring_id_min1 = $row_upearring_select['item_id'] - 1;
																$char_upearring_name = $xmlload->item[$char_upearring_id_min1]['name'];

																// Here we select the earring2 enchant level.
																$char_upearring_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_upearring_enchant_select->bindValue(1, $locdata9, PDO::PARAM_INT);
																$char_upearring_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_upearring_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_upearring_enchant_select->execute();
																$row_upearring_enchant_select = $char_upearring_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the gloves item id.
																$char_gloves_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_gloves_select->bindValue(1, $locdata10, PDO::PARAM_INT);
																$char_gloves_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_gloves_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_gloves_select->execute();
																$row_gloves_select = $char_gloves_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_gloves_id_min1 = $row_gloves_select['item_id'] - 1;
																$char_gloves_name = $xmlload->item[$char_gloves_id_min1]['name'];

																// Here we select the gloves enchant level.
																$char_gloves_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_gloves_enchant_select->bindValue(1, $locdata10, PDO::PARAM_INT);
																$char_gloves_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_gloves_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_gloves_enchant_select->execute();
																$row_gloves_enchant_select = $char_gloves_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the leggings item id.
																$char_leggings_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_leggings_select->bindValue(1, $locdata11, PDO::PARAM_INT);
																$char_leggings_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_leggings_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_leggings_select->execute();
																$row_leggings_select = $char_leggings_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_leggings_id_min1 = $row_leggings_select['item_id'] - 1;
																$char_leggings_name = $xmlload->item[$char_leggings_id_min1]['name'];

																// Here we select the leggings enchant level.
																$char_leggings_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_leggings_enchant_select->bindValue(1, $locdata11, PDO::PARAM_INT);
																$char_leggings_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_leggings_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_leggings_enchant_select->execute();
																$row_leggings_enchant_select = $char_leggings_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the boots item id.
																$char_boots_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_boots_select->bindValue(1, $locdata12, PDO::PARAM_INT);
																$char_boots_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_boots_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_boots_select->execute();
																$row_boots_select = $char_boots_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_boots_id_min1 = $row_boots_select['item_id'] - 1;
																$char_boots_name = $xmlload->item[$char_boots_id_min1]['name'];

																// Here we select the boots enchant level.
																$char_boots_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_boots_enchant_select->bindValue(1, $locdata12, PDO::PARAM_INT);
																$char_boots_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_boots_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_boots_enchant_select->execute();
																$row_boots_enchant_select = $char_boots_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the ring1 item id.
																$char_lowring_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_lowring_select->bindValue(1, $locdata13, PDO::PARAM_INT);
																$char_lowring_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_lowring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_lowring_select->execute();
																$row_lowring_select = $char_lowring_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_lowring_id_min1 = $row_lowring_select['item_id'] - 1;
																$char_lowring_name = $xmlload->item[$char_lowring_id_min1]['name'];

																// Here we select the ring1 enchant level.
																$char_lowring_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_lowring_enchant_select->bindValue(1, $locdata13, PDO::PARAM_INT);
																$char_lowring_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_lowring_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_lowring_enchant_select->execute();
																$row_lowring_enchant_select = $char_lowring_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the ring2 item id.
																$char_upring_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_upring_select->bindValue(1, $locdata14, PDO::PARAM_INT);
																$char_upring_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_upring_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_upring_select->execute();
																$row_upring_select = $char_upring_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_upring_id_min1 = $row_upring_select['item_id'] - 1;
																$char_upring_name = $xmlload->item[$char_upring_id_min1]['name'];

																// Here we select the ring2 enchant level.
																$char_upring_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_upring_enchant_select->bindValue(1, $locdata14, PDO::PARAM_INT);
																$char_upring_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_upring_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_upring_enchant_select->execute();
																$row_upring_enchant_select = $char_upring_enchant_select->fetch(PDO::FETCH_ASSOC);

																// Here we select the belt item id.
																$char_belt_select = $user_home->runQuery('SELECT item_id FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_belt_select->bindValue(1, $locdata24, PDO::PARAM_INT);
																$char_belt_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_belt_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_belt_select->execute();
																$row_belt_select = $char_belt_select->fetch(PDO::FETCH_ASSOC);
																// Full item name based on id and xml. Always use character item id minus 1.
																$char_belt_id_min1 = $row_belt_select['item_id'] - 1;
																$char_belt_name = $xmlload->item[$char_belt_id_min1]['name'];

																// Here we select the belt enchant level.
																$char_belt_enchant_select = $user_home->runQuery('SELECT enchant_level FROM items WHERE loc_data = ? AND owner_id = ? AND loc = ? LIMIT 1');
																$char_belt_enchant_select->bindValue(1, $locdata24, PDO::PARAM_INT);
																$char_belt_enchant_select->bindValue(2, $row_character_id['charId'], PDO::PARAM_INT);
																$char_belt_enchant_select->bindValue(3, $loc_paper, PDO::PARAM_STR);
																$char_belt_enchant_select->execute();
																$row_belt_enchant_select = $char_belt_enchant_select->fetch(PDO::FETCH_ASSOC);
															}
														catch(PDOException $e) {

															// Visible end user reporting.
															if ($use_reporting == true)
															{
																echo 'ERROR: ' . $e->getMessage();
															}

																// Logging: Timestamp.
																$local_log = '['.date('m/d/Y g:i A').'] - ';

																// Logging: response from the server.
																$local_log .= "INDEX.PHP ENCHANT ITEMS ERROR: ". $e->getMessage();
																$local_log .= '</td></tr><tr><td>';

																// Write to log.
																$fp=fopen('../log/website_error_log.php','a');
																fwrite($fp, $local_log . ""); 

																// Close file.
																fclose($fp);
														}
														?>
												<center>
													<form action="enchant_items_finish.php" method="post">
														<input type="hidden" name="on0" value="enchant">
														<select name="os0">
														<?php
													// Checks if item enchant is enabled in config.
													if ($shirt_enchant_enabled == true)
														{
														// Checks if shirt is equipped.
														if ($row_shirt_select['item_id'] == '')
															{
																?>
																<option value="Shirt" disabled><?php echo $lang['enchant_4'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_shirt_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Shirt" disabled><?php echo $char_shirt_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else{
																		// Checks if config shirt enchant amount is higher than the equipped item.
																		if ($shirt_enchant_amount > $row_shirt_enchant_select['enchant_level'])
																		{
																			?>
																			<option value="Shirt"><?php echo '+', $shirt_enchant_amount, ' ', 'Shirt: ', $char_shirt_name, ' ', $currency_code_html, $shirt_donate_amount;?>.00 </option>
																			<?php
																		}
																		// Cannot be enchanted higher.
																		else
																		{
																			?>
																			<option value="Shirt" disabled><?php echo '+', $row_shirt_enchant_select['enchant_level'], ' ', $char_shirt_name, ' ', $lang['enchant_3'];?></option>
																			<?php
																		}
																	}
																}
															}
														// This function is disabled.
														else
															{
																?>
																<option value="Shirt" disabled><?php echo $lang['enchant_16'];?></option>
																<?php
															}
													// Checks if helmet enchant is enabled in config.
													if ($helmet_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_helmet_select['item_id'] == '')
															{
																?>
																<option value="Helmet" disabled><?php echo $lang['enchant_5'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_helmet_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Helmet" disabled><?php echo $char_helmet_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($helmet_enchant_amount > $row_helmet_enchant_select['enchant_level'])
																		{
																			?>
																			<option value="Helmet"><?php echo '+', $helmet_enchant_amount, ' ', 'Helmet: ', $char_helmet_name, ' ', $currency_code_html, $helmet_donate_amount;?>.00 </option>
																			<?php
																		}
																		// Cannot be enchanted higher.
																		else
																		{
																			?>
																			<option value="Helmet" disabled><?php echo '+', $row_helmet_enchant_select['enchant_level'], ' ', $char_helmet_name, ' ', $lang['enchant_3'];?></option>
																			<?php
																		}
																	}
																}
															}
														// This function is disabled.
														else
															{
																?>
																<option value="Helmet" disabled><?php echo $lang['enchant_17'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($necklace_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_necklace_select['item_id'] == '')
															{
																?>
																<option value="Necklace" disabled><?php echo $lang['enchant_6'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_necklace_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Necklace" disabled><?php echo $char_necklace_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($necklace_enchant_amount > $row_necklace_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Necklace"><?php echo '+', $necklace_enchant_amount, ' ', 'Necklace: ', $char_necklace_name, ' ', $currency_code_html, $necklace_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Necklace" disabled><?php echo '+', $row_necklace_enchant_select['enchant_level'], ' ', $char_necklace_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Necklace" disabled><?php echo $lang['enchant_18'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($weapon_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_weapon_select['item_id'] == '')
															{
																?>
																<option value="Weapon" disabled><?php echo $lang['enchant_7'];?></option>
																<?php
															}
														else
															{
															// Checks if player got a blocked item id equipped.
															if (in_array($row_weapon_select['item_id'], $enc_item_blocked)) 
																{
																	?>
																	<option value="Weapon" disabled><?php echo $char_weapon_name, ' ', $lang['enchant_32'];?></option>
																	<?php
																}
																else{
																	// Checks if config enchant amount is higher than the equipped item.
																	if ($weapon_enchant_amount > $row_weapon_enchant_select['enchant_level'])
																		{
																			?>
																			<option value="Weapon"><?php echo '+', $weapon_enchant_amount, ' ', 'Weapon: ', $char_weapon_name, ' ', $currency_code_html, $weapon_donate_amount;?>.00 </option>
																			<?php
																		}
																		// Cannot be enchanted higher.
																		else
																		{
																			?>
																			<option value="Weapon" disabled><?php echo '+', $row_weapon_enchant_select['enchant_level'], ' ', $char_weapon_name, ' ', $lang['enchant_3'];?></option>
																			<?php
																		}
																	}
																}
															}
														// This function is disabled.
														else
															{
																?>
																<option value="Weapon" disabled><?php echo $lang['enchant_19'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($breastplate_full_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_breastplate_full_select['item_id'] == '')
															{
																?>
																<option value="FullarmorBreastplate" disabled><?php echo $lang['enchant_8'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_breastplate_full_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="FullarmorBreastplate" disabled><?php echo $char_breastplate_full_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($breastplate_full_enchant_amount > $row_breastplate_full_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="FullarmorBreastplate"><?php echo '+', $breastplate_full_enchant_amount, ' ', 'Armor: ', $char_breastplate_full_name, ' ', $currency_code_html, $breastplate_full_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="FullarmorBreastplate" disabled><?php echo '+', $row_breastplate_full_enchant_select['enchant_level'], ' ', $char_breastplate_full_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="FullarmorBreastplate" disabled><?php echo $lang['enchant_20'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($shield_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_shield_select['item_id'] == '')
															{
																?>
																<option value="Shield" disabled><?php echo $lang['enchant_9'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_shield_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Shield" disabled><?php echo $char_shield_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($shield_enchant_amount > $row_shield_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Shield"><?php echo '+', $shield_enchant_amount, ' ', 'Shield: ', $char_shield_name, ' ', $currency_code_html, $shield_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Shield" disabled><?php echo '+', $row_shield_enchant_select['enchant_level'], ' ', $char_shield_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Shield" disabled><?php echo $lang['enchant_21'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($ring_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_lowring_select['item_id'] == '')
															{
																?>
																<option value="Ring1" disabled><?php echo $lang['enchant_10'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_lowring_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Ring1" disabled><?php echo $char_lowring_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($ring_enchant_amount > $row_lowring_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Ring1"><?php echo '+', $ring_enchant_amount, ' ', 'Ring: ', $char_lowring_name, ' ', $currency_code_html, $ring_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Ring1" disabled><?php echo '+', $row_lowring_enchant_select['enchant_level'], ' ', $char_lowring_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
														// Checks if item is equipped.
														if ($row_upring_select['item_id'] == '')
															{
																?>
																<option value="Ring2" disabled><?php echo $lang['enchant_10'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_upring_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Ring2" disabled><?php echo $char_upring_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($ring_enchant_amount > $row_upring_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Ring2"><?php echo '+', $ring_enchant_amount, ' ', 'Ring: ', $char_upring_name, ' ', $currency_code_html, $ring_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Ring2" disabled><?php echo '+', $row_upring_enchant_select['enchant_level'], ' ', $char_upring_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Ring1" disabled><?php echo $lang['enchant_22'];?></option>
																<option value="Ring2" disabled><?php echo $lang['enchant_22'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($earring_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_lowearring_select['item_id'] == '')
															{
																?>
																<option value="Earring1" disabled><?php echo $lang['enchant_11'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_lowearring_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Earring1" disabled><?php echo $char_lowearring_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($earring_enchant_amount > $row_lowearring_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Earring1"><?php echo '+', $earring_enchant_amount, ' ', 'Earring: ', $char_lowearring_name, ' ', $currency_code_html, $earring_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Earring1" disabled><?php echo '+', $row_lowearring_enchant_select['enchant_level'], ' ', $char_lowearring_name, ' ', $lang['enchant_3'];?>option>
																				<?php
																			}
																		}
																	}
														// Checks if item is equipped.
														if ($row_upearring_select['item_id'] == '')
															{
																?>
																<option value="Earring2" disabled><?php echo $lang['enchant_11'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_upearring_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Earring2" disabled><?php echo $char_upearring_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($earring_enchant_amount > $row_upearring_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Earring2"><?php echo '+', $earring_enchant_amount, ' ', 'Earring: ', $char_upearring_name, ' ', $currency_code_html, $earring_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Earring2" disabled><?php echo '+', $row_upearring_enchant_select['enchant_level'], ' ', $char_upearring_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Earring1" disabled><?php echo $lang['enchant_23'];?></option>
																<option value="Earring2" disabled><?php echo $lang['enchant_23'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($gloves_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_gloves_select['item_id'] == '')
															{
																?>
																<option value="Gloves" disabled><?php echo $lang['enchant_12'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_gloves_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Gloves" disabled><?php echo $char_gloves_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($gloves_enchant_amount > $row_gloves_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Gloves"><?php echo '+', $gloves_enchant_amount, ' ', 'Gloves: ', $char_gloves_name, ' ', $currency_code_html, $gloves_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Gloves" disabled><?php echo '+', $row_gloves_enchant_select['enchant_level'], ' ', $char_gloves_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Gloves" disabled><?php echo $lang['enchant_24'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($leggings_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_leggings_select['item_id'] == '')
															{
																?>
																<option value="Leggings" disabled><?php echo $lang['enchant_13'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_leggings_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Leggings" disabled><?php echo $char_leggings_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($leggings_enchant_amount > $row_leggings_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Leggings"><?php echo '+', $leggings_enchant_amount, ' ', 'leggings: ', $char_leggings_name, ' ', $currency_code_html, $leggings_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Leggings" disabled><?php echo '+', $row_leggings_enchant_select['enchant_level'], ' ', $char_leggings_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Leggings" disabled><?php echo $lang['enchant_25'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($boots_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_boots_select['item_id'] == '')
															{
																?>
																<option value="Boots" disabled><?php echo $lang['enchant_14'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_boots_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Boots" disabled><?php echo $char_boots_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($boots_enchant_amount > $row_boots_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Boots"><?php echo '+', $boots_enchant_amount, ' ', 'Boots: ', $char_boots_name, ' ', $currency_code_html, $boots_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Boots" disabled><?php echo '+', $row_boots_enchant_select['enchant_level'], ' ', $char_boots_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Boots" disabled><?php echo $lang['enchant_26'];?></option>
																<?php
															}
													// Checks if item enchant is enabled in config.
													if ($belt_enchant_enabled == true)
														{
														// Checks if item is equipped.
														if ($row_belt_select['item_id'] == '')
															{
																?>
																<option value="Belt" disabled><?php echo $lang['enchant_15'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_belt_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="Belt" disabled><?php echo $char_belt_name, ' ', $lang['enchant_32'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if ($belt_enchant_amount > $row_belt_enchant_select['enchant_level'])
																			{
																				?>
																				<option value="Belt"><?php echo '+', $belt_enchant_amount, ' ', 'Belt: ', $char_belt_name, ' ', $currency_code_html, $belt_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="Belt" disabled><?php echo '+', $row_belt_enchant_select['enchant_level'], ' ', $char_belt_name, ' ', $lang['enchant_3'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="Belt" disabled><?php echo $lang['enchant_27'];?></option>
																<?php
															}
													// Checks if ALL item enchant is enabled in config.
													if ($all_enchant_enabled == true)
														{
														// Checks if all items are equipped.
														if (($row_shirt_select['item_id'] == '') || ($row_helmet_select['item_id'] == '') || ($row_necklace_select['item_id'] == '') || ($row_weapon_select['item_id'] == '') || ($row_breastplate_full_select['item_id'] == '') || ($row_shield_select['item_id'] == '') || ($row_lowring_select['item_id'] == '') || ($row_upring_select['item_id'] == '') || ($row_lowearring_select['item_id'] == '') || ($row_upearring_select['item_id'] == '') || ($row_gloves_select['item_id'] == '') || ($row_leggings_select['item_id'] == '') || ($row_boots_select['item_id'] == '') || ($row_belt_select['item_id'] == ''))
															{
																?>
																<option value="All_Enc" disabled><?php echo $lang['enchant_28'];?></option>
																<?php
															}
															else
																{
																// Checks if player got a blocked item id equipped.
																if (in_array($row_shirt_select['item_id'], $enc_item_blocked) || in_array($row_helmet_select['item_id'], $enc_item_blocked) || in_array($row_necklace_select['item_id'], $enc_item_blocked) || in_array($row_weapon_select['item_id'], $enc_item_blocked) || in_array($row_breastplate_full_select['item_id'], $enc_item_blocked) || in_array($row_shield_select['item_id'], $enc_item_blocked) || in_array($row_lowring_select['item_id'], $enc_item_blocked) || in_array($row_upring_select['item_id'], $enc_item_blocked) || in_array($row_lowearring_select['item_id'], $enc_item_blocked) || in_array($row_upearring_select['item_id'], $enc_item_blocked) || in_array($row_gloves_select['item_id'], $enc_item_blocked) || in_array($row_leggings_select['item_id'], $enc_item_blocked) || in_array($row_boots_select['item_id'], $enc_item_blocked) || in_array($row_belt_select['item_id'], $enc_item_blocked)) 
																	{
																		?>
																		<option value="All_Enc" disabled><?php echo $lang['enchant_33'];?></option>
																		<?php
																	}
																	else
																		{
																		// Checks if config enchant amount is higher than the equipped item.
																		if (($all_enchant_amount > $row_shirt_enchant_select['enchant_level']) && ($all_enchant_amount > $row_helmet_enchant_select['enchant_level']) && ($all_enchant_amount > $row_necklace_enchant_select['enchant_level']) && ($all_enchant_amount > $row_weapon_enchant_select['enchant_level']) && ($all_enchant_amount > $row_breastplate_full_enchant_select['enchant_level']) && ($all_enchant_amount > $row_shield_enchant_select['enchant_level']) && ($all_enchant_amount > $row_lowring_enchant_select['enchant_level']) && ($all_enchant_amount > $row_upring_enchant_select['enchant_level']) && ($all_enchant_amount > $row_lowearring_enchant_select['enchant_level']) && ($all_enchant_amount > $row_upearring_enchant_select['enchant_level']) && ($all_enchant_amount > $row_gloves_enchant_select['enchant_level']) && ($all_enchant_amount > $row_leggings_enchant_select['enchant_level']) && ($all_enchant_amount > $row_boots_enchant_select['enchant_level']) && ($all_enchant_amount > $row_belt_enchant_select['enchant_level']))
																			{
																				?>
																				<option value="All_Enc"><?php echo '+', $all_enchant_amount, ' ', $lang['enchant_30'], ' ', $currency_code_html, $all_donate_amount;?>.00 </option>
																				<?php
																			}
																			// Cannot be enchanted higher.
																			else
																			{
																				?>
																				<option value="All_Enc" disabled><?php echo $lang['enchant_31'];?></option>
																				<?php
																			}
																		}
																	}
																}
														// This function is disabled.
														else
															{
																?>
																<option value="All_Enc" disabled><?php echo $lang['enchant_29'];?></option>
																<?php
															}
														?>
													</select>
													<input type="hidden" name="option_index" value="0">
													<input type="hidden" name="option_select0" value="Shirt">
													<input type="hidden" name="option_amount0" value="<?php echo $shirt_donate_amount?>.00">
													<input type="hidden" name="option_select1" value="Helmet">
													<input type="hidden" name="option_amount1" value="<?php echo $helmet_donate_amount?>.00">
													<input type="hidden" name="option_select2" value="Necklace">
													<input type="hidden" name="option_amount2" value="<?php echo $necklace_donate_amount?>.00">
													<input type="hidden" name="option_select3" value="Weapon">
													<input type="hidden" name="option_amount3" value="<?php echo $weapon_donate_amount?>.00">
													<input type="hidden" name="option_select4" value="FullarmorBreastplate">
													<input type="hidden" name="option_amount4" value="<?php echo $breastplate_full_donate_amount?>.00">
													<input type="hidden" name="option_select5" value="Shield">
													<input type="hidden" name="option_amount5" value="<?php echo $shield_donate_amount?>.00">
													<input type="hidden" name="option_select6" value="Ring1">
													<input type="hidden" name="option_amount6" value="<?php echo $ring_donate_amount?>.00">
													<input type="hidden" name="option_select7" value="Ring2">
													<input type="hidden" name="option_amount7" value="<?php echo $ring_donate_amount?>.00">
													<input type="hidden" name="option_select8" value="Earring1">
													<input type="hidden" name="option_amount8" value="<?php echo $earring_donate_amount?>.00">
													<input type="hidden" name="option_select9" value="Earring2">
													<input type="hidden" name="option_amount9" value="<?php echo $earring_donate_amount?>.00">
													<input type="hidden" name="option_select10" value="Gloves">
													<input type="hidden" name="option_amount10" value="<?php echo $gloves_donate_amount?>.00">
													<input type="hidden" name="option_select11" value="Leggings">
													<input type="hidden" name="option_amount11" value="<?php echo $leggings_donate_amount?>.00">
													<input type="hidden" name="option_select12" value="Boots">
													<input type="hidden" name="option_amount12" value="<?php echo $boots_donate_amount?>.00">
													<input type="hidden" name="option_select13" value="Belt">
													<input type="hidden" name="option_amount13" value="<?php echo $belt_donate_amount?>.00">
													<input type="hidden" name="option_select14" value="All_Enc">
													<input type="hidden" name="option_amount14" value="<?php echo $all_donate_amount?>.00">
													<br>
													<input type="submit" class="btn btn-large btn-primary" name="enchantsubmit" value="<?php echo $lang['enchant_submit_button']; ?>">
										</form>
									</center>
							</blockquote>
							<?php
							}
							// Show message that the character needs to logout.
							else
								{
									echo "
											<div class='alert alert-error'>
												<center><strong>" . $lang['warning_logout_character'] . "</strong></center>
											</div>
										";
								}
							// Gives a message to the user to keep his character logged out.
							echo "
									<div class='alert alert-success'>
										<center><strong>" . $lang['warning_keep_logged_off'] . "</strong></center>
									</div>
								";
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
				// Dont show the page because its disabled in the config
				else
					{
						echo "
								<div class='alert alert-error'>
									<center><strong>" . $lang['warning_disabled'] . "</strong></center>
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