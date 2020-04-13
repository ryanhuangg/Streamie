<?php

    require_once "db_connect_config.php";
    session_save_path("sess");
    session_start();

    global $dbconn;

    // check db connection
    if(!$dbconn){
        die(" db Connection failed: " . mysqli_connect_error());
        //echo "cant connect to server<br>" ;
    }

    //session to save user id and rid

    if(!isset($_SESSION['uid']) && !isset($_SESSION['pw'])){

        $_SESSION['uid'] = "";
        $_SESSION['rid'] = "";
        $_SESSION['pw'] = "";
        $_SESSION['fullname'] = "";
        $_SESSION["loggined"] = false;
    }


    error_reporting(E_ERROR | E_PARSE);
    if(isset($_POST["username"]) && isset($_POST["password"])){


        $uid = $_POST["username"];
        $password = $_POST["password"];

        $file = fopen('users.txt', 'r');

        //flag for checking pw on file system
        $good=false;
        //flag for checking pw on database
        $validate = false;

        //check on file system
        while(!feof($file)){
    		$line = fgets($file);

    		list($user, $pass) = explode('|', $line);

    		if(trim($user) == $_POST['username'] && trim($pass) == $_POST['password']){
                $good=true;
    			break;
            }
            
        }

        //authenticate the account on db.
        //connect to db

        $query = "SELECT uid, rid, fullname, pass, gender, title FROM Streamie_User WHERE uid = ? and pass = ?";
        //bind username
        if($stmt = mysqli_prepare($dbconn, $query)){


            mysqli_stmt_bind_param($stmt, "ss", $q_uid, $q_pass);

            $q_uid = $uid;
            $q_pass = $password;


            //excute the stmt
            if(mysqli_stmt_execute($stmt)){

                //store the result
                mysqli_stmt_store_result($stmt);

                //check if exsist this user id, if yes check pw
                if (mysqli_stmt_num_rows($stmt) >= 1) {

                    // since there is an uid and pw exit in db, validate is true
                    $validate = true;
                    
                    //bind user result variable
                    mysqli_stmt_bind_result(
                        $stmt,
                        $r_uid,
                        $r_rid,
                        $r_fullname,
                        $r_pw,
                        $r_gender,
                        $r_title
                    );

                    //if the uid and ueranem exist in database
                    $user_input_error = "Please check your username and password";
    
                    //$validate = true;

                    if (mysqli_stmt_fetch($stmt)) {
                        $_SESSION['uid'] = $r_uid;
                        $_SESSION['rid'] = $r_rid;
                        $_SESSION['pw'] = $r_pw;
                        $_SESSION['fullname'] = $r_fullname;
                        $_SESSION['gender'] = $r_gender;
                        $_SESSION['title'] = $r_title;

                        $_SESSION["loggined"] = true;
                    } else {
                        //$user_input_error = "Please check your username and password";
                        $user_input_error = '.'."fetch failed <br>";
                    }
                }
            }else{
                $uid_error = "the uid does not exit <br>";
                echo $uid_error;
            }    
        }

        mysqli_stmt_close($stmt);

        mysqli_close($dbconn);

        if($good == true && $validate == true){   
            header("location: homepage.php");
            include 'homepage.php';
            exit;
            
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