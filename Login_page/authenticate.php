<?php
error_reporting(E_ERROR | E_PARSE);
if(isset($_POST["username"]) && isset($_POST["password"])){
    $file = fopen('users.txt', 'r');
    $good=false;
    while(!feof($file)){
		$line = fgets($file);

		list($user, $pass) = explode('|', $line);

		if(trim($user) == $_POST['username'] && trim($pass) == $_POST['password']){
			$good=true;
			break;
    }
}

    if($good){
        header("Location: homepage.php");
    }else{
        echo "<br> You have entered the wrong username or password. Please try again. <br>";
    }
    include 'message.html';
fclose($file);
}else{
    include 'login.html';
}
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
<a href="login.html">Click here to return to login page.</a>
</body>
</html>