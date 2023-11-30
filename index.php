<?php
	session_start();
	
	//Set Terminal Session Variables
	$_SESSION['tDir'] = "home";
	$_SESSION["fs1"] = "<span class='terminal-programs'>Buy&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
	$_SESSION["fs2"] = "<span class='terminal-programs'>Play&nbsp;Game &nbsp; &nbsp; &nbsp;</span>";
	$_SESSION["fs3"] = "<span class='terminal-programs'>Create&nbsp;Deck &nbsp; &nbsp; &nbsp;</span>";
	$_SESSION["fs4"] = "<span class='terminal-dir'>View&nbsp;All&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
	$_SESSION["fs5"] = "<span class='terminal-programs'>Battle&nbsp;Simulator &nbsp; &nbsp; &nbsp;</span>";
	$_SESSION["promptNum"] = 1;
	
?>
<html>
<head>
	<?php include $_SERVER['DOCUMENT_ROOT'] . "/code/html/metatags.php"; ?>
	
	<title>Happy Fun Kill Club</title>
	
	<!-- CSS -->
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/style.php"; ?>
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/terminal-style.php"; ?>
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/buy-cards.php"; ?>
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/deck-builder.php"; ?>
	
	<!-- JS -->
	<script>
		var homePage = true;
		var inGame = false;
		
		var signedInToServerAs = "<?php if ($_SESSION['account']){echo $_SESSION['account'];}else{ echo 'false';}?>"
		console.log("signed into server as: " + signedInToServerAs);
		
		var queryString = window.location.search;
		var urlParams = new URLSearchParams(queryString);
		var deckBuilderMode = urlParams.get('buildDeck');
		var skipPrompt = urlParams.get('skipPrompt');
	</script>
	
	<script>
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/web3/web3.min.js";
		?>
	</script>
	
	<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/js.php";
	?>
	
</head>
<body>
	<div id="header">
		<h1>Happy Fun Kill Club</h1>
	</div>
	<div id="content-frame">
		<div id="content">
			<?php 
				include $_SERVER['DOCUMENT_ROOT'] . "/code/html/terminal.php"; //Standard
				include $_SERVER['DOCUMENT_ROOT'] . "/code/html/deck-builder-div.php"; //Deck Builder
				include $_SERVER['DOCUMENT_ROOT'] . "/code/html/tos-purchase.php"; //Buy Cards 2
			?>
			<div id='cpanel-buttons-div'>
			 &nbsp;
			</div>
			
		</div>
	</div>
</body>
<footer>
	<?php 
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/terminal.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/set-card-data.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/signin.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/buy-cards.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/build-deck.php";
	?>
</footer>
</html>

	