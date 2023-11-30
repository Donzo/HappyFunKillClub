<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	header('Content-Type: application/json');

	//Select all wallet addresses from the waitingRoom table
	$sql = "SELECT account FROM waitingRoom";

	try {
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->execute();

		//Fetch all wallet addresses
		$walletAddresses = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // 0 indicates the first column, which is 'account'

		//Closing the database connection
		$my_Db_Connection = NULL;

		//Encoding the wallet addresses as a JSON object and echoing it
		echo json_encode($walletAddresses);
	}
	catch (PDOException $e) {
		$my_Db_Connection = NULL;  
		// Send the error message as a JSON object
		echo json_encode(["error" => "Error fetching wallet addresses in the waiting room: " . $e->getMessage()]);
	}
?>