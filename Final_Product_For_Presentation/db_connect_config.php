<?php

	//$dbServername = "localhost";
    $dbServername = "192.168.2.88";
	$dbUsername = "streamie-db";
	$dbPassword = "kM514Sl8AgIR3jTc";
    $dbName = "csc301_db";
    
    $dbconn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

    if(!$dbconn){
        die(" db Connection failed: " . mysqli_connect_error());

        echo ' failed <br> ';
    }else{
        //echo ' connected successfully <br> ';
    }
    
?>