<?php
	session_start();

	header('Content-Type: application/json');

	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'matched' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	try {
		$sql = "SELECT game_id, p1WalletAddress, p2WalletAddress, status FROM games WHERE (p1WalletAddress = :account OR p2WalletAddress = :account) AND status = 'WAITING'";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':account', $_SESSION['account']);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($result){
			$opponent = ($_SESSION['account'] == $result['p1WalletAddress']) ? $result['p2WalletAddress'] : $result['p1WalletAddress'];
			echo json_encode(['matched' => true, 'opponent' => $opponent, 'game_id' => $result['game_id']]);
		}
		else {
			echo json_encode(['matched' => false]);
		}
		$my_Db_Connection = NULL;
	}
	catch (PDOException $e){
		echo json_encode(['error' => "Error checking match status: " . $e->getMessage(), 'matched' => false]);
		$my_Db_Connection = NULL;
	}
?>
