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
				// Gives a message if sandbox is enabled.
				if ($use_sandbox == false)
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
								// Get POST selected enchant option.
								$get_enchant_option = $user_home->sanitize($_POST['os0']);
							 // Here we check if something is selected otherwise redirect.
							if ($get_enchant_option == "")
								{
									$user_home->redirect('enchant_items.php');
								}
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

								// Used for checking.
								$shirt_enc = 'Shirt';
								$helmet_enc = 'Helmet';
								$necklace_enc = 'Necklace';
								$weapon_enc = 'Weapon';
								$fullarmorbreastplate_enc = 'FullarmorBreastplate';
								$shield_enc = 'Shield';
								$ring1_enc = 'Ring1';
								$ring2_enc = 'Ring2';
								$earring1_enc = 'Earring1';
								$earring2_enc = 'Earring2';
								$gloves_enc = 'Gloves';
								$leggings_enc = 'Leggings';
								$boots_enc = 'Boots';
								$belt_enc = 'Belt';
								$all_enc = 'All_Enc';
							
								// used for showing enchant items icons
								$icon_path_shirt = "../assets/images/icons/5-" . $row_shirt_select['item_id'] . ".jpg";
								$icon_path_helmet = "../assets/images/icons/5-" . $row_helmet_select['item_id'] . ".jpg";
								$icon_path_necklace = "../assets/images/icons/5-" . $row_necklace_select['item_id'] . ".jpg";
								$icon_path_weapon = "../assets/images/icons/5-" . $row_weapon_select['item_id'] . ".jpg";
								$icon_path_breastplate_full = "../assets/images/icons/5-" . $row_breastplate_full_select['item_id'] . ".jpg";
								$icon_path_shield = "../assets/images/icons/5-" . $row_shield_select['item_id'] . ".jpg";
								$icon_path_lowearring = "../assets/images/icons/5-" . $row_lowearring_select['item_id'] . ".jpg";
								$icon_path_upearring = "../assets/images/icons/5-" . $row_upearring_select['item_id'] . ".jpg";
								$icon_path_gloves = "../assets/images/icons/5-" . $row_gloves_select['item_id'] . ".jpg";
								$icon_path_leggings = "../assets/images/icons/5-" . $row_leggings_select['item_id'] . ".jpg";
								$icon_path_boots = "../assets/images/icons/5-" . $row_boots_select['item_id'] . ".jpg";
								$icon_path_lowring = "../assets/images/icons/5-" . $row_lowring_select['item_id'] . ".jpg";
								$icon_path_upring = "../assets/images/icons/5-" . $row_upring_select['item_id'] . ".jpg";
								$icon_path_belt = "../assets/images/icons/5-" . $row_belt_select['item_id'] . ".jpg";
							?>
								<blockquote>
								
								<center>
								<?php 
									if ($icons_enabled == true)
									{
										if ($shirt_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_shirt, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_shirt_name, ' +', $row_shirt_enchant_select['enchant_level'];
											}
										if ($helmet_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_helmet, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_helmet_name, ' +', $row_helmet_enchant_select['enchant_level'];
											}
										if ($necklace_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_necklace, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_necklace_name, ' +', $row_necklace_enchant_select['enchant_level'];
											}
										if ($weapon_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_weapon, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_weapon_name, ' +', $row_weapon_enchant_select['enchant_level'];
											}
										if ($fullarmorbreastplate_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_breastplate_full, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_breastplate_full_name, ' +', $row_breastplate_full_enchant_select['enchant_level'];
											}
										if ($shield_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_shield, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_shield_name, ' +', $row_shield_enchant_select['enchant_level'];
											}
										if ($ring1_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_lowring, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_lowring_name, ' +', $row_lowring_enchant_select['enchant_level'];
											}
										if ($ring2_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_upring, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_upring_name, ' +', $row_upring_enchant_select['enchant_level'];
											}
										if ($earring1_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_lowearring, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_lowearring_name, ' +', $row_lowearring_enchant_select['enchant_level'];
											}
										if ($earring2_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_upearring, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_upearring_name, ' +', $row_upearring_enchant_select['enchant_level'];
											}
										if ($gloves_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_gloves, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_gloves_name, ' +', $row_gloves_enchant_select['enchant_level'];
											}
										if ($leggings_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_leggings, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_leggings_name, ' +', $row_leggings_enchant_select['enchant_level'];
											}
										if ($boots_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_boots, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_boots_name, ' +', $row_boots_enchant_select['enchant_level'];
											}
										if ($belt_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_belt, '"><br>';
												echo $lang['enchant_2'], ' ';
												echo $char_belt_name, ' +', $row_belt_enchant_select['enchant_level'];
											}
										if ($all_enc == $get_enchant_option)
											{
												echo '<img src="', $icon_path_shirt, '">', ' ';
												echo '<img src="', $icon_path_helmet, '">', ' ';
												echo '<img src="', $icon_path_necklace, '">', ' ';
												echo '<img src="', $icon_path_weapon, '">', ' ';
												echo '<img src="', $icon_path_breastplate_full, '">', ' ';
												echo '<img src="', $icon_path_shield, '">', ' ';
												echo '<img src="', $icon_path_lowring, '">', ' ';
												echo '<img src="', $icon_path_upring, '">', ' ';
												echo '<img src="', $icon_path_lowearring, '">', ' ';
												echo '<img src="', $icon_path_upearring, '">', ' ';
												echo '<img src="', $icon_path_gloves, '">', ' ';
												echo '<img src="', $icon_path_leggings, '">', ' ';
												echo '<img src="', $icon_path_boots, '">', ' ';
												echo '<img src="', $icon_path_belt, '"><br>';
												
												echo $char_shirt_name, ' +', $row_shirt_enchant_select['enchant_level'], '<br>';
												echo $char_helmet_name, ' +', $row_helmet_enchant_select['enchant_level'], '<br>';
												echo $char_necklace_name, ' +', $row_necklace_enchant_select['enchant_level'], '<br>';
												echo $char_weapon_name, ' +', $row_weapon_enchant_select['enchant_level'], '<br>';
												echo $char_breastplate_full_name, ' +', $row_breastplate_full_enchant_select['enchant_level'], '<br>';
												echo $char_shield_name, ' +', $row_shield_enchant_select['enchant_level'], '<br>';
												echo $char_lowearring_name, ' +', $row_lowearring_enchant_select['enchant_level'], '<br>';
												echo $char_upearring_name, ' +', $row_upearring_enchant_select['enchant_level'], '<br>';
												echo $char_lowring_name, ' +', $row_lowring_enchant_select['enchant_level'], '<br>';
												echo $char_upring_name, ' +', $row_upring_enchant_select['enchant_level'], '<br>';
												echo $char_gloves_name, ' +', $row_gloves_enchant_select['enchant_level'], '<br>';
												echo $char_leggings_name, ' +', $row_leggings_enchant_select['enchant_level'], '<br>';
												echo $char_boots_name, ' +', $row_boots_enchant_select['enchant_level'], '<br>';
												echo $char_belt_name, ' +', $row_belt_enchant_select['enchant_level'], '<br>';
											}
										}
										?>
									<!-- oke now lets show the final donation page -->
									<form action="<?php if ($use_sandbox == true){ echo $SandboxpayPalURL;} else { echo $payPalURL; } ?>" method="post" class="payPalForm">
									<input type="hidden" name="cmd" value="_donations" />

									<!-- item name that will be passed to paypal -->
									<input type="hidden" name="item_name" value="<?php
										if ($shirt_enc == $get_enchant_option)
											{
												echo $char_shirt_name, ' +', $shirt_enchant_amount;
											}
										if ($helmet_enc == $get_enchant_option)
											{
												echo $char_helmet_name, ' +', $helmet_enchant_amount;
											}
										if ($necklace_enc == $get_enchant_option)
											{
												echo $char_necklace_name, ' +', $necklace_enchant_amount;
											}
										if ($weapon_enc == $get_enchant_option)
											{
												echo $char_weapon_name, ' +', $weapon_enchant_amount;
											}
										if ($fullarmorbreastplate_enc == $get_enchant_option)
											{
												echo $char_breastplate_full_name, ' +', $breastplate_full_enchant_amount;
											}
										if ($shield_enc == $get_enchant_option)
											{
												echo $char_shield_name, ' +', $shield_enchant_amount;
											}
										if ($ring1_enc == $get_enchant_option)
											{
												echo $char_lowring_name, ' +', $ring_enchant_amount;
											}
										if ($ring2_enc == $get_enchant_option)
											{ 
												echo $char_upring_name, ' +', $ring_enchant_amount;
											}
										if ($earring1_enc == $get_enchant_option)
											{
												echo $char_lowearring_name, ' +', $earring_enchant_amount;
											}
										if ($earring2_enc == $get_enchant_option)
											{
												echo $char_upearring_name, ' +', $earring_enchant_amount;
											}
										if ($gloves_enc == $get_enchant_option)
											{
												echo $char_gloves_name, ' +', $gloves_enchant_amount;
											}
										if ($leggings_enc == $get_enchant_option)
											{ 
												echo $char_leggings_name, ' +', $leggings_enchant_amount;
											}
										if ($boots_enc == $get_enchant_option)
											{
												echo $char_boots_name, ' +', $boots_enchant_amount;
											}
										if ($belt_enc == $get_enchant_option)
											{
												echo $char_belt_name, ' +', $belt_enchant_amount;
											}
										if ($all_enc == $get_enchant_option)
											{
												echo $lang['message_16'];
											}
										?>"/>

									<!-- Custom field enchant items -->
									<input type="hidden" name="custom" value="<?php echo $row['characterName']?>|Enchitems|<?php echo $get_enchant_option?>">

									<!-- Your PayPal email -->
									<input type="hidden" name="business" value="<?php if ($use_sandbox == true){ echo $SandboxPayPalEmail;} else { echo $myPayPalEmail; } ?>" />
									<!-- PayPal will send an IPN notification to this URL -->
									<input type="hidden" name="notify_url" value="<?php echo $donation_center_folder_loc ?>system/assets/ipn/ipn_donations.php" />

									<!-- The return page to which the user is navigated after the donations is complete -->
									<input type="hidden" name="return" value="<?php echo $donation_center_folder_loc?>system/user/done.php" />

									<!-- Signifies that the transaction data will be passed to the return page by POST -->
									<input type="hidden" name="rm" value="2" />

									<!-- General configuration variables for the paypal landing page. Consult -->
									<!-- http://www.paypal.com/IntegrationCenter/ic_std-variable-ref-donate.html for more info -->
									<input type="hidden" name="no_note" value="1" />
									<input type="hidden" name="cbt" value="Go Back To The Site" />
									<input type="hidden" name="no_shipping" value="1" />
									<input type="hidden" name="lc" value="US" />
									<input type="hidden" name="currency_code" value="<?php echo $currency_code?>" />

									<select name="amount">
										<?php
									if ($shirt_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $shirt_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_shirt_name, ' ', '+', $shirt_enchant_amount, ' ',  $currency_code_html, $shirt_donate_amount;?>.00 </option>
											<?php
										}
									if ($helmet_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $helmet_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_helmet_name, ' ', '+', $helmet_enchant_amount, ' ',  $currency_code_html, $helmet_donate_amount;?>.00 </option>
											<?php
										}
									if ($necklace_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $necklace_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_necklace_name, ' ', '+', $necklace_enchant_amount, ' ',  $currency_code_html, $necklace_donate_amount;?>.00 </option>
											<?php
										}
									if ($weapon_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $weapon_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_weapon_name, ' ', '+', $weapon_enchant_amount, ' ',  $currency_code_html, $weapon_donate_amount;?>.00 </option>
											<?php
										}
									if ($fullarmorbreastplate_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $breastplate_full_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_breastplate_full_name, ' ', '+', $breastplate_full_enchant_amount, ' ',  $currency_code_html, $breastplate_full_donate_amount;?>.00 </option>
											<?php
										}
									if ($shield_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $shield_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_shield_name, ' ', '+', $shield_enchant_amount, ' ',  $currency_code_html, $shield_donate_amount;?>.00 </option>
											<?php
										}
									if ($ring1_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $ring_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_lowring_name, ' ', '+', $ring_enchant_amount, ' ',  $currency_code_html, $ring_donate_amount;?>.00 </option>
											<?php
										}
									if ($ring2_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $ring_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_upring_name, ' ', '+', $ring_enchant_amount, ' ',  $currency_code_html, $ring_donate_amount;?>.00 </option>
											<?php
										}
									if ($earring1_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $earring_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_lowearring_name, ' ', '+', $earring_enchant_amount, ' ',  $currency_code_html, $earring_donate_amount;?>.00 </option>
											<?php
										}
									if ($earring2_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $earring_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_upearring_name, ' ', '+', $earring_enchant_amount, ' ',  $currency_code_html, $earring_donate_amount;?>.00 </option>
											<?php
										}
									if ($gloves_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $gloves_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_gloves_name, ' ', '+', $gloves_enchant_amount, ' ',  $currency_code_html, $gloves_donate_amount;?>.00 </option>
											<?php
										}
									if ($leggings_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $leggings_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_leggings_name, ' ', '+', $leggings_enchant_amount, ' ',  $currency_code_html, $leggings_donate_amount;?>.00 </option>
											<?php
										}
									if ($boots_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $boots_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_boots_name, ' ', '+', $boots_enchant_amount, ' ',  $currency_code_html, $boots_donate_amount;?>.00 </option>
											<?php
										}
									if ($belt_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $belt_donate_amount?>"><?php echo $lang['enchant_finish_1'], ':',' ',$char_belt_name, ' ', '+', $belt_enchant_amount, ' ',  $currency_code_html, $belt_donate_amount;?>.00 </option>
											<?php
										}
									if ($all_enc == $get_enchant_option)
										{
											?>
											<option value="<?php echo $all_donate_amount?>"><?php echo $lang['enchant_finish_2'], ' ', '+', $all_enchant_amount, ' ',  $currency_code_html, $all_donate_amount;?>.00 </option>
											<?php
										}
										?>
									</select><br><br>

									<!-- Here you can change the image of the enchant donation button  -->
									<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
									<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
									<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest" />
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
				// Dont show the page and gives a message if sandbox is enabled.
				else
					{
						echo "
								<div class='alert alert-error'>
									<center><strong>" . $lang['sandbox_mode'] . "</strong></center>
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