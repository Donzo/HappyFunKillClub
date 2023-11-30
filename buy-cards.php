<?php
	session_start();
?>
<html>
<head>
	<?php include $_SERVER['DOCUMENT_ROOT'] . "/code/html/metatags.php"; ?>
	
	<title>Happy Fun Kill Club</title>

	<!-- Web3 -->
	<!--script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script-->
	<script>
	<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/web3/web3.min.js";
	?>
	</script>
	<?php //require $_SERVER['DOCUMENT_ROOT'] . '/code/js/wallet-connect-02.php';?>
	
	<title>Buy Cards Now! | Happy Fun Kill Club</title>
	<style>
 		body {
  			background-color: black;
  			color: yellow;
  			font-family: 'Press Start 2P', cursive;
  			margin: 1em;
  			padding: 1em;
  			line-height: 2em;
  			font-size:.8em;
		}
		img {
			max-width: 100%;
		}
		h1 {
			text-align: center;
		}
		.row {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			width: 100%;
		}
		.column {
  			display: flex;
  			flex-direction: column;
  			flex-basis: 100%;
  			flex: 1;
  			border: 3px solid yellow;
  			padding: 1em;
  			margin: 1em;
  			font-size: .75em;
  		}
  		input, select, textarea {
			font-family: 'Press Start 2P', cursive;
			font-size: 100%;
			padding: 1em;
		}

  		input[type='radio'] { 
			transform: scale(2); 
 		}
 		.unit-field, climate-field{
 			margin: 1em;
  			padding: 1em;
 		}
  		.posture-buttons{

  		}
  		.button, .disabledbutton{
  			background-color: yellow;
			border: none;
			color: black;
			padding: 15px 32px;
			text-align: center;
			text-decoration: none;
			font-size: 2em;
			margin: 1em;
			display: flex;
			font-family: 'Press Start 2P', cursive;
  		}
  		.disabledbutton{
  			background-color: grey;
  			color: lightGrey;
  		}
  		.presetButton{
  			max-width: 10%;
  			display: inline;
  		}
  		#run-results{
  			margin: 1em;
  			padding: 1em;
  		}
  		.report-field{
  			margin: .25em;
  			margin-top: 5em;
  			padding: 1em;
  		}
  		.small-text{
  			font-size: .6em;
  		}
		.card-images-container {
			display: flex;
			flex-wrap: wrap;
			padding: 5px;
			justify-content: space-around;
			margin-top:2em;
		}
		.card-img {
			margin: 5px;
			max-width: 100%;
		}
		.card-link{
			display: inline;
			text-decoration: none;
			max-width: 10%;
		}
		#terms-of-service{
			background-color: #000000;
			color: #FFD700;
			border: 1px solid #C72C27;
			max-height: 400px; /* Adjust the height as needed */
			overflow: auto;
			padding: 2em;
			margin-bottom: 2em;
		}
		.terms-content {
			max-height: 100%;
		}
		.tosH2, .center-text{
			text-align: center;
		}
		.tosH3{
			display: inline;
		}
		#buy-button-div, #open-packs-div{
			margin-bottom: 1em;
			padding-bottom: 1em;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		#open-packs-div{
			margin-bottom: 5em;
		}
		.amountToBuyText, .users-packs-message{
			font-size: 1.5em;
			margin-bottom: .5em;
		}
		.sales-copy{
			margin-top: 2em;
			padding-top: 2em;
			line-height: 3em;
		}
		.slidecontainer {
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.slider {
			  -webkit-appearance: none;
			width: 50%;
			height: 25px;
			background: #d3d3d3;
			outline: none;
			opacity: 0.7;
			  -webkit-transition: .2s;
			transition: opacity .2s;
		}

		.slider:hover {
			opacity: 1;
		}

		.slider::-webkit-slider-thumb {
			  -webkit-appearance: none;
			appearance: none;
			width: 25px;
			height: 25px;
			background: #04AA6D;
  			cursor: pointer;
		}

		.slider::-moz-range-thumb {
			width: 25px;
			height: 25px;
			background: #04AA6D;
 			cursor: pointer;
		}
		#purchase-packs-slider-unit{
			margin-top: 4em;
		}
		#loading-wheel-div{
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size:2em;
		}
		.hide{
			display: none !important;
		}
		#confirmation-message{
			text-align: center;
			font-size:2.2em;
			margin:2em;
		}
		input[type=checkbox]{
			transform: scale(2);
			padding: 10px;
		}
		#tos-agree{
			font-size: 1.5em;
			line-height: 1.2em;
		}
		#pack-balance-div{
			margin:1em;
			background: #87CEEB;
			font-size: 2.2em;
			line-height: 1.5em;
			padding: 1em;
			color: #C72C27;
		}
		#pulled-cards-div{
			display: flex;
			flex-wrap: wrap;
			padding: 5px;
			justify-content: space-around;
			margin-top:2em;
		}
		.my-card-img{
			max-width: 100%;
			display: inline-flex;
			padding-bottom: 2em;
		}
		.flexLink{
			max-width: 20%;
			display: inline-flex;
		}
		.flexBreak {
			flex-basis: 100%;
			height: 0;
		}
		#just-pulled-hdr{
			margin-bottom: 2em;
			font-size: 2em;
			line-height: 1.5em;
		}
		#play-game-button-div{
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
		}
		#tosCheckBoxDiv{
			display: inline;
		}
		#checkboxWrapper {
			position: relative;
		}
		#checkboxOverlay {
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
		}
	</style>
	<!-- CSS -->
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/style.php"; ?>
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/terminal-style.php"; ?>
</head>
<body>
	<div id="header">
		<h1>Happy Fun Kill Club | Buy Cards Now!</h1>
	</div>
	<div id="content-frame">
		<div id="content">
		<div class="terminal space shadow" id="terminal-div">
				<div class="top">
					<div class="btns">
						<span class="circle red" onclick="tButtonPressed('red')"></span>
						<span class="circle yellow" onclick="tButtonPressed('yellow')"></span>
						<span class="circle green" onclick="tButtonPressed('green')"></span>
					</div>
					<div class="terminal-title">
					BUY CARDS NOW!	-zsh -- 80x24
					</div>
				</div>
				<div class="terminalBody" id="terminalBody">
				<div class='terminal-text' id='introPara'>
					<!-- Welcome to the Happy Kill Fun Club, Player. -->
				</div>
				<form id='terminal-form' action="javascript:void(0);" onsubmit="terminalInput()">
					<label id='tLBL' for="t1in"><span id='termUsrName'>player1@hfkc</span> ~ %:</label>
					<input style="display:inline;" type="text" id="t1in" class="tinput" name="t1in" autofocus>
					<input type="hidden" id="walletStatus" name="walletStatus" value="0">
					<input type="hidden" id="walletAddressField" name="walletAddressField" value="false">
				</form>
			</div>
		</div>
	
		<h2 class='sales-copy center-text'>Seven Random Cards in a Pack.<br/>Pack tokens cost .05 ETH.<br/>Series 1 Limited to 10,000 Packs.</h2>
	
		<div id='pack-balance-div'>
			<span id='packsLeft'>0</span> of 10,000 PACKS remaining in this sale.
		</div>
	
		<div class="card-images-container">
			<a href='https://www.romecardgame.com/character-cards/01-king-romulus-the-founder.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/01-king-romulus-the-founder.jpg" alt="Romulus Card"></a>
			<a href='https://www.romecardgame.com/character-cards/03-aeneas-of-troy.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/03-aeneas-of-troy.jpg" alt="Aeneas Card"></a>
			<a href='https://www.romecardgame.com/character-cards/04-scipio-africanus.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/04-scipio-africanus.jpg" alt="Scipio Card"></a>
			<a href='https://www.romecardgame.com/character-cards/07-appius-the-builder.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/07-appius-the-builder.jpg" alt="Appius Card"></a>
			<a href='https://www.romecardgame.com/character-cards/10-pyrrhus-of-epirus.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/10-pyrrhus-of-epirus.jpg" alt="Pyrrhus Card"></a>
			<a href='https://www.romecardgame.com/character-cards/11-brennus.jpg' target='_blank' class="card-link"><img class="card-img" src="https://www.romecardgame.com/character-cards/11-brennus.jpg" alt="Brennus Card"></a>
		</div>
	
		<h2 class='tosH2'>Terms of Service for Happy Fun Kill Club NFT Pack Sale</h2>
		<div class="terminal space shadow" id="terminal-div-02">
				<div class="top">
					<div class="btns">
						<span class="circle red" onclick="tButtonPressed('red' , 2)"></span>
						<span class="circle yellow" onclick="tButtonPressed('yellow' , 2)"></span>
						<span class="circle green" onclick="tButtonPressed('green', 2)"></span>
					</div>
					<div class="terminal-title">
					TERMS OF SERVICE -zsh -- 80x24
					</div>
				</div>
				<div class="terminalBody" id="terminalBody-03">
					<div class="terms-content" id="terms-content">
						<h3 class='tosH3'>1. Ownership and Sale of NFT Cards:</h3>
							<p>a. The NFT cards ("Cards") and packs ("Packs") being sold in the sale ("Sale") represent virtual items within the Game.</p>
							<p>b. Each Pack purchased during the Sale grants you a non-exclusive right to exchange the Pack for Cards to use within the Game.</p>
							<p>c. Ownership of the Packs is transferred to the buyer upon successful purchase and payment.</p>
							<p>d. Ownership of the Cards is transferred to the buyer upon successful conversion of a Pack token.</p>
						<h3 class='tosH3'>2. Sale Process:</h3>
							<p>a. The Sale will be conducted through a designated platform or marketplace approved by the Game's developer.</p>
							<p>b. The availability, quantity, and pricing of the Cards will be determined by the Game's developer, subject to change without notice.</p>
							<p>c. Participation in the Sale may require the creation of an account on the designated platform.</p>
							<p>d. The purchase of Packs during the Sale is subject to availability and additional terms and conditions imposed by the platform, blockchain, or marketplace hosting the Sale.</p>
							<p>e. The buyer agrees to be solely responsible for any fees, including gas or network fees imposed by the platform, blockchain, or marketplace hosting the Sale. The buyer shall cover 100% of all fees, even in the case of failed sales transactions..</p>
							<p>f. Opening or converting Packs into Cards requires separate transactions. The buyer agrees to pay ALL network fees required for such conversions. The Game's developer shall not be held liable for any fees imposed by the platform, blockchain, or marketplace during the conversion process. The buyer shall cover 100% of all fees for converting Packs into Cards, even in the case of failed or reverted transactions.</p>	
						<h3 class='tosH3'>3. Payment and Refunds:</h3>
							<p>a. Payment for the purchased Cards must be made using the designated payment methods accepted during the Sale.</p>
							<p>b. All payments are final and non-refundable, except as required by applicable law or at the sole discretion of the Game's developer.</p>
							<p>c. The Game's developer reserves the right to cancel or modify the Sale, including Card availability, pricing, or any other Sale details, at any time without liability.</p>
						<h3 class='tosH3'>4. License and Restrictions:</h3>
							<p>a. The Cards are licensed to you for personal, non-commercial use within the Game.</p>
							<p>b. Any unauthorized use, reproduction, or distribution of the Cards may result in immediate termination of your access to the Game.</p>
						<h3 class='tosH3'>5. Intellectual Property:</h3>
							<p>a. The Game, including its characters, artwork, and other related content, is protected by copyright and other intellectual property laws.</p>
							<p>b. You acknowledge and agree that all intellectual property rights in the Game and the Cards are owned by the Game's developer.</p>
							<p>c. You may not use, reproduce, or modify the Game or the Cards, in whole or in part, without prior written permission from the Game's developer.</p>	
						<h3 class='tosH3'>6. Disclaimers and Limitation of Liability:</h3>
 							<p>a. The Game's developer provides the Cards on an "as is" basis, and makes no warranties or guarantees, express or implied, regarding their functionality, availability, or performance.</p>
							<p>b. To the fullest extent permitted by law, the Game's developer shall not be liable for any direct, indirect, incidental, consequential, or special damages, including but not limited to, damages for loss of profits, data, or other intangible losses, arising out of or in connection with the purchase, use, or inability to use the Cards or the Game.</p>
							<p>c. While the Game's developer will make reasonable efforts to maintain the use of Cards and Packs for a period of 10 years after the start of the Sale, the Game's developer does not guarantee uninterrupted access or availability. The Game's developer shall not be held liable for any interruptions, disruptions, or downtime that may occur.</p>
						<h3 class='tosH3'>7. Modifications to Terms:</h3>
							<p>a. The Game's developer reserves the right to modify or update these Terms of Service at any time without notice.</p>
							<p>b. Your continued participation in the Sale or use of the Cards after any modifications to the Agreement constitutes your acceptance of the revised terms.</p>
						<h3 class='tosH3'>8. Governing Law and Jurisdiction:</h3>
							<p>a. This Agreement shall be governed by and construed in accordance with the laws of the jurisdiction where the Game's developer is located.</p>
							<p>b. Any disputes arising out of or in connection with this Agreement shall be subject to the exclusive jurisdiction of the courts in the aforementioned jurisdiction.</p>
							<p>By participating in the Sale, you acknowledge that you have read, understood, and agreed to be bound by these Terms of Service. If you do not agree with any provision of this Agreement, you should refrain from participating in the Sale or using the Cards.</p>
					</div>
				</div>
			</div>
	<span id="checkboxWrapper">
		<input type="checkbox" id="tosCheckBox" onchange="handleCheckboxChange()" disabled="disabled"/>
		<div id="checkboxOverlay" onclick="scrollTOU()"></div>
	</span>

	<label for="tosCheckBox"><span id='tos-agree'>I have read, understood, and agreed to the Terms of Service for the Happy Fun Kill Club NFT Pack Sale.</span></label>
	
	<div id='purchase-packs-slider-unit' class='hide'>
		<!-- Amount to Buy Text -->
		<div class='center-text amountToBuyText'>Buy <span id='amountOfPacks'>1</span> <span id='congegateNounPack'>Pack</span> for <span id='costOfPacks'>.1</span> ETH.</div>
		<!-- Amount to Slider -->
		<div class="slidecontainer">
			1 Pack &nbsp; <input type="range" min="1" max="10" value="1" class="slider" id="slidePacksToBuy" oninput="adjustPackAmount();"> &nbsp; 10 Packs
		</div>
		<div class='center-text'>Adjust this slider to buy more packs.</div>
	</div>
	
	<div id='loading-wheel-div' class='hide'>
		<div>
			<img src='/images/loading-wheel.gif'/>
		</div>
		<div id='waiting-msg'>
			Buying Now...
		</div>
	</div>
	<div id='confirmation-message' class='hide'>
		Thank you! Your purchase is complete.
	</div>
	
	<div id='buy-button-div'>
		<button id='buy-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO BUY PACK</button>
	</div>
	<div id='users-packs-message' class='center-text users-packs-message'>
	</div>
	<div id='open-packs-div'>
		<button id='open-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO OPEN PACK</button>
	</div>
	<div id="pulled-cards-div" class="my-cards">
	</div>
	<div id="play-game-button-div" class='hide'>
		<button id='play-game-button' class='button' onclick = 'playGame()'>Play the Game Now</button>
	</div>
