<?php
	function fetchDeckData($selectedDeck){
		if (!isValidDeck($selectedDeck)){
			die("invalid deck.");
		}
		$myDeck = empty($selectedDeck) ? [] : explode(',', $selectedDeck);
		return setCardData($myDeck);
	}
	function isValidDeck($deckString){
		//Check if empty
		if (empty($deckString)){
			return true;
		}
		//Check if the string matches the pattern c followed by a number and comma-separated
		if (preg_match('/^c\d+(,c\d+)*$/', $deckString)){
			return true;
		}

		return false;
	}
	function setCardData($deck){
		$myArrayOfCards = [];
    
	    //Define a mapping for card IDs to their details
		$cardDetails = [
			"c1" => ["name" => "Sir Nibblet Crossfield", "Id" => "c1", "dir" => "/cards/characters/sir-nibblet-crossfield/", "type" => "character"],
			"c2" => ["name" => "Clyde Derringer", "Id" => "c2", "dir" => "/cards/characters/clyde-derringer/", "type" => "character"],
			"c3" => ["name" => "Kira Musashi", "Id" => "c3", "dir" => "/cards/characters/kira-musashi/", "type" => "character"],
			"c4" => ["name" => "Edmund Arrowfly", "Id" => "c4", "dir" => "/cards/characters/edmund-arrowfly/", "type" => "character"],
			"c5" => ["name" => "Freyja Snowbinder", "Id" => "c5", "dir" => "/cards/characters/freyja-snowbinder/", "type" => "character"]
		];
    
		foreach ($deck as $cardId){
			if (isset($cardDetails[$cardId])){
				$myArrayOfCards[] = $cardDetails[$cardId];
    		}
		}   
		return $myArrayOfCards;
	}
?>