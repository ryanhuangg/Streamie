<?php
    //include db config file
    require_once "db_connect_config.php";
    // initialize  session
    session_save_path("sess");
    session_start();

    // initialize variable, c_pw implies current pw
    $c_uid = $_SESSION['uid'];
    $c_rid = $_SESSION['rid'];
    $c_pw = $_SESSION['pw'];
    $c_fullname = $_SESSION['fullname'];
    $c_gender = $_SESSION['gender'];
    $c_title = $_SESSION['title'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>UserProfile</title>
    <link rel="stylesheet" href="UserProfile.css">
</head>

<body>

        <div class="header">
            <h1>Streamie Home Page</h1>
        </div>
        <div class="nav">
            
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
            <a href="UserProfile.php" class="btn btn-warning">User Profile</a>
        </div>
        

    <div class="outer">
        <div class="middle">
            <div class="inner">

                    <h1>User Profile</h1>

                    <div class="status">     
                    <div class="id">Your User ID: <?php echo $c_uid; ?> </div>
                    <div class="regid">Your Registered ID: <?php echo $c_rid; ?> </div>
                    <div class="name">Name: <?php echo $c_fullname; ?> </div>
                </div>

                    <div style="text-align:center;">
                        <form method="POST" action="UserProfile.php">
                            <table>
                                <!-- Trick below to re-fill the user form field -->
                                <!-- dropdown -->
                                <tr>
                                    <th><label for="title">Your title</label></th>
                                    <td>
                                        <select name="title">
										<option value="---">---</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Miss">Miss</option>
                                    
                                        <option value="Prince">Prince</option>
                                        <option value="Princess">Princess</option>
                                        <option value="King">King</option>
                                        <option value="Queen">Queen</option>
                    </select>

                                        <td>
                                </tr>

                                <!-- </form> -->

                                <tr>
                                    <th><label for="gender">Gender</label></th>
                                    <td>
                                        <input type="radio" name="gender" value="Male"> Male
                                        <input type="radio" name="gender" value="Female"> Female
                                        <input type="radio" name="gender" value="Other"> Other <br>
                                        <input type="radio" name="gender" value="Monster"> Monster <br>
                                        <input type="radio" name="gender" value="Elf"> Elf <br>


                                        <td>
                                </tr>

                                <td>
                                    </tr>
                                    <tr>
                                        <th><label for="name">*Your Full Name</label></th>
                                        <td>
                                            <input type="text" name="name" placeholder="Username" /></td>
                                    </tr>

                                    <tr>
                                        <th><label for="new_password">Password</label></th>
                                        <td>
                                            <input type="password" name="new_password" placeholder="Password" /></td>
                                    </tr>

                                    <tr>
                                        <th><label for="confirm_password">Confirm Password</label></th>
                                        <td>
                                            <input type="password" name="confirm_password" placeholder="Password" /></td>
                                    </tr>



                            </table>
                            <tr>
                                <th>
                                    <input type="submit" name="update" value="update" class="btn_sub"/>
                                </th>
                                
                            </tr>
                        </form>
                        <tr>
                            <th>

                                <a href="homepage.php" class="btn_sub1" name="back"> Back </a>
                            </th>
                        </tr>

                    </div>
                    <li>
                        Please Fill In the Information Need To Be Change!
                    </li>
                </div>
            </div>
        </div>
    </div>

</body>

</html>


<?php
    // check if user loggin, if not redirect back to login page

    //the user could only change:
    //password
    //fullname
    //gender
    //title

    $change_profile_error ="";
    
    error_reporting(E_ERROR | E_PARSE);

    //set new profile, return true if able to set new profile
    //change_(variable): bool means if the user want to title
    //change_title = true, if not = false

    
    function change_user_profile($check_rid,$check_uid,$new_password,
        $new_fullname, $new_gender, $new_title,
        $change_pw, $change_fullname, $change_gender, $change_title){

        global $dbconn, $c_uid, $c_rid, $c_pw, $c_fullname, $c_gender, $c_title;

        $c_rid = $check_rid;
        $c_uid = $check_uid;
        

		$friendlist = "";

		$query = "UPDATE Streamie_User SET pass = ?, fullname = ?, gender = ?, title = ? WHERE rid = ? and uid = ?";

		//$stmt = mysqli_stmt_init($dbconn);

		if($stmt = mysqli_prepare($dbconn, $query)){
			
            mysqli_stmt_bind_param($stmt, 'ssssis', $q_newpw, $q_fullname, $q_gender, $q_title,
            $q_rid, $q_uid);

            $q_uid = $check_uid;
            $q_rid = $check_rid;

            $flag = false;
            if($flag == false){
                //set new pw
                if($change_pw == true){

                    $q_newpw = $new_password;
                }else{

                    $q_newpw = $c_pw;
                }

                //set new fullname
                if($change_fullname == true){

                    $q_fullname = $new_fullname;
                }else{

                    $q_fullname = $c_fullname;
                }

                //set new gender
                if($change_gender == true){

                    $q_gender = $new_gender;
                }else{

                    $q_gender = $c_gender;
                }

                //set new title
                if($change_title == true){

                    $q_title = $new_title;
                }else{

                    $q_title = $c_title;
                }

                $flag = true;
            }

            if($flag == true){
                if(mysqli_stmt_execute($stmt)){

                    mysqli_stmt_store_result($stmt);

                    if(mysqli_stmt_affected_rows($stmt) > 0){

                        return true;

                    }else{

                        return false;
                    }
                }else{
                    echo "<br> did not excute setting friend list <br>" . mysqli_stmt_error($stmt)."<br>";
                }
            }

			mysqli_stmt_close($stmt);
		}

    }

    //initilize change checker
    $change_pw = false;
    $change_fullname = false;
    $change_gender = false;
    $change_title = false;


    if(isset($_POST['update'])){

        //change UP
        $checking_up_var = true;

        if( !empty($_POST["new_password"]) && !empty($_POST["confirm_password"]) ){


            $new_password = $_POST["new_password"];
            $confirm_password = $_POST["confirm_password"];

            // check change pw
            if($new_password == $confirm_password){
                $c_pw = $new_password;
                $_SESSION['pw'] = $c_pw;
                $change_pw = true;
            }
        }

        // check change gender
        if(!empty($_POST['gender'])){
            $c_gender = $_POST['gender'];
            $_SESSION['gender'] = $c_gender;
            $change_gender = true;
        }

        // check change title
        if(!empty($_POST['title'])){
            $c_title = $_POST['title'];
            $_SESSION['title'] = $c_title;
            $change_title = true;
        }
        
        // check change in name
        if(!empty($_POST['name'])){
            $c_fullname = $_POST['name'];
            $_SESSION['fullname'] = $c_fullname;
            $change_fullname = true;
        }
 

        if(change_user_profile($c_rid,$c_uid,$c_pw,
                    $c_fullname, $c_gender, $c_title,
                    $change_pw, $change_fullname, 
                    $change_gender, $change_title)
                        == true){
                            echo "changed all user profile setting sucess <br>";
                    }else{
                        echo "changed all user profile setting failed <br>";
                    }
        

        // also udpate the user in file system
        $fp = fopen('Register_data', 'a');
        $string = $c_fullname.",".$c_uid.",".$c_pw.",".$c_title.",".$c_gender.","."\n";

        //fwrite($fp, $data);
        fputs($fp, $string);

        fclose($fp);

        /// this is just a temp method for storing login data and register data
        $file = fopen('users.txt', 'a+');
        $register_user = $c_uid."|".$c_pw."\n";
        fputs($file, $register_user);
        fclose($file);

    }


?>

