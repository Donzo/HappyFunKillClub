<?php 
	$my_Db_Connection = NULL;
	/*DB Credentials*/
	$database = 'dbname';
	$password = '12345';
	$servername = 'localhost';
	$username = 'user12345';
	$sql = "mysql:host=$servername;dbname=$database;";
		
	$dsn_Options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	
	try { 
  		$my_Db_Connection = new PDO($sql, $username, $password, $dsn_Options);
  		//echo "Connected successfully";
	} 
	catch (PDOException $error) {
  		echo 'Connection error: ' . $error->getMessage();
	}