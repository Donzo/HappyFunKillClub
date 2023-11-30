<?php
	session_start();
?>
<head>
	<!-- Space Mono -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	
	<title>Battle Simulator!</title>
	
	<!-- CSS -->
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/simulator-style.php"; ?>
	
	<script>
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/web3/web3.min.js";
		?>
	</script>
	
	<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/js.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/signin.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/setCardData.php";
	?>
	
</head>
<body>

	<h1>Battle Simulator!</h1>
	<div class='signin'>
		<button onclick="signIn()">Sign In</button>
	</div>
	<div class='unit-wrapper'>
	
		<div class='row'>
			<div class='column'>
				<h2>Player 1</h2>
				
				<label for="p1CharacterCards">Choose 1-3 Character Cards:</label>
				<!-- Empty Card Selector Get Populated After Signin -->
				<select name="p1CharacterCards" id="p1CharacterCards" onChange="displayCard(1)" multiple>
					<option value="signin">SIGN IN TO SEE YOUR CARDS</option>
				</select>
				<div id="cardDisplayWrapperP1" class="cardDisplayWrapper">
					<div id="cardDisplayP1" class="cardDisplay"></div>
				</div>
				
				<button id='p1AddToDeck' class='button' type="button" onclick="addToDeck(1)" disabled>Add to Deck</button>
				
				<h2>Your Deck:</h2>
				<div id='p1Deck' class='column-full myDeckView'>
				</div>
				
				<button id='p1DumpDeck' class='button' type="button" onclick="dumpDeck(1)">Dump Deck</button>
				
			</div>

			<div class='column'>
				<h2>Player 2</h2>
				<!-- Name -->
			</div>
		</div>
	</div>
</body>
	