<?php
	session_start();
		
	//Load ECRECOVER (Verify Sign)
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/ecrecover/vendor/autoload.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/ecrecover/ecrecover_helper.php');
				
		
	if (isset($_GET['wallet']) && isset($_GET['signature'])) {
		$signature = $_GET['signature'];
		$wallet = $_GET['wallet'];
			
		
		$msg = 'Sign here to login.';
		$signed = $signature;
		$signer = personal_ecRecover($msg, $signed);	
			
		if ($signer == $wallet ){
			
			//Generate a secure random token
			$tokenLength = 12; //Length of the token
			$token = bin2hex(random_bytes($tokenLength));
				
			//GET DB CONNECTION
			require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
			
			//Insert token into user row (use it to authorize mints of redCoins)
			$stmt = $my_Db_Connection->prepare("INSERT INTO users (account, signature, tkn) VALUES (:wallet, :signature, :tkn) ON DUPLICATE KEY UPDATE signature = :signature, tkn = :tkn");
			$stmt->bindParam(':wallet', $wallet);
			$stmt->bindParam(':signature', $signature);
			$stmt->bindParam(':tkn', $token);
			$stmt->execute();
				
			//Select redCoins and token for the user
			$selectStmt = $my_Db_Connection->prepare("SELECT redCoins FROM users WHERE account = :wallet");
			$selectStmt->bindParam(':wallet', $wallet);
			$selectStmt->execute();
			$userData = $selectStmt->fetch(PDO::FETCH_ASSOC);

			$_SESSION["account"] = $wallet;

			echo json_encode([
				'success' => true, 
				'message' => 'Signer Match!', 
				'token' => $token,
				'redCoins' => $userData['redCoins']
			]);
			$my_Db_Connection = NULL;				
		}
		else{
			die("Signer does NOT Match!");
		}
	}
?>

