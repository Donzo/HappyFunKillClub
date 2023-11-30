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

	try {
		$query = "SELECT * FROM gameCharacters WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($query);
		$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$stmt->execute();
		$characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

		echo json_encode(['success' => true, 'characters' => $characters]);
		$my_Db_Connection = NULL;
	}
	catch (PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>