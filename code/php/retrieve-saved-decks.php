<?php
	session_start();
	
	if (!isset($_SESSION["account"]) || empty($_SESSION["account"])) {
		die('No signed in account...');
	}
		
	//Check if the content type is JSON
    if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
		$data = json_decode(file_get_contents('php://input'), true);
		$whichDeck = $data['savedDeckNum'] ?? '';
	}
	else{
		//For regular POST data used in DECKBUILDER mode
		$whichDeck = $_POST['savedDeckNum'] ?? '';
    }
	
	//Check if the content type is JSON
	if (strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
		$data = json_decode(file_get_contents('php://input'), true);
		$whichDeck = $data['savedDeckNum'] ?? '';
	}
	else {
		$whichDeck = $_POST['savedDeckNum'] ?? '';
	}
	
	$allowedDecks = ['deck1', 'deck2', 'deck3'];
	
	if (!in_array($whichDeck, $allowedDecks)) {
		die('Invalid deck specified.');
	}
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
	

	$stmt = $my_Db_Connection->prepare("SELECT $whichDeck FROM users WHERE account = :account");
	$stmt->bindParam(':account', $_SESSION["account"]);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($result && isset($result[$whichDeck])) {
		$deckData = $result[$whichDeck];
		$_SESSION[$whichDeck] = $deckData;
		echo($deckData); // This will print the deck data array
	}
	$my_Db_Connection = NULL;
?>