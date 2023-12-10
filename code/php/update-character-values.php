<?php
	session_start();
	header('Content-Type: application/json');
	
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	try {
		$gameID = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

		if ($gameID <= 0){
			echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
			exit;
		}
		
		//Select Relevant Character Attributes
		$currentLocationsSql = "SELECT * FROM gameCharacters WHERE game_id = :gameID";
		$currentLocationsStmt = $my_Db_Connection->prepare($currentLocationsSql);
		$currentLocationsStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$currentLocationsStmt->execute();
		$currentLocations = $currentLocationsStmt->fetchAll(PDO::FETCH_ASSOC);

		//Retrieve moves JSON objects from games table
		$movesSql = "SELECT p1MovesJSON, p2MovesJSON, p1ActionsJSON, p2ActionsJSON FROM games WHERE game_id = :gameID";
		$movesStmt = $my_Db_Connection->prepare($movesSql);
		$movesStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$movesStmt->execute();
		$moves = $movesStmt->fetch(PDO::FETCH_ASSOC);

		if (!$moves){
			echo json_encode(['error' => 'Moves not found for the game', 'success' => false]);
			exit;
		}
		//Decode the JSON objects into arrays
		$p1Actions = json_decode($moves['p1ActionsJSON'], true);
		$p2Actions = json_decode($moves['p2ActionsJSON'], true);
		
		//Decode the JSON objects into arrays
		$p1Moves = json_decode($moves['p1MovesJSON'], true);
		$p2Moves = json_decode($moves['p2MovesJSON'], true);
		
		
		//Include validation functions to get range scores
		require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/validation-functions.php');

		//TIME FOR SOME ACTION
		require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/time-time-for-some-time-for-some-action.php');

		
		function deployCharacterIfNeeded(&$playerMoves, $player, &$currentLocations, $deploymentSquare){
			//Check if any character is already on the board (locations 1-11)
			$onBoard = array_filter($playerMoves, function($move){
				return $move['location'] > 0 && $move['location'] <= 11;
			});

			//If no character is on the board, deploy one from the deck
			if (empty($onBoard)){
				foreach ($playerMoves as &$move){
					if ($move['location'] == 0){
						//Deploy this character
						$move['location'] = $deploymentSquare;
	
						//Update the currentLocations array
						foreach ($currentLocations as &$char){
							if ($char['character_id'] == $move['characterKey']){
								$char['location'] = $deploymentSquare;
								break;
							}
						}
						

						
						break; //Stop after deploying one character
					}
				}
			}
		}

		deployCharacterIfNeeded($p1Moves, 'p1', $currentLocations, 4); //For player 1
		deployCharacterIfNeeded($p2Moves, 'p2', $currentLocations, 8); //For player 2

		
		
		require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/resolve-move-conflicts.php');
		
		
		//$conflictDump = print_r($conflicts, true);
		
		$p1Result = updateCharacterLocations($p1Moves, $gameID, 'p1', $my_Db_Connection, $currentLocations);
		$p2Result = updateCharacterLocations($p2Moves, $gameID, 'p2', $my_Db_Connection, $currentLocations);

		//Combine the summaries and simple move records from both players
		//Combine the summaries and simple move records from both players
		$allMoveSummaries = array_merge($p1Result['summaries'] ?? [], $p2Result['summaries'] ?? []);
		$allSimpleMoveRecords = array_merge($p1Result['simpleSummaries'] ?? [], $p2Result['simpleSummaries'] ?? []);

		
		//$allMoveSummaries = array_merge($p1Result['summaries'], $p2Result['summaries']);
		//$allSimpleMoveRecords = array_merge($p1Result['simpleSummaries'], $p2Result['simpleSummaries']);

		$encodedMoveSummaries = json_encode($allMoveSummaries);
		$encodedSimpleMoveRecords = json_encode($allSimpleMoveRecords);
		
		
		
		$encodedActionSummaries = json_encode($actionSummaries);
		$encodedSimpleActionSummaries = json_encode($simpleActionSummaries);
		

		//Store the summaries in the games table
		$updateSummariesSql = "UPDATE games SET moveSummariesJSON = :moveSummaries, simpleMoveSummariesJSON = :simpleMoveSummaries, actionSummariesJSON = :actionSummaries, simpleActionSummariesJSON = :simpleActionSummaries WHERE game_id = :gameID";
		$updateSummariesStmt = $my_Db_Connection->prepare($updateSummariesSql);
		$updateSummariesStmt->bindParam(':moveSummaries', $encodedMoveSummaries, PDO::PARAM_STR);
		$updateSummariesStmt->bindParam(':simpleMoveSummaries', $encodedSimpleMoveRecords, PDO::PARAM_STR);
		$updateSummariesStmt->bindParam(':actionSummaries', $encodedActionSummaries, PDO::PARAM_STR);
		$updateSummariesStmt->bindParam(':simpleActionSummaries', $encodedSimpleActionSummaries, PDO::PARAM_STR);
		$updateSummariesStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
		$updateSummariesStmt->execute();
		
		
		
		echo json_encode(['success' => true, 'message' => 'Character locations updated successfully', 'gameOverPlayerWon' => $playerWon]);
	}
	catch (PDOException $e){
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
	}
	
	function updateCharacterLocations($moves, $gameID, $player, $dbConnection, $currentLocations){
		//Encode these from time-time-for-some-time-for-some action script
		$encodedActionSummaries = json_encode($actionSummaries);
		$encodedSimpleActionSummaries = json_encode($simpleActionSummaries);
		
		$moveSummaries = [];
		$simpleMoveSummaries = [];
		$playerWon = false;
		
		foreach ($moves as $move){
			if (isset($move['characterKey']) && isset($move['location'])){
				//Use characterKey (character_id) to find current location and name
				$characterInfo = array_filter($currentLocations, function ($char) use ($move){
					return $char['character_id'] == $move['characterKey'];
				});

				if (empty($characterInfo)){
					continue; //Skip if character not found
				}

				$characterInfo = reset($characterInfo); //Get the first (and should be only) result
				$currentLocation = $characterInfo['location'];
				$cardName = $characterInfo['card_name'];
				$characterId = $characterInfo['character_id']; //Retrieve the character_id
				
				//Run some additional checks to make sure dead guys end up in the right locations.
				if ($characterInfo['health'] <= 0){
					$characterInfo['location'] = 86;
				}
				if ($characterInfo['location'] == 86){
					$characterInfo['health'] = 0;
				}
				//Update the character data
				$properMove = $characterInfo['location'] == 86 ? $characterInfo['location'] : $move['location'];
				
				$updateSql = "UPDATE gameCharacters SET location = :location, health = :health, energy = :energy, aim = :aim, speed = :speed, defend = :defend, luck = :luck, h_status = :hStatus, hs_int = :hsInt, t_status = :tStatus, v_status = :vStatus WHERE game_id = :gameID AND character_id = :characterId";
				$updateStmt = $dbConnection->prepare($updateSql);
				$updateStmt->bindParam(':gameID', $gameID, PDO::PARAM_INT);
				$updateStmt->bindParam(':characterId', $characterId, PDO::PARAM_INT);
				$updateStmt->bindParam(':location', $properMove, PDO::PARAM_INT);
				$updateStmt->bindParam(':health', $characterInfo['health'], PDO::PARAM_INT);
				$updateStmt->bindParam(':energy', $characterInfo['energy'], PDO::PARAM_INT);
				$updateStmt->bindParam(':aim', $characterInfo['aim'], PDO::PARAM_INT);
				$updateStmt->bindParam(':speed', $characterInfo['speed'], PDO::PARAM_INT);
				$updateStmt->bindParam(':defend', $characterInfo['defend'], PDO::PARAM_INT);
				$updateStmt->bindParam(':luck', $characterInfo['luck'], PDO::PARAM_INT);
				$updateStmt->bindParam(':hStatus', $characterInfo['h_status'], PDO::PARAM_STR);
				$updateStmt->bindParam(':hsInt', $characterInfo['hs_int'], PDO::PARAM_INT);
				$updateStmt->bindParam(':tStatus', $characterInfo['t_status'], PDO::PARAM_STR);
				$updateStmt->bindParam(':vStatus', $characterInfo['v_status'], PDO::PARAM_STR);
				$updateStmt->execute();

				//Create a summary of the move
				if ($move['location'] == 0 || $move['location'] == 86){
					//Character is dead or in hand -- do NOTHING
				}
				else if ($move['location'] == $currentLocation && $move['location'] != 0 && $move['location'] != 86){
					$moveSummary = "$cardName stayed at square $currentLocation";
					$moveSummaries[] = $moveSummary;
				}
				else{
					$moveSummary = "$cardName moved to square {$move['location']}";
					$moveSummaries[] = $moveSummary;
				}
				


				//Create simple summary of the move using character_id
				$simpleMoveSummary = [
					'characterId' => $characterId,
					'location' => $move['location'],
				];
				$simpleMoveSummaries[] = $simpleMoveSummary;
			}
		}

		return [
			//Actions
			'success' => true, 
			'message' => 'Character locations and actions updated successfully', 
			'actionSummaries' => $encodedActionSummaries, 
			'simpleActionSummaries' => $encodedSimpleActionSummaries,
			//Moves
			'conflicts' => $conflicts,
			'summaries' => $moveSummaries,
			'simpleSummaries' => $simpleMoveSummaries,
		];
	}

?>
