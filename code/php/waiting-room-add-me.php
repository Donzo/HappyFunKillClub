<?php
	session_start();

	if(!isset($_SESSION['account']) || !isset($_SESSION['deck'])) {
		die("Session data missing");
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	//Getting the player's wallet address and deck data from session
	$walletAddress = $_SESSION['account'];
	$deckData = $_SESSION['deck'];

	// Check if $deckData is a valid JSON string
	$jsonDeckData = json_encode($deckData);
	if (json_last_error() !== JSON_ERROR_NONE) {
		// If $deckData was not valid JSON, let's log the error and the data for debugging
		error_log("Invalid JSON data: " . json_last_error_msg());
		error_log("Deck data: " . print_r($deckData, true));
		die("Invalid deck data");
	}

	//Insert a new player into the waitingRoom table
	$sql = "INSERT INTO waitingRoom (account, deck) VALUES (:walletAddress, :jsonDeckData)
		ON DUPLICATE KEY UPDATE account=account";
	try{
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':walletAddress', $walletAddress);
		$stmt->bindParam(':jsonDeckData', $jsonDeckData);
		$stmt->execute();

		$my_Db_Connection = NULL;

		echo "Waiting";
	}
	catch (PDOException $e){	
		$my_Db_Connection = NULL;
		echo "Error adding player: " . $e->getMessage();
	}
	finally{
		$my_Db_Connection = NULL;
	}
?>