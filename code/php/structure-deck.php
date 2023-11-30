<?php
	session_start();

	if (!$_SESSION["account"]){
		die('No signed in account...');
	}

	//Check if the data comes as a JSON payload
	if ($_SERVER["CONTENT_TYPE"] == "application/json"){
		$input = json_decode(file_get_contents('php://input'), true);
		$flattenedArrayJSON = trim($input['deck'], '"');
	}
	else{ // If the data comes via form data
		$flattenedArrayJSON = trim($_POST['savedDeck'], '"');
	}

	$flattenedArray = explode(',', $flattenedArrayJSON);

	$resultArray = array(
    	"account" => $_SESSION["account"],
    	"lastChecked" => null
	);

	//Filling the result array with c1 to c150 keys with initial values of 0
	for ($i = 1; $i <= 150; $i++){
		$key = "c" . $i;
		$resultArray[$key] = 0;
	}

	//Populate the counts based on the flattenedArray
	foreach ($flattenedArray as $item){
		// Explicitly check if the key exists and is a numeric value
		if (array_key_exists($item, $resultArray) && is_numeric($resultArray[$item])){
			$resultArray[$item]++;
		}
		else{
    	    //If there's any unexpected item in flattenedArray
     	   echo "Unexpected item in flattenedArray: $item<br/>";
 	   }
	}

	$output = array($resultArray);

	//Return the result as a JSON response
	header("Content-Type: application/json");
	echo json_encode($output);
?>