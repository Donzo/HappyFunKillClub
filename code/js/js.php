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