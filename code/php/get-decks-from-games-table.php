<?php
	session_start();

	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	//Retrieve the game ID from the client request
	$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

	if ($gameID <= 0){
		echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
		$my_Db_Connection = NULL;
		exit;
	}

	try {
	    $sql = "SELECT p1DeckJSON, p2DeckJSON FROM games WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($result){
		// Send the deck JSON data back to the client
			echo json_encode([
				'success' => true,
				'p1DeckJSON' => $result['p1DeckJSON'],
				'p2DeckJSON' => $result['p2DeckJSON']
			]);
			$my_Db_Connection = NULL;
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
