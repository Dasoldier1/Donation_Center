<?php
class Database
{
	public $conn;
	public $conn_login;

	public function dbConnection()
	{
		$this->conn = null;
		include('system/connect.php');
		include('system/config.php');
		try
		{
			$this->conn = new PDO("mysql:host=$db_host;dbname=$db_database;", $db_user, $db_pass, array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::ATTR_TIMEOUT => $connection_timeout,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		}
		catch(PDOException $e)
		{
			//logging: Timestamp
			$local_log = '['.date('m/d/Y g:i A').'] - '; 

			//logging: response from the server
			$local_log .= "DBCONFIG.PHP CONNECTION ERROR: ". $e->getMessage();
			$local_log .= '<hr />';

			// Write to log
			$fp=fopen('system/log/website_error_log.php','a');
			fwrite($fp, $local_log . ""); 

			fclose($fp);  // close file
		}
		return $this->conn;
	}
	public function dbConnection_Login()
	{
		$this->conn_login = null;
		include('system/connect.php');
		include('system/config.php');
		try
		{
			$this->conn_login = new PDO("mysql:host=$login_host;dbname=$login_database;", $login_user, $login_pass, array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::ATTR_TIMEOUT => $connection_timeout,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		}
		catch(PDOException $e)
		{
			//logging: Timestamp
			$local_log = '['.date('m/d/Y g:i A').'] - '; 

			//logging: response from the server
			$local_log .= "DBCONFIG.PHP CONNECTION ERROR: ". $e->getMessage();
			$local_log .= '<hr />';

			// Write to log
			$fp=fopen('system/log/website_error_log.php','a');
			fwrite($fp, $local_log . ""); 

			fclose($fp);  // close file
		}
		return $this->conn_login;
	}
}
?>