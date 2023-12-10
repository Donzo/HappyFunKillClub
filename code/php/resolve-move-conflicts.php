<?php
	//Filter out moves without a specific location
	$filterMoves = function($move){
		return isset($move['location']) && intval($move['location']) !== 0;
	};

	$p1FilteredMoves = array_filter($p1Moves, $filterMoves);
	$p2FilteredMoves = array_filter($p2Moves, $filterMoves);
	
	//Function to resolve conflict
	function resolveConflict($charID1, $charID2){
		$chStats1 = getCharacterSpeedAndLuckByID($charID1, $currentLocations);
		$chStats2 = getCharacterSpeedAndLuckByID($charID2, $currentLocations);
		if (intval($chStats1['speed']) > intval($chStats2['speed'])){
			return 1;
		}
		else if (intval($chStats1['speed']) < intval($chStats2['speed'])){
			return 2;
		}
		else if (intval($chStats1['luck']) > intval($chStats2['luck'])){
			return 1;
		}
		else if (intval($chStats1['luck']) < intval($chStats2['luck'])){
			return 2;
		}
		else{
			return rand(0, 1) ? 1 : 2; //Randomly choose winner
		}
	}
	
	//Identify and resolve conflicts
	$conflicts = [];
	foreach ($p1FilteredMoves as $p1Index => $p1Move){
		foreach ($p2FilteredMoves as $p2Index => $p2Move){
			if (intval($p1Move['location']) == intval($p2Move['location'])){
				//Conflict detected, resolve it
				$winner = resolveConflict($p1Move['characterKey'], $p2Move['characterKey']);
				
				$losingCharacterCurrentLocation = findCharacterLastTurnLocation(
					$winner == 1 ? $p2Move['characterKey'] : $p1Move['characterKey'],
					$currentLocations
				);

				
				if ($winner == 1){
					//Player 1 wins, update Player 2's move
					$losingCharacterCurrentLocation = findCharacterLastTurnLocation($p2Move['characterKey'], $currentLocations);
					$p2Moves[$p2Index]['location'] = intval($losingCharacterCurrentLocation);
					if ($p2Moves[$p2Index]['location'] == 0){
						$p2Moves[$p2Index]['location'] = 8;
					}
				}
				else{
					//Player 2 wins, update Player 1's move
					$losingCharacterCurrentLocation = findCharacterLastTurnLocation($p1Move['characterKey'], $currentLocations);
					$p1Moves[$p1Index]['location'] = intval($losingCharacterCurrentLocation);
					if ($p1Moves[$p1Index]['location'] == 0){
						$p1Moves[$p1Index]['location'] = 4;
					}
				}
				
				//Add to conflicts array for record-keeping
				$conflicts[] = [
					'location' => $p1Move['location'],
					'p1CharacterKey' => $p1Move['characterKey'],
					'p2CharacterKey' => $p2Move['characterKey'],
					'winner' => $winner
				];
			}
		}
	}
	

	function getCharacterSpeedAndLuckByID($characterId, $currentLocations){
		foreach ($currentLocations as $character){
			if ($character['character_id'] == $characterId){
				return [
					'speed' => $character['speed'],
					'luck' => $character['luck']
				];
			}
		}
		return null; //Return null if character not found
	}
	
	//Function to find the last turn location of a character by characterKey
	function findCharacterLastTurnLocation($characterKey, $currentLocations){
		foreach ($currentLocations as $character){
			if ($character['character_id'] == $characterKey){
				return $character['location'];
			}
		}
		return null; //or some default value if not found
	}
	
?>
