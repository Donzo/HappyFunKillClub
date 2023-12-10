<?php
	session_start();
	header('Content-Type: application/json');

	//Check if the user is logged in
	if (!isset($_SESSION['account'])) {
		echo json_encode(['error' => 'User not logged in', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	try {
		//Get the user's account number from the session
		$userAccount = $_SESSION['account'];

		//Prepare SQL query to select redCoins, tkn, and coinsMinted
		$query = "SELECT redCoins, tkn, mintRequests FROM users WHERE account = :account";
		$stmt = $my_Db_Connection->prepare($query);
		$stmt->bindParam(':account', $userAccount);
		$stmt->execute();

		//Fetch the user data
		$userData = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($userData) {
			//Reset Coins if We Have a Lot of Mint Requests
			if ($userData['mintRequests'] > 0) {
				//Update redCoins to 0 and coinsMinted to false
				$updateQuery = "UPDATE users SET redCoins = '0', mintRequests = 0 WHERE account = :account AND mintRequests > 0";
				$updateStmt = $my_Db_Connection->prepare($updateQuery);
				$updateStmt->bindParam(':account', $userAccount);
				$updateStmt->execute();

				//Set redCoins to 0 for the response
				$userData['redCoins'] = '0';
			}

			//User data found, return redCoins and tkn
			echo json_encode([
				'success' => true,
				'redCoins' => $userData['redCoins'],
				'token' => $userData['tkn']
			]);
		} else {
			//User data not found
			echo json_encode(['error' => 'User data not found', 'success' => false]);
		}
	}
	catch (PDOException $e) {
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
	}
?>
