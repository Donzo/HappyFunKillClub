<script>
		function setCardData(deck, index){
			var myCardNumber = 1;
			var myArrayOfCards = [];
			//console.log(JSON.stringify(deck));
			console.log(deck[0]);
			
			//Card 1
			if (deck[index].c1 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Sir Nibblet Crossfield", "Id": "c1", "dir": "/cards/characters/sir-nibblet-crossfield/", "type": "character", "Quantity": deck[0].c1};
				myCardNumber++;
			}
			//Card 2
			if (deck[index].c2 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Clyde Derringer", "Id": "c2", "dir": "/cards/characters/clyde-derringer/", "type": "character", "Quantity": deck[0].c2};
				myCardNumber++;
			}
			//Card 3
			if (deck[index].c3 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Kira Musashi", "Id": "c3", "dir": "/cards/characters/kira-musashi/", "type": "character", "Quantity": deck[0].c3};
				myCardNumber++;
			}
			//Card 4
			if (deck[index].c4 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Edmund Arrowfly", "Id": "c4", "dir": "/cards/characters/edmund-arrowfly/", "type": "character", "Quantity": deck[0].c4};
				myCardNumber++;
			}
			//Card 5
			if (deck[index].c5 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Freyja Snowbinder", "Id": "c5", "dir": "/cards/characters/freyja-snowbinder/", "type": "character", "Quantity": deck[0].c5};
				myCardNumber++;
			}
			return myArrayOfCards;
		}
	</script>
	
	