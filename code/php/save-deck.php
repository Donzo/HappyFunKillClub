<?php
	session_start();
	
	if (!isset($_SESSION["account"]) || empty($_SESSION["account"])){
		die('No signed in account...');
	}

	if (!isset($_POST['savedDeck']) || !is_string($_POST['savedDeck'])){
		die('Invalid deck data.');
	}

	$savedDeck = explode(',', $_POST['savedDeck']);
	
	if (!isset($_POST['savedDeckNum'])){
		die('Invalid deck number specified.');
	}
	
	$allowedDecks = ['deck1', 'deck2', 'deck3'];
	
	if (!in_array($_POST['savedDeckNum'], $allowedDecks)){
		die('Invalid deck specified.');
	}

	
	$savedDeck = $_POST['savedDeck']; //Array containing the card IDs the player would like to save as a deck.
	$whichDeck = $_POST['savedDeckNum']; //Name of column in which to save the JSON

	$myCardIDsFlat = [];

	if (isset($_SESSION['myCardsObj']) && is_array($_SESSION['myCardsObj'])){
		foreach ($_SESSION['myCardsObj'] as $sessionItem){
			foreach ($sessionItem as $key => $value){
				if (is_numeric($value) && $value > 0){
					for ($i = 0; $i < $value; $i++){
						$myCardIDsFlat[] = $key;
					}
				}
			}
		}
	}

	$arrayA = $myCardIDsFlat; 
	$arrayB = $savedDeck;  
	
	$isValid = true;

	foreach ($arrayB as $item){
		$key = array_search($item, $arrayA);
		if ($key !== false){
			//Remove the item from Array A
			unset($arrayA[$key]);
			//Re-index the array
			$arrayA = array_values($arrayA);
		} 
		else{
			//The item doesn't exist in Array A or it has been claimed too many times.
			$isValid = false;
			break;
		}
	}

	if ($isValid){
		$jsonCardDeck = json_encode($savedDeck); //Return the same data but it has now been validated.
		
		
		require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');
		
		
				
		$stmt = $my_Db_Connection->prepare("UPDATE users SET $whichDeck = :cardDeck WHERE ( account = :account)");
		$stmt->bindParam(':account', $_SESSION["account"]);
		$stmt->bindParam(':cardDeck', $jsonCardDeck);
		$stmt->execute();

		$my_Db_Connection = NULL;
		
		echo print_r($jsonCardDeck);
	}
	else{
		echo "The player is claiming assets they don't have.";
	}
?>