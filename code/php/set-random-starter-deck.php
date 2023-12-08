<?php
	session_start();

	function getRandomStarterDeck() {
		//Define a few preset starter decks
		$starterDecks = [
			['c1', 'c2', 'c3'], // Random deck 1
			['c4', 'c5', 'c1'], // Random deck 2
			['c6', 'c2', 'c7'], // Random deck 3
			['c4', 'c3', 'c8'], // Random deck 4
			['c9', 'c10', 'c5'], // Random deck 5
			['c2', 'c11', 'c7'], // Random deck 6
			['c3', 'c9', 'c8'], // Random deck 7
			['c2', 'c7', 'c9'], // Random deck 8
        ];
        
		// Randomly select one of the preset decks
		return $starterDecks[array_rand($starterDecks)];
	}

	header("Content-Type: application/json");

	// If the player doesn't have a saved deck, assign them a random starter deck
	if (!isset($_SESSION['deck'])) {
		$_SESSION['deck'] = implode(',', getRandomStarterDeck());
		echo json_encode(['success' => true, 'message' => 'Random starter deck assigned']);
	}
	else{
		echo json_encode(['success' => false, 'message' => 'Player already has a deck']);
	}
?>