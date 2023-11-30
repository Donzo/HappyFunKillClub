<?php
		session_start();
		//Needs Session Variable Set in SIGNIN PHP file or DIE!
		if ($_SESSION["account"]){
			//GET DB CONNECTION
			require_once($_SERVER['DOCUMENT_ROOT'] . '/code/php/mysql-connect.php');

			//Look for User in User Cards Table
			$stmt = $my_Db_Connection->prepare("SELECT * FROM usersCards WHERE account=:account");
			$stmt->execute(['account' => $_SESSION["account"]]); 
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if(!$row){	//User Not Found Mint New Cards
				$stmt2 = $my_Db_Connection->prepare("INSERT IGNORE INTO usersCards (account) VALUES(:account)");
				$stmt2->bindParam(':account', $_SESSION["account"]);
				$stmt2->execute();
				
				$stmt3 = $my_Db_Connection->prepare('SELECT * FROM usersCards WHERE account=:account');
				$stmt3->execute(['account' => $_SESSION["account"]]); 
				$stmt3->execute();
				$row = $stmt3->fetchAll(PDO::FETCH_ASSOC);
				//Set Session Variables with Newly Minted Card Set
				$_SESSION["myCardsObj"] = $row;
				$_SESSION["myCardsJSON"] = json_encode($row);
				echo $_SESSION["myCardsJSON"];
			}
			else{	//User Found Use Stored Card Deck
				//Set Session Variables with Stored Card Set
				$_SESSION["myCardsObj"] = $row;
				$_SESSION["myCardsJSON"] = json_encode($row);
				echo $_SESSION["myCardsJSON"];
			}
			$my_Db_Connection = NULL;
			
		}
		else{
			die("NO SESSION DATA! User not signed in.");
		}
?>

