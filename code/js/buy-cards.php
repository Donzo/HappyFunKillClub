<script>
		//connectMyWallet();
		var amountToBuy = 1;
		var costPerUnit = .1;
		var costToBuy = .1;
		var tosSCroll = 0;
		
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
		}
		function handleCheckboxChange() {//Respond to checkbox toggles
			var checkbox = document.getElementById("tosCheckBox");
  			var buyButtonDiv = document.getElementById("buy-button-div");
  			var ppSliderDiv = document.getElementById("purchase-packs-slider-unit");
			if (checkbox.checked) {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='button' onclick = 'buyPacks()'>BUY PACKS NOW</button>";
				ppSliderDiv.className = "";
				//Once we get the smartcontracts up, let's do this ---- check for packs.
				//checkIfUserHasPacks();
			}
			else {
				buyButtonDiv.innerHTML = "<button id='buy-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO BUY PACKS</button>";
				ppSliderDiv.className = "hide";
			}
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
		
	</script>