	<script>
		//Useful functions and global variables in this file...
		var playerNum = false;
		var account = false;
		var myCards = null;
		var myCardChoices = [];
		var mySelectedCard = false;
		var mySelectedCards = [];
		var selectedCardNum = 0;
		var userCards = false;
		var mySavedDeck1 = [];
		var mySavedDeck2 = [];
		var mySavedDeck3 = [];
		var mySavedDeck1Flat = [];
		var mySavedDeck2Flat = [];
		var mySavedDeck3Flat = [];
		var p1Characters = [];
		var p2Characters = [];
		var selectedDeck = 1;
		var savedDeckCounter = 0;
		var lookingForMatch = false;
		var matchCheckInterval = null;
		var gameStatusIntervalId = null;
		var pollingIntervalId = null;
		
		var redCoinTokenAddress = '0x4E78Ca0D3B4dcd9b030F61B58BaC521b901545f5';
		var redCoinMintingContractAddress = '0xFc47A741D4ba3AC0c25b74bE46c121621947fae7'; //Uses Chainlink Functions
		var clSubscriptionID = 1569;
		var clFunctionArgs = [];
		window.userToken 
		//["0xac4222bbfd2a0184c95be18f3626efab68b675c9", "18a3fa3224cffc5a044b"]
		
		
		async function mintRedCoins(){
			clFunctionArgs.length = 0;
			var tkn = window.userToken.toString();
			var accountStr = window['userAccountNumber'].toString();
			clFunctionArgs.push(accountStr);
			clFunctionArgs.push(tkn);
			console.log(clFunctionArgs);
			
			let web3 = new Web3(Web3.givenProvider);
			
			var accountBalance = await web3.eth.getBalance(accountStr);
			var mintCost = 10000000000000000;
			console.log(accountBalance);
			if (accountBalance >= mintCost){
				var contract = new web3.eth.Contract(abi1, redCoinMintingContractAddress, {});
				openingPack = false;
				waitingForOracle = false;
			
				//document.getElementById("tosCheckBox").disabled = true;
				await contract.methods.checkAndMintRedCoins(clSubscriptionID, clFunctionArgs).send({
					from: window['userAccountNumber'],
					//value: web3.utils.toWei(sendEth, "ether"),
					value: web3.utils.toWei("0.1", "ether"), 
					//value: web3.utils.toWei(payThis, "ether"), 
					gas: 1500000,
					maxPriorityFeePerGas:5000000000
			
				}).on('transactionHash', function(hash){
					ig.game.titleScreenTxt = "Transaction Request Submitted...";
					console.log('transaction hashed...');
					//displayOpenPack();
				}).on('receipt', function(receipt){
					ig.game.titleScreenTxt = "Transaction Request Mined...";
					console.log('transaction received...');
					console.log(receipt);
					//displayWaitingForOracle();
					waitForOracle();				
				});
				//ig.game.titleScreenTxt = "Welcome to the Happy Fun Kill Club!";  //Welcome Menu
			}
			else{
				var cTxt = `You need testnet AVAX to mint RedCoin. Would you like to get some at the Chainlink Faucet?`;
				setConfirmation(cTxt, 4, 8);
			}
		}
		function setTitleTxt(txt) {
			if (txt == `Success! RedCoins Minted.`){
				ig.game.playMintRedCoinsSound();
			}
			ig.game.titleScreenTxt = txt;
		}
		async function waitForOracle(){
		
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi1, redCoinMintingContractAddress, {});
		 	oracleReturnDisplay = false;
			myRequestID = await contract.methods.s_lastRequestId().call();
			var redCoinsToMint = await contract.methods.redCoins().call();
			console.log('myRequestID:', myRequestID);
			console.log(myRequestID);
			var length = 7;
			var trimmedID = myRequestID.substring(0, length);
			trimmedID += "..."
			var ttx1 = `Chainlink Functions Request #${trimmedID} Received.`;
			setTitleTxt(ttx1);
			
			var timeout1 = setTimeout(setTitleTxt, 3000, `Minting ${redCoinsToMint} RedCoins...`);
			var timeout2 = setTimeout(setTitleTxt, 7000, `Success! RedCoins Minted.`);
			var timeout2b = setTimeout(fetchUserRedCoinsAndToken, 7000);
			
			var timeout3 = setTimeout(setTitleTxt, 10000, "Welcome to the Happy Fun Kill Club!");
			
			contract.events.Response({
				filter: {requestId: myRequestID}, 
				fromBlock: "pending"
			}, function(error, event){ console.log(event); })
			.on('data', function(event){
				console.log(event);
				ig.game.titleScreenTxt = "Chainlink Functions Request Received...";
				
				//var mintingRedCoins = event.returnValues.redCoins;
				if (mintingRedCoins){
					ig.game.titleScreenTxt = "Minting " + mintingRedCoins + " RedCoins...";
				}
				else{
					
				}
				//displayOracleReturn(num1, num2, num3, num4, num5, num6, num7);
			})
			.on('changed', function(event){
				console.log("changed:");
				console.log(event); 
				// remove event from local database
			})
			.on('error', console.error);
		}

		async function getUsersCards(){
			
			fetch('/code/php/getCards.php')
			.then(response => response.text())
   			.then((response) => {
       			if (response){
       				myCards = JSON.parse(response);
       				userCards = setCardData(myCards, 0);
       				setCardChoices(userCards);
       			}
			})
		}
		
		async function fetchCardJSON(){
			const response = await fetch(mySelectedCard + 'game.json');
			//console.log(response);
		}
		function displayNoneIt(eID){
			document.getElementById(eID).style.display = "none";
		}
		function openNewTab(URL){
			window.open(URL, '_blank');
		}
		function openSameTab(URL){
			window.open(URL, '_self');
		}
		function reloadPage(){
			location.reload();
		}
		function playGame(){
			openSameTab("/game");
		}
		function loadMarketPlace(){
			openSameTab("/?buyCards=true");
		}
		function buildDeck(){ //Reloads page and adds query tag
			openSameTab("/?buildDeck=true");
		}
		
		function adjustButton(enable, id){//Helper function to toggle disable/enable of any button based on ID
			if (enable){
				document.getElementById(id).disabled = false;
			}
			else{
				document.getElementById(id).disabled = true;
			}
		}
		
		
	</script>