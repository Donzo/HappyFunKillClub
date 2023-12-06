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
			//Card 6
			if (deck[index].c6 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Agent Mason", "Id": "c6", "dir": "/cards/characters/agent-mason/", "type": "character", "Quantity": deck[0].c6};
				myCardNumber++;
			}
			//Card 7
			if (deck[index].c7 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Xyrex Nebulae", "Id": "c7", "dir": "/cards/characters/xyrex-nebulae/", "type": "character", "Quantity": deck[0].c7};
				myCardNumber++;
			}
			//Card 8
			if (deck[index].c8 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Necrocleric Malachor", "Id": "c8", "dir": "/cards/characters/necrocleric-malachor/", "type": "character", "Quantity": deck[0].c8};
				myCardNumber++;
			}
			//Card 9
			if (deck[index].c9 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Lyana Greenmantle", "Id": "c9", "dir": "/cards/characters/lyana-greenmantle/", "type": "character", "Quantity": deck[0].c9};
				myCardNumber++;
			}
			//Card 10
			if (deck[index].c10 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Solace Etherbound", "Id": "c10", "dir": "/cards/characters/solace-etherbound/", "type": "character", "Quantity": deck[0].c10};
				myCardNumber++;
			}
			//Card 11
			if (deck[index].c11 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Sierra \'Sightline\' Kestrel", "Id": "c11", "dir": "/cards/characters/sierra-sightline-kestrel/", "type": "character", "Quantity": deck[0].c11};
				myCardNumber++;
			}
			//Card 12
			if (deck[index].c12 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Ragnar Vane", "Id": "c12", "dir": "/cards/characters/ragnar-vane/", "type": "character", "Quantity": deck[0].c12};
				myCardNumber++;
			}
			//Card 13
			if (deck[index].c13 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Callow Skyshriek", "Id": "c13", "dir": "/cards/characters/callow-skyshriek/", "type": "character", "Quantity": deck[0].c13};
				myCardNumber++;
			}
			//Card 14
			if (deck[index].c14 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Sir Mortan The Undying", "Id": "c14", "dir": "/cards/characters/sir-mortan-the-undying/", "type": "character", "Quantity": deck[0].c14};
				myCardNumber++;
			}
			//Card 15
			if (deck[index].c15 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Zhan Shen", "Id": "c15", "dir": "/cards/characters/zhan-shen/", "type": "character", "Quantity": deck[0].c15};
				myCardNumber++;
			}
			//Card 16
			if (deck[index].c16 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Tukkuk Nanook", "Id": "c16", "dir": "/cards/characters/tukkuk-nanook/", "type": "character", "Quantity": deck[0].c16};
				myCardNumber++;
			}
			//Card 17
			if (deck[index].c17 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Mycelius Rex", "Id": "c17", "dir": "/cards/characters/mycelius-rex/", "type": "character", "Quantity": deck[0].c17};
				myCardNumber++;
			}
			//Card 18
			if (deck[index].c18 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Eron Hushblade", "Id": "c18", "dir": "/cards/characters/eron-hushblade/", "type": "character", "Quantity": deck[0].c18};
				myCardNumber++;
			}
			//Card 19
			if (deck[index].c19 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Lorien Spectrum", "Id": "c19", "dir": "/cards/characters/lorien-spectrum/", "type": "character", "Quantity": deck[0].c19};
				myCardNumber++;
			}
			//Card 20
			if (deck[index].c20 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Frankie Stubbs", "Id": "c20", "dir": "/cards/characters/frankie-stubbs/", "type": "character", "Quantity": deck[0].c20};
				myCardNumber++;
			}
			//Card 21
			if (deck[index].c21 > 0){
				myArrayOfCards[myCardNumber] = { "name": "John \'Riptide\' McTavish", "Id": "c21", "dir": "/cards/characters/john-riptide-mctavish/", "type": "character", "Quantity": deck[0].c21};
				myCardNumber++;
			}			
			//Card 22
			if (deck[index].c22 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Kaelo Vex", "Id": "c22", "dir": "/cards/characters/kaelo-vex/", "type": "character", "Quantity": deck[0].c22};
				myCardNumber++;
			}
			//Card 23
			if (deck[index].c23 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Valor Wildsong", "Id": "c23", "dir": "/cards/characters/valor-wildsong/", "type": "character", "Quantity": deck[0].c23};
				myCardNumber++;
			}
			//Card 24
			if (deck[index].c24 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Ursaon Ironpelt", "Id": "c24", "dir": "/cards/characters/ursaon-ironpelt/", "type": "character", "Quantity": deck[0].c24};
				myCardNumber++;
			}
			//Card 25
			if (deck[index].c25 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Thump The Ripper", "Id": "c25", "dir": "/cards/characters/thump-the-ripper/", "type": "character", "Quantity": deck[0].c25};
				myCardNumber++;
			}
			//Card 26
			if (deck[index].c26 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Azuron The Starweaver", "Id": "c26", "dir": "/cards/characters/azuron-the-starweaver/", "type": "character", "Quantity": deck[0].c26};
				myCardNumber++;
			}
			//Card 27
			if (deck[index].c27 > 0){
				myArrayOfCards[myCardNumber] = { "name": "Mancala Naga", "Id": "c27", "dir": "/cards/characters/mancala-naga/", "type": "character", "Quantity": deck[0].c27};
				myCardNumber++;
			}
			return myArrayOfCards;
		}
	</script>
	
	