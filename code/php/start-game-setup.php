<?php
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	header('Content-Type: application/json');
	
	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'success' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
	require_once($_SERVER['DOCUMENT_ROOT'] .'/code/php/set-card-data.php'); 
	
	//Validate game ID
	if (!isset($_POST['game_id']) || !is_numeric($_POST['game_id']) || intval($_POST['game_id']) <= 0){
		echo json_encode(['error' => 'Invalid game ID', 'success' => false]);
		exit;
	}

	$gameID = $_POST['game_id'];

	try {
		//Begin a transaction
		$my_Db_Connection->beginTransaction();

		//Update the game status to LOADING and set gameStartedBy
		$sql = "UPDATE games SET status = 'LOADING', gameStartedBy = :gameStartedBy WHERE game_id = :gameID AND status = 'WAITING'";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':gameID', $gameID);
		$stmt->bindParam(':gameStartedBy', $_SESSION['account']); //Bind the session account to gameStartedBy
		$stmt->execute();

		//Check if the status was updated successfully
		if ($stmt->rowCount() == 1){
			//Fetch the deck JSON data
			$sqlDeck = "SELECT p1DeckJSON, p2DeckJSON FROM games WHERE game_id = :gameID";
			$stmtDeck = $my_Db_Connection->prepare($sqlDeck);
			$stmtDeck->bindParam(':gameID', $gameID);
			$stmtDeck->execute();
			$deckData = $stmtDeck->fetch(PDO::FETCH_ASSOC);

			//Convert the JSON strings into arrays
			$p1Deck = json_decode($deckData['p1DeckJSON']);
			$p2Deck = json_decode($deckData['p2DeckJSON']);
			
			
			//Call fetchDeckData for each player's deck
			$p1CardDetails = fetchDeckData($p1Deck);
			$p2CardDetails = fetchDeckData($p2Deck);
			
			require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/insert-characters.php');
			
			if ($charactersInsertedSuccessfully){
				//Update the game status to READY
				$sqlUpdateStatus = "UPDATE games SET status = 'READY' WHERE game_id = :gameID";
				$stmtUpdateStatus = $my_Db_Connection->prepare($sqlUpdateStatus);
				$stmtUpdateStatus->bindParam(':gameID', $gameID);
				$stmtUpdateStatus->execute();

				//Check if the status was updated successfully
				if ($stmtUpdateStatus->rowCount() == 1){
					//Commit the transaction since everything is successful
					$my_Db_Connection->commit();
					echo json_encode(['success' => true, 'status' => 'READY']);
					$my_Db_Connection = NULL;
				}
				else{
					//If the status update fails, roll back the transaction
					$my_Db_Connection->rollBack();
					echo json_encode(['success' => false, 'error' => 'Failed to update game status to READY']);
					$my_Db_Connection = NULL;
				}
			}
			else{
				//If the character insertion failed, roll back the transaction
				$my_Db_Connection->rollBack();
				$my_Db_Connection = NULL;
				echo json_encode(['success' => false, 'error' => 'Character insertion failed']);
			}
		}
		else{
			//If the initial status update to LOADING did not succeed, roll back the transaction
			$my_Db_Connection->rollBack();
			$my_Db_Connection = NULL;
			echo json_encode(['success' => false, 'error' => 'Game status update failed']);
		}
	}
	catch (PDOException $e){
		//If an exception occurred, roll back the transaction
		$my_Db_Connection->rollBack();
		$my_Db_Connection = NULL;
		echo json_encode(['error' => "Error starting game setup: " . $e->getMessage(), 'success' => false]);
	}
?>
