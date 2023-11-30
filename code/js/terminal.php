<script>
		var txt1 = 'Welcome to the Happy Fun Kill Club, Player!';
		var txt2 = 'We have collected DNA samples from the strongest warriors throughout the Metaverse. Here, they will do battle for your education and amusement.';
		var txt3 = ' ';
		var chars = 0;
		var lines = 0;
		var maxLines = 2;
		var speed = 8;
		var terUsrNm = "player1@hfkc";
		var termLbl = terUsrNm + " ~ %: ";
		var terminalSize = "regular";
		var buyCards = false;
		
		function setTerminalTxt(walletStatus){//Sets initial terminal text

			buyCards = document.getElementById("buyCardsField").value;
			
			console.log('skipPrompt = ' + skipPrompt);
			
			//Buy Cards Page
			if (buyCards){
				
				setTerminalUserName();
				
				focusOnTerminal();
				chars = 0;
				lines = 0;
				maxLines = 3;
				txt1 = 'WELCOME TO THE MARKETPLACE!';
				txt2 = 'All players receive a starter set of HFKC cards. Unlock HIDDEN CARDS by playing the game. Players can also purchase PACKS of UNCOMMON and RARE CARDS here.';
				txt3 = "Would you like to purchase PACKS of CARDS?";
				if (skipPrompt){
					document.getElementById("terminal-div").style.display = "none";
					document.getElementById("tos-purchase-div").style.display = "block";
					setcPanelButtons(2);
				}
			}
			else if (deckBuilderMode){
				setTerminalUserName();
				
				focusOnTerminal();
				chars = 0;
				lines = 0;
				maxLines = 3;
				txt1 = 'WELCOME TO THE DECK BUILDER!';
				txt2 = 'Here you can arrange your CARDS into DECKS so that you are prepared for battle.';
				txt3 = "Would you like to arrange your CARDS into a DECK?";

				if (skipPrompt){
					if (myWalletStatus == 2){
						if (signedInToServerAs != "false"){
							document.getElementById("terminal-div").style.display = "none";
							document.getElementById("deck-builder-div").style.display = "block";
							setcPanelButtons(2);
						}
					}
				}
			}
			//INDEX PAGE
			else{
				if (walletStatus == 0){
					maxLines = 3;
					txt3 = "You don't have a wallet on this system. Would you like to install one?";
				}
				else if (walletStatus == 1){
					maxLines = 3;
					txt3 = "Your wallet is not connected to this application. Would you like to connect now?";
				}
				else if (walletStatus == 2){
					
					setTerminalUserName();
					txt1 = 'Welcome to the Happy Fun Kill Club, Player ' + account + '!';
				
					if (signedInToServerAs != "false"){
						txt3 = "Your wallet is connected and you are signed into the system. Would you like to play a game?";
					}
					else{
						txt3 = "Your wallet is connected to this application but you must sign in to verify your account. Would you like to sign in now?";	
					}
				
					maxLines = 3;
				}
			}
			setcPanelButtons();
			
		}
		function typingEffect(txt, divID){//Loads text as though it has been typed
			if (chars < txt.length){
				document.getElementById(divID).innerHTML += txt.charAt(chars);
				chars++;
				//Speed variable sets the length of the timeout (typing speed)
				setTimeout(typingEffect, speed, txt, divID);
			}
			else if (chars == txt.length){
				chars = 0;
				lines++;
				document.getElementById(divID).innerHTML += "<br/><br/>";
				if (lines == 1 && lines <= maxLines){
					typingEffect(txt2, divID);
				}
				else if (lines == 2 && lines <= maxLines){
					typingEffect(txt3, divID);
				}
				else{
					focusOnTerminal();
					setTimeout(focusOnTerminal, 100);
				}
			}
		}
		function setTerminalSize(size){//Change terminal size based on button presses
			if (terminalSize == "close"){
				document.getElementById("terminal-div").style.visibility = "visible";
			}
			if (size == "big"){
				document.getElementById("terminalBody").style.display = "block";
				document.getElementById("terminalBody").className = "terminalBody terminalBodyExpanded";
				terminalSize = "big";
			}
			else if (size == "regular"){
				document.getElementById("terminalBody").style.display = "block";
				document.getElementById("terminalBody").className = "terminalBody";
				terminalSize = "regular";
			}
			else if (size == "minimize"){
				document.getElementById("terminalBody").style.display = "none";
				terminalSize = "minimize";
			}
			else if (size == "close"){
				document.getElementById("terminal-div").style.visibility = "hidden";
				terminalSize = "close";
			}
		}
		function tButtonPressed(color, which){//Respond to button presses on terminal window
			if (which == 1){
				if (color == "green"){
					if (terminalSize == "regular"){
						setTerminalSize("big");
					}
					else{
						setTerminalSize("regular");
					}
					focusOnTerminal();	
				}
				else if (color == "yellow"){
					if (terminalSize == "big" || terminalSize == "minimize"){
						setTerminalSize("regular");
					}
					else{
						setTerminalSize("minimize");
					}
					focusOnTerminal();
				}
				else if (color == "red"){
					setTerminalSize("close");
				}
			}
			else{
				alert('tbutton on ' + which + ' pressed');
			}
		}
		async function terminalInput(which){//Send terminal input to PHP file and get a response. Print response into history.
			if (which == 1){
				//Set Wallet Address Field Value
				document.getElementById("walletAddressField").value = account;
				//Send Form DATA (input) to Terminal File
				let query = await fetch('/code/php/terminal.php', {method: "POST", body: new FormData(document.querySelector('form'))})
				let text = await query.text();
			
				var inputField = document.getElementById("t1in");
				//Add to History
				document.getElementById("terminalHistory").innerHTML += termLbl + " " + inputField.value + "<br/>"; 
				//Add Return to History
				if (text == "cd1"){
					text = "";
					setTerminalLabel('~');
				}
				else if (text == "cd2"){
					text = "";
					setTerminalLabel('View All Cards');
				}
				else if (text == "playGame"){
					 playGame();
				}
				else if (text == "showTOS"){
					document.getElementById("terminal-div").style.display = "none";
					document.getElementById("tos-purchase-div").style.display = "block";
					setcPanelButtons(2);
				}
				else if (text == "arrangeDeck"){
					if (myWalletStatus == 0){
						text = "You must have a wallet to build a deck. Opening a third-party website where you can download a wallet..."
						setTimeout(openNewTab, 900, "https://metamask.io/download/");
					}
					else if (myWalletStatus == 1){
						text = "Your wallet must be connected to this site to build a deck. Attempting to connect to your wallet..."
						setTimeout(connectMyWallet, 900);
					}
					else if (myWalletStatus == 2){
						if (signedInToServerAs != "false"){
							document.getElementById("terminal-div").style.display = "none";
							document.getElementById("deck-builder-div").style.display = "block";
							setcPanelButtons(2);
						}
						else{
							text = "You must sign in to this system to build a deck. Sign this transaction to log in..."
							setTimeout(getSignature, 900);
						}
					}
				}
				else if (text == "deckBuilder"){
					if (myWalletStatus == 0){
						text = "You must have a wallet to build a deck. Opening a third-party website where you can download a wallet..."
						setTimeout(openNewTab, 900, "https://metamask.io/download/");
					}
					else if (myWalletStatus == 1){
						text = "Your wallet must be connected to this site to build a deck. Attempting to connect to your wallet..."
						setTimeout(connectMyWallet, 900);
					}
					else if (myWalletStatus == 2){
						if (signedInToServerAs != "false"){
							text = "Initiating Deck Builder..."
							buildDeck();
						}
						else{
							text = "You must sign in to this system to build a deck. Sign this transaction to log in..."
							setTimeout(getSignature, 900);
						}
					}
				}
				else if (text == "buyCards"){ //Buy Cards
					text = "All players can use the basic starter deck and unlock hidden cards. Additional cards are available for purchase. Loading marketplace...";
					document.getElementById("buyCardsField").value = true;
					setTimeout(loadMarketPlace, 1900);
				}
				else if (text == "yp1ws0"){ //No Wallet - Request One
					text = "I will open a third-party website where you can download a wallet. Please read, understand, and agree to the terms of service before installing. Also, self-custody of value bearing tokens and coins is risky and challenging, so make sure you are only storing small amounts until you fully understand what you are doing.";
					setTimeout(openNewTab, 2900, "https://metamask.io/download/");
				}
				else if (text == "yp1ws1" || text == "connectWallet"){ //Wallet Not Connected - Request Connection
					if (!account){
						text = "I will attempt to connect your wallet to this site. You must approve the connection.";
						setTimeout(connectMyWallet, 500);
					}
					else{
						text = "You are already connected to account " + account;
					}
				}
				else if (text == "tryLogin"){
					text = "I must collect your signature to verify your wallet. Sign this transaction to log in the system.";
					setTimeout(getSignature, 500);
				}
				else if (text == "logOut"){
					text = "User logged out... Restarting system...";
					setTimeout(reloadPage, 1000);
				}
				//Print Response
				printTerminalHistory(text);
				//Clean Input
				inputField.value = "";
				focusOnTerminal();
			}
		}
		function printTerminalHistory(text){//Print terminal responses on terminal
			document.getElementById("terminalHistory").innerHTML += text + "<br/>";
		}
		
		function focusOnTerminal(){//Make cursor blink and typing work
			var inputField = document.getElementById("t1in");
			
			//Scroll to Bottom
			var terminalBody = document.getElementById("terminalBody");
			terminalBody.scrollTo(0, terminalBody.scrollHeight - 20);
			inputField.focus();
		}
		function setTerminalLabel(text){
			termLbl = terUsrNm + " " + text + " %: ";
			document.getElementById("tLBL").innerHTML = termLbl;
		}
		function setTerminalUserName(){
			if (account){
				var lastThree = account.substr(account.length - 3);
				lastThree = lastThree.toUpperCase();
				terUsrNm = "player" + lastThree + "@hfkc";
				termLbl = terUsrNm + " ~ %: ";
			}
			document.getElementById("termUsrName").innerHTML = terUsrNm;
			
		}
		
		function sendCommand(command, delay, whichTerminal){//Used by buttons to autoenter terminal commands
			var timeToWait = 1000;
			if (delay){
				timeToWait = delay;
			}
			document.getElementById("t1in").value = command;
			if (whichTerminal){
				setTimeout(terminalInput, timeToWait, whichTerminal);
			}
			else{
				setTimeout(terminalInput, timeToWait, 1);
			}
		}
		async function setcPanelButtons(mode){
			var query = false;
			
			if (window.ethereum && !deckBuilderMode){
				query = await checkWalletStatus();
			}			
			
			var myButtons = null;
			
			//Default is to send commands, but sometimes the terminal doesn't accept commands so have a second mode that just calls functions
			var installWalletButton = "<div class='cPanel-button'><button class='button' id='installWallet' onclick='sendCommand(\"install wallet\", 125,1)'>Install Wallet</button></div>";
			var connectWalletButton = "<div class='cPanel-button'><button class='button' id='connectWalletButton' onclick='sendCommand(\"connect wallet\", 125,1)'>Connect Wallet</button></div>";
			var signInButton = "<div class='cPanel-button'><button class='button' id='signInButton' onclick='sendCommand(\"sign in\", 125,1)'>Sign In</button></div>";
			var buildDeckButton = "<div class='cPanel-button'><button class='button' id='buildDeckButton' onclick='sendCommand(\"build deck\", 125,1)'>Build Deck</button></div>";
			var buyCardsButton = "<div class='cPanel-button'><button class='button' id='buyCards' onclick='sendCommand(\"buy cards\", 125,1)'>Buy Cards</button></div>";
			var playGameButton = "<div class='cPanel-button'><button class='button' id='playGameButton' onclick='sendCommand(\"play game\", 125,1)'>Play Game</button></div>";
			var logOutButton = "<div class='cPanel-button'><button class='button' id='logOutButton' onclick='sendCommand(\"log out\", 125,1)'>Log Out</button></div>";
			
			if (mode == 2){
				installWalletButton = "<div class='cPanel-button'><button class='button' id='installWallet' onclick='openNewTab(\"https://metamask.io/download/\")'>Install Wallet</button></div>";
				connectWalletButton = "<div class='cPanel-button'><button class='button' id='connectWalletButton' onclick='connectMyWallet()'>Connect Wallet</button></div>";
				signInButton = "<div class='cPanel-button'><button class='button' id='signInButton' onclick='getSignature(33)'>Sign In</button></div>";
				buildDeckButton = "<div class='cPanel-button'><button class='button' id='buildDeckButton' onclick='buildDeck()'>Build Deck</button></div>";
				buyCardsButton = "<div class='cPanel-button'><button class='button' id='buyCards' onclick='openSameTab(\"\?buyCards=true\")'>Buy Cards</button></div>";
				playGameButton = "<div class='cPanel-button'><button class='button' id='playGameButton' onclick='playGame()'>Play Game</button></div>";
			}
			
			//Alter BUY CARDS BUTTON IF ON BUY CARDS PAGE
			if (buyCards){
				buyCardsButton = "<div class='cPanel-button'><button class='button' id='buyCards' onclick='sendCommand(\"yes\", 100,1)'>Buy Cards</button></div>";
				if (mode == 2){
					buyCardsButton = "";
				}
			}
			//Alter BUILD DECK Button if on that page
			if (deckBuilderMode){
				buildDeckButton = "<div class='cPanel-button'><button class='button' id='buildDeckButton' onclick='sendCommand(\"yes\", 100,1)'>Build Deck</button></div>";
			}
			
			if (myWalletStatus == 0){
				myButtons = installWalletButton + playGameButton + buildDeckButton + buyCardsButton;
			}
			else if (myWalletStatus == 1){
				myButtons = connectWalletButton + playGameButton + buildDeckButton + buyCardsButton;
			}
			else if (myWalletStatus == 2){
				myButtons = signInButton + playGameButton + buildDeckButton + buyCardsButton;
				//Wallet is connected. User is verified.
				if (signedInToServerAs != "false"){
					myButtons = playGameButton + buildDeckButton + buyCardsButton + logOutButton;
				}
			}
			document.getElementById("cpanel-buttons-div").innerHTML = myButtons;
			setTimeout(focusOnTerminal, 1000);
		}
		function connectMeBut(){
			sendCommand("connect wallet", 125);
		}
	</script>