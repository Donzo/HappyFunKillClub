<?php
	session_start();

	header('Content-Type: application/json');

	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'matched' => false]);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	try {
		//Start transaction
		$my_Db_Connection->beginTransaction();

		//Fetch the oldest two entries from the waiting room along with their decks
		$sql = "SELECT account, deck FROM waitingRoom ORDER BY enteredAt ASC LIMIT 2 FOR UPDATE";
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->execute();
		$waitingPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($waitingPlayers) >= 2){
			$player1 = $waitingPlayers[0]['account'];
			$player2 = $waitingPlayers[1]['account'];

			$deck1 = $waitingPlayers[0]['deck'];
			$deck2 = $waitingPlayers[1]['deck'];
			
			//Add a new game entry
			$sqlInsertGame = "INSERT INTO games (p1WalletAddress, p1DeckJSON, p2WalletAddress, p2DeckJSON, status) VALUES (:player1, :deck1, :player2, :deck2, 'WAITING')";
			$stmtInsertGame = $my_Db_Connection->prepare($sqlInsertGame);
			$stmtInsertGame->bindParam(':player1', $player1);
			$stmtInsertGame->bindParam(':deck1', $deck1);
			$stmtInsertGame->bindParam(':player2', $player2);
			$stmtInsertGame->bindParam(':deck2', $deck2);
			$stmtInsertGame->execute();

			if ($stmtInsertGame->rowCount() !== 1){
				throw new Exception("Failed to insert game into database.");
			}
			
			//Get the last inserted game ID
			$game_id = $my_Db_Connection->lastInsertId();
			
			//Remove these two players from the waitingRoom table
			$sqlDelete = "DELETE FROM waitingRoom WHERE account IN (:player1, :player2)";
			$stmtDelete = $my_Db_Connection->prepare($sqlDelete);
			$stmtDelete->bindParam(':player1', $player1);
			$stmtDelete->bindParam(':player2', $player2);
			$stmtDelete->execute();

			if ($stmtDelete->rowCount() !== 2){
				throw new Exception("Failed to remove players from waiting room.");
			}

			//Commit the changes
			$my_Db_Connection->commit();

			if ($_SESSION['account'] == $player1 || $_SESSION['account'] == $player2){
				$opponent = ($_SESSION['account'] == $player1) ? $player2 : $player1;
				$opponentDeck = ($_SESSION['account'] == $player1) ? $deck2 : $deck1;
				echo json_encode(['matched' => true, 'opponent' => $opponent, 'opponentDeck' => $opponentDeck, 'game_id' => $game_id]);
			}
			else {
				echo json_encode(['matched' => false]);
			}
		}
		else {
			echo json_encode(['matched' => false]);
		}
		$my_Db_Connection = NULL;
	}
	catch (Exception $e){
		$my_Db_Connection->rollBack();
		$my_Db_Connection = NULL;
		echo json_encode(['error' => "Error matching players: " . $e->getMessage(), 'matched' => false]);
	}
?>