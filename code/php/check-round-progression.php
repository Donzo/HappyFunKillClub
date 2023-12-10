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
		exit;
	}

	try {
		//Check the current status, moves of both players, and round number
		$sql = "SELECT status, p1Moved, p2Moved, nextRoundEnd, roundNumber, valuesUpdated FROM games WHERE game_id = :gameID";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID);
		$stmt->execute();

		$gameData = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($gameData){
			$currentTime = date("Y-m-d H:i:s");
			$nextRoundEnd = $gameData['nextRoundEnd'];

			if (($gameData['p1Moved'] && $gameData['p2Moved']) || $currentTime >= $nextRoundEnd){
				if ($gameData['valuesUpdated'] != TRUE){
					try {
   						 //Start transaction
						$my_Db_Connection->beginTransaction();
						//Update game status to PROCESSING
						$updateSql = "UPDATE games SET status = 'PROCESSING' WHERE game_id = :gameID";
						$updateStmt = $my_Db_Connection->prepare($updateSql);
						$updateStmt->bindParam(':gameID', $gameID);
						$updateStmt->execute();
	
						//Execute update-character-values script
						
						//HERE IS WEHRE WE UPDATE CHARACTER STATS IN THE DATABASE
						require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/update-character-values.php');

						$setValueUpdatedStmt = "UPDATE games SET valuesUpdated = TRUE WHERE game_id = :gameID";
						$setValueUpdatedStmt = $my_Db_Connection->prepare($setValueUpdatedStmt);
						$setValueUpdatedStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
						$setValueUpdatedStmt->execute();
						//Commit the transaction
    					$my_Db_Connection->commit();
    					
    					if ( $currentTime >= $nextRoundEnd){
							echo json_encode(['success' => true, 'message' => 'Out of time. Round Processed.']);
						}
						else{
							echo json_encode(['success' => true, 'message' => 'Both players ready. Round Processed.']);
						}
						$my_Db_Connection = NULL;
					}
					catch(PDOException $e){
						//Rollback the transaction
						$my_Db_Connection->rollBack();
  						echo json_encode(['error' => $e->getMessage(), 'success' => false]);
  						$my_Db_Connection = NULL;
					}	
				}
				else {
					echo json_encode(['success' => false, 'message' =>  'Round already processed.']);
					$my_Db_Connection = NULL;
				}
			}
			else {
				
				if ($currentTime > $_SESSION['nextRoundEnd']){
					echo json_encode(['success' => true, 'message' => 'Out of time. Round Processed.']);
				}
				/*else if ($gameData['roundNumber'] > $_SESSION['roundNumber'] ){
					echo json_encode(['success' => true, 'message' => 'Other player has advanced the round.','valuesUpdated' => $gameData['valuesUpdated']]);
					$_SESSION['roundNumber'] = $gameData['roundNumber'];
				}*/
				else{
					//Convert strings to DateTime objects
					$nextRoundEndDateTime = new DateTime($nextRoundEnd);
					$currentTimeDateTime = new DateTime($currentTime);
					//Calculate the difference
					$interval = $nextRoundEndDateTime->getTimestamp() - $currentTimeDateTime->getTimestamp();
					echo json_encode(['success' => true, 'message' => 'Round in progress...', 'serverTimeRemaining' => $interval, 'roundNumber' => $gameData['roundNumber'] ]);
					//Return round time and correct the players clock...
				}
				$my_Db_Connection = NULL;
			}
		}
		else {
			echo json_encode(['error' => 'Game not found', 'success' => false]);
			$my_Db_Connection = NULL;
		}
	}
	catch(PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>