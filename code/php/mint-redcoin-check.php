<?php
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");
	
	//Check if wallet and token are provided
	if (isset($_GET['wallet']) && isset($_GET['tkn'])) {
		
		//Validate Address
		if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $_GET['wallet'])) {
			die('Invalid wallet address format.');
		}
		//Validate Token
		if (strlen($_GET['tkn']) > 24) { 
			die('Invalid token format.');
		}
	
		$walletAddress = $_GET['wallet'];
		$userToken = $_GET['tkn'];

		require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

		//Prepare and execute the SELECT statement
		$stmt = $my_Db_Connection->prepare("SELECT redCoins FROM users WHERE account = :wallet AND tkn = :token"); 
		$stmt->bindParam(':wallet', $walletAddress);
		$stmt->bindParam(':token', $userToken);
		$stmt->execute();

		//Initialize variable to hold redCoins value
		$redCoins = "0";

		//Fetch the result
		if ($row = $stmt->fetch()) {
			$redCoins = $row['redCoins']; 
		}

		//Return the redCoins value as JSON
		
		//Outputs "redCoins": "32"
		$response = json_encode(['redCoins' => $redCoins]);

		
		echo $response;
		
		//Reset Redcoin Count and Token if there were any Redcoins to mint...
		if ($redCoins > 0){
			//Generate a new secure random token
			$tokenLength = 12; //Length of the token
			$token = bin2hex(random_bytes($tokenLength));
		
			//Reset redCoins to 0 (comment out during initial tests)
			$resetStmt = $my_Db_Connection->prepare("UPDATE users SET redCoins = '0', tkn = :token WHERE account = :wallet");
			$resetStmt->bindParam(':wallet', $walletAddress);
			$resetStmt->bindParam(':token', $token);
			$resetStmt->execute();
		}	
		$my_Db_Connection = NULL;
	}
	else {
		die('Invalid request parameters.');
	}
?>