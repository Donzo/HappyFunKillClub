<?php
	session_start();
	
	function isValidDeck($savedDeck, $myCardsObj){
		$myCardIDsFlat = [];

		foreach ($myCardsObj as $sessionItem){
			foreach ($sessionItem as $key => $value){
				if (is_numeric($value) && $value > 0){
					for ($i = 0; $i < $value; $i++){
						$myCardIDsFlat[] = $key;
					}
				}
			}
		}

		$arrayA = $myCardIDsFlat; 
		$arrayB = $savedDeck;  

		foreach ($arrayB as $item){
			$key = array_search($item, $arrayA);
			if ($key !== false){
				unset($arrayA[$key]);
				$arrayA = array_values($arrayA);
			} 
			else {
				return false;
			}
		}
		return true;
	}
	
	header("Content-Type: application/json");

	if ($_SERVER["CONTENT_TYPE"] == "application/json"){
		$input = json_decode(file_get_contents('php://input'), true);

		if (isset($input['deck']) && is_string($input['deck'])){
			$savedDeck = explode(',', $input['deck']);

			//Validate the deck
			if (!isValidDeck($savedDeck, $_SESSION['myCardsObj'])){
				echo json_encode(['success' => false, 'message' => 'The player is claiming assets they don\'t have.']);
				exit;
			}
			
			$_SESSION['deck'] = $input['deck'];
			echo json_encode(['success' => true]);
		}
		else {
			echo json_encode(['success' => false, 'message' => 'Deck data not provided or invalid format']);
		}
	} 
	else {
		echo json_encode(['success' => false, 'message' => 'Invalid request']);
	}
	
?>