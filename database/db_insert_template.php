
<?php
	/// this is a template of the confidential for connection the db
	// method to connect db

	$dbServername = "192.168.2.88";
	$dbUsername = "streamie-db";
	$dbPassword = "kM514Sl8AgIR3jTc";
	$dbName = "csc301_db";

	global $dbconn;

	//more info on https://www.php.net/manual/en/mysqli.prepare.php

	// we are using procedual style

	// method to connect db

	//create connection

	$dbconn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
	
	//check connection
	if(!$dbconn){
		die(" db Connection failed: " . mysqli_connect_error());

		echo ' failed <br> ';
	}else{
		echo ' connected successfully <br> ';
	}

	echo'0';

	// $query = "INSERT INTO test_guest (firstname, lastname, email) VALUES ("John", "Doe", "john@example.com")";

	// prepare and bind

	$query = "INSERT INTO shop (article, dealer, price) VALUES ('1','acd','3')";
	
	echo '1';

	$stmt = mysqli_prepare($dbconn, $query);


	// "the middle parameter means datatype in sql"
	// "i: integer, s: string, d: digit, etc"
	// mysqli_stmt_bind_param($stmt, "sss", $firstname, $lastname, $emial);

	echo '2';

	echo '3';
	
	//execute
	mysqli_stmt_execute($stmt);


	echo "New records created successfully";

	
	// Close statement

	mysqli_stmt_close($stmt);
	 
	// Close connection
	mysqli_close($dbconn);
	


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
