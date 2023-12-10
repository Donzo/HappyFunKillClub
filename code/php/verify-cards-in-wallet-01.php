<?php
	session_start();

	header('Content-Type: application/json');

	//Ensure the user is in a session
	if (!isset($_SESSION['account'])){
		echo json_encode(['error' => 'User not in session', 'matched' => false]);
		exit;
	}

	//Web3
	require_once ($_SERVER['DOCUMENT_ROOT'] . "/code/php/vendor/autoload.php");		
		
	//Set Web3
	use Web3\Web3;
	use Web3\Providers\HttpProvider;
	use Web3\RequestManagers\HttpRequestManager;
	
	
	
	$web3 = new Web3(new HttpProvider(new HttpRequestManager("https://avalanche-fuji.infura.io/v3/my_end_point")));
	$eth = $web3->eth;
	
	use Web3\Contract;
	
	require_once ($_SERVER['DOCUMENT_ROOT'] . "/code/php/abi05.php");
	$contract = new Contract($web3->provider, $nftCardABI);
	$eth = $web3->eth;

	$contractAddress = '0x5CDFa54dAA24ec9C19B8B8Af8Cb0EF5Ee8d78A73';
	$userAddress = $_SESSION['account'];

	$functionName1 = "balanceOf";
	$_SESSION["playerCardCount"] = 0;
	
	
	$contract->at($contractAddress)->call($functionName1, $userAddress, function ($err, $result) {
		if ($err !== null) {
			throw $err;
		}
		if ($result) {
			$myCardCount = implode($result);
			$_SESSION["playerCardCount"] =  $myCardCount;
			$resp = json_encode(['success' => true, 'cardCount' => $myCardCount]);
			echo $resp;
		}
	});
?>
