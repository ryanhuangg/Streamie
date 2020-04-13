<?php
	session_save_path("sess");
	session_start(); 

?>
<!DOCTYPE html>
<html lang="en">
		<head>
		<meta charset="utf-8">
		<title>Register</title>
		</head> 

		<body> 
			<main>
				<h1>Register</h1>
				<form action="#" method="post">
				<legend>Register</legend>
				<table>
					<!-- Trick below to re-fill the user form field -->
					<!-- dropdown -->
					<form action="/action_page.php">

					<tr><th><label for="title">Your title</label></th><td>
					<select name ="title">
										<option value="---">---</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Miss">Miss</option>
                                    
                                        <option value="Prince">Prince</option>
                                        <option value="Princess">Princess</option>
                                        <option value="King">King</option>
                                        <option value="Queen">Queen</option>

					<td></tr>

					</form>
					
					<tr><th><label for="gender">Gender</label></th><td>
					<input type="radio" name="gender" value="Male"> Male 
					<input type="radio" name="gender" value="Female"> Female 
					<input type="radio" name="gender" value="Other"> Other <br>
					<input type="radio" name="gender" value="Monster"> Monster <br>
					<input type="radio" name="gender" value="Elf"> Elf <br>
					

					<td></tr>

					<td></tr>
					<tr><th><label for="name">*Your Full Name</label></th><td>
						<input type="text" name="name" /></td></tr>

					<tr><th><label for="UserId">*UserId</label></th><td>
						<input type="text" name="UserId" /></td></tr>

					<tr><th><label for="new_password">*Password</label></th><td> 
						<input type="password" name="new_password" /></td></tr>

					<tr><th>
						<form method="POST" action="login.html">
						<input type="submit" name="back" value="back" />
						</form>
					</th><td>
						<input type="submit" name="register" value="register" />
					</td></tr>
				
				</table>
			</form>

<?php
error_reporting(E_ERROR | E_PARSE);
if( !empty($_POST["UserId"]) && !empty($_POST["new_password"]) && !empty($_POST["name"]) ){

			///this is prepareing for the database

			$UserId =$_POST['UserId'];
			$name = $_POST['name'];
			$new_password = $_POST['new_password'];
			$title = $_POST['title'];
			$gender = $_POST['gender'];
			//$data = $_POST['Register_data'];
			$fp = fopen('Register_data', 'a');
			$string = $name.",".$UserId.",".$new_password.",".$title.",".$gender.","."\n";

			//fwrite($fp, $data);
			fputs($fp, $string);

			fclose($fp);

			/// this is just a temp method for storing login data and register data
			$file = fopen('users.txt', 'a+');
			$register_user = $UserId."|".$new_password."\n";
			fputs($file, $register_user);
			fclose($file);

			echo "You have Register!!!";

   }else{
   	echo "Please Full In the * Information such as Name, User Id, and Password";
   }


?>
		</main>
		</body>
</html>