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
		$stmt = $my_Db_Connection->prepare("SELECT redCoins FROM users WHERE account = :wallet AND tkn = :token AND mintRequests <= 7");
		$stmt->bindParam(':wallet', $walletAddress);
		$stmt->bindParam(':token', $userToken);
		$stmt->execute();

		//Initialize variable to hold redCoins value
		$redCoins = "0";

		//Fetch the result
		if ($row = $stmt->fetch()) {
			$redCoins = $row['redCoins']; 
		}
		
		//Outputs "redCoins": "32"
		$response = json_encode(['redCoins' => $redCoins]);
		echo $response;
		
		$stmt2 = $my_Db_Connection->prepare("UPDATE users SET mintRequests = mintRequests + 1 WHERE account = :wallet AND tkn = :token");
		$stmt2->bindParam(':wallet', $walletAddress);
		$stmt2->bindParam(':token', $userToken);
		$stmt2->execute();
		
		$my_Db_Connection = NULL;
		
	}
	else {
		die('Invalid request parameters.');
	}
?>
