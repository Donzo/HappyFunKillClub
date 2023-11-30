<?php
	session_start();

	header('Content-Type: application/json');

	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

	if ($gameID <= 0){
		echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
		$my_Db_Connection = NULL;
		exit;
	}

	try{
		$sql = "SELECT status, p1WalletAddress, p2WalletAddress FROM games WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID);
		$stmt->execute();

		$gameData = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($gameData){
			$status = $gameData['status'];
			if ($status === 'READY' || $status === 'IN_PROGRESS'){
				$playerNumber = ($_SESSION['account'] == $gameData['p1WalletAddress']) ? 'p1' : 'p2';
				echo json_encode(['success' => true, 'message' => 'Game ready', 'status' => $status, 'playerNumber' => $playerNumber]);
				$my_Db_Connection = NULL;
			}
			else {
				echo json_encode(['success' => false, 'message' => 'Game not ready', 'status' => $status]);
				$my_Db_Connection = NULL;
			}
		}
		else{
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			$my_Db_Connection = NULL;
		}
	}
	catch (PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>
