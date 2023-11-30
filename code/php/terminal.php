<?php
	session_start();
	
	//Log out user from session if address changes
	$walletAddress = $_POST['walletAddressField'];
	if ($walletAddress != $_SESSION['account']){
		$_SESSION['account'] = false;
	}
	
	$terminalInput = $_POST['t1in'];

	$walletStatus = $_POST['walletStatus'];
	$cleanInput = strtolower(trim($terminalInput));
	$cleanerInput = preg_replace('/[\p{P}\p{S}]/u', '', $cleanInput);
	
	$affirmativeResponse = array('y', 'yes', 'yeah', 'please', 'please do', 'of course', 'thank you very much', 'yes please', 'si', 'do please', 'i would like that very much', 'could you', 'yes of course', 'affirmative');
	$negativeResponse = array('n', 'no', 'nah', 'nay', 'no thank you', 'dont', 'no dont', 'please dont', 'negatory', 'nooooo', 'never', 'no way', 'not', 'definitely not', 'negatory');
	
	/***********************************************************
	//Terminal Input is sent in the body of FETCH requests. 
	These are then parsed by this file and responses are returned.
	***********************************************************/
	
	//Key word buy is in terminal input
	if (preg_match('/buy/i', $cleanerInput)) {
		echo "buyCards";
	}
	//FIRST PROMPT - YES RESPONSE
	else if (in_array($cleanerInput, $affirmativeResponse)){ //YES
	
		if ($_POST['buyCardsField']){
			echo "showTOS";
		}
		else if ($_POST['deckBuilderField']){
			echo "arrangeDeck";
		}
		else if ($_SESSION["promptNum"] == 1){
			if ($walletStatus == 0){ //Has no wallet but wants one.
				echo "yp1ws0";
			}
			else if ($walletStatus == 1){ //Has wallet, not connected, would like to connect.
				echo "yp1ws1";
			}
			else if ($walletStatus == 2){ //Has wallet, connected, said Y
				if ($_SESSION['account']){//IS Verified
					echo "playGame";
				}
				else{ //Is NOT verified
					echo "tryLogin";
				}
			}
			$_SESSION["promptNum"] = 2;
		}	
	}
	//FIRST PROMPT - NO RESPONSE
	else if (in_array($cleanerInput, $negativeResponse)){ //NO
		if ($_POST['buyCardsField']){
			echo "Ok, do you want to buy cards now?";
		}
		else if ($_POST['deckBuilderField']){
			echo "Ok, do you want to arrange your CARDS into a DECK now?";
		}
		else if ($_SESSION["promptNum"] == 1){
			if ($walletStatus == 0){ //Has no wallet and doesn't want one.
				echo "Ok, well, maybe with account abstraction you can play one day, but right now you need a wallet to play. Do you want to install a wallet?";//Has no wallet and doesn't want one.
			}
			else if ($walletStatus == 1){ //Has wallet, not connected, would not like to connect.
				echo "You must connect your wallet to this site to play the game. Would you like to connect your wallet to this site?";
			}
			else if ($walletStatus == 2){ //Has wallet, connected, would not like to play the game.
				echo "Ok, well, enter command 'load game' at anytime to start playing.";
				$_SESSION["promptNum"] = 2;
			}
		}
	}
	//Keyword CD is inputted
	else if (preg_match('/cd/i', $cleanerInput)) {
		if (preg_match('/View All Card/i', $cleanerInput)){
			setDirectory("allCards");
			echo "cd2";
		}
		else if (preg_match('/home/i', $cleanerInput)){
			setDirectory("home");
			echo "cd1";
		}
		else if (preg_match('/\.\.\//', $cleanerInput)){
			if ($_SESSION['tDir'] == "allCards"){
				setDirectory("home");
				echo "cd1";
			}
			else if ($_SESSION['tDir'] == "cCards" || $_SESSION['tDir'] == "eCards" || $_SESSION['tDir'] == "iCards" ){
				setDirectory("allCards");
				echo "cd2";
			}
			else{
				echo "cd: no such file or directory: "  . $terminalInput;
			}
		}
		else{
			echo "cd: no such file or directory: "  . $terminalInput;
		}
	}
	//Keyword LS is inputted
	else if (preg_match('/ls/i', $cleanerInput)) {
		echo $_SESSION["fs1"] . $_SESSION["fs2"] . $_SESSION["fs3"] . $_SESSION["fs4"] . $_SESSION["fs5"];
	}
	else if (preg_match('/game/i', $cleanerInput) || preg_match('/play/i', $cleanerInput)){
		echo "playGame";
	}
	else if (preg_match('/deck/i', $cleanerInput) || preg_match('/arrange cards/i', $cleanerInput) || preg_match('/sort/i', $cleanerInput) ){
		echo "deckBuilder";
	}
	else if (preg_match('/connect/i', $cleanerInput)){
		$_SESSION['account'] = false;
		echo "connectWallet";
	}
	else if (preg_match('/log in/i', $cleanerInput) || preg_match('/sign in/i', $cleanerInput) || preg_match('/signin/i', $cleanerInput) || preg_match('/login/i', $cleanerInput)){
		echo "tryLogin";
	}
	else if (preg_match('/log out/i', $cleanerInput) || preg_match('/sign out/i', $cleanerInput) || preg_match('/signout/i', $cleanerInput) || preg_match('/logout/i', $cleanerInput)){
		unset($_SESSION['account']);
		$_SESSION['account'] = false;
		echo "logOut";
	}
	else if (preg_match('/install wallet/i', $cleanerInput) || preg_match('/install metamask/i', $cleanerInput)){
		echo "yp1ws0";
	}
	else{
		echo "zsh: command not found: " . $terminalInput;
	}
	
	function setDirectory($which) {
		if ($which == "allCards"){
			$_SESSION['tDir'] = "allCards";
			$_SESSION['fs1'] = "<span class='terminal-dir'>Character&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION['fs2'] = "<span class='terminal-dir'>Item&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION['fs3'] = "<span class='terminal-dir'>Effect&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION['fs4'] = " &nbsp;";
			$_SESSION['fs5'] = " &nbsp;";
		}
		//Home
		else{
			$_SESSION['tDir'] = "home";
			$_SESSION["fs1"] = "<span class='terminal-programs'>Buy&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION["fs2"] = "<span class='terminal-programs'>Play&nbsp;Game &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION["fs3"] = "<span class='terminal-programs'>Create&nbsp;Deck &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION["fs4"] = "<span class='terminal-dir'>View&nbsp;All&nbsp;Cards &nbsp; &nbsp; &nbsp;</span>";
			$_SESSION["fs5"] = "<span class='terminal-programs'>Battle&nbsp;Simulator &nbsp; &nbsp; &nbsp;</span>";
		}
	} 
	
?>
