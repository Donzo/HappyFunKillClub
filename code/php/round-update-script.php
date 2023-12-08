<?php
	session_start();
	header('Content-Type: application/json');

	if (!isset($_SESSION['account'])) {
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

	if ($gameID <= 0) {
		echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
		$my_Db_Connection = NULL;
		exit;
	}

	try {
		//Fetch game data
		$gameSql = "SELECT roundNumber, nextRoundEnd, status, p1Moved, p2Moved, moveSummariesJSON, actionSummariesJSON, simpleActionSummariesJSON, simpleMoveSummariesJSON, valuesUpdated FROM games WHERE game_id = :gameID";
		$gameStmt = $my_Db_Connection->prepare($gameSql);
		$gameStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$gameStmt->execute();
		$gameData = $gameStmt->fetch(PDO::FETCH_ASSOC);

		if (!$gameData) {
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			exit;
		}

		//Check if round needs to be updated
		$currentRound = $gameData['roundNumber'];
		$newRoundNumber = $currentRound + 1;
		$nextRoundEnd = new DateTime($gameData['nextRoundEnd']);
		$currentTime = new DateTime();
		
		//Fetch move and action summaries
		$moveSummaries = json_decode($gameData['moveSummariesJSON'], true) ?? [];
		$simpleMoveSummaries = json_decode($gameData['simpleMoveSummariesJSON'], true) ?? [];
		$simpleActionSummaries = json_decode($gameData['simpleActionSummariesJSON'], true) ?? [];
		$actionSummaries = json_decode($gameData['actionSummariesJSON'], true) ?? [];
		
		if ($currentTime >= $nextRoundEnd || ($gameData['p1Moved'] && $gameData['p2Moved'] && $gameData['valuesUpdated'] == TRUE)) {
			//Increment round number and update session
			$newRoundNumber = $currentRound + 1;
			$_SESSION['roundNumber'] = $newRoundNumber;
	
			//Reset game for new round
			$updateGameSql = "UPDATE games SET 
								roundNumber = :newRoundNumber, 
								lastRoundEnd = NOW(),
								nextRoundEnd = DATE_ADD(NOW(), INTERVAL 75 SECOND),
								p1Moved = FALSE,
								p2Moved = FALSE,
								p1MovesJSON = NULL,
								p2MovesJSON = NULL,
								p1ActionsJSON = NULL,
								p2ActionsJSON = NULL,
								valuesUpdated = FALSE,
								status = 'IN_PROGRESS'
							  WHERE game_id = :gameID";
			$updateGameStmt = $my_Db_Connection->prepare($updateGameSql);
			$updateGameStmt->bindParam(':newRoundNumber', $newRoundNumber, PDO::PARAM_INT);
			$updateGameStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
			$updateGameStmt->execute();

			$responseMessage = 'New round started';
			$_SESSION['nextRoundEnd'] = $nextRoundEnd;
			
		}
		else{
			//Match player session round number to current round
			if ($_SESSION['roundNumber'] < $currentRound && $gameData['status'] == 'IN_PROGRESS'){
				$responseMessage = 'Other player advanced round';
				$_SESSION['roundNumber'] = $currentRound;
				$_SESSION['nextRoundEnd'] = $nextRoundEnd;
			}
			else{
				$responseMessage = 'Continuing current round';	
			}
			
		}

	
		echo json_encode([
			'success' => true, 
			'message' => $responseMessage,
			'moveSummaries' => $moveSummaries,
			'simpleMoveSummaries' => $simpleMoveSummaries,
			'actionSummaries' => $actionSummaries, 
			'simpleActionSummaries' => $simpleActionSummaries,
			'roundNumber' => $_SESSION['roundNumber']
		]);
		$my_Db_Connection = NULL;
	}
	catch (PDOException $e) {
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>
