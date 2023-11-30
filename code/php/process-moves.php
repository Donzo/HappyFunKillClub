<?php
	session_start();
	header('Content-Type: application/json');


	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/validation-functions.php');

	$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
	$playerMoves = isset($_POST['player_moves']) ? json_decode($_POST['player_moves'], true) : null;
	
	
	
	//Debug: Output the entire player moves array
	$errorLogger = print_r($playerMoves, true);
	
	if ($gameID <= 0 || $playerMoves === null){
		echo json_encode(['error' => 'Invalid game ID or moves', 'success' => false]);
		exit;
	}

	
	try {
		$currentLocationsSql = "SELECT character_id, location FROM gameCharacters WHERE game_id = :gameID";
		$currentLocationsStmt = $my_Db_Connection->prepare($currentLocationsSql);
		$currentLocationsStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$currentLocationsStmt->execute();
		$currentLocations = $currentLocationsStmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($playerMoves as $move){
			$characterKey = $move['characterKey'];  //Use characterKey here
			$newLocation = intval($move['location']);

			$currentLocation = array_column($currentLocations, 'location', 'character_id')[$characterKey] ?? null;

			if ($currentLocation === null){
				echo json_encode(['error' => "Character with ID $characterKey not found", 'success' => false]);
				exit;
			}

			$validLocations = $currentLocation == 0 ? [0, 4, 8, 1, 5, 9, 3, 7, 11] : getAdjacentTilesAndCurrent($currentLocation);
			if (!in_array($newLocation, $validLocations)){
				echo json_encode(['error' => "$errorLogger  Invalid move for character with ID $characterKey who is at $currentLocation", 'success' => false]);
				exit;
			}
		}

		
		//Get current game status and player wallet addresses
		$sql = "SELECT status, p1WalletAddress, p2WalletAddress FROM games WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$stmt->execute();
		$gameData = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$gameData){
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			exit;
		}

		$playerColumn = ($_SESSION['account'] === $gameData['p1WalletAddress']) ? 'p1Moved' : 'p2Moved';
		$movesColumn = ($_SESSION['account'] === $gameData['p1WalletAddress']) ? 'p1MovesJSON' : 'p2MovesJSON';

		$playerActions = array_map(function($move){
			return [
				'characterKey' => $move['characterKey'],
				'action' => $move['action'],
				'target' => $move['target'],
			];
		}, $playerMoves);

		//Determine the current player
		$isPlayerOne = $_SESSION['account'] === $gameData['p1WalletAddress'];
		$actionColumn = $isPlayerOne ? 'p1ActionsJSON' : 'p2ActionsJSON';

		//Encode the valid moves to JSON
		$validMovesJson = json_encode($validMoves);

		//Update the moves JSON and moved status for the player
		$updateSql = "UPDATE games SET $movesColumn = :playerMoves, $playerColumn = TRUE, $actionColumn = :actionsJson WHERE game_id = :gameID";
		$updateStmt = $my_Db_Connection->prepare($updateSql);
		$updateStmt->bindParam(':playerMoves', json_encode($playerMoves), PDO::PARAM_STR);
		$updateStmt->bindParam(':actionsJson', json_encode($playerActions), PDO::PARAM_STR);
		$updateStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$updateStmt->execute();

		echo json_encode(['success' => true, 'message' => "$validMovesDump Moves processed successfully"]);
		$my_Db_Connection = NULL;
	}
	catch(PDOException $e){
		echo json_encode(['error' => "$validMovesDump  Something didnt work", 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>