<script>
		//NFT MINTER ADDRESS
		var nftContract = '0x5CDFa54dAA24ec9C19B8B8Af8Cb0EF5Ee8d78A73';	
		
		var myNFTs = [];
		var myNFTURIs = [];
		var myNFTNames = [];
		var selectedCards = [];
		var cardsInDeck = [];
		var cardsInHand = [];
		var cardsPlayed = [];
		var unitsOnBoard = [];
		var charactersOnBoard = [];
		var cardsDiscarded = [];
		var spliceThese = [];
		
		async function synchronizeMyDeck(){
			//getNFTsInWallet();
			askServerToGetNFTsInWallet();
			document.getElementById("loading-wheel-div-02").className = "";
		}
		async function askServerToGetNFTsInWallet() {
			document.getElementById("sync-message").innerHTML = `Getting NFTs in Wallet #${window['userAccountNumber']}`;
			fetch('/code/php/verify-cards-in-wallet-01.php')
				.then(response => response.json())
				.then(data => {
				if (data.error) {
					console.error('Error:', data.error);
					document.getElementById("sync-message").innerHTML = `ERROR ${data.error}`;
				}
				else{
					console.log('Total Card Count:', data.cardCount);
					if (data.cardCount > 1){
						askServerToGetCardTokenIDs();
					}
				}
			})
			.catch((error) => {
				console.error('Error fetching card count:', error);
				document.getElementById("loading-wheel-div-02").className = "hide";
			});
		}
		async function askServerToGetCardTokenIDs() {
			document.getElementById("sync-message").innerHTML = `Getting Token IDs in Wallet #${window['userAccountNumber']}`;
			fetch('/code/php/verify-cards-in-wallet-02.php')
				.then(response => response.json())
				.then(data => {
				if (data.error) {
					console.error('Error:', data.error);
					document.getElementById("sync-message").innerHTML = `ERROR ${data.error}`;
					document.getElementById("loading-wheel-div-02").className = "hide";
				}
				else{
					console.log('Card IDS:', data.cardIDs);
					if (data.cardIDs.length > 0){
						console.log(data.cardIDs);
						convertTokenIDsIntoURIs();
					}
				}
			})
			.catch((error) => {
				console.error('Error fetching card count:', error);
			});
		}
		async function convertTokenIDsIntoURIs(){
			document.getElementById("sync-message").innerHTML = `Coverting Token IDs Into URIs...`;
			fetch('/code/php/verify-cards-in-wallet-03.php')
				.then(response => response.json())
				.then(data => {
				if (data.error) {
					console.error('Error:', data.error);
					document.getElementById("loading-wheel-div-02").className = "hide";
					document.getElementById("sync-message").innerHTML = `ERROR ${data.error}`;
				}
				else{
					console.log('Card IDS:', data.cardURIs);
					if (data.cardURIs.length > 1){
						console.log(data.cardURIs);
						convertURIsAndStoreCards();
					}
				}
			})
			.catch((error) => {
				console.error('Error fetching Token URIs:', error);
			});
		}
		async function convertURIsAndStoreCards(){
			document.getElementById("sync-message").innerHTML = `Matching URIs to Cards and Storing In Database...`;
			fetch('/code/php/verify-cards-in-wallet-04.php')
				.then(response => response.json())
				.then(data => {
				if (data.error) {
					console.error('Error:', data.error);
				}
				else{
					//Success!
					document.getElementById("loading-wheel-div-02").className = "hide";
					document.getElementById("sync-message").innerHTML = `Cards Synchronized.`;
					console.log('My Card Data Blob:', data);
					getUsersCards();
				}
			})
			.catch((error) => {
				console.error('Error fetching Token URIs:', error);
			});
		}
		async function getNFTsInWallet(){
			
			let web3 = new Web3(Web3.givenProvider);
			document.getElementById("sync-message").innerHTML = `Getting NFTs in Wallet #${window['userAccountNumber']}`;
			var contract = new web3.eth.Contract(abi5, nftContract, {});
			const balance = await contract.methods.balanceOf(window['userAccountNumber']).call();
			console.log(balance);
			document.getElementById("sync-message").innerHTML = `Wallet #${window['userAccountNumber']} has ${balance} cards.`;
			
			for (let i = 0; i < balance; i++) {
				let nftTokenID = await contract.methods.tokenOfOwnerByIndex(window['userAccountNumber'], i).call();
				myNFTs.push(nftTokenID);
				document.getElementById("sync-message").innerHTML = `I have ${i} Happy Fun Kill Club NFT Cards in my wallet...`;
			}
			if (myNFTs.length > 0){
				console.log('I have ' + myNFTs.length + ' nfts...');
				getURIs();
			}
			else{
				console.log('I have 0 nfts...');
			}	
		}
		function convertURIToCardName(URL){
			var obj = "";
			//if (URL == 'https://www.romecardgame.com/character-cards/01-king-romulus-the-founder.json' || URL == 'https://romecardgame.com/character-cards/01-king-romulus-the-founder.json'){
			//	obj = {name:'King Romulus the Founder', classOf:"character", selected:false, inHand: false, played: false, discarded: false, cardIMG:'https://romecardgame.com/character-cards/01-king-romulus-the-founder.jpg', characterID: false, attachedTo: false};
			//}
			console.log('URL = ' + URL);
			
			
			return obj;
		}
		
		
		function convertURIsToCardNames(){
			document.getElementById("sync-message").innerHTML = `Converting URIs to Card Names...`;
			for (let i = 0; i < myNFTURIs.length; i++) {
				var myNFTName = convertURIToCardName(myNFTURIs[i]);
				myNFTNames.push(myNFTName);
			}
			console.log('names converted');
			console.log(myNFTNames);
			
		}
		async function getURIs(){
			
			for (let i = 0; i < myNFTs.length; i++) {
				let nftTokenURI = await getNFTMetadata(myNFTs[i]);
				myNFTURIs.push(nftTokenURI);
				document.getElementById("sync-message").innerHTML = `Getting URI ${nftTokenURI}.`;
			}
			console.log(myNFTURIs);
			convertURIsToCardNames();
		}
		async function getNFTMetadata(myTokenId){
	
			let web3 = new Web3(Web3.givenProvider);
			var tokenId = myTokenId;
			var contract = new web3.eth.Contract(abi5, nftContract, {});
			const result = await contract.methods.tokenURI(tokenId).call();
			return result;
		}
</script>