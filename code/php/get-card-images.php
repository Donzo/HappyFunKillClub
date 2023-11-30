<?php
	session_start();

	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
	require_once($_SERVER['DOCUMENT_ROOT'] .'/code/php/set-card-data.php');

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
			$p1Deck = fetchDeckData(json_decode($result['p1DeckJSON']));
			$p2Deck = fetchDeckData(json_decode($result['p2DeckJSON']));

			echo json_encode([
				'success' => true,
				'p1DeckImages' => array_map('getCardImageUrl', $p1Deck),
				'p2DeckImages' => array_map('getCardImageUrl', $p2Deck)
			]);
			$my_Db_Connection = NULL;
		}
		else {
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			$my_Db_Connection = NULL;
		}
	}
	catch (PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}

	function getCardImageUrl($card){
		//return $card['dir'] . 'card.jpg';
		return $card['dir'] . 'img.jpg';
	}
?>
