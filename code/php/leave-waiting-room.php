<?php
	session_start();

	if(!isset($_SESSION['account'])){
		echo json_encode(['success' => false, 'message' => 'Session data missing']);
		exit;
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	//Get the player's wallet address from the session
	$walletAddress = $_SESSION['account'];

	//Delete the player from the waitingRoom table
	$sql = "DELETE FROM waitingRoom WHERE account = :walletAddress";

	try {
		$stmt = $my_Db_Connection->prepare($sql);
		$stmt->bindParam(':walletAddress', $walletAddress);
		$stmt->execute();

		if ($stmt->rowCount() > 0){
			echo json_encode(['success' => true]);
		}
		else{
			echo json_encode(['success' => false, 'message' => 'Player not found in the waiting room']);
		}
	} 
	catch (PDOException $e){	
		echo json_encode(['success' => false, 'message' => 'Error removing player: ' . $e->getMessage()]);
	} 
	finally {
		$my_Db_Connection = NULL;
	}
?>