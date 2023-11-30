<?php
	session_start();

	header('Content-Type: application/json');

	if (!isset($_SESSION['account'])) {
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}
	//Set Round Number
	$_SESSION['roundNumber'] = 1;
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

	if ($gameID <= 0) {
		echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
		exit;
	}

	try {
		//Fetch the current status and the account that started the game
		$checkSql = "SELECT status, gameStartedBy FROM games WHERE game_id = :gameID";
		$checkStmt = $my_Db_Connection->prepare($checkSql);
		$checkStmt->bindParam(':gameID', $gameID);
		$checkStmt->execute();
		$gameData = $checkStmt->fetch(PDO::FETCH_ASSOC);

		if (!$gameData) {
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			$my_Db_Connection = NULL;
			exit;
		}

		if ($gameData['status'] === 'READY' && $_SESSION['account'] != $gameData['gameStartedBy']){
			//Only allow the other player to start the game
			$updateSql = "UPDATE games SET status = 'IN_PROGRESS', lastRoundEnd = NOW(), nextRoundEnd = DATE_ADD(NOW(), INTERVAL 66 SECOND) WHERE game_id = :gameID";
			$updateStmt = $my_Db_Connection->prepare($updateSql);
			$updateStmt->bindParam(':gameID', $gameID);
			$updateStmt->execute();

			echo json_encode(['success' => true, 'message' => 'Game started']);
			$my_Db_Connection = NULL;
		}
		else if ($gameData['status'] === 'IN_PROGRESS') {
			echo json_encode(['success' => true, 'message' => 'Game already in progress']);
			$my_Db_Connection = NULL;
		}
		else {
			echo json_encode(['error' => 'Waiting for other player...', 'success' => false]);
			$my_Db_Connection = NULL;
		}
	} 
	catch (PDOException $e) {
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>