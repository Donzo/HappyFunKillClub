<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Happy Fun Kill Club | Game</title>

	<?php include $_SERVER['DOCUMENT_ROOT'] . "/code/html/metatags.php"; ?>
	
	<style>
		body {
  			background-color: #FF69B4;
  			color: #FFFFFF;
  			font-family: 'Space Mono', monospace;
  			margin: 0;
			padding: 0;
  			line-height: 1em;
  			font-size:1em;
		}
		
		#canvas {
			position: absolute;
			left: 0;
			top: 100;
			margin: auto;
		}

		/* Tall Screen */
		@media only screen 
		  and (min-height: 1200px) { 

		}
		
	</style>
	
	<!-- JS -->
	<script>
		var homePage = false;
		var inGame = true;
		
		var signedInToServerAs = "<?php if ($_SESSION['account']){echo $_SESSION['account'];}else{ echo 'false';}?>"
		console.log("signed into server as: " + signedInToServerAs);
		
		/*Preload Title Screen Images*/
		var tsImage = new Image();
		tsImage.src = 'media/buttons-and-logos/title-screen-main.png';
		
		var conbut = new Image();
		conbut.src = 'media/buttons-and-logos/continue-button.png';
		
		var ngbut = new Image();
		ngbut.src = 'media/buttons-and-logos/new-game-button.png';
		
		var dsImage = new Image();
		dsImage.src = 'media/buttons-and-logos/deck-screen-img.png';
		
		var menuUpBut = new Image();
		menuUpBut.src = 'media/buttons-and-logos/menu-up-button.png';
		var menuUpButDisabled = new Image();
		menuUpButDisabled.src = 'media/buttons-and-logos/menu-up-button-disabled.png';
		
		var menuDownBut = new Image();
		menuDownBut.src = 'media/buttons-and-logos/menu-down-button.png';
		var menuDownButDisabled = new Image();
		menuDownButDisabled.src = 'media/buttons-and-logos/menu-down-button-disabled.png';
		
		<!-- Web3 -->
		<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/web3/web3.min.js";	?>
	</script>
	<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/js.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/game-js.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/matchmaking.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/build-deck.php";
	?>
	
	<script src="lib/impact/impact.js"></script>
	<script src="lib/game/main.js"></script>
	
	<!--Disable the Backspace Button-->
	<script type="text/javascript">
		    function killBackSpace(e) {
			   e = e ? e : window.event;
			   var t = e.target ? e.target : e.srcElement ? e.srcElement : null;
			   if (t && t.tagName && (t.type && /(password)|(text)|(file)/.test(t.type.toLowerCase())) || t.tagName.toLowerCase() == 'textarea')
				  return true;
			   var k = e.keyCode ? e.keyCode : e.which ? e.which : null;
			   if (k == 8) {
				  if (e.preventDefault)
					 e.preventDefault();
				  return false;
			   };
			   return true;
		    };
		 
		    if (typeof document.addEventListener != 'undefined')
			   document.addEventListener('keydown', killBackSpace, false);
		    else if (typeof document.attachEvent != 'undefined')
			   document.attachEvent('onkeydown', killBackSpace);
		    else {
			   if (document.onkeydown != null) {
				  var oldOnkeydown = document.onkeydown;
				  document.onkeydown = function(e) {
				  oldOnkeydown(e);
				  killBackSpace(e);
				  };
			   }
		 
			   else
				  document.onkeydown = killBackSpace;
		    }
	</script>
	
	
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	
	<meta name="description" content="Welcome to the Happy Fun Kill Club! Let the games begin.">
	<meta property="og:url" content="0000000000PUTURLHERE9999999999999999990000000000PUTURLHERE999999999999999999" />
	<meta property="og:title" content="Happy Fun Kill Club | Game" />
	<meta property="og:description" content="Welcome to the Happy Fun Kill Club! Let the games begin." /> 
	<meta property="og:image" content="0000000000PUTURLHERE9999999999999999990000000000PUTURLHERE999999999999999999" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="630" />

	<meta name="twitter:image" content="https://ereadinggames.com/images/idiom-unicorn-tw.jpg">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:creator" content="@donzomortini">
	<meta name="twitter:title" content="Happy Fun Kill Club | Game">
	<meta name="twitter:description" content="Welcome to the Happy Fun Kill Club! Let the games begin.">
	
</head>
<body>
	<canvas id="canvas"><font color="#FEFF04"><center>You are using an outdated browser. Why don't you download <a href='https://www.google.com/chrome'>Chrome</a>?</center></font></canvas>
</body>
<footer>
	<?php 
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/set-card-data.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/signin.php";
	?>
	<!-- ABI FOR WEB 3 STUFF -->
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/abi1.php'); ?>
	<!-- ABI FOR WEB 3 STUFF -->
	<?php //require_once($_SERVER['DOCUMENT_ROOT'] . '/code/js/abi2.php'); ?>
</footer>
</html>
