<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<body>

<?php
	// Echo session variables that were set on previous page
	echo "Session Account VAR is " . $_SESSION["account"]. ".<br>";
	print_r($_SESSION);
?>

</body>
</html>