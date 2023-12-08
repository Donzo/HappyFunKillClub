<script>
		//connectMyWallet();
		var amountToBuy = 1;
		var costPerUnit = .1;
		var costToBuy = .1;
		var tosSCroll = 0;
		var userPackBalance = 0;
		var fulfillCheckCount = 0;
		
		var packsTokenAddress = '0x29C127821CB160672f47d734C97B32c88d38AFD1';
		var packsVendingContractAddress = '0xc668259Df5a68FB8A95C407342f61AaB82F853D0';
		var openPackContractAddress = '0x6a54A95de0A505Aef16785164CCa3184C3f960C9';
		
		//Intreval
		var oracleCheck = false;
		
		async function checkIfUserHasPacks(){
			let web3 = new Web3(Web3.givenProvider);
			//console.log("window['userAccountNumber'] = " + window['userAccountNumber']);
			var contract = new web3.eth.Contract(abi2, packsTokenAddress, {});
			balance = await contract.methods.balanceOf(window['userAccountNumber']).call();
			userPackBalance = balance * .000000000000000001;
			userPackBalance = userPackBalance.toFixed(1)
			//document.getElementById("packsLeft").innerHTML = packBalance;
			if (userPackBalance >= 1){
				var checkbox = document.getElementById("tosCheckBox");
				var conJPack = userPackBalance == 1 ? "PACK" : "PACKS";
				
				var refAmount = "One";
				if (userPackBalance == 1){
					refAmount = "It"	
				}
				document.getElementById("users-packs-message").innerHTML = "You have " + userPackBalance + " " + conJPack + ". <br/>Click the Button Below to Open " + refAmount + ".";
				if (checkbox.checked) {
					enableOpenButton('OPEN PACK');					
				}
				else{
					disableOpenButton('AGREE TO TOS TO OPEN PACK');
				}
			}
			else{
				document.getElementById("users-packs-message").innerHTML = "You don't have any PACKS.<br/>Buy PACKS to open them.";
				document.getElementById("open-packs-div").innerHTML = "";
			}
			
		}
		/*
		async function withdrawl(){
			
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi3, packsVendingContractAddress, {});
			
			await contract.methods.withdraw().send({
				from: window['userAccountNumber'],
				//value: web3.utils.toWei(payThis, "ether"), 
		
			}).on('transactionHash', function(hash){
				console.log('purchase hashed...');
				console.log(hash);
				var loadingWheelDiv = document.getElementById("loading-wheel-div");
				//var lwMsg = document.getElementById("waiting-msg");
				loadingWheelDiv.className = "";
				//lwMsg.innerHTML = ""
				changePacksMessage("Your withdraw request sent...");
			}).on('receipt', function(receipt){
				console.log('purchase complete...');
				var loadingWheelDiv = document.getElementById("loading-wheel-div");
				loadingWheelDiv.className = "hide";
				changePacksMessage("Withdraw successful!");
				checkPacksInTime(5000);
				return;		
			});
		}
		withdrawl();
		*/
		
		async function buyPacks(amount){
			document.getElementById("tosCheckBox").disabled = true;
			document.getElementById("slidePacksToBuy").disabled = true;
			var payThis = costToBuy.toFixed(1);
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi3, packsVendingContractAddress, {});
			//confirmMes.className = "hide";
			var accountBalance = await web3.eth.getBalance(window['userAccountNumber']);
			var mintCost = 10000000000000000;
			console.log(accountBalance);
			if (accountBalance >= mintCost){
			
				try{
					await contract.methods.buyTokens().send({
						from: window['userAccountNumber'],
						//value: web3.utils.toWei(sendEth, "ether"),
						//value: web3.utils.toWei("0.1", "ether"), 
						value: web3.utils.toWei(payThis, "ether"), 
						//gas: 1500000,
						//maxPriorityFeePerGas:5000000000
			
					}).on('transactionHash', function(hash){
						console.log('purchase hashed...');
						console.log(hash);
						disableBuyButton('Already Buying Packs');
						toggleLoadingWheel(1, "on");
						changePacksMessage("Your transaction to purchase " + amountToBuy + " PACKS was sent.");
					}).on('receipt', function(receipt){
						console.log('purchase complete...');
						toggleLoadingWheel(1, "off");
						changePacksMessage("Your purchase is complete.<br/>Click the Button Below to Open PACKS.");
						enableBuyButton('Buy Packs Now')
						checkPacksInTime(3000);
						return;		
					});
				}
				catch(error){
					//Handle Connection Errors
					if (error.code == 4001){
						document.getElementById("slidePacksToBuy").disabled = false;
						changePacksMessage("You rejected the transaction to buy PACKS.");
						checkPacksInTime(3000);
					}
				}
			}
			else{
				let text;
				if (confirm("You need AVAX to purchase PACKS. Want to get some from the faucet?") == true) {
					openNewTab("https://faucets.chain.link/fuji")
				}
				else{
					text = "Ok but you can't buy PACKS without AVAX.";
				}
			}
		}
		function checkPacksInTime( time){
			setTimeout(checkIfUserHasPacks, time);
		}
		function changePacksMessage(txt){
			document.getElementById("users-packs-message").innerHTML = txt;
		}
		function enableCheckBox(){//Enable checkbox after TOU reaches bottom
			document.getElementById("tosCheckBox").disabled = false;
			document.getElementById("checkboxOverlay").style.display = "none";
			document.getElementById("tos-agree").style.color = "#FFFFFF";
		}
		function scrollTOU(){//Scroll TOU if not at bottom of TOU
			tosSCroll +=120;
			if (document.getElementById("tosCheckBox").disabled){
				document.getElementById('terminalBody-03').scroll(0, tosSCroll);
			}
			else{
				window.scrollTo(0, document.body.scrollHeight);
			}
		}
		function handleCheckboxChange() {//Respond to checkbox toggles
			var checkbox = document.getElementById("tosCheckBox");
  			var buyButtonDiv = document.getElementById("buy-button-div");
  			var ppSliderDiv = document.getElementById("purchase-packs-slider-unit");
  			var usrPackMessWrapper = document.getElementById("users-packs-message-wrapper");
			if (checkbox.checked) {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='button' onclick = 'buyPacks()'>BUY PACKS NOW</button>";
				ppSliderDiv.className = "";
				usrPackMessWrapper.className = "";
				checkIfUserHasPacks(); //Check for tokens
				window.scrollTo(0, document.body.scrollHeight);				
			}
			else {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO BUY PACKS</button>";
				ppSliderDiv.className = "hide";
				usrPackMessWrapper.className = "displayNone";
				setTimeout(function(){
					//console.log('scroll to bottom 2');
					window.scrollTo(0, document.body.scrollHeight);
				}, 100);
			}
		}
		
		function disableOpenButton(msg){
			document.getElementById("open-packs-div").innerHTML = "<button id='open-pack-button' class='disabledbutton' disabled>" + msg + "</button>";
		}
		function enableOpenButton(msg){
			document.getElementById("open-packs-div").innerHTML = "<button id='open-pack-button' class='button' onclick='checkPackAllowance()'>" + msg + "</button>";
		}
		function disableBuyButton(msg){
			document.getElementById("buy-button-div").innerHTML = "<button id='buy-pack-button' class='disabledbutton' disabled>" + msg + "</button>";
		}
		function enableBuyButton(msg){
			document.getElementById("buy-button-div").innerHTML = "<button id='buy-pack-button' class='button' onclick='buyPacks()'>" + msg + "</button>";
		}
		function adjustPackAmount(){//Respond to slider bar changes
			amountToBuy = document.getElementById("slidePacksToBuy").value;
			document.getElementById("amountOfPacks").innerHTML = amountToBuy;
			document.getElementById("congegateNounPack").innerHTML = amountToBuy > 1 ? "Packs" : "Pack";
			
			costToBuy = amountToBuy * costPerUnit;
			document.getElementById("costOfPacks").innerHTML = costToBuy.toFixed(1);
		}
		
		document.addEventListener('DOMContentLoaded', function() {//Make Sure Terms Have Been Read
			var termsContent = document.getElementById('terminalBody-03');
			termsContent.addEventListener('scroll', function() {
				if (termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight - 100) {
					enableCheckBox();
				}
			});
		});
		
		async function checkPackAllowance(){
			
			//First Check Allowance
			let web3 = new Web3(Web3.givenProvider);
				
			var contract = new web3.eth.Contract(abi2, packsTokenAddress, {});
			const allowance = await contract.methods.allowance(window['userAccountNumber'], openPackContractAddress).call();
						
			if (parseInt(allowance) < 1000000000000000000){
				approving = true;
				approvingComplete = true;
				var amount = 9999999999999999;
				var approveNum =  web3.utils.toWei(amount.toString(), 'ether')
				alert("You need to approve this contract to spend your PACK.");
				const approveAmount = await contract.methods.approve(openPackContractAddress,approveNum).send({
					from: window['userAccountNumber']
				}).on('transactionHash', function(hash){
					toggleLoadingWheel(1, "on");
					document.getElementById("users-packs-message").innerHTML = "Approving PACK Spend...";
				}).on('receipt', function(receipt){
					console.log('PACK spend approved. Ready to open a PACK.');
					toggleLoadingWheel(1, "off");
					document.getElementById("users-packs-message").innerHTML = "PACK Spend Approved. Opening PACK...";
					openAPack();
					return;		
				});
			}
			else{
				openAPack();
			}
		
		}
		function toggleLoadingWheel(which, way){
			var loadingWheelDiv = document.getElementById("loading-wheel-div");
			var loadingWheelDiv3wrapper = document.getElementById("loading-wheel-div-03-wrapper");
			if (which == 1){
				loadingWheelDiv.className = way == "on" ? "" : "hide";
			}
			else{
				if (way == "on"){
					var loadingWheel3 = `<div id='loading-wheel-div-03'>
						<div>
							<img src='/images/loading-wheel-02.gif'/>
						</div>
						<div id='waiting-msg-03'>
							&nbsp;
						</div>
					</div>`;

					loadingWheelDiv3wrapper.innerHTML = loadingWheel3;
				}
				else{
					loadingWheelDiv3wrapper.innerHTML = "";
				}
			}
		}
		async function openAPack(){
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi4, openPackContractAddress, {});
			openingPack = false;
			waitingForOracle = false;
			document.getElementById("tosCheckBox").disabled = true;
			await contract.methods.openPack().send({
				from: window['userAccountNumber'],
				//value: web3.utils.toWei(sendEth, "ether"),
				//value: web3.utils.toWei("0.1", "ether"), 
				//value: web3.utils.toWei(payThis, "ether"), 
				//gas: 1500000,
				//maxPriorityFeePerGas:5000000000
			
			}).on('transactionHash', function(hash){
				toggleLoadingWheel(2, "on");
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Mining Transaction...";
				disableOpenButton("Already Opening a Pack");
				disableBuyButton("Opening a Pack Right Now");
				
			}).on('receipt', function(receipt){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Waiting for Oracles...";
				waitForOracle();				
			});
		}
		async function checkOracleRequest(requestID) {
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi4, openPackContractAddress, {});
			var didFulfill = await contract.methods.mapIdToFulfilled(requestID).call();
			console.log("didFulfil = " + didFulfill);
			fulfillCheckCount++;
			
			if (fulfillCheckCount == 10){
				var shortReqID = requestID.substring(0,12);
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Request " + shortReqID + "[...] is Waiting for Fulfilment...";
			}
			else if (fulfillCheckCount == 20){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Still Waiting... I'm not sure why this takes several minutes, but it may.";
			}
			else if (fulfillCheckCount == 30){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: I promise it's not broken. It just takes a long time to fulfill on this testnet.";
			}
			else if (fulfillCheckCount == 40){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: For real it will work. Just give it a minute.";
			}
			else if (fulfillCheckCount == 50){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Request Pending...";
			}
			else if (fulfillCheckCount == 60){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Just a little bit longer...";
			}
			else if (fulfillCheckCount == 70){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Now we are cooking with peanut oil...";
			}
			else if (fulfillCheckCount == 80){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Any minute now...";
			}
			else if (fulfillCheckCount == 90){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Here it comes...";
			}
			else if (fulfillCheckCount == 100){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Hard to believe, but it can take up to 5 minutes to fulfill one of these requests.";
			}
			else if (fulfillCheckCount == 110){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Has it been five minutes yet? We are close.";
			}
			else if (fulfillCheckCount == 120){
				document.getElementById("users-packs-message").innerHTML = "OPEN PACK: Thank you for your patience!";
			}
			
			if (didFulfill){
				toggleLoadingWheel(2, "on");
				clearInterval(oracleCheck);
				fulfillCheckCount = 0;
				enableOpenButton("OPEN PACK");
				enableBuyButton("Buy Packs Now");
				document.getElementById("users-packs-message").innerHTML = "Your Pack Was Opened. Your cards are below!";
				checkPacksInTime(7000);
				toggleLoadingWheel(2, "off");
				var word1 = await contract.methods.mapIdToWord1(requestID).call();
				var word2 = await contract.methods.mapIdToWord2(requestID).call();
				var word3 = await contract.methods.mapIdToWord3(requestID).call();
				var word4 = await contract.methods.mapIdToWord4(requestID).call();
				var word5 = await contract.methods.mapIdToWord5(requestID).call();
				var word6 = await contract.methods.mapIdToWord6(requestID).call();
				var word7 = await contract.methods.mapIdToWord7(requestID).call();
				//setTimeout(displayCards, 3000, word1, word2, word3, word4, word5, word6, word7);
				displayCards(word1, word2, word3, word4, word5, word6, word7);
				console.log(`Numbers: ${word1}, ${word2}, ${word3}, ${word4}, ${word5}, ${word6}, ${word7}`)
			}
		}
		async function waitForOracle(){
			
			let web3 = new Web3(Web3.givenProvider);
			var contract = new web3.eth.Contract(abi4, openPackContractAddress, {});
			toggleLoadingWheel(2, "on");
		 	oracleReturnDisplay = false;
			//myRequestID = await contract.methods.lastRequestID.call();
			myRequestID = await contract.methods.lastRequestID().call();
			console.log('myRequestID = ' + myRequestID);
			oracleCheck = setInterval(checkOracleRequest, 4000, myRequestID);
			
			contract.events.RequestFulfilled({
				filter: {requestId: myRequestID}, 
				fromBlock: "pending"
			}, function(error, event){ console.log(event); })
			.on('data', function(event){
				console.log("random num1 = " + event.returnValues.randomNum1);
				var num1 = event.returnValues.randomNum1;
				console.log("random num2 = " + event.returnValues.randomNum2);
				var num2 = event.returnValues.randomNum2;
				console.log("random num3 = " + event.returnValues.randomNum3);
				var num3 = event.returnValues.randomNum3;
				console.log("random num4 = " + event.returnValues.randomNum4);
				var num4 = event.returnValues.randomNum4;
				console.log("random num5 = " + event.returnValues.randomNum5);
				var num5 = event.returnValues.randomNum5;
				console.log("random num6 = " + event.returnValues.randomNum6);
				var num6 = event.returnValues.randomNum6;
				console.log("random num7 = " + event.returnValues.randomNum7);
				var num7 = event.returnValues.randomNum7;
				console.log(event);
				//displayOracleReturn(num1, num2, num3, num4, num5, num6, num7);
			})
			.on('changed', function(event){
				console.log("changed:");
				console.log(event); 
				// remove event from local database
			})
			.on('error', console.error);
		}
		function returnCardNumber(ranNum){
			var cardNum = "c1";
			if (ranNum < 10){
				cardNum = "c1";
			}
			else if (ranNum < 20){
				cardNum = "c2";
			}
			else if (ranNum < 30){
				cardNum = "c3";
			}
			else if (ranNum < 40){
				cardNum = "c4";
			}
			else if (ranNum < 50){
				cardNum = "c5";
			}
			else if (ranNum < 100){
				cardNum = "c6";
			}
			else if (ranNum < 140){
				cardNum = "c7";
			}
			else if (ranNum < 180){
				cardNum = "c8";
			}
			else if (ranNum < 220){
				cardNum = "c9";
			}
			else if (ranNum < 260){
				cardNum = "c10";
			}
			else if (ranNum < 300){
				cardNum = "c11";
			}
			else if (ranNum < 340){
				cardNum = "c12";
			}
			else if (ranNum < 380){
				cardNum = "c13";
			}
			else if (ranNum < 420){
				cardNum = "c14";
			}
			else if (ranNum < 460){
				cardNum = "c15";
			}
			else if (ranNum < 500){
				cardNum = "c16";
			}
			else if (ranNum < 540){
				cardNum = "c17";
			}
			else if (ranNum < 680){
				cardNum = "c18";
			}
			else if (ranNum < 720){
				cardNum = "c19";
			}
			else if (ranNum < 860){
				cardNum = "c20";
			}
			else if (ranNum < 900){
				cardNum = "c21";
			}
			else if (ranNum < 920){
				cardNum = "c22";
			}
			else if (ranNum < 940){
				cardNum = "c23";
			}
			else if (ranNum < 960){
				cardNum = "c24";
			}
			else if (ranNum < 970){
				cardNum = "c25";
			}
			else if (ranNum < 990){
				cardNum = "c26";
			}
			else if (ranNum < 1000){
				cardNum = "c27";
			}
			return cardNum;
		}
		function displayCards(w1, w2, w3, w4, w5, w6, w7){
			var pulledCards = [w1, w2, w3, w4, w5, w6, w7].map(returnCardNumber);

			var mockDeck = pulledCards.reduce((deck, cardId) => {
				deck[cardId] = (deck[cardId] || 0) + 1;
				return deck;
			}, {});
	
			var structuredPulledCardData = setCardData([mockDeck], 0);

			//Flatten the card data to account for quantity
			var flattenedCardData = [];
			structuredPulledCardData.forEach(card => {
				for (let i = 0; i < card.Quantity; i++) {
					flattenedCardData.push(card);
				}
			});

			//Use the flattenedCardData to display the images
			var pulledCardsDiv = document.getElementById("pulled-cards-div");
			pulledCardsDiv.innerHTML = "<h2 id='just-pulled-hdr' class='center-text'>Here Are The Cards That You Just Pulled!</h2>";
			pulledCardsDiv.innerHTML += "<div class='flexBreak'></div>";
			flattenedCardData.forEach((card, index) => {
				if (index < 7) { // Limit to 7 cards
					var cardImage = card.dir + "card.jpg";
					var cardElement = "<a class='flexLink' target='_blank' href='" + cardImage + "'><div class='my-card-img'><img src='" + cardImage + "'/></div></a>";
					pulledCardsDiv.innerHTML += cardElement;
					if (index == 3) { // Add a break after the fourth card
						pulledCardsDiv.innerHTML += "<div class='flexBreak'></div>";
					}
				}
			});
			
			pulledCardsDiv.innerHTML += "<div class='flexBreak'></div>";
			var syncMess = "<div id='sync-message'>Synchronizing New Cards to Your Account...</div>";
			pulledCardsDiv.innerHTML +=syncMess;
			synchronizeMyDeck();
			document.getElementById("play-game-button-div").className = "center-text";
			
		}
	</script>