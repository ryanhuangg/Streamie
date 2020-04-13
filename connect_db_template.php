
<?php
	/// this is a template of the confidential for connection the db
	// method to connect db

	$dbServername = "192.168.2.88";
	$dbUsername = "streamie-db";
	$dbPassword = "kM514Sl8AgIR3jTc";
	$dbName = "csc301_db";

	//create connection
	$dbconn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
	echo "1";
	echo "2";
		//check connection

	if(!$dbconn){
		die(" db Connection failed: " . mysqli_connect_error());
		echo ' failed ';
	}else{
		echo 'connected sucess';
	}




?>



<!DOCTYPE html>
<html>
<head>
</head>
<body>

<li> test_db_2 </li>
<br>


				
</body>
</html>