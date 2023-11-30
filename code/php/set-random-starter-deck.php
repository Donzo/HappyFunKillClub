<?php
	session_start();

	function getRandomStarterDeck() {
		//Define a few preset starter decks
		$starterDecks = [
			['c1', 'c2', 'c3'], // Random deck 1
			['c4', 'c5', 'c1'], // Random deck 2
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