</body>
<footer>
	<script>
		//connectMyWallet();
		var amountToBuy = 1;
		var costPerUnit = .1;
		var costToBuy = .1;
		var tosSCroll = 0;
		
		function enableCheckBox(){
			document.getElementById("tosCheckBox").disabled = false;
			document.getElementById("checkboxOverlay").style.display = "none";
		}
		function scrollTOU(){
			tosSCroll +=120;
			if (document.getElementById("tosCheckBox").disabled){
				document.getElementById('terminalBody-03').scroll(0, tosSCroll);
				console.log('scroll');
			}
		}
		function handleCheckboxChange() {
			var checkbox = document.getElementById("tosCheckBox");
  			var buyButtonDiv = document.getElementById("buy-button-div");
  			var ppSliderDiv = document.getElementById("purchase-packs-slider-unit");
			if (checkbox.checked) {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='button' onclick = 'buyPacks()'>BUY PACKS NOW</button>";
				ppSliderDiv.className = "";
				checkIfUserHasPacks();
			}
			else {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO BUY PACKS</button>";
				ppSliderDiv.className = "hide";
			}
		}
		function adjustPackAmount(){
			amountToBuy = document.getElementById("slidePacksToBuy").value;
			document.getElementById("amountOfPacks").innerHTML = amountToBuy;
			document.getElementById("congegateNounPack").innerHTML = amountToBuy > 1 ? "Packs" : "Pack";
			
			costToBuy = amountToBuy * costPerUnit;
			document.getElementById("costOfPacks").innerHTML = costToBuy.toFixed(1);
		}
		function playGame(){
			if (window.opener){
				window.opener.document.location.href = "https://romecardgame.com/game";
				window.close();
			}
			else{
				window.open("https://romecardgame.com/game","_blank");
			}
		}
		
		//Make Sure Terms Have Been Read
		document.addEventListener('DOMContentLoaded', function() {
		    var termsContent = document.getElementById('terminalBody-03');
			termsContent.addEventListener('scroll', function() {
				if (termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight - 100) {
					enableCheckBox();
				}
			});
		});
		
	</script>
	<?php 
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/terminal.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/signin.php";
	?>
	<script>
		var signedInToServerAs = "<?php if ($_SESSION['account']){echo $_SESSION['account'];}else{ echo 'false';}?>"
		console.log("signed into server as: " + signedInToServerAs);
	</script>
	<!-- ABI FOR WEB 3 STUFF -->
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/abi2.php'); ?>
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/abi3.php'); ?>
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/abi4.php'); ?>
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/buy-packs.php'); ?>
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/show-cards.php'); ?>
</footer>
</html>