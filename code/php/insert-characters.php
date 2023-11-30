<?php

	$charactersInsertedSuccessfully = true;

	function insertCharacter($player, $cardDetails, $gameID, $my_Db_Connection) {
		// Get the JSON content from the character file
		$jsonFilePath = $_SERVER['DOCUMENT_ROOT'] . $cardDetails['dir'] . 'game.json'; // Note the file name change to 'game.json'
		if (!file_exists($jsonFilePath)) {
			$charactersInsertedSuccessfully = false;
			return;
		}
    
		$characterData = json_decode(file_get_contents($jsonFilePath), true);
    
		$health = intval($characterData['health']);
		$energy = intval($characterData['energy']);
		$aim = intval($characterData['aim']);
		$speed = intval($characterData['speed']);
		$defend = intval($characterData['defend']);
		$luck = intval($characterData['luck']);
    
		$action1 = $characterData['action1'][0];
		$action2 = $characterData['action2'][0];
		$action3 = $characterData['action3'][0];

		$a1_effect = intval($action1['effect']);
		$a1_cost = intval($action1['cost']);
		$a2_effect = intval($action2['effect']);
		$a2_cost = intval($action2['cost']);
		$a3_effect = intval($action3['effect']);
		$a3_cost = intval($action3['cost']);
    
		$status = $characterData['status'][0];
		$hs_int = intval($status['hs_int']);
    
		$insertSql = "INSERT INTO gameCharacters (
			game_id, player, card_id, card_name, health, energy, aim, speed, defend, luck,
			h_status, hs_int, t_status, v_status,
			a1_name, a1_type, a1_trait, a1_effect, a1_cost,
			a2_name, a2_type, a2_trait, a2_effect, a2_cost,
			a3_name, a3_type, a3_trait, a3_effect, a3_cost,
			location
		) VALUES (
			:game_id, :player, :card_id, :card_name, :health, :energy, :aim, :speed, :defend, :luck,
			:h_status, :hs_int, :t_status, :v_status,
			:a1_name, :a1_type, :a1_trait, :a1_effect, :a1_cost,
			:a2_name, :a2_type, :a2_trait, :a2_effect, :a2_cost,
			:a3_name, :a3_type, :a3_trait, :a3_effect, :a3_cost,
			0
		)";

		$stmt = $my_Db_Connection->prepare($insertSql);
		$stmt->bindParam(':game_id', $gameID);
		$stmt->bindParam(':player', $player);
		$stmt->bindParam(':card_id', $cardDetails['Id']);
		$stmt->bindParam(':card_name', $characterData['name']);
		$stmt->bindParam(':health', $health, PDO::PARAM_INT);
		$stmt->bindParam(':energy', $energy, PDO::PARAM_INT);
		$stmt->bindParam(':aim', $aim, PDO::PARAM_INT);
		$stmt->bindParam(':speed', $speed, PDO::PARAM_INT);
		$stmt->bindParam(':defend', $defend, PDO::PARAM_INT);
		$stmt->bindParam(':luck', $luck, PDO::PARAM_INT);
		$stmt->bindParam(':h_status', $status['healthStatus']);
		$stmt->bindParam(':hs_int', $hs_int, PDO::PARAM_INT);
		$stmt->bindParam(':t_status', $status['terrainStatus']);
		$stmt->bindParam(':v_status', $status['visionStatus']);
		$stmt->bindParam(':a1_name', $action1['name']);
		$stmt->bindParam(':a1_type', $action1['type']);
		$stmt->bindParam(':a1_trait', $action1['trait']);
		$stmt->bindParam(':a1_effect', $a1_effect, PDO::PARAM_INT);
		$stmt->bindParam(':a1_cost', $a1_cost, PDO::PARAM_INT);
		$stmt->bindParam(':a2_name', $action2['name']);
		$stmt->bindParam(':a2_type', $action2['type']);
		$stmt->bindParam(':a2_trait', $action2['trait']);
		$stmt->bindParam(':a2_effect', $a2_effect, PDO::PARAM_INT);
		$stmt->bindParam(':a2_cost', $a2_cost, PDO::PARAM_INT);
		$stmt->bindParam(':a3_name', $action3['name']);
		$stmt->bindParam(':a3_type', $action3['type']);
		$stmt->bindParam(':a3_trait', $action3['trait']);
		$stmt->bindParam(':a3_effect', $a3_effect, PDO::PARAM_INT);
		$stmt->bindParam(':a3_cost', $a3_cost, PDO::PARAM_INT);
    
	    if (!$stmt->execute()) {
	        $errorInfo = $stmt->errorInfo();
	        echo "SQL Error: " . $errorInfo[2];
	        $charactersInsertedSuccessfully = false;
	        return;
	    }
	}

	//Loop through each player's card details and insert them into the database
	foreach ($p1CardDetails as $cardDetails) {
	    insertCharacter('p1', $cardDetails, $gameID, $my_Db_Connection);
	    if (!$charactersInsertedSuccessfully) {
	        break;
	    }
	}

	foreach ($p2CardDetails as $cardDetails) {
		insertCharacter('p2', $cardDetails, $gameID, $my_Db_Connection);
		if (!$charactersInsertedSuccessfully) {
			break; 
		}
	}

?>
