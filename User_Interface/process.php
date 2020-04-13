<?php
$username = $_POST['username'];
$password = $_POST['password'];

mysql_connect("localhost", "root", "");
mysql_select_db("demo");

$result = mysql_query("select * from loginform where username='$username' and password='$password'")
	or die ("Failed to fetch database ".mysql_error());
	
$row = mysql_fetch_array($result);

if ($row['username']==$username && $row['password']==$password){
	echo "Login success.";
}else{
	echo "Login unsuccessful";
}

}




?>