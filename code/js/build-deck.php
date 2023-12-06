<script>
		var selectedCardID = null; //This is the actual ID
		var lastCardSelectedID = null; //This is for the link on the menu list
		var lastCardSelectedName = null;
		
		var selCardId = null;
		var selCardDir = null;
		var selCardName = null;
		var selCardType = null;
		var viewCardInDeckID = null;
		
		var characterCardLimit = 3;
		var itemCardLimit = 7;
		var effectCardLimit = 7;
		var characterCardsSelected = 0;
		var itemCardsSelected = 0;
		var effectCardsSelected = 0;
		
		var workingOnDeckNum = 1;
		var newDeckMakerHTML = false;
		var savedCardHTML = '<div class="savedDeck" id="savedDeck"><h2 class="termH2">SAVED DECK <span id="savedDeckCountDisp">1</span></h2><div class="cards" id="saved-character-cards"><h3 class="termH3">Character Cards</h3><ul id="saved-character-cards-list" class="card-list"><li id="svdLic1">Card 1</li><li id="svdLic2">Card 2</li><li id="svdLic3">Card 3</li></ul></div></div>';
		var viewingSavedDeck = false;
		
		
		async function checkDeckBuilderMode(){ //Manages Terminal Interface
			var text = "";
			var query = false;
			
			if (window.ethereum){
				query = await checkWalletStatus();
				
			}
			var inputField = document.getElementById("t1in");
			
			if (deckBuilderMode){
				checkForSavedDecks(1);
				checkForSavedDecks(2);
				checkForSavedDecks(3);
				document.getElementById('deckBuilderField').value = deckBuilderMode;
				if (myWalletStatus == 0){
					text = "You must have a wallet to build a deck. Opening a third-party website where you can download a wallet..."
					setTimeout(openNewTab, 900, "https://metamask.io/download/");
				}
				else if (myWalletStatus == 1){
					text = "Your wallet must be connected to this site to build a deck. Attempting to connect to your wallet..."
					setTimeout(connectMyWallet, 500);
				}
				else if (myWalletStatus == 2){
					if (signedInToServerAs != "false"){
						text = "Initiating Deck Builder..."
						getUsersCards();
					}
					else{
						text = "You must sign in to this system to build a deck. Sign this transaction to log in..."
						setTimeout(getSignature, 500);
					}
				}
				//Print Response
				printTerminalHistory(text);
				//Clean Input
				inputField.value = "";
				focusOnTerminal();
				
			}
			else{
				document.getElementById('deckBuilderField').value = false;
			}
		}
		
		if (!inGame){
			checkDeckBuilderMode();
		}	
		
		function displayCard(directory){ //Displays a selected card
			document.getElementById("cardDisplayFrame").style.display = "flex";
			
			var dispEle = false;			
			dispEle = document.getElementById("cardDisplay");
			
			var selectedDir = directory;
			dispEle.innerHTML = `<a href="${selectedDir}card.jpg" target="_blank"><img src="${selectedDir}card.jpg"/></a>`;
			//"<a href='" + selectedDir + "card.jpg' target='_blank'><img src='" + selectedDir + "card.jpg'/></a>";
		}
		function describeCard(name, type, dir){ //Add information about selected card
			if (type == "character"){
				type = "Character Card";
			}
			var cdName = document.getElementById("cdName");
			cdName.innerHTML = name;
			var cdType = document.getElementById("cdType");
			cdType.innerHTML = type;
			var cdLink = document.getElementById("cdLink");
			cdLink.innerHTML = `<a href="${dir}card.jpg" target="_blank">View Card</a>`;
			//"<a href='"+ dir +"card.jpg' target='_blank'>View Card</a>";
		}

		function setCardChoices(deck){ //Iterates through the set-card-data.php (js) object to find PLAYER'S cards - creates links that use that data to select and display cards
			var characterCards = document.getElementById("character-cards-list"); 
			
			if (characterCards){
				characterCards.innerHTML = "";
				// Populate list with options:
				for(var i = 1; i < deck.length; i++) {
					var cardId = deck[i].Id;
					var cardName = deck[i].name;
					var cardDir = deck[i].dir;
					var cardQuan = deck[i].Quantity;
					var cardType = deck[i].type;
					var escapedCardName = cardName.replace(/'/g, "\\'");//Handle names with single quotes
					var myFunction = `selectCard('${cardId}', '${cardDir}', '${escapedCardName}', '${cardType}')`;
					characterCards.innerHTML += `<li id="li${cardId}"><a id="card${cardId}" class="cardPick" onclick="${myFunction}" >${cardName} (<span id="q${cardId}">${cardQuan}</span>)</a></li>`;
				}
			}
			else{
				//console.log('no chracter cards DIV');
			}
		}
		function selectCard(id, dir, name, type){ //View a card that is available to be selected 
			clearSelections();
			var eID = "card" + id;
			selectedCardID = id;
			var e = document.getElementById(eID);
			e.className = "cardPick cardPickSelected";
			if (eID != lastCardSelectedID){
				lastCardSelectedID = eID;
				lastCardSelectedName = name;
			}
			
			selCardId = id;
			selCardDir = dir;
			selCardName = name;
			selCardType = type;
			
			displayCard(dir);
			describeCard(name, type, dir);
			adjustButton(true, "addToDeck");
		}
		function viewCard(id, dir, name, type){ //View an already selected card that is in player's deck
			
			clearSelections();
			adjustButton(false, "addToDeck");
			var e = document.getElementById(id);
			e.className = "cardPick cardPickSelected";
			
			viewCardInDeckID = id;
			
			displayCard(dir);
			describeCard(name, type, dir);
		}
		function clearSelections(){//Removes highlighting from menu options
			if (lastCardSelectedID){
				var pe = document.getElementById(lastCardSelectedID);
				if (pe){
					pe.className = "cardPick";
				}
			}
			if (viewCardInDeckID){
				var vcid = document.getElementById(viewCardInDeckID);
				if (vcid){
					vcid.className = "cardPick";
				}
				viewCardInDeckID = false;
			}
		}
		async function addToDeck() {//moves cards from list of available cards to list of selected cards
			if (!viewCardInDeckID){
				//If we are at the limit, just return
				if (selCardType == "character" && characterCardsSelected == characterCardLimit){
					adjustButton(false, "addToDeck");
					alert('You already have the MAX amount of CHARACTER CARDS added to your deck.');
					return;
				}
				else if (selCardType == "character"){
					characterCardsSelected++;
					document.getElementById('ccSelectedH3').innerHTML = characterCardsSelected;
					adjustButton(true, "saveDeck");
				}
				
				mySelectedCards.push(selectedCardID)
				addToMenu();
				removeFromMenu();
				adjustButton(false, "addToDeck");
				adjustButton(true, "dumpDeck");
			}
		}
		function addToMenu(){ //Used in a loop to populate a list of card choices with an onclick function
			selectedCardNum++;
			var menuOfPicks = document.getElementById('character-cards-indeck');
			var idOfPick = 'cc' + selectedCardNum;
			var escapedCardName = selCardName.replace(/'/g, "\\'");//Handle names with single quotes
			var myFunction = `viewCard('${idOfPick}', '${selCardDir}', '${escapedCardName}', '${selCardType}')`;
			var newNode = `<li><a id="${idOfPick}" onclick="${myFunction}">${lastCardSelectedName}</a></li>`;

			
			if (selectedCardNum == 1){
				menuOfPicks.innerHTML = newNode;
			}
			else{
				menuOfPicks.innerHTML += newNode;
			}
		}
		function removeFromMenu(){//Remove a card from list of choices after it has been added
			var qID = 'q' + selectedCardID;
			var theLI = 'li' + selectedCardID;
			var amount = document.getElementById(qID).innerHTML;
			if (amount == 1){
				document.getElementById(theLI).remove();
			}
			else if (amount > 1){
				console.log('greater than one, subtract it');
				amount--;
				document.getElementById(qID).innerHTML = amount;
			}
		}
		
		async function checkForSavedDecks(which){

			document.getElementById("savedDeckNum").value = "deck1";
			
			if (which == 2){
				document.getElementById("savedDeckNum").value = "deck2";	
			}
			else if (which == 3){
				document.getElementById("savedDeckNum").value = "deck3";	
			}
			
			let query = await fetch('/code/php/retrieve-saved-decks.php', {method: "POST", body: new FormData(document.querySelector('form'))})
			if (!query.ok) {
				throw new Error(`HTTP error! Status: ${query.status}`);
			}
			let text = await query.text();  // first get it as text
			console.log(`text = ${text}`);
			if (text && text != "No signed in account...") {
				console.log(`text = ${text}`)
				let jsonData = JSON.parse(text);
							
				if (which == 1){
					mySavedDeck1 = await structureDeck(jsonData);
					mySavedDeck1 = setCardData(mySavedDeck1, 0);
					savedDeckCounter++;
				}
				else if (which == 2){
					mySavedDeck2 = await structureDeck(jsonData);
					mySavedDeck2 = setCardData(mySavedDeck2, 0);
					savedDeckCounter++;
				}
				else if (which == 3){
					mySavedDeck3 = await structureDeck(jsonData);
					mySavedDeck3 = setCardData(mySavedDeck3, 0);
					savedDeckCounter++;
				}
				selectedDeck = savedDeckCounter + 1;
				if (mySavedDeck1.length > 0 && mySavedDeck2.length > 0 && mySavedDeck3.length > 0){
					maxDecksNoNew()
				}
				document.getElementById('saved-decks').style.display = "block";
			}
			else{
				if (which == 1){
					displayNoneIt("sDeckBut1");
				}
				else if (which == 2){
					displayNoneIt("sDeckBut2");
				}
				else if (which == 3){
					displayNoneIt("sDeckBut3");
				}
			}
		}
		function printSavedDeckData(deck){
			var l = deck.length - 1;
			document.getElementById("saved-character-cards-list").innerHTML = "";
			
			for (let i = 1; i <= l; i++) {
				var myFunction = `viewCard('${deck[i].Id}', '${deck[i].dir}', '${deck[i].name}', '${deck[i].type}')`;
				var newNode = `<li><a id="${deck[i].Id}" onclick="${myFunction}">${deck[i].name}</a></li>`;
				document.getElementById("saved-character-cards-list").innerHTML +=  newNode;
			}
		}
		function selectDeck(which, eID){
			displayNoneIt("cardDisplayFrame"); //Hide Card Viewer because new deck is coming up
			deselectAllButtons(); //Change CSS Class of Button
			var e = document.getElementById(eID);
			e.className = "sDeckBut sDeckButSel"; //Add new class to selected button
			
			var mainDiv = document.getElementById('db-main-row');
			//Restore Regular MAKE NEW Deck Screen
			if (!newDeckMakerHTML){
				newDeckMakerHTML = mainDiv.innerHTML;
			}
			
			adjustButton(false, "saveDeck");
			adjustButton(false, "addToDeck");
			adjustButton(true, "dumpDeck");
			
			viewingSavedDeck = true;
			
			if (which == 1){
				mainDiv.innerHTML = savedCardHTML;
				printSavedDeckData(mySavedDeck1);
				selectedDeck = 1;
				document.getElementById('savedDeckCountDisp').innerHTML = "1";
			}
			else if (which == 2){
				mainDiv.innerHTML = savedCardHTML;
				printSavedDeckData(mySavedDeck2);
				adjustButton(true, "dumpDeck");
				selectedDeck = 2;
				document.getElementById('savedDeckCountDisp').innerHTML = "2";
			}
			else if (which == 3){
				mainDiv.innerHTML = savedCardHTML;
				printSavedDeckData(mySavedDeck3);
				adjustButton(true, "dumpDeck");
				selectedDeck = 3;
				document.getElementById('savedDeckCountDisp').innerHTML = "3";
			}
			else if (which == 4){
				viewingSavedDeck = false;
				mainDiv.innerHTML = newDeckMakerHTML;
				if (savedDeckCounter < 3){
					if (mySavedDeck1.length == 0){
						selectedDeck = 1;
					}
					else if (mySavedDeck2.length == 0){
						selectedDeck = 2;
					}
					else if (mySavedDeck3.length == 0){
						selectedDeck = 3;
					}
					else{
						alert("You already have 3 saved decks. You must dump one to make a new one.");
					}
				}
			}
			//console.log('I have ' + savedDeckCounter + ' saved decks and I am working on deck ' + selectedDeck);
		}
		function deselectAllButtons(){
			if (document.getElementById("sDeckBut1")){
				document.getElementById("sDeckBut1").className = "sDeckBut";
			}
			if (document.getElementById("sDeckBut2")){
				document.getElementById("sDeckBut2").className = "sDeckBut";
			}
			if (document.getElementById("sDeckBut3")){
				document.getElementById("sDeckBut3").className = "sDeckBut";
			}
			if (document.getElementById("sDeckBut4")){
				document.getElementById("sDeckBut4").className = "sDeckBut";
			}
		}
		function maxDecksNoNew(){
			displayNoneIt("sDeckBut4");
			selectDeck(1, "sDeckBut1");
		}
		function dumpDeck(){//Clear all selections and start over
			var resetHTML = "<li id='cc1'>You must ADD at least 1 CHARACTER CARD to make a DECK.</li>";
			myCards.length = 0
			mySelectedCards.length = 0;
			selectedCardNum = 0;
			characterCardsSelected = 0;
			itemCardsSelected = 0;
			effectCardsSelected = 0;
			if (!viewingSavedDeck){	
				document.getElementById('ccSelectedH3').innerHTML = characterCardsSelected;
				document.getElementById("character-cards-indeck").innerHTML = resetHTML;
			}
			else{
				//Delete a selected deck.
				//First Hide DECK button so it can't be selected anymore.
				var deckButID = "sDeckBut" + selectedDeck;
				displayNoneIt(deckButID);
				//Next erase array
				if (selectedDeck == 1){
					mySavedDeck1.length = 0;
				}
				else if (selectedDeck == 2){
					mySavedDeck2.length = 0;
				}
				else if (selectedDeck == 3){
					mySavedDeck3.length = 0;
				}
				savedDeckCounter--;
				//Then just change the display and which deck we are working on...
				var mainDiv = document.getElementById('db-main-row');
				mainDiv.innerHTML = newDeckMakerHTML;
			}
			document.getElementById("sDeckBut4").style.display = "flex";
			setCardChoices(userCards);
			//Activate Make New Deck Button
			document.getElementById("sDeckBut4").className =  "sDeckBut sDeckButSel";
			viewingSavedDeck = false;
			
			adjustButton(false, "addToDeck");
			adjustButton(false, "dumpDeck");
			adjustButton(false, "saveDeck");
		}
		async function saveDeck(){//Save the deck the player has created
			var saveThisDeck = "deck1";
			if (selectedDeck == 2){
				saveThisDeck = "deck2";
			}
			else if (selectedDeck == 3){
				saveThisDeck = "deck3";
			}
			document.getElementById("savedDeck").value = mySelectedCards;
			document.getElementById("savedDeckNum").value = saveThisDeck;
			let query = await fetch('/code/php/save-deck.php', {method: "POST", body: new FormData(document.querySelector('form'))})
			if (!query.ok) {
				throw new Error(`HTTP error! Status: ${query.status}`);
			}
			let text = await query.text();
			if (text){
				console.log('deck saved!');
				var savedDeckNum = selectedDeck;
				savedDeckCounter = 0;
				await checkForSavedDecks(1);
				await checkForSavedDecks(2);
				await checkForSavedDecks(3);
				var buttonID = "sDeckBut" + savedDeckNum;
				characterCardsSelected = 0;
				characterCardsSelected = 0;
				itemCardsSelected = 0;
				effectCardsSelected = 0;
				document.getElementById(buttonID).style.display = "flex";
				selectDeck(savedDeckNum, buttonID);
				setCardChoices(userCards);
				if (mySavedDeck1.length > 0 && mySavedDeck2.length > 0 && mySavedDeck3.length > 0){
					maxDecksNoNew()
				}
			}
		}
		async function structureDeck(deck){//Turn flat array of cards into a structured array.
			document.getElementById("savedDeck").value = JSON.stringify(deck);
			let query = await fetch('/code/php/structure-deck.php', {method: "POST", body: new FormData(document.querySelector('form'))})
			let text = await query.json();
			//console.log(text)
			return text;
		}
		
	</script>