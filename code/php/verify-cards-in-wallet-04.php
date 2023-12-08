<?php
	session_start();

	header('Content-Type: application/json');

	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'matched' => false]);
		exit;
	}
	$uris = $_SESSION['myCardURIs'] ?? [];

	$cardDetails = [
			"c1" => "https://happyfunkillclub.com/cards/characters/sir-nibblet-crossfield/nft.json",
			"c2" => "https://happyfunkillclub.com/cards/characters/clyde-derringer/nft.json",
			"c3" => "https://happyfunkillclub.com/cards/characters/kira-musashi/nft.json",
			"c4" => "https://happyfunkillclub.com/cards/characters/edmund-arrowfly/nft.json",
			"c5" => "https://happyfunkillclub.com/cards/characters/freyja-snowbinder/nft.json",
			"c6" => "https://happyfunkillclub.com/cards/characters/agent-mason/nft.json",
			"c7" => "https://happyfunkillclub.com/cards/characters/xyrex-nebulae/nft.json",
			"c8" => "https://happyfunkillclub.com/cards/characters/necrocleric-malachor/nft.json",
			"c9" => "https://happyfunkillclub.com/cards/characters/lyana-greenmantle/nft.json",
			"c10" => "https://happyfunkillclub.com/cards/characters/solace-etherbound/nft.json",
			"c11" => "https://happyfunkillclub.com/cards/characters/sierra-sightline-kestrel/nft.json",
			"c12" => "https://happyfunkillclub.com/cards/characters/ragnar-vane/nft.json",
			"c13" => "https://happyfunkillclub.com/cards/characters/callow-skyshriek/nft.json",
			"c14" => "https://happyfunkillclub.com/cards/characters/sir-mortan-the-undying/nft.json",
			"c15" => "https://happyfunkillclub.com/cards/characters/zhan-shen/nft.json",
			"c16" => "https://happyfunkillclub.com/cards/characters/tukkuk-nanook/nft.json",
			"c17" => "https://happyfunkillclub.com/cards/characters/mycelius-rex/nft.json",
			"c18" => "https://happyfunkillclub.com/cards/characters/eron-hushblade/nft.json",
			"c19" => "https://happyfunkillclub.com/cards/characters/lorien-spectrum/nft.json",
			"c20" => "https://happyfunkillclub.com/cards/characters/frankie-stubbs/nft.json",
			"c21" => "https://happyfunkillclub.com/cards/characters/john-riptide-mctavish/nft.json",
			"c22" => "https://happyfunkillclub.com/cards/characters/kaelo-vex/nft.json",
			"c23" => "https://happyfunkillclub.com/cards/characters/valor-wildsong/nft.json",
			"c24" => "https://happyfunkillclub.com/cards/characters/ursaon-ironpelt/nft.json",
			"c25" => "https://happyfunkillclub.com/cards/characters/thump-the-ripper/nft.json",
			"c26" => "https://happyfunkillclub.com/cards/characters/azuron-the-starweaver/nft.json",
			"c27" => "https://happyfunkillclub.com/cards/characters/mancala-naga/nft.json"
	];

	//Function to extract card ID from URI
	function getCardIdFromUri($uri, $cardDetails) {
		foreach ($cardDetails as $cardId => $cardUri) {
	        if ($uri === $cardUri) {
	            return $cardId;
	        }
	    }
	    return null;
	}

	//Count occurrences of each card
	$cardCounts = array_fill_keys(array_keys($cardDetails), 0);

	foreach ($uris as $uri) {
	    $cardId = getCardIdFromUri($uri, $cardDetails);
	    if ($cardId !== null) {
	        $cardCounts[$cardId]++;
	    }
	}
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

	try{
		//Prepare the SQL statement
		$sql = "UPDATE usersCards SET ";
		$updateParts = [];
		$params = [];

		foreach ($cardCounts as $cardId => $count) {
			//Update only cards c12 and onwards
			if (intval(substr($cardId, 1)) >= 12) {
				$updateParts[] = "$cardId = :$cardId";
				$params[$cardId] = $count;
			}
		}

		if (count($updateParts) > 0) {
			$sql .= implode(", ", $updateParts);
			$sql .= " WHERE account = :account";

			$stmt = $my_Db_Connection->prepare($sql);
			$stmt->bindParam(':account', $_SESSION['account']);

			foreach ($params as $key => &$value) {
				$stmt->bindParam(':'.$key, $value);
			}

			$stmt->execute();
			echo json_encode(['success' => 'Cards updated successfully']);
		}
		else{
			echo json_encode(['error' => 'No cards to update']);
		}
	}
	catch (PDOException $e) {
		echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
	}
	//Output the final counts
	//echo json_encode([$cardCounts]);