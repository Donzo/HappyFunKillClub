<?php
	session_start();

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
		//Fetch character data for both players
		$sql = "SELECT * FROM gameCharacters WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID);
		$stmt->execute();

		$p1Characters = [];
		$p2Characters = [];

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			if ($row['player'] == 'p1'){
				$p1Characters[] = $row;
			}
			else if ($row['player'] == 'p2'){
				$p2Characters[] = $row;
			}
		}

		echo json_encode([
			'success' => true,
			'p1Characters' => $p1Characters,
			'p2Characters' => $p2Characters
		]);
		$my_Db_Connection = NULL;
	}
	catch (PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>
