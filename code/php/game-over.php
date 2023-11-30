<?php
	session_start();
	header('Content-Type: application/json');

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
		
		//First, check if the game still exists
		$checkGameExistenceSql = "SELECT game_id, p1WalletAddress, p2WalletAddress FROM games WHERE game_id = :gameID";
		$checkGameExistenceStmt = $my_Db_Connection->prepare($checkGameExistenceSql);
		$checkGameExistenceStmt->bindParam(':gameID', $gameID);
		$checkGameExistenceStmt->execute();

		if ($checkGameExistenceStmt->rowCount() == 0){
			//Game already concluded by the other player
			echo json_encode(['success' => true, 'message' => 'Game already concluded']);
			exit;
		}
		
		//Check the location of all characters for both players
		$characterCheckSql = "SELECT player, COUNT(*) as count, SUM(location = 86) as eliminatedCount FROM gameCharacters WHERE game_id = :gameID GROUP BY player";
		$characterCheckStmt = $my_Db_Connection->prepare($characterCheckSql);
		$characterCheckStmt->bindParam(':gameID', $gameID);
		$characterCheckStmt->execute();
		$characterCounts = $characterCheckStmt->fetchAll(PDO::FETCH_ASSOC);
		$printedCC = print_r($characterCounts);
		$winner = null;
		$loser = null;
		
		foreach ($characterCounts as $row){
			if ($row['count'] == $row['eliminatedCount']){ //All characters of this player are eliminated
				$loser = $row['player'];
			}
		}

		//Determine the winner
		if ($loser){
			$winner = ($loser == 'p1') ? 'p2' : 'p1';
		} 

		//If a winner and loser are not determined, exit
		if (!$winner || !$loser){
			echo json_encode(['error' => '$printedCC Unable to determine game results', 'success' => false]);
			$my_Db_Connection = NULL;
			exit;
		}
		
		//Get the wallet addresses of the winner and loser
		$gameInfoSql = "SELECT p1WalletAddress, p2WalletAddress FROM games WHERE game_id = :gameID";
		$gameInfoStmt = $my_Db_Connection->prepare($gameInfoSql);
		$gameInfoStmt->bindParam(':gameID', $gameID);
		$gameInfoStmt->execute();
		$gameInfo = $gameInfoStmt->fetch(PDO::FETCH_ASSOC);

		$winnerAddress = ($winner == 'p1') ? $gameInfo['p1WalletAddress'] : $gameInfo['p2WalletAddress'];
		$loserAddress = ($loser == 'p1') ? $gameInfo['p1WalletAddress'] : $gameInfo['p2WalletAddress'];
		
		//Start transaction
		$my_Db_Connection->beginTransaction();

		//Update users table for the winner
		$updateWinnerSql = "UPDATE users SET redCoins = redCoins + 3, pvpMatches = pvpMatches + 1, pvpWin = pvpWin + 1 WHERE account = :winner";
		$updateWinnerStmt = $my_Db_Connection->prepare($updateWinnerSql);
		$updateWinnerStmt->bindParam(':winner', $winnerAddress);
		$updateWinnerStmt->execute();

		//Update users table for the loser
		$updateLoserSql = "UPDATE users SET redCoins = redCoins + 1, pvpMatches = pvpMatches + 1, pvpLose = pvpLose + 1 WHERE account = :loser";
		$updateLoserStmt = $my_Db_Connection->prepare($updateLoserSql);
		$updateLoserStmt->bindParam(':loser', $loserAddress);
		$updateLoserStmt->execute();

		//Delete the game
		$deleteGameSql = "DELETE FROM games WHERE game_id = :gameID";
		$deleteGameStmt = $my_Db_Connection->prepare($deleteGameSql);
		$deleteGameStmt->bindParam(':gameID', $gameID);
		$deleteGameStmt->execute();

		//Commit the transaction
		$my_Db_Connection->commit();

		echo json_encode(['success' => true, 'message' => 'Game concluded and rewards updated']);
		$my_Db_Connection = NULL;
	}
	catch (PDOException $e){
		//Rollback transaction on error
		$my_Db_Connection->rollBack();
		echo json_encode(['error' => $e->getMessage(), 'success' => false]);
		$my_Db_Connection = NULL;
	}
?>
