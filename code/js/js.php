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
		var itemMintingContractAddress = '0xfeB47D1FD7593E1764B1CD7bACAAf09526Aa4917';
		var clSubscriptionID = 1569;
		var clFunctionArgs = [];
		
		//Make sure user window account object exists...
		if (window['userAccountNumber'] || signedInToServerAs){
			if (!window['userAccountNumber'] ){
				window['userAccountNumber'] = signedInToServerAs;
			}
		}
		
		async function mintRedCoins(){
			clFunctionArgs.length = 0;
			var tkn = window.userToken.toString();
			console.log('tkn = ' + tkn);
			var accountStr = window['userAccountNumber'].toString();
			console.log('accountStr = ' + accountStr);
			clFunctionArgs.push(accountStr);
			clFunctionArgs.push(tkn);
			//console.log(clFunctionArgs);
			
			let web3 = new Web3(Web3.givenProvider);
			
			var accountBalance = await web3.eth.getBalance(accountStr);
			var mintCost = 10000000000000000;
			//console.log(accountBalance);
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
					//console.log('transaction hashed...');
					//displayOpenPack();
				}).on('receipt', function(receipt){
					ig.game.titleScreenTxt = "Transaction Request Mined...";
					//console.log('transaction received...');
					//console.log(receipt);
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
			//console.log('myRequestID:', myRequestID);
			//console.log(myRequestID);
			var length = 7;
			var trimmedID = myRequestID.substring(0, length);
			trimmedID += "..."
			var ttx1 = `Chainlink Functions Request #${trimmedID} Received.`;
			setTitleTxt(ttx1);
			
			var timeout1 = setTimeout(setTitleTxt, 8000, `Minting ${window.userRedCoins} RedCoins...`);
			var timeout2 = setTimeout(setTitleTxt, 15000, `Success! RedCoins Minted.`);
			var timeout2b = setTimeout(fetchUserRedCoinsAndToken, 21000);
			
			var timeout3 = setTimeout(setTitleTxt, 21000, "Welcome to the Happy Fun Kill Club!");
			
			contract.events.Response({
				filter: {requestId: myRequestID}, 
				fromBlock: "pending"
			}, function(error, event){ console.log(event); })
			.on('data', function(event){
				//console.log(event);
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
		async function buyItem(which, cost){
			checkRedCoinAllowance(which, cost);
		}
		async function buyTheItem(whichItem, itemCost){
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi6, itemMintingContractAddress, {});
			const mintItem = await contract.methods.spendRedCoin(whichItem).send({
					from: window['userAccountNumber']
				}).on('transactionHash', function(hash){
					toggleRCSLoadingWheel("on", "Buying Item...");
					document.body.scrollTop = document.documentElement.scrollTop = 0;
					//console.log('Spending RedCoin...')
				}).on('receipt', function(receipt){
					toggleRCSLoadingWheel("off", "Purchase Successful.");
					sendRCSaMessage("Purchase Successful!")
					console.log('Item minted.');
					console.log(receipt)
					return;		
			});
		}
		function toggleRCSLoadingWheel(way, progressMessage){
			var loadingWheelDiv = document.getElementById("purchase-progress-div");
			
			if (!progressMessage){
				progressMessage = "Loading...";
			}
			
			if (way == "on"){
				var loadingWheelCode = `<div id='loading-wheel-div-03'>
					<div>
						<img src='/images/loading-wheel-02.gif'/>
					</div>
					<div id='waiting-msg-05'>
						${progressMessage}
					</div>
				</div>`;

				loadingWheelDiv.innerHTML = loadingWheelCode;
			}
			else{
				loadingWheelDiv.innerHTML = "";
			}
		}
		function sendRCSaMessage(progressMessage, link){
			
			var loadingWheelDiv = document.getElementById("purchase-progress-div");
			loadingWheelDiv.innerHTML = progressMessage;
		}	
		async function checkRedCoinAllowance(whichItem, itemCost){
			//Add a bunch of 0 points because tokens have a lot of zero values.
			var adjustedItemCost = itemCost * 10**18;
			//First Check Allowance
			let web3 = new Web3(Web3.givenProvider);
				
			var contract = new web3.eth.Contract(abi2, redCoinTokenAddress, {});
			const allowance = await contract.methods.allowance(window['userAccountNumber'], itemMintingContractAddress).call();
						
			if (parseInt(allowance) < adjustedItemCost){
				approving = true;
				approvingComplete = true;
				var amount = 999999999999999999;
				var approveNum =  web3.utils.toWei(amount.toString(), 'ether')
				alert("You need to give the store approval to spend your RedCoin.");
				const approveAmount = await contract.methods.approve(itemMintingContractAddress,approveNum).send({
					from: window['userAccountNumber']
				}).on('transactionHash', function(hash){
					toggleRCSLoadingWheel("on", "Approving RedCoin Store to Spend RedCoin Token...");
				}).on('receipt', function(receipt){
					toggleRCSLoadingWheel("off", "RedCoin Spend Approved. Buy Item?");
					sendRCSaMessage("RedCoin Spend Approved.")
					buyTheItem(whichItem, adjustedItemCost);
					return;		
				});
			}
			else{
				sendRCSaMessage("RedCoin Spend Already Approved. Buy Item?")
				buyTheItem(whichItem, adjustedItemCost);
			}
		
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
		async function getRedCoinAmount(){
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi2, redCoinTokenAddress, {});
			balance = await contract.methods.balanceOf(window['userAccountNumber']).call();
			userRedCoinBalance = balance * .000000000000000001;
			userRedCoinBalance = userRedCoinBalance.toFixed(1)
			sendRCSaMessage("You have " + userRedCoinBalance + " RedCoins.");
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