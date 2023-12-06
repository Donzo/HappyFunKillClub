<script>
		var charNum = false;
		var checkForMyRoundEnd = false;
		var pollCheckID = false;
		
		async function getSavedDeckInGame(which){

			let deckName = "deck" + which;
			
			let payload = {
				savedDeckNum: deckName
			};
			
			let query = await fetch('/code/php/retrieve-saved-decks.php', {
				method: "POST",
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload)
			});
			
			if (!query.ok){
				throw new Error(`HTTP error! Status: ${query.status}`);
			}
			let text = await query.text();  //first get it as text
			
			if (text && text != "Invalid deck specified."){
				let jsonData = JSON.parse(text);
				if (which == 1){
					mySavedDeck1Flat = jsonData;
					mySavedDeck1 = await structureDeckInGame(jsonData);
					mySavedDeck1 = setCardData(mySavedDeck1, 0);
					savedDeckCounter++;
				}
				else if (which == 2){
					mySavedDeck2Flat = jsonData;
					mySavedDeck2 = await structureDeckInGame(jsonData);
					mySavedDeck2 = setCardData(mySavedDeck2, 0);
					savedDeckCounter++;
				}
				else if (which == 3){
					mySavedDeck3Flat = jsonData;
					mySavedDeck3 = await structureDeckInGame(jsonData);
					mySavedDeck3 = setCardData(mySavedDeck3, 0);
					savedDeckCounter++;
				}
				if (mySavedDeck1.length > 0){
					selectedDeck = 1;
					updateDeckInSession(mySavedDeck1Flat);
				}
				else if (mySavedDeck2.length > 0){
					selectedDeck = 2;
					updateDeckInSession(mySavedDeck2Flat)	
				}
				else if (mySavedDeck3.length > 0){
					selectedDeck = 3;
					updateDeckInSession(mySavedDeck3Flat)	
				}
				else{
					selectedDeck = false;
				}
				
				

			}
		}
		function makeSureWePollForRoundEnd(){
			if (!gameStatusIntervalId && !checkForMyRoundEnd){
				startPollingRoundStatus();
			}
			checkForMyRoundEnd = false;
		}
		function startPollingRoundStatus(){
			if (!gameStatusIntervalId){
				gameStatusIntervalId = setInterval(() => checkForRoundEnd(ig.game.currentGameId), 2500); //Poll every 2.5 seconds
			}
		}
		function stopPollingRoundStatus(){
			clearInterval(gameStatusIntervalId);
			gameStatusIntervalId = null;
			roundAdvance();
		}
		function checkForRoundEnd(gameId){
			checkForMyRoundEnd = true;
			
			fetch('/code/php/check-round-progression.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `game_id=${gameId}`
			})
			.then(response => {
				if (!response.ok){
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then(data => {
				if (data.success){
					if (data.message == 'Out of time. Round Processed.' || data.message == 'Both players ready. Round Processed.' ){
						stopPollingRoundStatus();
						ig.game.turnEndScreenStatusTxt = "Players ready!";
					} 
					else if (data.message == 'Round already processed.'){
						stopPollingRoundStatus();
						ig.game.turnEndScreenStatusTxt = "Players ready!";
					}
					else if (data.message == 'Other player has advanced the round.'){
						stopPollingRoundStatus();
						ig.game.turnEndScreenStatusTxt = "Players ready!";
					}
					else{
						//Syncing player clock here.
						var gameClock = false;
						var serverTimeAdjusted = data.serverTimeRemaining - 10;
						if (ig.game.timeLeftInTurn){
							gameClock = ig.game.timeLeftInTurn.delta() * -1
						}
						if (gameClock > serverTimeAdjusted){
							ig.game.timeLeftInTurn.set(serverTimeAdjusted)
						}
						//Sync Round Number too
						var roundNumber =  data.roundNumber;
						if (ig.game.gData.turnNumber != roundNumber){
							ig.game.gData.turnNumber = roundNumber;
						}
					}
				} 
				else if (data.message == 'Round already processed.'){
					stopPollingRoundStatus();
				}
			})
			.catch(error => {
				//console.log(error);
			});
		}

		function roundAdvance(){
			ig.game.turnEndScreenStatusTxt = "Advancing to Next Round...";
			//Logic to transition to the new round
			startPollingRoundUpdate();
		}
		function startPollingRoundUpdate(){
			if (pollingIntervalId == null){
				pollingIntervalId = setInterval(() => {
						fetchRoundUpdate();
				}, 2500); //Poll every 2.5 seconds
			}
		}
		function stopPollingRoundUpdate(){
			if (pollingIntervalId != null){
				clearInterval(pollingIntervalId);
				pollingIntervalId = null;
			}
		}
		function fetchRoundUpdate(){
			const formData = new FormData();
			formData.append('game_id', ig.game.currentGameId);

			fetch('/code/php/round-update-script.php', {
				method: 'POST',
				body: formData
			})
			.then(response => {
				if (!response.ok){
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then(data => {
				console.log("Round update response:", data);
				if (data.error == "Game not found"){
					console.log('looks like the game is over...');
					postGameEndResults();
					
					stopPollingRoundUpdate();
				}
				ig.game.lastRoundMoveSummaries = data.simpleMoveSummaries;
				
				ig.game.opponentCharactersDeployedLastRound = ig.game.playerNumber == "p1" ? ig.game.p1ChDeployed : ig.game.p2ChDeployed;
				
				updateDeploymentValues(data); //Change value of hasDeployed to true for all deployed characters
				var opponentCharactersDeployedThisRound = ig.game.playerNumber == "p1" ? ig.game.p1ChDeployed : ig.game.p2ChDeployed;
				
				var eNum = ig.game.playerNumber == "p1" ? 2 : 1;
				
				if (opponentCharactersDeployedThisRound > ig.game.opponentCharactersDeployedLastRound ){
					var deploymentTile = ig.game.playerNumber == "p1" ? 8 : 4;
					var tileName = "tn" + deploymentTile;
					var tile = ig.game.getEntityByName(tileName);
					//Spawn a move attempt on deployment tile...
					ig.game.thePieceWasSpawned = false;
					ig.game.spawnEntity( EntityMoveattempt, tile.pos.x, tile.pos.y, {trMA: true, playerNum: eNum, myTile: deploymentTile, characterNum: ig.game.lastDeployedOpponentCharNum });	
				}
				
				checkForGameEnd();
				
				ig.game.lastRoundActionSummaries = data.simpleActionSummaries;
				ig.game.lastRoundActionSummariesLong = data.actionSummaries;

				if (data.success){
					stopPollingRoundUpdate();
					beginTurn(1);
					updateActionReporting(data);
				}
				else {
					console.error('Error updating round:', data.error);
				}
			})
			.catch(error => {
				//Game is probably over...
				checkForGameEnd();
			});
		}
		function checkForGameEnd(){
			if (!ig.game.playerWon){
				var p1Characters = ['p1C1data', 'p1C2data', 'p1C3data'];
				var p2Characters = ['p2C1data', 'p2C2data', 'p2C3data'];

				var p1AllDead = p1Characters.every(identifier => {
					var character = ig.game[identifier];
					return character && (parseInt(character.health) <= 0 || parseInt(character.location) === 86);
				});

				var p2AllDead = p2Characters.every(identifier => {
					var character = ig.game[identifier];
					return character && (parseInt(character.health) <= 0 || parseInt(character.location) === 86);
				});

				//Determine the winner or if the game continues
				if (p1AllDead || p2AllDead) {
					//Assign the winner based on who is not all dead
					ig.game.playerWon = p1AllDead ? 2 : 1; // if p1AllDead is true, player 2 wins, otherwise player 1 wins
					console.log('player has won: ' + ig.game.playerWon)
					postGameEndResults(); 
					return true;
				}
				else{
					//No winner yet, game continues
					ig.game.playerWon = false;
					return false;
				}
			}
		}
		function postGameEndResults(){
			fetch('/code/php/game-over.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: `game_id=${ig.game.currentGameId}`
			})
			.then(response => response.json())
			.then(data => {
				if (data.message == 'Game already concluded' || data.message == 'Game concluded and rewards updated'){
					ig.game.readyToEnd = true;
					stopPollingRoundUpdate();
					stopPollingRoundStatus();
					console.log(data.message);
					stopPollingRoundUpdate();
					checkForGameEnd();
				}
			})
			.catch(error => {
				ig.game.readyToEnd = true;				
			});
		}
		function getLocationByCharacterId(characterId){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

			for (const identifier of characterIdentifiers){
				if (ig.game[identifier] && ig.game[identifier].character_id == characterId){
					return ig.game[identifier].location;
				}
			}
			return null; 
		}
		function getHealthByCharacterId(characterId){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

			for (const identifier of characterIdentifiers){
				if (ig.game[identifier] && ig.game[identifier].character_id == characterId){
					return ig.game[identifier].health;
				}
			}
			return null; 
		}
		function applyDamageToCharacterById(characterId, damage){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

			for (const identifier of characterIdentifiers){
				if (ig.game[identifier] && ig.game[identifier].character_id == characterId){
					ig.game[identifier].health -= damage;

					//Ensure health does not drop below 0
					if (ig.game[identifier].health <= 0){
						ig.game[identifier].health = 0;
						if (ig.game.getEntityByName(`ch${characterId}`)){
							var Char = ig.game.getEntityByName(`ch${characterId}`);
							Char.killMe();
						}
					}

					return ig.game[identifier].health;
				}
			}
			return null;
		}
		function returnOnBoardCharacters() {
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];
			let onBoardCharactersCount = 0;

			for (const identifier of characterIdentifiers) {
				if (ig.game[identifier] && ig.game[identifier].location != 86 && ig.game[identifier].location != 0) {
					onBoardCharactersCount++;
				}
			}
			return onBoardCharactersCount;
		}
		function makeTheActionStatement(actorName, actionName, targetName, trait, effect, result, actionType, targetID, actorID){
			console.log(`1: actorName:${actorName}, actionName: ${actionName}, targetName: ${targetName}, trait: ${trait}, effect: ${effect}, result: ${result}, actionType: ${actionType}, targetID: ${targetID}, actorID: ${actorID}`)
			
			//1: actorName:Tukkuk Nanook, actionName: Spear Blizzard, targetName: Sir Nibblet Crossfield, trait: health, effect: 0, result: , actionType: Ranged, targetID: 3213, actorID: 3216
			ig.game.actionReportCount++;
			var actionStatement = "";
			var actionStatement1 = `${actorName} used ${actionName}`;
			var actionStatementMissed1 = `${actorName} attempted to use ${actionName}`;
			var targetStatement1 = `on ${targetName}`;
			var wasWounded = "was wounded";
			var targetHealth = getHealthByCharacterId(targetID);
			
			var actorAlreadyDead = "was dead and could not take action";
			var targetKilled = "was killed";
			var targetAlreadyDead = "was already dead";
			
			var actorLoc = getLocationByCharacterId(actorID);
			var targetLoc = getLocationByCharacterId(targetID);
			ig.game.clearTileColors();
			var piece = false;
			var piecePosX = false;
			var piecePosY = false;
			//var myTargetID = targetID.trim();
			var targetVarName = "ch" + targetID;
			targetVarName = targetVarName.replace(/\s/g, "");
			targetNameStr = targetVarName.toString();

			if (ig.game.getEntityByName(targetNameStr)){
				piece = ig.game.getEntityByName(targetNameStr);
				piecePosX = piece.pos.x;
				piecePosY = piece.pos.y;
			}
			
			var statusNumName = `sn${actorID}on${targetID}`;
			
			if (actionType == "Ranged" && parseInt(effect) > 0 && targetID || actionType == "Melee" && parseInt(effect) > 0 && targetID){
				applyDamageToCharacterById(targetID, effect);
			}
			
			if (result == wasWounded){
				if (actionType == "Melee"){
					ig.game.playMeleeHitSound();
					ig.game.colorTile(actorLoc, 'actor');
					ig.game.colorTile(targetLoc, 'target');
				}
				else if (actionType == "Ranged"){
					ig.game.playRangedHitSound();
					ig.game.colorTile(actorLoc, 'actor');
					ig.game.colorTile(targetLoc, 'target');
				}
			}
			if (result == actorAlreadyDead){
				//Character attempted to use attack but was killed in action.
				actionStatement = actionStatementMissed1 + " but was killed in action.";
				ig.game.playDeadGuyActingSound();
				ig.game.colorTile(actorLoc, 'target');
			}
			else if (result == targetAlreadyDead){
				//Character used attack was but was already dead.
				actionStatement = `${actionStatementMissed1} but ${targetName} was already dead.`;
				ig.game.playMissSound();
				ig.game.colorTile(actorLoc, 'actor');
				ig.game.colorTile(targetLoc, 'target');
			}
			//actionStatment: Clyde Derringer, Quick Draw, Edmund Arrowfly, health, 9, was killed, Ranged,  3055, 3059

			else if (result == targetKilled){
				//Character used attack on dead character.
				actionStatement = `${actionStatement1} caused ${effect} damage and killed ${targetName}.`;
				ig.game.playKillCharacterSound();
				ig.game.colorTile(actorLoc, 'actor');
				ig.game.colorTile(targetLoc, 'target');
				effect = "Killed";
				if (targetID){
					if (ig.game.getEntityByName(targetNameStr)){
						var Char = ig.game.getEntityByName(targetNameStr);
						Char.killMe();
					}
				}
			}
			
			else{
				
				var theVerb = actionType == "Boost" || actionType == "Support" || actionType == "R Support" ? "increased" : "decreased";
				
				
				if (parseInt(effect) > 0){
					//Actor used action on target
					actionStatement = `${actionStatement1} ${targetStatement1} and ${theVerb} ${trait} by ${effect} points.`;
					if (actionType == "Boost" || actionType == "Support" || actionType == "R Support" ){
						ig.game.playSupportHitSound();
						ig.game.colorTile(actorLoc, 'actor');
						ig.game.colorTile(targetLoc, 'sTarget');
					}
					else if (actionType == "Melee"){
						ig.game.playMeleeHitSound();
						ig.game.colorTile(actorLoc, 'actor');
						ig.game.colorTile(targetLoc, 'target');
					}
					else if (actionType == "Ranged"){
						ig.game.playRangedHitSound();
						ig.game.colorTile(actorLoc, 'actor');
						ig.game.colorTile(targetLoc, 'target');
					}
					else if (actionType == "Drain" || actionType == "R Drain" ){
						ig.game.playDrainHitSound();
						ig.game.colorTile(actorLoc, 'actor');
						ig.game.colorTile(targetLoc, 'target');
					}
					
				}
				else{
					actionStatement = `${actionStatementMissed1} ${targetStatement1} but missed.`
					ig.game.playMissSound();
					ig.game.colorTile(actorLoc, 'actor');
					ig.game.colorTile(targetLoc, 'missed');
				}
			}
			//Try to kill again:
			if (targetID){
				var targetHealth = getHealthByCharacterId(targetID);
				//Kill Target Who Has No Health
				if (ig.game.getEntityByName(targetNameStr) && parseInt(targetHealth) <= 0){
					var Char = ig.game.getEntityByName(targetNameStr);
					Char.killMe();
				}
			}
			
			var statusNumName = `sn${actorID}on${targetID}`;
						
			if (piecePosX && piecePosY){
				ig.game.spawnEntity( EntityStatuseffectnumbers, piecePosX, piecePosY - 35, {name: statusNumName,theActionType: actionType, theEffect: effect, theTrait: trait, characterID: targetID, myPiecePosX: piecePosX, myPiecePosY: piecePosY });
			}

			return actionStatement;
		}
		
		function updateActionReporting(data){
			var onBoardChars= returnOnBoardCharacters();
					
			if (onBoardChars < 3){
				ig.game.timeLeftInTurn.set(64.5);
				ig.game.turnReportingTime.set(4);
			}
			else if (onBoardChars < 4){
				ig.game.timeLeftInTurn.set(66.5);
				ig.game.turnReportingTime.set(6);
			}
			else if (onBoardChars < 5){
				ig.game.timeLeftInTurn.set(68.5);
				ig.game.turnReportingTime.set(8);
			}
			else{
				ig.game.timeLeftInTurn.set(70.5);
				ig.game.turnReportingTime.set(10);
			} 

			
			ig.game.actionReportCount = 0;
			//Clear old values
			for (let i = 1; i <= 6; i++){
				ig.game[`moveReportingTxt${i}`] = " ";
			}

			if (data.success && data.simpleActionSummaries.length > 0){
				//Handle the first action immediately
				if (data.simpleActionSummaries.length > 0){
					var theStatement = makeTheActionStatement(data.actionSummaries[0].actorName, data.actionSummaries[0].actionName, data.actionSummaries[0].targetName, data.actionSummaries[0].trait, data.simpleActionSummaries[0].effect, data.actionSummaries[0].result, data.actionSummaries[0].actionType, data.simpleActionSummaries[0].targetId, data.simpleActionSummaries[0].actorId);
					console.log(theStatement);
					ig.game.centerCameraOnCharacterByID(data.simpleActionSummaries[0].actorId); //Center Camera on Actor
					ig.game.moveReportingTxt1 = theStatement;
					ig.game.lastMRTXT1 = theStatement;
					
					
				}

				//Calculate the interval for the remaining statements
				var intBase = 5000;
				if (data.simpleActionSummaries.length < 3){
					intBase = 2000;
				}
				const interval = intBase / data.simpleActionSummaries.length;
				let index = 1; //Start from the second statement

				const intervalId = setInterval(() => {
					if (!ig.game.turnReporting){
						clearInterval(intervalId);
						updateMoveReporting(data);
						checkForGameEnd();
					}
					else if (index < data.simpleActionSummaries.length){
	
						var theStatement =  makeTheActionStatement(`${data.actionSummaries[index].actorName}`, `${data.actionSummaries[index].actionName}`, `${data.actionSummaries[index].targetName}`, `${data.actionSummaries[index].trait}`, `${data.simpleActionSummaries[index].effect}`,`${data.actionSummaries[index].result}`, `${data.actionSummaries[index].actionType}`, ` ${data.simpleActionSummaries[index].targetId}`, `${data.simpleActionSummaries[index].actorId}`);
						
						ig.game.centerCameraOnCharacterByID(`${data.simpleActionSummaries[index].actorId}`); //Center Camera on Actor
						ig.game[`moveReportingTxt${index + 1}`] = theStatement;
						ig.game[`lastMRTXT${index + 1}`] = theStatement;

						index++;
					}
					
					else{
						clearInterval(intervalId);
						updateMoveReporting(data);
						checkForGameEnd();
					}
				}, interval);
			}
			else{
				updateMoveReporting(data);
			}
		}
		function updateMoveReporting(data){
			
			//Clear Tile Colors			
			ig.game.clearTileColors();
			
			//Clear old values
			ig.game.moveReportingTxt1 = " ";
			ig.game.moveReportingTxt2 = " ";
			ig.game.moveReportingTxt3 = " ";
			ig.game.moveReportingTxt4 = " ";
			ig.game.moveReportingTxt5 = " ";
			ig.game.moveReportingTxt6 = " ";

			if (data.success && data.moveSummaries.length > 0){

				data.moveSummaries = data.moveSummaries.filter(summary => !summary.endsWith("square 0"));
				data.moveSummaries = data.moveSummaries.filter(summary => !summary.endsWith("square 86"));

				//Filter out bad data
				data.simpleMoveSummaries = data.simpleMoveSummaries.filter(item => item.location != 0);
				data.simpleMoveSummaries = data.simpleMoveSummaries.filter(item => item.location != 86);
				
				//Add the first statement immediately
				ig.game.moveReportingTxt1 = data.moveSummaries[0] + ".";
				ig.game.moveReportingTxt1 = changeReportWording(ig.game.moveReportingTxt1, ig.game.lastMRTXT1);
				ig.game.lastMRTXT1 = ig.game.moveReportingTxt1;
				var stayedAtSquareRegex = /stayed at square \d+$/;
				if (stayedAtSquareRegex.test(ig.game.moveReportingTxt1)){
					//Do Nothing
				}
				else{
					ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[0].characterId); 
				}
				
				
				makeTheMove(data.simpleMoveSummaries[0]);
				ig.game.clearTileColors();
				ig.game.colorTile(data.simpleMoveSummaries[0].location, 'actor');
				

				if (data.moveSummaries.length > 1){
					//Calculate the interval for the remaining statements
					var intBase = 5000;
					if (data.moveSummaries.length < 3){
						intBase = 2000;
					}
					const interval = intBase / (data.moveSummaries.length);
					let index = 1; //Start from the second statement

					const intervalId = setInterval(() => {
						if (index < data.moveSummaries.length){
							if (index == 1){
								ig.game.moveReportingTxt2 = data.moveSummaries[1] + ".";
								ig.game.moveReportingTxt2 = changeReportWording(ig.game.moveReportingTxt2, ig.game.lastMRTXT2);
								makeTheMove(data.simpleMoveSummaries[1]);
								ig.game.clearTileColors();
								ig.game.colorTile(data.simpleMoveSummaries[1].location, 'actor');
								if (stayedAtSquareRegex.test(ig.game.moveReportingTxt2)){
									//Do Nothing
								}
								else{
									ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[1].characterId); 
								}
								ig.game.lastMRTXT2 = ig.game.moveReportingTxt2;
							}
							else if (index == 2){
								ig.game.moveReportingTxt3 = data.moveSummaries[2] + ".";
								ig.game.moveReportingTxt3 = changeReportWording(ig.game.moveReportingTxt3, ig.game.lastMRTXT3);
								makeTheMove(data.simpleMoveSummaries[2]);
								ig.game.clearTileColors();
								ig.game.colorTile(data.simpleMoveSummaries[2].location, 'actor');
								if (stayedAtSquareRegex.test(ig.game.moveReportingTxt3)){
									//Do Nothing
								}
								else{
									ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[2].characterId); 
								}
								ig.game.lastMRTXT3 = ig.game.moveReportingTxt3;
							}
							else if (index == 3){
								ig.game.moveReportingTxt4 = data.moveSummaries[3] + ".";
								ig.game.moveReportingTxt4 = changeReportWording(ig.game.moveReportingTxt4, ig.game.lastMRTXT4);
								makeTheMove(data.simpleMoveSummaries[3]);
								ig.game.clearTileColors();
								ig.game.colorTile(data.simpleMoveSummaries[3].location, 'actor');
								if (stayedAtSquareRegex.test(ig.game.moveReportingTxt4)){
									//Do Nothing
								}
								else{
									ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[3].characterId); 
								}
								ig.game.lastMRTXT4 = ig.game.moveReportingTxt4;
							}
							else if (index == 4){
								ig.game.moveReportingTxt5 = data.moveSummaries[4] + ".";
								ig.game.moveReportingTxt5 = changeReportWording(ig.game.moveReportingTxt5, ig.game.lastMRTXT5);
								makeTheMove(data.simpleMoveSummaries[4]);
								ig.game.clearTileColors();
								ig.game.colorTile(data.simpleMoveSummaries[4].location, 'actor');
								if (stayedAtSquareRegex.test(ig.game.moveReportingTxt5)){
									//Do Nothing
								}
								else{
									ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[4].characterId); 
								}
								ig.game.lastMRTXT5 = ig.game.moveReportingTxt5;
							}
							else if (index == 5){
								ig.game.moveReportingTxt6 = data.moveSummaries[5] + ".";
								ig.game.moveReportingTxt6 = changeReportWording(ig.game.moveReportingTxt6, ig.game.lastMRTXT6);
								makeTheMove(data.simpleMoveSummaries[5]);
								ig.game.clearTileColors();
								ig.game.colorTile(data.simpleMoveSummaries[5].location, 'actor');
								if (stayedAtSquareRegex.test(ig.game.moveReportingTxt6)){
									//Do Nothing
								}
								else{
									ig.game.centerCameraOnCharacterByID(data.simpleMoveSummaries[5].characterId); 
								}
								ig.game.lastMRTXT6 = ig.game.moveReportingTxt6;
							}
						}
						index++;

						//Clear interval when all statements are added
						if (index >= data.moveSummaries.length || !ig.game.turnReporting){
							clearInterval(intervalId);
							ig.game.clearTileColors();
						}
					}, interval);
				}
			}
		}
		function changeReportWording(newStr, oldStr){
			var maybeChngStr = newStr
				if (newStr == oldStr){
					maybeChngStr = newStr.replace("moved to", "stayed at");
				}
			return maybeChngStr;
		}
		
		function clearPlayerActions(){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

			for (let i = 0; i < characterIdentifiers.length; i++){
				const characterIdentifier = characterIdentifiers[i];
		
				if (ig.game[characterIdentifier]){
					ig.game[characterIdentifier].action = false;
					ig.game[characterIdentifier].target = false;
				}
			}
		}
		
		function makeTheMove(data){

			//Function to get character data based on characterKey
			function getCharacterDataByKey(characterKey){
				const characterIdentifiers = ['p1C1', 'p1C2', 'p1C3', 'p2C1', 'p2C2', 'p2C3'];
				for (let id of characterIdentifiers){
					charNum = id.slice(-1) //Extract the last character (number)
					if (ig.game[`${id}data`].character_id == characterKey){
						return ig.game[`${id}data`];
					}
				}
				return null;
			}

			const characterData = getCharacterDataByKey(data.characterId);
			if (characterData){
				
				var pNum = characterData.player == "p1" ? 1: 2;
				var charEntName = `ch${data.characterId}`;
				var newLocation = `${data.location}`;

				if (parseInt(characterData.location) == 0 && parseInt(newLocation) != 0 && parseInt(characterData.health) > 0){
					var tileName = "tn"+ data.location;
					var tile = ig.game.getEntityByName(tileName);					
					ig.game.playDeployCharacterSound();
					tile.occupiedBy = charEntName;
					ig.game.thePieceWasSpawned = true;
					ig.game.spawnEntity( EntityCharacterpiece, tile.pos.x, tile.pos.y, { player: pNum, characterNum: charNum, myTile: data.location ,otherPlayer: true, name: charEntName});
				}
				//Move the character who is on the board
				else if (parseInt(newLocation) != 0 && parseInt(newLocation) != 86){
					var tileName = "tn"+ data.location;
					var tile = ig.game.getEntityByName(tileName);
					
					if (ig.game.getEntityByName(charEntName)){
						tile.occupiedBy = charEntName;
						var characterToMove = ig.game.getEntityByName(charEntName);
						characterToMove.myTile = data.location;
						characterToMove.pos.x = tile.pos.x;
						characterToMove.pos.y = tile.pos.y;
					}
					else if (parseInt(characterData.health) > 0 && parseInt(characterData.location) != 86){
						var tileName = "tn"+ data.location;
						var tile = ig.game.getEntityByName(tileName);
						tile.occupiedBy = charEntName;				
						ig.game.playDeployCharacterSound();
						ig.game.thePieceWasSpawned = true;
						ig.game.spawnEntity( EntityCharacterpiece, tile.pos.x, tile.pos.y, { player: pNum, characterNum: charNum, myTile: data.location ,otherPlayer: true, name: charEntName});
					}
				}
			} 
			else {
				console.log("Character not found for the given key.");
			}
		}

		async function structureDeckInGame(deck){
			const payload = {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify({ deck: deck })
			};
			
			let response = await fetch('/code/php/structure-deck.php', payload);
	
			let data = await response.json();
	
			return data;
		}
		async function updateDeckInSession(deck){
		
			const deckData = JSON.stringify({ deck: deck });
			
			try {
			
				const response = await fetch('/code/php/update-deck-in-session.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: deckData
				});

				const result = await response.json();

				if (result.success){
					console.log('Deck updated successfully in session!');
				}
				else{
					console.error('Failed to update deck in session:', result.message);
				}
			} 
			catch (err){
				console.error('Error while updating deck in session:', err);
			}
		}
		function start2pGame(){
			//Start polling
			gameStatusInterval = setInterval(() => checkGameStatus(ig.game.currentGameId), 1100);
			loadDeckImages(ig.game.currentGameId);
			ig.game.timeLeftInTurn.set(60);
			
			ig.game.menuScreen = false;
			ig.game.gameActive = true;
			ig.game.spawnEntity( EntityCharactercard, 0, 0);
			ig.game.spawnEntity( EntityEndturn, 0, 0);
			ig.game.playMusicBro(2);
			getCharactersAtGameStart();
			ig.game.roundNumber = 1;
			ig.game.active = true;
			ig.game.p1ChDeployed = 0;
			ig.game.p2ChDeployed = 0;
			
		}
		function playCPU(){
			/*
			ig.game.menuScreen = false;
			ig.game.gameActive = true;
			ig.game.spawnEntity( EntityCharactercard, 0, 0);
			ig.game.spawnEntity( EntityEndturn, 0, 0);
			ig.game.playMusicBro(2);
			getCharactersAtGameStart();
			ig.game.roundNumber = 1;
			ig.game.p1ChDeployed = 0;
			ig.game.p2ChDeployed = 0;
			*/
			var msg = `Solo game under construction...`;
			ig.game.spawnAlertBox(msg, 5, 99998);
		}
		function endGame(){
			clearInterval(pollCheckID);
		}
		function endTurn(where){
			if (ig.game.gameActive){
				submitPlayerMoves();
				closeAllOpenWindows();
				ig.game.turnEndedScreen = 1;
				ig.game.clearTileColors(); 
				ig.game.gameActive = false;
				ig.game.turnEnded = true;
			}
		}
		function beginTurn(where){
			ig.game.clearTileColors();
			if (ig.game.turnEnded){
				checkForGameEnd();
				ig.game.turnEnded = false;
				ig.game.timeLeftInTurn.set(70);
				ig.game.turnReportingTime.set(10);
				ig.game.turnReporting = true;
				ig.game.newRoundEntities();
				ig.game.tryingToPlayCharacterNumber = this.num;
				ig.game.openButtonMenu = false;
				ig.game.openButtonMenuDisplay = false;
				ig.game.gData.turnNumber++;
				ig.game.pieceSelected = false;
				ig.game.lookingAtMyCharacter = false;
				ig.game.actionCall = false;
				ig.game.characterDeployed = false;
				ig.game.gameActive = true;
				startPollingRoundStatus();
				ig.game.turnEndScreenStatusTxt = "Waiting for Other Player...";
				updateCharacterPositionsFromSummaries();
				clearPlayerActions();
				ig.game.initialOccupiedTiles = ig.game.determineInitialPlayerOccupiedTiles();
			}
		}

		function endTurnReporting(){
			ig.game.turnReporting = false;
			ig.game.timeLeftInTurn.set(60);
			ig.game.gameActive = true;
			ig.game.clearTileColors();
			synchronizeCharacters();
		}

		function closeAllOpenWindows(){
			ig.game.alertBox = false;
			ig.game.confirmBox = false;
			ig.game.openButtonMenu = false;
			ig.game.openButtonMenuDisplay = false;
			ig.game.displayCard = false;
			ig.game.displayCardView = 1;
		}
		

		function initializeGameMoves(){
			const gameMoves = {
				gameId: ig.game.currentGameId, 
				player: ig.game.playerNumber, 
				moves: []
			};

			for (let i = 1; i <= 3; i++){
				const characterData = ig.game[`${ig.game.playerNumber}C${i}data`];
				if (characterData){
					
					var actionNumber = false;
					
					
					const move = {
						characterId: characterData.card_id,
						characterKey: characterData.character_id,
						location: parseInt(characterData.location, 10),
						action: characterData.action,
						target: characterData.target,
					};
					gameMoves.moves.push(move);
				}
			}
			return gameMoves;
		}
		//First player ends turn which submits there moves. This calls the process-moves script.
		function submitPlayerMoves(){
			const gameMoves = initializeGameMoves();
			fetch('/code/php/process-moves.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `game_id=${gameMoves.gameId}&player_moves=${JSON.stringify(gameMoves.moves)}`
			})
			.then(response => response.json())
			.then(data => {
				if (data.success){
					if (!pollCheckID){
						pollCheckID = setInterval(makeSureWePollForRoundEnd, 7000); 
					}
				}
				else{
					checkForGameEnd();
					console.log('Game over...');
				}
			})
			.catch(error => {
				console.error('Network or other error:', error);
			});
		}
		async function synchronizeCharacters(){
			try {
				const response = await fetch('/code/php/fetch-character-data.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: `game_id=${ig.game.currentGameId}`
				});

				const data = await response.json();

				if (data.success && Array.isArray(data.characters)){
					updateCharacterDataIG(data.characters);
				}
				else {
					console.error("Error synchronizing characters:", data.error);
				}
			}
			catch (error){
				console.error("Error in synchronizeCharacters:", error);
			}
		}
		function updateCharacterDataIG(fetchedCharacters){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

			fetchedCharacters.forEach(fetchedCharacter => {
				//Find the matching character in ig.game using character_id
				const matchingIdentifier = characterIdentifiers.find(identifier => 
					ig.game[identifier] && ig.game[identifier].character_id == fetchedCharacter.character_id
				);

				//Update ig.game object if a match is found
				if (matchingIdentifier){
					Object.keys(fetchedCharacter).forEach(key => {
						ig.game[matchingIdentifier][key] = fetchedCharacter[key];
					});

					//Additionally, find the corresponding game entity and update its properties
					const theChar = ig.game.getEntityByName(`ch${ig.game[matchingIdentifier].character_id}`);
					if (theChar){
						theChar.myTile = parseInt(ig.game[matchingIdentifier].location);
					}
				}
			});
		}
			
		function updateCharacterPositionsFromSummaries(){
			//Iterate through the last round move summaries
			ig.game.lastRoundMoveSummaries.forEach(summary => {
				//Check each character for a match and update data and entity
				updateCharacterIfMatch(summary, ig.game.p1C1data);
				updateCharacterIfMatch(summary, ig.game.p1C2data);
				updateCharacterIfMatch(summary, ig.game.p1C3data);
				updateCharacterIfMatch(summary, ig.game.p2C1data);
				updateCharacterIfMatch(summary, ig.game.p2C2data);
				updateCharacterIfMatch(summary, ig.game.p2C3data);
			});
		}
		//Updates Locations
		function updateCharacterIfMatch(summary, characterData){
			if (!characterData){
				return;
			}
			if (summary.characterId == characterData.character_id && characterData.location != 86){
				//Update location data
				characterData.location = summary.location;

				//Update the entity's myTile property
				const characterEntity = ig.game.getEntityByName(`ch${characterData.character_id}`);
				if (characterEntity){
					characterData.myLastTile = summary.location;
					characterEntity.myTile = summary.location;
				}
			}
		}
		let initialCharacterLocations = {};

		function initalizeCharacterLocations(){ //Called in matchmaking.js
			characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];
			
			characterIdentifiers.forEach(identifier => {
				if (ig.game[identifier]){
					initialCharacterLocations[identifier] = {
						location: ig.game[identifier].location,
						characterId: ig.game[identifier].character_id,
					};
					ig.game[identifier].hasDeployed = false;
					ig.game[identifier].startingHealth = ig.game[identifier].health;
				}
			});
		}
		
		function updateDeploymentValues(data){
			const characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];
			
			
			
			characterIdentifiers.forEach(identifier => {
				//Ensure the character data exists
				if (ig.game[identifier]){
					let characterId = ig.game[identifier].character_id;
					
					//Filter out cards still (in hand from movement data)
					data.simpleMoveSummaries = data.simpleMoveSummaries.filter(item => item.location != 0);
					
					//Check if this characterId is in the data (simpleMoveSummaries)
					var characterInMoveSummary = data.simpleMoveSummaries.find(summary => summary.characterId == characterId);

					if (characterInMoveSummary && !ig.game[identifier].hasDeployed){
						//If found, update the hasDeployed property to true
						ig.game[identifier].hasDeployed = true;
						var charNumMatch = identifier.match(/C(\d+)data/);
						
						//Increment the respective deployment counter
						if (identifier.startsWith('p1')){
							ig.game.p1ChDeployed++;
							if (ig.game.playerNumber == "p2"){
								ig.game.lastDeployedOpponentCharNum = parseInt(charNumMatch[1], 10);
							}
						}
						else if (identifier.startsWith('p2')){
							ig.game.p2ChDeployed++;
							if (ig.game.playerNumber == "p1"){
								ig.game.lastDeployedOpponentCharNum = parseInt(charNumMatch[1], 10);
							}
						}
						
					}
				}
			});
		}
		
		function returnGameScale(){
			var width = window.innerWidth;
			var height = window.innerHeight;
			var scale = 1;
			
			//Mobile Phones in Landscape
			if (height < 450){
				scale = 1.5;
				//console.log('scale 1');
			}
			else if (width > 1920){
				scale = .75;
				//console.log('scale 2');
			}
			else if (width > 1536){
				scale = .85;
				//console.log('scale 3');
			}
			else if (width > 1440){
				scale = 1.2;
				//console.log('scale 4');
			}
			else if (width > 1366){
				scale = 1.15;
				//console.log('scale 5');
			}
			else if (width > 1280){
				scale = 1.1;
				//console.log('scale 6');
			}
			else if (width > 1000){
				scale = 1;
				//console.log('scale 7');
			}
			else if (width > 844){
				scale = .8;
				//console.log('scale 8');
			}
			else if (width > 450){
				scale = .75;
				//console.log('scale 9');
			}
			else{
				scale = .65;
				//console.log('scale 10');
			}
			if( ig.ua.mobile && width < 500 || ig.ua.mobile && height < 500 ){
				scale = 1.75;
				//console.log('scale 11 MOBILE');
			}
			
			return scale;
		}
	</script>