<?php

	//require_once "db_connect_config.php";
	session_save_path("sess");
	session_start(); 

	// //$dbServername = "localhost";
	$dbServername = "192.168.2.88";
	$dbUsername = "streamie-db";
	$dbPassword = "kM514Sl8AgIR3jTc";
	$dbName = "csc301_db";
	
	$dbconn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

	if(!$dbconn){
		die(" db Connection failed: " . mysqli_connect_error());

		echo " failed <br> ";
	}else{
		echo " connected successfully <br> ";
		echo " <br> Starting Logging: <br> ";
	}

	//helper that use to check if $check_rid already exist in the $tab in the table
	//to do

	//echo "1 check dup <br>";


	function check_dup_uid($check_rid, $check_uid){
		global $dbconn;
		echo "check dup <br>";

		$query = "SELECT rid, uid FROM Streamie_User WHERE rid = ? and uid = ?";

		if($stmt = mysqli_prepare($dbconn, $query)){

			echo "<br> prepare check dup <br>";
			mysqli_stmt_bind_param($stmt,'is',$rid, $uid);
			echo "bind check dup uid <br>";
			$rid = $check_rid;
			$uid = $check_uid;

			if(mysqli_stmt_execute($stmt)){
				echo "excute check dup <br>";
				mysqli_stmt_store_result($stmt);

				if(mysqli_stmt_num_rows($stmt) > 1){
					$uid_error = "This username is already taken.";
					echo $uid_error;
					echo "check_dup rid: .$rid.";

					return true;
				}else{
					echo "1 no dup <br>";
					return false;
				}
			}

			mysqli_stmt_close($stmt);
		}
	}


	//check exist friend list, return true if there is already exist friend list
	//echo "1 check friend list <br>";
	function check_friendlist($check_rid,$check_uid){
		global $dbconn;
		echo "<br> checking for friendlist<br>";

		$query = "SELECT rid, uid FROM User_FriendList WHERE rid = ? and uid = ?";

		//$stmt = mysqli_stmt_init($dbconn);

		if($stmt = mysqli_prepare($dbconn, $query)){
			echo "check friend list <br>";

			mysqli_stmt_bind_param($stmt,'is',$rid, $uid);
			echo "bind friend list <br>";
			$uid = $check_uid;
			$rid = $check_rid;

			if(mysqli_stmt_execute($stmt)){
				echo "excute friend list <br>";
				mysqli_stmt_store_result($stmt);

				if(mysqli_stmt_num_rows($stmt) > 1){
					return true;
				}else{
					return false;
				}
			}
			mysqli_stmt_close($stmt);
		}
	}


	//set friend list, return true if able to set friend list 
	//echo "1 set friend list <br>";
	function set_friendlist($check_rid,$check_uid){
		global $dbconn;

		echo "<br> setting friend list <br>";
		$friendlist = "";

		$query = "INSERT INTO User_FriendList (rid, uid, friend_list) VALUES ('".$check_rid."','".$check_uid."', '".$friendlist."')";

		//$stmt = mysqli_stmt_init($dbconn);

		if($stmt = mysqli_prepare($dbconn, $query)){
			echo "prepare set friend list <br>";
			
			// binding parameter doesnt work dont know why
			// just use the direct inject above
			// mysqli_stmt_bind_param($stmt,"iss", $rid, $uid);

			// echo "bind set friend list <br>";

			// $uid = $check_uid;
			// $rid = $check_rid;
			// $add_friendlist = $friendlist;

			if(mysqli_stmt_execute($stmt)){
				echo "excute setting friend list <br>";
				mysqli_stmt_store_result($stmt);

				if(mysqli_stmt_affected_rows($stmt) > 0){

					//echo "excute set friend list sucess <br";
					return true;

				}else{
					//echo "excute set friend list failed <br";
					return false;
				}
			}

			mysqli_stmt_close($stmt);
		}

	}




	error_reporting(E_ERROR | E_PARSE);

	if( !empty($_POST["UserId"]) && !empty($_POST["new_password"]) && !empty($_POST["name"]) ){

				///this is prepareing for the database

				$reply_msg = $_POST['reply_msg'];

				$UserId =$_POST['UserId'];
				$name = $_POST['name'];
				$new_password = $_POST['new_password'];

				$title = $_POST['title'];
				$gender = $_POST['gender'];
				//$data = $_POST['Register_data'];
				$rid = rand(1000000, 9999999);


				// if the gender is not filled its empty
				if(empty($_POST['gender'])){
					$gender = "";
				}

				//if gender is not filled set it

				// if the uid and rid does not have duplicate, create a new acc
				if(check_dup_uid($rid, $UserId) == false){
					echo "no dup <br>";

					//method 1 directly injecting parameter

					//prepare query
				   //$query = "INSERT INTO Streamie_User (rid, uid, fullname, gender, title, pass) VALUES ('".$rid."', '".$UserId."', '".$name."', '".$gender."','".$title."','".$new_password."')";

					//$stmt = mysqli_prepare($dbconn, $query);

					//echo "prepare db <br>";

					////////////////////////////////////////

					//method 2 bind parameter later on

					$query = "INSERT INTO Streamie_User (rid, uid, fullname, gender, title, pass) VALUES (?,?,?,?,?,?)";

   

					if(($stmt = mysqli_prepare($dbconn, $query)) == false){
						echo "cannot prepare for new user <br>";
					}else{
						echo "prepare db <br>";
					}

					if(mysqli_stmt_bind_param($stmt, 'isssss', $q_rid, $q_UserId, $q_name, $q_gender, $q_title, $q_new_password) == false){
						echo "bind failed <br>". mysqli_stmt_error($stmt) ;
					}else{
						echo "binded param <br>";
					}

					$q_rid = $rid;
					$q_UserId = $UserId;
					$q_name = $name;
					$q_gender = $gender;
					$q_title = $title;
					$q_new_password = $new_password;

					//printf("Error: %s.\n", mysqli_stmt_error($stmt));

					//echo "prepare db <br>";

					if(mysqli_stmt_execute($stmt) == true){

						echo "excute db <br>";

						echo "New user records created successfully <br>";
						mysqli_stmt_close($stmt);

						//check friendlist
                        if (check_friendlist($rid, $UserId) == false) {
							echo "user friendlist is not yet exist, creating one <br>";
                
							if(set_friendlist($rid,$UserId) == true){
								echo "user friendlist is set <br>";
							}else{
								echo "user friendlist is not set <br>";
							}

						}else{
							echo "check friendlist is already exist <br>";
						}

						$reply_msg = "You have Register!!! on our database <br>" ;
					}

					//mysqli_stmt_close($stmt);

					mysqli_close($dbconn);

				}else{
					echo "there is a dup <br>";

					$reply_msg = "The user id already exist, please input a new one <br>";

					mysqli_stmt_close($stmt);

					mysqli_close($dbconn);
				}


				// also register the user in file system
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

				//echo "You have Register!!!";



				$reply_msg = "You have Register!!!" ;




	}else{
		//echo "Please Full In the * Information such as Name, User Id, and Password";
		$reply_msg = "Please Full In the * Information such as Name, User Id, and Password";
	}


?>

<link rel="stylesheet" href="register.css">
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div class="outer">
		<div class="middle">
			<div class ="inner">
				<div class="register">
					<div style="text-align:center;">

					<from action = "Register.php" method = "POST">
					<!-- <input type='text' name='reply_msg' value='<?php //echo "$reply_msg";?>'/>  -->
						<?php
						echo $reply_msg;
						?>
					</from>
					<br>
					<a href="login.html">Click here to return to login page.</a>
					<br>
					<a href="Register.html">Click here to return to Register page.</a>
					</div>
				</div>
			</div>
		</div>
	</div>

				
</body>
</html>

