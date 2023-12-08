<script>
		var myWalletStatus = false;
		var byPassChecks = false;
		var onPreferredNetwork = false;
		var preferredNetwork1 = "Avalanche Fuji Testnet";
		var preferredNetworkChainID = "0xa869";
		var preferredNetworkSwitchCode = 9;
		var preferredProviderNumber = 43113;
		var chain = false;
		var chainId = false;
		
		function signIn(){
			getSignature();
		}
		
		async function connectMyWallet(){			
			
			try {
				await ethereum.request({ method: 'eth_requestAccounts' });
			}
			catch(error){
				//Handle Connection Errors
				if (error.code == 4001){
					var respTxt = "You rejected the request to connect.";
					if (ig.game){
						setAlert(`${respTxt}`, 5, 2)
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else if (error.code == -32002){
					var respTxt = "You already have a pending connection request. Check your wallet.";
					if (ig.game){
						setAlert(`${respTxt}`, 4, 3)
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else{
					alert('Something is wrong with your wallet.');
				}
				return;
			}
			
			
			const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
			
			if (accounts.length){
				console.log(`You're connected to: ${accounts[0]}`);	
			}
			
			account = accounts[0];
			window['userAccountNumber'] = account;
			
			
			if (account){
				window['userAccountNumber'] = account;
			}
			else{
				console.log('no account num.')
			}
			
			//Create Web3 Object
			let web3 = new Web3(Web3.givenProvider);
			const chain = await web3.eth.getChainId();
						
			//Get Provider
			web3.eth.net.getId().then(
				function(value){
					provider = value;
					if (provider){
	  					reportProvider();
	  				}
  				}	
  			);			
		}
		async function getSignature(which){
			if (which){
				console.log('signature requested from ' + which);
			}
			//Create Web3 Object
			let web3 = new Web3(Web3.givenProvider);
			
			//Get Signature
			var mySignature = false;
			
			try {
				mySignature = await web3.eth.personal.sign("Sign here to login.", account, "");
			}
			catch(error){
				//Handle Connection Errors
				if (error.code == 4001){
					
					var respTxt = "You rejected the signature request. We need your signature to verify that you control the wallet address that you are claiming.";
					if (inGame){
						setAlert(`${respTxt}`, 4, 4)
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else if (error.code == -32002){
					var respTxt = "You already have a pending signature request. Check your wallet.";
					if (inGame){
						setAlert(`${respTxt}`, 4, 5)
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else{
					var respTxt = "There is a disturbance in the force."
					if (inGame){
						setAlert(`${respTxt}`, 5, 6)
					}
					else{
						alert(respTxt);
					}
				}
				return;
			}
			
			//console.log("mySignature = " + mySignature);
			if (mySignature){
				window['signature'] = mySignature;
				loginUser();
			}
		}

		async function loginUser(){
			//console.log("signing in " + window['userAccountNumber'] + " with signature " + window['signature']);
			fetch('/code/php/signin.php?wallet=' + window['userAccountNumber'] + "&signature=" + window['signature'])
			.then(response => response.json())
   			.then((response) => {
				if (response.success){
					console.log("User Authenticated. Token: ", response.token, "RedCoins: ", response.redCoins);
					//Store the token and redCoins
					window.userToken = response.token;
					window.userRedCoins = response.redCoins;
					
					signedInToServerAs = window['userAccountNumber'];
	   				
	   				if (inGame){
	   					letsDoThis();
	   				}
	   				else{
	   					setcPanelButtons();
	   				
	   					var respTxt = "SIGNATURE matches - - - Account Verified and User Signed In.";
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
	   					//If Signature Matches, this user is authenticated 
	   					//and we store their address in a session variable.
	   					//Then we get their cards.
	   					if (deckBuilderMode){
	   						reloadPage();
	   					}
	   					else{
		   					getUsersCards();
		   				}
		   			}
		   		}
				else{
	   				var respTxt = "SIGNATURE does NOT match - - - Sign In Denied.";
					document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
	   			}
			})
		}
		async function fetchUserRedCoinsAndToken(){
			try {
				const response = await fetch('/code/php/fetch-redcoins.php');
				const data = await response.json();

				if (data.success){
					console.log('User Data:', data);
					window.userRedCoins = data.redCoins;
					window.userToken = data.token;
				}
				else{
					console.error('Error:', data.error);
				}
			}
			catch(error){
				console.error('Fetch error:', error);
			}
		}
		async function reportProvider(){

			//Get networkName
			if (chainId == "0x89" || provider == 137){
  				networkName = "Polygon";
  			}	
			else if (chainId == "0x5" || provider == 5){
  				networkName = "Goerli";
  			}
			else if (chainId == "0xa86a" || provider == 43114){
				networkName = "Avalanche";
			}
			else if (chainId == "0x1" || provider == 1){
  				networkName = "Ethereum";
  			}
  			else if (chainId == "0x2a" || provider == 42){
  				networkName = "Kovan";
  			}
  			else if (chainId == "0x4" || provider == 4){
  				networkName = "Rinkeby";
  			}
  			else if (chainId == "0xa4b1" || provider == 42161){
  				networkName = "Arbitrum";
  			}
  			else if (chainId == "0x66eed" || provider == 421613){
  				networkName = "ArbiGoerli";
  			}
  			else if (chainId == "0xa869" || provider == 43113){
  				networkName = "Ava Fuji";
  			}  			
  			else if (window.ethereum){
  		 		chainId = window.ethereum.chainId;
  		 		networkName = "Ethereum?";
			}
  			else{
  				networkName = "unhandled network";
  			}
  			
  			//console.log('User is on ' + networkName + ' with ID number ' + provider + ' and chainid ' + chainId + '.');
  			
  			//This is the preferred chain
  			if (chainId == preferredNetworkChainID || provider == preferredProviderNumber){
  				onPreferredNetwork = true;
  				if (signedInToServerAs != "false"){
					if (inGame){
						letsDoThis();
					}
				}
				else{
					if (inGame){
						var cTxt = `You must sign in to verify that you control this wallet. Would you like to sign in now?`;
						setConfirmation(cTxt, 4, 3);
					}
					else{
						let text;
						if (confirm("You have to sign in to the server to buy or open PACKS. Would you like to sign in now?") == true) {
							text = "Signing in...";
							signIn();
						}
						else{
							text = "Ok but you have to sign in to buy or open packs.";
						}	
					}
				}
			}	
  			else{
  				var cTxt = `You must be on the ${preferredNetwork1} to your to play this game. Would you like to switch to ${preferredNetwork1} now?`
				setConfirmation(cTxt, 4, 4);
  			}
  			
  			
		}
		async function switchNetwork(which){
			var theChainID = false;
			
			if (which == 1){
				//Polygon
				theChainID = '0x89';
				theRPCURL = 'https://polygon-rpc.com';
				nn = "polygon";
			}
			else if (which == 2){
				//AVAX
				theChainID = '0xa86a';
				theRPCURL = 'https://api.avax.network/ext/bc/C/rpc';
				nn = "avalanche";
			}
			else if (which == 3){
				//Mainnet
				theChainID = '0x1';
				theRPCURL = 'https://main-light.eth.linkpool.io/';
				nn = "ethereum";
			}
			else if (which == 4){
				//Kovan
				theChainID = '0x2a';
				theRPCURL = 'https://kovan.infura.io';
				nn = "kovan";
			}
			else if (which == 5){
				//Rinkeby
				theChainID = '0x4';
				theRPCURL = 'https://rinkeby-light.eth.linkpool.io/';
				nn = "rinkeby";
			}
			else if (which == 6){
				//Arbitrum
				theChainID = '0xa4b1';
				theRPCURL = 'https://arb1.arbitrum.io/rpc';
				nn = 'arbitrum';
			}
			else if (which == 7){
				//Goerli
				theChainID = '0x5';
				theRPCURL = 'https://goerli.infura.io/v3/';
				nn = 'goerli';
			}
			else if (which == 8){
				//Arbitrum Goerli
				theChainID = '0x66eed';
				theRPCURL = 'https://arbitrum-goerli.publicnode.com';
				nn = 'arbi goerli';
			}
			else if (which == 9){
				//Avalanche Fuji
				theChainID = '0xa869';
				theRPCURL = 'https://api.avax-test.network/ext/bc/C/rpc';
				nn = "Ava Fuji";
			}
			try {
					await window.ethereum.request({
						method: 'wallet_switchEthereumChain',
						params: [{ chainId: theChainID }],
					});
					//If On Correct Chain Now, Run the Start Checks To See If User is Signed In
					runStartChecks();
				} catch (switchError){
  				//This error code indicates that the chain has not been added to MetaMask.
				if (switchError.code == 4902){
					try {
						await window.ethereum.request({
							method: 'wallet_addEthereumChain',
							params: [{ chainId: theChainID, 
										chainName: preferredNetwork1,
										rpcUrls: [theRPCURL]
									}],
						});
						runStartChecks();
					}
					catch (addError){
						var cTxt = `You must add the ${preferredNetwork1} to your wallet to play this game. Would you like to do that now?`
						setConfirmation(cTxt, 4, 4);
					}
				}
				else if (switchError.code == -32002){
					var respTxt = `You already have a pending request to switch to the ${preferredNetwork1}. Please check your wallet.`;
					if (inGame){
						setAlert(`${respTxt}`, 4, 4);
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else if (switchError.code == 4001){
					
					var respTxt = `You rejected the request to switch to ${preferredNetwork1}. You need to be on the ${preferredNetwork1} to play this game.`;
					if (inGame){
						setAlert(`${respTxt}`, 4, 4)
					}
					else{
						document.getElementById("terminalHistory") ?  printTerminalHistory(respTxt) : alert(respTxt);
					}
				}
				else{
					try {
						await window.ethereum.request({
							method: 'wallet_addEthereumChain',
							params: [{ chainId: theChainID, 
										chainName: preferredNetwork1,
										rpcUrls: [theRPCURL]
									}],
						});
						runStartChecks();
					}
					catch (addError){
						var cTxt = `You must add the ${preferredNetwork1} to your wallet to play this game. Would you like to do that now?`
						setConfirmation(cTxt, 4, 4);
					}
				}
			}
		}
		function setWalletStatus(status){
			myWalletStatus = status;
			if (document.getElementById("walletStatus")){
				document.getElementById("walletStatus").value = status;
			}
		}
		function startApp(walletStatus){
			setWalletStatus(walletStatus);
			if (walletStatus == 0){
				if (homePage){
					setTerminalTxt(0);
					typingEffect(txt1, 'introPara');
				}
				else{
					console.log('no wallet');
				}
			}
			else if (walletStatus == 1){
				if (homePage){
					setTerminalTxt(1);
					typingEffect(txt1, 'introPara');
				}
				else{
					console.log('not connected');
				}
			}
			else if (walletStatus == 2){
				if (homePage){
					setTerminalTxt(2);
					typingEffect(txt1, 'introPara');
				}
				else{
					console.log('connected!');
				}
			}
		}
		function startGame(){
			if (myWalletStatus == 0){
				if (byPassChecks){
					letsDoThis();
				}
				else{
					var cTxt = `You need a wallet to play this game. Would you like to install a browser wallet?`;
					setConfirmation(cTxt, 4, 1);
				}
			}
			else if (myWalletStatus == 1){
				var cTxt = `You must connect your wallet to this website to play this game. Would you like to connect now?`;
				setConfirmation(cTxt, 4, 2);
			}
			else if (myWalletStatus == 2){
				runStartChecks();
			}
		}
		async function runStartChecks(){
			await connectMyWallet();
			
			if (onPreferredNetwork && signedInToServerAs != "false"){
				letsDoThis();
			}
			else if (onPreferredNetwork){
				var cTxt = `You must sign in to verify that you control this wallet. Would you like to sign in now?`;
				setConfirmation(cTxt, 4, 3);
			}
		}
		function letsDoThis(){ 
			//////We have now confirmed that the user has an account, 
			//////is connected to the website, and controls the account.
			//////Let's Do THIS!
			fetchUserRedCoinsAndToken();
			getUsersCards();
			getSavedDeckInGame(1);
			getSavedDeckInGame(2);
			getSavedDeckInGame(3);
			ig.game.confirmBox = false;
			ig.game.alertBox = false;
			ig.game.titleScreen = false;
			ig.game.playSwordSound();
			ig.game.menuScreen = true;
			ig.game.menuScreenNum = 1;
			setTimeout(playTheMusic, 500);
		}
		function playTheMusic(){
			ig.game.playMusicBro(1);
			ig.game.spawnEntity( EntityDeck, 0, 0, { num: 1 });
			ig.game.spawnEntity( EntityDeck, 0, 0, { num: 2 });
			ig.game.spawnEntity( EntityDeck, 0, 0, { num: 3 });
			ig.game.spawnEntity( EntityDeck, 0, 0, { num: 4 });
			ig.game.spawnEntity( EntityMatchmake, 0, 0);
			ig.game.spawnEntity( EntityMintredcoins, 0, 0);
			ig.game.spawnEntity( EntityPlaycpu, 0, 0);
		}
		function setAlert(txt, txtSize, myNum){
			if (!ig.game.confirmBox && !ig.game.alertBox){
				ig.game.alertBox = true;
				ig.game.alertMsg = txt;
				ig.game.spawnEntity( EntityOk, 0, 0, { num: myNum });
				ig.game.alertSize = txtSize;
			}
		}
		function setConfirmation(txt, txtSize, myNum){
			if (!ig.game.confirmBox && !ig.game.alertBox){
				ig.game.confirmMsg = txt;
				ig.game.confirmSize = txtSize;
				ig.game.confirmBox = true;
				ig.game.spawnEntity( EntityConfirm, 0, 0, { name: "cYes", num: myNum });
				ig.game.spawnEntity( EntityConfirm, 0, 0, { name: "cNo", num: myNum });
			}
		}
		function setChainListeners(){
			//Listen for account and network changes
			window.ethereum.on('accountsChanged', function (accounts){
				console.log('accountChanged');
				if (!inGame){
					window.location.reload();
				}
			})

			window.ethereum.on('chainChanged', function (chainId){
				console.log('chainChanged');
				if (!inGame){
					//Reload Page
					location.reload();
				}
			})
		}
		//Find Wallet Status
		async function checkWalletStatus(){
			const accounts = await ethereum.request({method: 'eth_accounts'});
			if (window.ethereum){
				if (accounts.length){
					setWalletStatus(2);
				}
				else{
					setWalletStatus(1);
				}
			}
			else{
				setWalletStatus(0);
			}
			//console.log('wallet status check = ' + myWalletStatus)
		}
		//Check if account is connected...
		async function isConnected(){
			const accounts = await ethereum.request({method: 'eth_accounts'});	   
			if (accounts.length){
				account = accounts[0];
				window['userAccountNumber'] = account;
				startApp(2);
			}
			else{
				console.log("Wallet is not connected");
				startApp(1);
			}
			setChainListeners();
		}
		
		/**********************
		//This function is called 
		at load time to determine 
		wallet status of user 
		**********************/
		
		window.onload = (event) => {
			if (window.ethereum){
				isConnected();
			}
			else{
				//console.log('no wallet');
				startApp(0);
			}
		};
	</script>