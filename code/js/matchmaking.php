<script>
		var matchCheckInterval = false;
		var statusCheckIntervalID = false;
		var opponentID = false;
		var trimmedOpponentID = false;
		var gameIsSettingUp = false;
		var playerOne = false;
		var checkIfGameIsReadyInterval = false;
		var gameStatusInterval = false;

		function turnOnMatchChecks(){
			if (!lookingForMatch){
			//Start polling the server every 10 seconds for a match.
				matchCheckInterval = setInterval(checkForMatch, 9999);
				lookingForMatch = true;

				//Poll every 5 seconds to check match status.
				statusCheckIntervalID = setInterval(function(){
					checkMatchStatus();
				}, 4777);
			}
		}

		function checkForMatch(){
			fetch('/code/php/matchmaker.php')
				.then(response => {
					//Ensure the response is valid before proceeding
					if (!response.ok){
						throw new Error("Network response was not ok");
					}
					return response.json();
				})
				.then(data => {
					if (data.matched){
						playerOne = true;
						playerNum = 1;
						console.log(`Matched with: ${data.opponent}`);
						opponentID = data.opponent;
						trimmedOpponentID = opponentID.slice(-6);
						ig.game.currentGameId = data.game_id;				
						//Stop the interval once a match is found
						clearInterval(matchCheckInterval);
						clearInterval(statusCheckIntervalID);
						lookingForMatch = false;
						ig.game.opponentID = opponentID;
						ig.game.trimmedOpponentID = trimmedOpponentID;
						ig.game.startMatchMakingGame(false);
					}
					else{
						console.log("Waiting for match...");
						//Poll the waiting-room-query script to get the number of players in the waiting room
						fetch('/code/php/waiting-room-query.php')
							.then(response => response.json())
							.then(waitingPlayers => {
							//Display the number of players waiting for a match
							console.log(`Players waiting for a match: ${waitingPlayers.length}`);
							ig.game.playersWaitingNum = `${waitingPlayers.length}`;
						})
						.catch(error => {
							console.error("Error fetching players in the waiting room:", error);
						});
				}
			})
			.catch(error => {
				console.error("Error checking for match:", error);
			});
		}
		function addToWaitingRoom(){
			
			return fetch('/code/php/waiting-room-add-me.php')
				.then(response => {
					if (!response.ok){
						throw new Error("Network response was not ok");
					}
					//Success!
					ig.game.menuScreenNum = 2;
					ig.game.spawnEntity( EntityLeavemm, 0, 0);
					return response.text();
				})
				.then(result => {
					if (result == "Waiting"){
						console.log("Added to waiting room");
						//Start the matchmaking checks once the player is added to the waiting room
						turnOnMatchChecks();
					}
					else{
						console.error("Error:", result);
					}
				})
				.catch(error => {
					console.error("Error adding to waiting room:", error);
				});
		}
		//Exit Matchmaking if No Match Found
		function tryToLeaveWaitingRoom(){
			//Stop Match Checking...
			if (lookingForMatch){
				clearInterval(matchCheckInterval);
				clearInterval(statusCheckIntervalID);
				lookingForMatch = false;
				console.log("Exited matchmaking");
			}
			//Request the server to remove the player from the waiting room
			fetch('/code/php/leave-waiting-room.php')
				.then(response => response.json())
				.then(data => {
				if (data.success){
					ig.game.menuScreenNum = 1;
					console.log("Successfully exited the waiting room.");
				}
				else {
					console.error("Error exiting the waiting room:", data.message);
				}
			})
			.catch(error => {
				console.error("Error while sending leave request:", error);
			});
		}
		async function enterMatchmaking(){
			if (mySavedDeck1.length == 0 && mySavedDeck2.length == 0 && mySavedDeck3.length == 0){
				console.log('no saved deck!');

				let deckAssigned = await fetchRandomDeck();

				if (deckAssigned == "already has a deck"){
					//Player already has a deck, so just proceed
					addToWaitingRoom();
				}
				else if (deckAssigned){
					//New random deck was successfully assigned
					addToWaitingRoom();
				} 
				else{
					console.log("There was an issue assigning a random deck.");
				}
			}
			else{
				//Add to waiting room and look for matches...
				addToWaitingRoom();
			}
		}
		async function fetchRandomDeck(){
			try {
				let response = await fetch('/code/php/set-random-starter-deck.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				});

				let data = await response.json();

				if (data.success){
					console.log(data.message);
					return true;
				}
				else if (data.message == "Player already has a deck"){
					return "already has a deck";
				} 
				else{
					console.log(data.message);
					return false;
				}
			} 
			catch (error){
				console.error("There was an error fetching the random deck:", error);
				return false; //There was an error
			}
		}
		function checkMatchStatus(){
			fetch('/code/php/check-match-status.php')
				.then(response => {
					if (!response.ok){
						throw new Error("Network response was not ok");
					}
					return response.json();
				})
				.then(data => {
					if (data.matched){
						if (!playerOne){
							ig.game.currentGameId = data.game_id;
							opponentID = data.opponent;
							trimmedOpponentID = opponentID.slice(-6);
							//Stop the polling and proceed to start the game
							clearInterval(matchCheckInterval);
							clearInterval(statusCheckIntervalID);
							ig.game.opponentID = opponentID;
							ig.game.trimmedOpponentID = trimmedOpponentID
							ig.game.startMatchMakingGame(true);
						}
					}
				})
				.catch(error => {
					console.error("Error checking match status:", error);
				});
		}
		async function setUpGame(){
			if (gameIsSettingUp) return; //Ensures that this function is only called once

				var gameId = ig.game.currentGameId; 

				try {
					const response = await fetch('/code/php/start-game-setup.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						},
						body: `game_id=${gameId}`
					});
		
					const data = await response.json();

					if (data.success){
						gameIsSettingUp = true; //Set this flag to ensure the function isn't called again
					}
					else {
						console.error('Error starting game setup:', data.error);
					}
				}
				catch (error){
					console.error('Error in setUpGame:', error);
				}
				
		}
		function isGameReady(gameId){
			fetch('/code/php/is-game-ready.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: `game_id=${gameId}`
			})
				.then(response => response.json())
				.then(data => {
					if (data.success && data.status == 'READY' || data.success && data.status == 'IN_PROGRESS' && !ig.game.gameActive ){
						//Determine player number
						if (data.playerNumber){
							ig.game.playerNumber = data.playerNumber;
						}
						//Stop polling as the game is ready
						clearInterval(checkIfGameIsReadyInterval);
						//Proceed with the game
						if (ig.game.playerNumber == "p1"){
							setTimeout("start2pGame()", 3000);
						}
						else{
							start2pGame();
						}
					}
					else{
						console.log(data.message);
					}
			})
			.catch(error => {
				console.error('Error checking game status:', error);
			});
		}
		function checkGameStatus(gameId){
			fetch('/code/php/start-game.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: `game_id=${gameId}`
			})
			.then(response => response.json())
			.then(data => {
				if (data.success){
					console.log(data.message);
					clearInterval(gameStatusInterval);
					startPollingRoundStatus();
				}
				else {
					console.log(data.error || 'Checking game status...');
				}
			})
			.catch(error => {
				console.error('Error checking game status:', error);
			});
		}

		async function getCharactersAtGameStart(){

			try {
				const response = await fetch('/code/php/get-characters-at-game-start.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: `game_id=${ig.game.currentGameId}`
				});

				const data = await response.json();

				if (data.success){
					console.log("Characters Data:", JSON.stringify(data, null, 2));
					p1Characters = data.p1Characters;
					p2Characters = data.p2Characters;
					setCharacterDataIG();
					initalizeCharacterLocations();
				}
				else {
					console.error("Error fetching characters:", data.error);
				}
			}
			catch (error){
				console.error("Error in getCharactersAtGameStart:", error);
			}
		}
		function setCharacterDataIG(){
			//Assign player 1 characters
			if (Array.isArray(p1Characters)){
				p1Characters.forEach((character, index) => {
					let characterDataKey = `p1C${index + 1}data`;
					ig.game[characterDataKey] = character;
					//console.log(`${characterDataKey}:`, ig.game[characterDataKey]);
				});
			}

			//Assign player 2 characters
			if (Array.isArray(p2Characters)){
				p2Characters.forEach((character, index) => {
					let characterDataKey = `p2C${index + 1}data`;
					ig.game[characterDataKey] = character;
					//console.log(`${characterDataKey}:`, ig.game[characterDataKey]);
				});
			}
		}
		async function loadDeckImages(gameId){
			try {
				const response = await fetch('/code/php/get-card-images.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: `game_id=${gameId}`
				});

				const data = await response.json();

				if (data.success){
					data.p1DeckImages.forEach((imgUrl, index) => {
						ig.game['p1CardImage' + index] = new Image();
						ig.game['p1CardImage' + index].src = imgUrl;
					});

					data.p2DeckImages.forEach((imgUrl, index) => {
						ig.game['p2CardImage' + index] = new Image();
						ig.game['p2CardImage' + index].src = imgUrl;
					});
				}
				else {
					console.error("Error fetching deck images:", data.error);
				}
			}
			catch (error){
				console.error("Error loading deck images:", error);
			}
		}

	</script>