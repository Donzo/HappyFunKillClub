<?php
	session_start();
?>
<head>
	<!-- Space Mono -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	
	<title>Character Balancer</title>
	
	<!-- CSS -->
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/code/css/style.php"; ?>
	
	<script>
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/web3/web3.min.js";
		?>
	</script>
	
	<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/js.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/code/js/signin.php";
	?>
	
</head>
<body>

	<h1>Character Balancer</h1>

	<div class='unit-wrapper'>
	
		<div class='row'>
			<div class='column-full'>
				<h2 class='field-lbl'>Rarity</h2>
				<div class='field'>
					<input type="radio" id="common" name="rarity" value="common" onclick="calcScores()" checked>
					<label class='stat-lbl' for="common">Common</label>
					<input type="radio" id="uncommon" name="rarity" value="uncommon" onclick="calcScores()">
					<label class='stat-lbl' for="uncommon">Uncommon</label>
					<input type="radio" id="rare" name="rarity" value="rare" onclick="calcScores()">
					<label class='stat-lbl' for="rare">Rare</label>
					<input type="radio" id="veryRare" name="rarity" value="veryRare" onclick="calcScores()">
					<label class='stat-lbl' for="veryRare">Very Rare</label>
					<input type="radio" id="ultraRare" name="rarity" value="ultraRare" onclick="calcScores()">
					<label class='stat-lbl' for="ultraRare">Ultra Rare</label>
					<input type="radio" id="legendary" name="rarity" value="legendary" onclick="calcScores()">
					<label class='stat-lbl' for="legendary">Legendary</label>
				</div>
				<h2 class='field-lbl'>Character Stats</h2>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="totalCharPoints">Total Character Points:</label>
						<input type="text" size="5" id="totalCharPoints" name="totalCharPoints" value="300">
					</div>
					<div class='stat-field highlight'>
						<label class='stat-lbl' for="pointsRemaining">Points Remaining:</label>
						<input type="text" size="5" id="pointsRemaining" name="pointsRemaining" value="265">
					</div>
				</div>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="health">Health (x 1):</label>
						<input type="text" size="5" id="health" name="health" value="50" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="energy">Energy (x 10):</label>
						<input type="text" size="5" id="energy" name="energy" value="5" onchange="calcScores()">
					</div>
				</div>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="aim">Aim (x 1):</label>
						<input type="text" size="5" id="aim" name="aim" value="50" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="speed">Speed (x 1):</label>
						<input type="text" size="5" id="speed" name="speed" value="50" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="defend">Defend (x 1.5):</label>
						<input type="text" size="5" id="defend" name="defend" value="30" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="luck">Luck (x 1.5):</label>
						<input type="text" size="5" id="luck" name="luck" value="10" onchange="calcScores()">
					</div>
					<!--div class='stat-field-small'-->
				</div>
				<h2 class='field-lbl'>Base Actions</h2>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="jabDam">Jab Damage (1 energy):</label>
						<input type="text" size="5" id="jabDam" name="jabDam" value="10" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="crossDam">Cross Damage (2 energy):</label>
						<input type="text" size="5" id="crossDam" name="crossDam" value="25" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="hookDam">Hook Damage (3 energy):</label>
						<input type="text" size="5" id="hookDam" name="hookDam" value="40" onchange="calcScores()">
					</div>
				</div>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="upctDam">Uppercut Damage (4 energy):</label>
						<input type="text" size="5" id="upctDam" name="upctDam" value="60" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="hymkDam">Haymaker Damage (5 energy):</label>
						<input type="text" size="5" id="hymkDam" name="hymkDam" value="80" onchange="calcScores()">
					</div>
				</div>
				<div class='stat-fields'>
					<div class='stat-field'>
						<label class='stat-lbl' for="boost1">Boost (1 energy):</label>
						<input type="text" size="5" id="boost1" name="boost1" value="10" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="boost2">Boost (2 energy):</label>
						<input type="text" size="5" id="boost2" name="boost2" value="20" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="drain1">Drain (1 energy):</label>
						<input type="text" size="5" id="drain1" name="drain1" value="10" onchange="calcScores()">
					</div>
					<div class='stat-field'>
						<label class='stat-lbl' for="drain2">Drain (2 energy):</label>
						<input type="text" size="5" id="drain2" name="drain2" value="20" onchange="calcScores()">
					</div>
				</div>
			</div>
			
		</div>
		<button style='margin-left:5em;' class='button' onclick='location.reload()'>Reset</button>
	</div>
</body>

<footer>
	<script>
		var tCP = 300; //Total Character Points
		function calcScores(){
			setTotalCharPoints();
			
			calcPointsRemaining();
		}
		function setTotalCharPoints(){
			var rarity = document.querySelector('input[name="rarity"]:checked').value;
			if (rarity == "common"){
				tCP = 300;
			}
			else if (rarity == "uncommon"){
				tCP = 320;
			}
			else if (rarity == "rare"){
				tCP = 340;
			}
			else if (rarity == "veryRare"){
				tCP = 360;
			}
			else if (rarity == "ultraRare"){
				tCP = 380;
			}
			else if (rarity == "legendary"){
				tCP = 400;
			}
			document.getElementById("totalCharPoints").value = tCP;
		}
		function calcPointsRemaining(){
			//Health
			var health = document.getElementById("health").value * 1;
			tCP -= health;
			
			//Energy
			var energy = document.getElementById("energy").value * 10;
			tCP -= energy;
			
			//Aim
			var aim = document.getElementById("aim").value * 1;
			tCP -= aim;
			
			//Speed
			var speed = document.getElementById("speed").value * 1;
			tCP -= speed;
			
			//Defend
			var defend = document.getElementById("defend").value * 1.5;
			tCP -= defend;
			
			//Luck
			var luck = document.getElementById("luck").value * 1.5;
			tCP -= luck;
			
			//Jab
			var jabDam = document.getElementById("jabDam").value - 10;
			tCP -= jabDam;
			
			//crossDam
			var crossDam = document.getElementById("crossDam").value - 25;
			tCP -= crossDam;
			
			//hookDam
			var hookDam = document.getElementById("hookDam").value - 40;
			tCP -= hookDam;
			
			//upctDam
			var upctDam = document.getElementById("upctDam").value - 60;
			tCP -= upctDam;
			
			//hymkDam
			var hymkDam = document.getElementById("hymkDam").value - 80;
			tCP -= hymkDam;
			
			//boost1
			var boost1 = (document.getElementById("boost1").value - 10) * 2;
			tCP -= boost1;
			
			//boost2
			var boost2 = (document.getElementById("boost2").value - 20) * 1.5;
			tCP -= boost2;
			
			//drain1
			var drain1 = (document.getElementById("drain1").value - 10) * 2;
			tCP -= drain1;
			
			//drain2
			var drain2 = (document.getElementById("drain2").value - 20) * 1.5;
			tCP -= drain2;
			
			document.getElementById("pointsRemaining").value = tCP;
		}
				
		calcScores();
	</script>
	
</footer>
	