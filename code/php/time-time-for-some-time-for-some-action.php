<?php
		$finalEffect = 0;
		$finalEffectNarrative = "";
		$damage = 0;
		$maxEnergy = 20;

		
		//This function pulls all the data from out $currentLocations database query object
		function getAllCharacterStatsById($characterId, $currentLocations){
			foreach ($currentLocations as $character){
				if ($character['character_id'] == $characterId){
					return [
						'character_id' => $character['character_id'],
						'card_name' => $character['card_name'],
						'health' => $character['health'],
						'energy' => $character['energy'],
						'aim' => $character['aim'],
						'speed' => $character['speed'],
						'defend' => $character['defend'],
						'luck' => $character['luck'],
						'h_status' => $character['h_status'],
						'hs_int' => $character['hs_int'],
						't_status' => $character['t_status'],
						'v_status' => $character['v_status'],
						'a1_name' => $character['a1_name'],
						'a1_type' => $character['a1_type'],
						'a1_trait' => $character['a1_trait'],
						'a1_effect' => $character['a1_effect'],
						'a1_cost' => $character['a1_cost'],
						'a2_name' => $character['a2_name'],
						'a2_type' => $character['a2_type'],
						'a2_trait' => $character['a2_trait'],
						'a2_effect' => $character['a2_effect'],
						'a2_cost' => $character['a2_cost'],
						'a3_name' => $character['a3_name'],
						'a3_type' => $character['a3_type'],
						'a3_trait' => $character['a3_trait'],
						'a3_effect' => $character['a3_effect'],
						'a3_cost' => $character['a3_cost'],
						'location' => $character['location']
					];
				}
			}
			return null; //Return null if character not found
		}
		
		function combineAndSortActions($p1Actions, $p2Actions, $currentLocations){
			//Filter out invalid actions where 'action' is false
			$filteredP1Actions = array_filter($p1Actions, function($action){
				return $action['action'] !== false;
			});
			$filteredP2Actions = array_filter($p2Actions, function($action){
				return $action['action'] !== false;
			});

			//Merge the filtered actions
			$combinedActions = array_merge($filteredP1Actions, $filteredP2Actions);

			//Populate 'speed' and 'luck' for each action
			foreach ($combinedActions as &$action){
				$stats = getAllCharacterStatsById($action['characterKey'], $currentLocations);
				$action['speed'] = $stats['speed'];
				$action['luck'] = $stats['luck'];
			}

			//Sort the actions based on 'speed' and 'luck'
			usort($combinedActions, function($a, $b){
				if ($a['speed'] == $b['speed']){
					if ($a['luck'] == $b['luck']){
						return rand(0, 1) ? 1 : -1; //More explicit randomness
					}
					return $b['luck'] - $a['luck'];
				}
				return $b['speed'] - $a['speed'];
			});

			return $combinedActions;
		}
		
		
		function getAndUpdateCharacterStats(&$currentLocations, $characterKey){
			foreach ($currentLocations as &$character){
				if ($character['character_id'] == $characterKey){
					//Return a reference to the character's stats for modification
					return $character;
				}
			}
			return null;
		}
		function calcHitOrMiss($actorAim, $targetSpeed, $actorLuck, $targetLuck, $distanceFromTarget){
			//Unmodified 100% hit chance for melee attack
			$hitChance = 100;
			//Reduce hit chance with distance
			if ($distanceFromTarget == 2){
				$hitChance = 60;
			}
			else if ($distanceFromTarget == 3){
				$hitChance = 40;
			}
			else if ($distanceFromTarget == 4){
				$hitChance = 20;
			}
			
			//First modified is result of actor's aim - target's speed
			$hitChanceModifier1 = $actorAim - $targetSpeed; //(let's say 70 aim - 50 speed = +20 or reverse = -20)
			$hitChance += $hitChanceModifier1;
			
			//Second modifier is luck
			$hitChanceModifier2 = $actorLuck - $targetLuck;
			$hitChance += $hitChanceModifier2;
			
			//Always have at least a 10% chance of hitting (puncher's chance)
			if ($hitChance < 10){
				$hitChance = 10;
			}
			
			$diceRoll = rand(1, 100);
			
			return $diceRoll <= $hitChance  ? true : false;
			
		}
		function calcHitOrMissRangedSupport($actorAim, $targetSpeed, $actorLuck, $targetLuck, $distanceFromTarget){
			//Unmodified 100% hit chance for melee support
			$hitChance = 100;
			//Reduce hit chance with distance
			if ($distanceFromTarget == 2){
				$hitChance = 60;
			}
			else if ($distanceFromTarget == 3){
				$hitChance = 40;
			}
			else if ($distanceFromTarget == 4){
				$hitChance = 20;
			}
			
			//Modify support chance with BOTH target and actor luck
			$hitChanceModifier = $actorLuck + $targetLuck;
			$hitChance += $hitChanceModifier;
			
			//Always have at least a 10% chance of hitting (puncher's chance)
			if ($hitChance < 10){
				$hitChance = 10;
			}
			else if ($hitChance > 100){
				$hitChance = 100;
				if ($distanceFromTarget > 1){ //Always some chance of missing if at a distance
					$hitChance = 95;
				}
			}
			
			$diceRoll = rand(1, 100);
			
			return $diceRoll <= $hitChance  ? true : false;
			
		}
		function calcDamage($maxDamage, $targetDefend, $actorLuck, $targetLuck){
			$minimumDamageModifier = 10;
			$defenseModifier = ($targetDefend - 100) * -1;
			$luckModifier = $targetLuck - $actorLuck;
			$totalModifier = $defenseModifier + $luckModifier;
			if ($totalModifier > 100){
				$totalModifier = 100;
			}
			else if ($totalModifier < $minimumDamageModifier){ //Always take the minimum damage modifier for a successful hit.
				$totalModifier = $minimumDamageModifier;
			}
			
			$totalModifier *= .01;
			
			return round($maxDamage * $totalModifier);	
		}
		function checkEnergy($energyCost, $myEnergy){
			return $myEnergy >= $energyCost ? true : false;
		}
		
				function updateCharacterInLocations(&$currentLocations, $characterKey, $updatedStats){
			foreach ($currentLocations as &$character){
				if ($character['character_id'] == $characterKey){
					$character = array_merge($character, $updatedStats);
					break;
				}
			}
		}
		
		
		//Reads the action number and applies the correct response based on DB values / transferred to the character object
		function getActionDetails($stats, $actionString){
			//Extract the number from the action string (e.g., "action1" becomes "1")
			preg_match('/\d+/', $actionString, $matches);
			$actionNumber = $matches[0] ?? null;
			switch ($actionNumber){
				case 1:
					return [
						'name' => $stats['a1_name'],
						'type' => $stats['a1_type'],
						'trait' => $stats['a1_trait'],
						'effect' => $stats['a1_effect'],
						'cost' => $stats['a1_cost']
					];
				case 2:
					return [
						'name' => $stats['a2_name'],
						'type' => $stats['a2_type'],
						'trait' => $stats['a2_trait'],
						'effect' => $stats['a2_effect'],
						'cost' => $stats['a2_cost']
					];
				case 3:
					return [
						'name' => $stats['a3_name'],
						'type' => $stats['a3_type'],
						'trait' => $stats['a3_trait'],
						'effect' => $stats['a3_effect'],
						'cost' => $stats['a3_cost']
					];
				}
			return null;
		}
		
		
		$sortedActions = combineAndSortActions($p1Actions, $p2Actions, $currentLocations);
		
		
		
		//Calculate and apply the effects of the actions in the order of the sortedActions array
		foreach ($sortedActions as $action){
			
			//Reset These Values
			$finalEffect = 0;
			$finalEffectNarrative = "";
			$damage = 0;
			
			$actorStats = getAndUpdateCharacterStats($currentLocations, $action['characterKey']);
			
			
			//Determine which action to use based on the action number
			$actionDetails = getActionDetails($actorStats, $action['action']);
			
			//Verify that the actor ACTUALLY has the energy to perform the attack (also checked on client side).
			$energyCheck = checkEnergy($actionDetails['cost'], $actorStats['energy']);

			
			//Check if the action has a target and the target is valid, and the actor is not dead oh And he needs the energy too.
			if ($action['target'] != null && $actorStats['location'] != 86 && $actorStats['health'] > 0 &&  $energyCheck){
				
				//Spend the energy
				$actorStats['energy'] -= $actionDetails['cost'];
				
				//Now Get Target Stats
				$targetStats = getAndUpdateCharacterStats($currentLocations, $action['target']);
				
				
				//Process the action based on its type
				switch ($actionDetails['type']){
					case 'Melee':
					case 'Ranged':
						if ($targetStats['health'] > 0){ //Make sure the target is alive
							
							//For Melee or Ranged actions, calculate and apply damage
							$actorDistance = calculateRangeScore($actorStats['location'], $targetStats['location']);
							
							//Calculate if Character Hits
							$didHit = calcHitOrMiss($actorStats['aim'], $targetStats['speed'], $actorStats['luck'], $targetStats['luck'], $actorDistance );
							if ($didHit){ 
								//Calculate Damage
								$damage = calcDamage($actionDetails['effect'], $targetStats['defend'], $actorStats['luck'], $targetStats['luck']);
								$finalEffect = $damage; //Record final effect of attack for summary
								
								//Alter health of target
								$targetStats['health'] -= $damage;
								
								//Adjust health to zero if it went under and move character out of game.
								if ($targetStats['health'] <= 0){
									$targetStats['health'] = 0;
									$targetStats['location'] = 86;
									$finalEffectNarrative = "was killed";
								}
								else{
									$finalEffectNarrative = "was wounded";
								}
							}
						}
						else{
							//This target was attacked with 0 health.
							$finalEffectNarrative = "was already dead";
						}
						break;

					case 'Boost':
						if ($actorStats['health'] > 0){ //Make sure the target is alive
							//For Boost actions, apply the effect to the trait, no rolling since this is melee and helps the player.
							$traitToBoost = $actionDetails['trait'];
							$boostAmount = $actionDetails['effect'];
							$actorStats[$traitToBoost] += $boostAmount;
							$finalEffect = $boostAmount;
							$finalEffectNarrative = "increased $traitToBoost by $finalEffect";
						}
						else{
							$finalEffect = 0;
							//This actor was dead by this time.
							$finalEffectNarrative = "was already dead";
						}
						break;
					case 'Support':
						if ($targetStats['health'] > 0){ //Make sure the target is alive
							//Same as boost but used on an ally.
							$traitToBoost = $actionDetails['trait'];
							$boostAmount = $actionDetails['effect'];
							$targetStats[$traitToBoost] += $boostAmount;
							$finalEffect = $boostAmount;
							$finalEffectNarrative = "increased $traitToBoost by $finalEffect";
						}
						else{
							$finalEffect = 0;
							//This target was dead by this time.
							$finalEffectNarrative = "was already dead";
						}
						break;
					case 'R Support':
						if ($targetStats['health'] > 0){ //Make sure the target is alive
							
							$traitToBoost = $actionDetails['trait'];
							
							//For Melee or Ranged actions, calculate and apply damage
							$actorDistance = calculateRangeScore($actorStats['location'], $targetStats['location']);
							
							//Calculate if Character Hits
							$didHit = calcHitOrMissRangedSupport($actorStats['aim'], $targetStats['speed'], $actorStats['luck'], $targetStats['luck'], $actorDistance );
							if ($didHit){ 
															
								//Support effective... Boost Trait
								$traitToBoost = $actionDetails['trait'];
								$boostAmount = $actionDetails['effect'];
								$targetStats[$traitToBoost] += $boostAmount;
								$finalEffect = $boostAmount;
								$finalEffectNarrative = "increased $traitToBoost by $finalEffect";
							}
						}
						else{
							//This target was supported with 0 health.
							$finalEffectNarrative = "was already dead";
						}						
					break;
					case 'Drain':
						if ($targetStats['health'] > 0){ //Make sure the target is alive
							
							//For Melee or Ranged actions, calculate and apply damage
							$actorDistance = calculateRangeScore($actorStats['location'], $targetStats['location']);
							
							//Calculate if Character Hits
							$didHit = calcHitOrMiss($actorStats['aim'], $targetStats['speed'], $actorStats['luck'], $targetStats['luck'], $actorDistance );
							if ($didHit){ 
								//It hit lower trait...
								$finalEffect = $actionDetails['effect']; //Record final effect of attack for summary
								
								//Alter health of target
								$traitToDrain = $actionDetails['trait'];
								$targetStats[$traitToDrain] -= $actionDetails['effect'];
								
								//Do not lower a stat below zero.
								if ($targetStats[$traitToDrain] < 0){
									$targetStats[$traitToDrain] = 0;
								}
								else{
									$targetStats[$traitToDrain] -= $actionDetails['effect'];
								}
								$finalEffectNarrative = "was drained";
							}
						}
						else{
							//This target was attacked with 0 health.
							$finalEffectNarrative = "was already dead";
						}
					case 'R Drain':
						if ($targetStats['health'] > 0){ //Make sure the target is alive
							
							//For Melee or Ranged actions, calculate and apply damage
							$actorDistance = calculateRangeScore($actorStats['location'], $targetStats['location']);
							
							//Calculate if Character Hits
							$didHit = calcHitOrMiss($actorStats['aim'], $targetStats['speed'], $actorStats['luck'], $targetStats['luck'], $actorDistance );
							if ($didHit){ 
								//It hit lower trait...
								$finalEffect = $actionDetails['effect']; //Record final effect of attack for summary
								
								//Alter health of target
								$traitToDrain = $actionDetails['trait'];
								$targetStats[$traitToDrain] -= $actionDetails['effect'];
								
								//Do not lower a stat below zero.
								if ($targetStats[$traitToDrain] < 0){
									$targetStats[$traitToDrain] = 0;
								}
								else{
									$targetStats[$traitToDrain] -= $actionDetails['effect'];
								}
								$finalEffectNarrative = "was drained";
							}
						}
						else{
							//This target was attacked with 0 health.
							$finalEffectNarrative = "was already dead";
						}
					break;
				}
		
				//Final Death Catch
				if ($finalEffectNarrative == "was killed"){
					$targetStats['health'] = 0;
					$targetStats['location'] = 86;
				}
				
				//Update the target's stats in the $currentLocations array
				updateCharacterInLocations($currentLocations, $action['target'], $targetStats);
				
				$simpleActionSummaries[] = [
					'actorId' => $action['characterKey'],
					'targetId' => $action['target'],
					'trait' => $actionDetails['trait'],
					'actionType' => $actionDetails['type'],
					'actionCost' => $actionDetails['cost'],
					'effect' => $finalEffect
				];
				
				$actionSummaries[] = [
					'actorName' => $actorStats['card_name'],
					'actionName' => $actionDetails['name'],
					'actionType' => $actionDetails['type'],
					'actionCost' => $actionDetails['cost'],
					'trait' => $actionDetails['trait'],
					'targetName' => $targetStats['card_name'],
					'result' => $finalEffectNarrative //Example narrative
				];
				
			}
			else if ($actorStats['location'] == 86 || $actorStats['health'] <= 0 ){
				$finalEffect = 0;
				//This target was dead by this time.
				$finalEffectNarrative = "was dead and could not take action";
				
				//Actors turn but he's dead...
				$simpleActionSummaries[] = [
					'actorId' => $action['characterKey'],
					'targetId' => $action['target'],
					'trait' => $actionDetails['trait'],
					'actionType' => $actionDetails['type'],
					'effect' => $finalEffect
				];
				
				$actionSummaries[] = [
					'actorName' => $actorStats['card_name'],
					'actionName' => $actionDetails['name'],
					'actionType' => $actionDetails['type'],
					'trait' => $actionDetails['trait'],
					'targetName' => $targetStats['card_name'],
					'result' => $finalEffectNarrative,
					'actorId' => $action['characterKey'],
					'targetId' => $action['target'],
					'effect' => $finalEffect
				];
			}
			//Update the actor's stats in the $currentLocations array
			updateCharacterInLocations($currentLocations, $action['characterKey'], $actorStats);
			
		}
		
		//Increase energy for each character after all actions
		foreach ($currentLocations as &$character){
			if ($character['location'] != 0 && $character['location'] != 86){
				//Increase energy by 1, or set to a maximum value
				$character['energy'] = min($character['energy'] + 1, $maxEnergy); 
			}
		}
?>		