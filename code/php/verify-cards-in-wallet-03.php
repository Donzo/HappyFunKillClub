<?php
	session_start();

	header('Content-Type: application/json');

	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'matched' => false]);
		exit;
	}

	require_once ($_SERVER['DOCUMENT_ROOT'] . "/code/php/vendor/autoload.php");		
		
	//Set Web3
	use Web3\Web3;
	use Web3\Providers\HttpProvider;
	use Web3\RequestManagers\HttpRequestManager;
	
	
	$web3 = new Web3(new HttpProvider(new HttpRequestManager("https://avalanche-fuji.infura.io/v3/your-endpoint")));
	$eth = $web3->eth;
	
	use Web3\Contract;
		
	require_once ($_SERVER['DOCUMENT_ROOT'] . "/code/php/abi05.php");
	$contract = new Contract($web3->provider, $nftCardABI);
	$eth = $web3->eth;


	$functionName3 = "tokenURI";
	$_SESSION['myCardURIs'] = array();
		
	// call contract function
	$contractAddress = '0x5CDFa54dAA24ec9C19B8B8Af8Cb0EF5Ee8d78A73';
	$userAddress = $_SESSION['account'];
	
	$i = 0;
	$myCardCount = $_SESSION['playerCardCount'];
	$myCardIDs = $_SESSION['myCardIDs'];
	$rateLimit = 20;
	$requests = 0;
	
	while ($i < $myCardCount) {
		//Rate Limit
		$requests++;
		if ($requests >= $rateLimit){
			$requests = 0;
			sleep(1);
		}
		$contract->at($contractAddress)->call($functionName3,  $_SESSION['myCardIDs'][$i], function ($err, $result) {
			if ($err !== null) {
				echo $err;
				throw $err;
			}
			if ($result) {
				$_SESSION['myCardURIs'][] = implode($result);
				//echo implode($result);
			}
		});
		$i++;
	}
	$resp = json_encode(['success' => true, 'cardURIs' => $_SESSION['myCardURIs']]);
	echo $resp;
?>
