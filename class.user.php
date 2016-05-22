<?php
require_once 'dbconfig.php';

class USER
{

	private $conn;
	private $conn_login;

	public function __construct()
	{
		// Game server connection
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
		
		// Login server connection
		$database_login = new Database();
		$db_login = $database_login->dbConnection_Login();
		$this->conn_login = $db_login;
	}

	// Game server query
	public function runQuery($sql)
	{
		$prepare_gs_sql = $this->conn->prepare($sql);
		return $prepare_gs_sql;
	}

	// Login server query
	public function runQueryLogin($sql)
	{
		$prepare_ls_sql = $this->conn_login->prepare($sql);
		return $prepare_ls_sql;
	}

	public function lasdID()
	{
		$last_id = $this->conn->lastInsertId();
		return $last_id;
	}

	public function register($uname,$email,$upass,$userrole,$code)
	{
		try
		{
			$password = password_hash($upass, PASSWORD_DEFAULT, ['cost' => 12]);
			$register_account = $this->conn->prepare("INSERT INTO paypal_donation_users(userName,userEmail,userPass,userRole,tokenCode) 
											VALUES(:user_name, :user_mail, :user_pass, :user_role, :active_code)");
			$register_account->bindparam(":user_name",$uname);
			$register_account->bindparam(":user_mail",$email);
			$register_account->bindparam(":user_pass",$password);
			$register_account->bindparam(":user_role",$userrole);
			$register_account->bindparam(":active_code",$code);
			$register_account->execute();
			return $register_account;
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
			$local_log .= "CLASS.USER.PHP ERROR: ". $e->getMessage();
			$local_log .= '<hr />';

			// Write to log
			$fp=fopen('system/log/website_error_log.php','a');
			fwrite($fp, $local_log . ""); 

			fclose($fp);  // close file
		}
	}

	public function login($uname,$email,$upass,$get_ip)
	{
		try
		{
			// Select account based on username or email.
			$select_user_email = $this->conn->prepare("SELECT * FROM paypal_donation_users WHERE userName=:user_name OR userEmail=:email_id");
			$select_user_email->execute(array(":user_name"=>$uname, ":email_id"=>$email));
			$userRow=$select_user_email->fetch(PDO::FETCH_ASSOC);
			
			// Checks if user exists.
			if($select_user_email->rowCount() == 1)
			{
				// Checks if account is verified.
				if($userRow['userStatus']=="Y")
				{
					//  Checks if password is correct.
					if(password_verify($upass, $userRow['userPass']))
					{
						$_SESSION['userSession'] = $userRow['userID'];
						return true;
					}
					else
					{
						// Mail user Notice checking to prevent mail spam.
						$notice_mail = 1;
					try
						{
							// Adds a failed login attempt to the database.
							$failed_attempts = $this->conn->prepare("INSERT INTO paypal_donation_login_attempts(address, usermail) 
															VALUES(:address, :usermail)");
							$failed_attempts->bindparam(":address",$get_ip);
							$failed_attempts->bindparam(":usermail",$notice_mail);
							$failed_attempts->execute();
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
							$local_log .= "CLASS.USER.PHP ERROR: ". $e->getMessage();
							$local_log .= '<hr />';

							// Write to log
							$fp=fopen('system/log/website_error_log.php','a');
							fwrite($fp, $local_log . ""); 

							fclose($fp);  // close file
						}
							header("Location: index.php?error");
							exit;
					}
				}
				else
				{
					header("Location: index.php?inactive");
					exit;
				}
			}
			else
			{
				header("Location: index.php?error");
				exit;
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
			$local_log .= "CLASS.USER.PHP ERROR: ". $e->getMessage();
			$local_log .= '<hr />';

			// Write to log
			$fp=fopen('system/log/website_error_log.php','a');
			fwrite($fp, $local_log . ""); 

			fclose($fp);  // close file
		}
	}

	public function is_logged_in()
	{
		if(isset($_SESSION['userSession']))
		{
			return true;
		}
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function logout()
	{
		session_destroy();
		$_SESSION['userSession'] = false;
	}

	function sanitize($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	function l2j_encrypt($password)
	{
		return base64_encode(pack("H*", sha1(utf8_encode($password))));
	}

	function send_mail($email,$message,$subject)
	{
		include_once 'system/assets/mailer/class.phpmailer.php';
		require('system/config.php');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug	= $smtp_debug;
		$mail->SMTPAuth		= $smtp_auth;
		$mail->SMTPSecure	= $smtp_secure;
		$mail->Host			= $mail_host;
		$mail->Port			= $mail_port;
		$mail->AddAddress($email);
		$mail->Username = $mail_username;
		$mail->Password = $mail_password;
		$mail->SetFrom($mail_setfrom, $mail_subject);
		$mail->AddReplyTo($mail_addreplyto, $mail_subject);
		$mail->Subject	= $subject;
		$mail->MsgHTML($message);
		$mail->Send();
	}
}