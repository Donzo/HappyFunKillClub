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
			"c5" => ["name" => "Freyja Snowbinder", "Id" => "c5", "dir" => "/cards/characters/freyja-snowbinder/", "type" => "character"],
			"c6" => ["name" => "Agent Mason", "Id" => "c6", "dir" => "/cards/characters/agent-mason/", "type" => "character"],
			"c7" => ["name" => "Xyrex Nebulae", "Id" => "c7", "dir" => "/cards/characters/xyrex-nebulae/", "type" => "character"],
			"c8" => ["name" => "Necrocleric Malachor", "Id" => "c8", "dir" => "/cards/characters/necrocleric-malachor/", "type" => "character"],
			"c9" => ["name" => "Lyana Greenmantle", "Id" => "c9", "dir" => "/cards/characters/lyana-greenmantle/", "type" => "character"],
			"c10" => ["name" => "Solace Etherbound", "Id" => "c10", "dir" => "/cards/characters/solace-etherbound/", "type" => "character"],
			"c11" => ["name" => "Sierra 'Sightline' Kestrel", "Id" => "c11", "dir" => "/cards/characters/sierra-sightline-kestrel/", "type" => "character"],
			"c12" => ["name" => "Ragnar Vane", "Id" => "c12", "dir" => "/cards/characters/ragnar-vane/", "type" => "character"],
			"c13" => ["name" => "Callow Skyshriek", "Id" => "c13", "dir" => "/cards/characters/callow-skyshriek/", "type" => "character"],
			"c14" => ["name" => "Sir Mortan The Undying", "Id" => "c14", "dir" => "/cards/characters/sir-mortan-the-undying/", "type" => "character"],
			"c15" => ["name" => "Zhan Shen", "Id" => "c15", "dir" => "/cards/characters/zhan-shen/", "type" => "character"],
			"c16" => ["name" => "Tukkuk Nanook", "Id" => "c16", "dir" => "/cards/characters/tukkuk-nanook/", "type" => "character"],
			"c17" => ["name" => "Mycelius Rex", "Id" => "c17", "dir" => "/cards/characters/mycelius-rex/", "type" => "character"],
			"c18" => ["name" => "Eron Hushblade", "Id" => "c18", "dir" => "/cards/characters/eron-hushblade/", "type" => "character"],
			"c19" => ["name" => "Lorien Spectrum", "Id" => "c19", "dir" => "/cards/characters/lorien-spectrum/", "type" => "character"],
			"c20" => ["name" => "Frankie Stubbs", "Id" => "c20", "dir" => "/cards/characters/frankie-stubbs/", "type" => "character"],
			"c21" => ["name" => "John 'Riptide' McTavish", "Id" => "c21", "dir" => "/cards/characters/john-riptide-mctavish/", "type" => "character"],
			"c22" => ["name" => "Kaelo Vex", "Id" => "c22", "dir" => "/cards/characters/kaelo-vex/", "type" => "character"],
			"c23" => ["name" => "Valor Wildsong", "Id" => "c23", "dir" => "/cards/characters/valor-wildsong/", "type" => "character"],
			"c24" => ["name" => "Ursaon Ironpelt", "Id" => "c24", "dir" => "/cards/characters/ursaon-ironpelt/", "type" => "character"],
			"c25" => ["name" => "Thump The Ripper", "Id" => "c25", "dir" => "/cards/characters/thump-the-ripper/", "type" => "character"],
			"c26" => ["name" => "Azuron The Starweaver", "Id" => "c26", "dir" => "/cards/characters/azuron-the-starweaver/", "type" => "character"],
			"c27" => ["name" => "Mancala Naga", "Id" => "c27", "dir" => "/cards/characters/mancala-naga/", "type" => "character"],
];

		foreach ($deck as $cardId){
			if (isset($cardDetails[$cardId])){
				$myArrayOfCards[] = $cardDetails[$cardId];
			}
		}   
		return $myArrayOfCards;
	}
?>