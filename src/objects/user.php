<?php

    $dbServername = "localhost";
    //$dbServername = "192.168.2.88";
	$dbUsername = "streamie-db";
	$dbPassword = "kM514Sl8AgIR3jTc";
    $dbName = "csc301_db";

    //set up for db connection
    $dbconn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

    if(!$dbconn){
        die(" db Connection failed: " . mysqli_connect_error());

        echo ' failed <br> ';
    }else{
        echo ' connected successfully <br> ';
    }

    $uid_error = "";
    $password_error = "";


    class user {
        public $title = "";
        public $gender = "";
        public $name = ""; //profile name
        public $id = ""; //displayed nickname
        public $password = "";
        public $rid = ""; //id that use to identify the user in the database
        public $friends = array();
        public $reviews = array();

        //error msg initialize





        //constructor for class user
        public function _construct($reguid, $regpassword, $regname, $regtitle, $reggender){

            $temp_rid = rand(1000000, 9999999);
            
            //to do: check with the database for possible name dups 

            while(!check_dup_uid($temp_rid, $reguid)){
                $temp_rid = rand(1000000, 9999999);
            }

            //optional info
            $this->title = $regtitle;
            $this->gender = $reggender;

            //rid
            $this->rid = strval($temp_rid);

            //uid
            $this->uid = $reguid;

            $this->password = $regpassword;

            //name and friend list
            $this->name = $regname;
            $this->friends = array();




        }




        //helper that use to check if $check_rid already exist in the $tab in the table
        //to do

        function check_dup_uid($check_rid, $check_uid){
            global $dbconn;
            echo "check dup <br>";

            $query = "SELECT rid, uid FROM Streamie_User WHERE rid = ? and uid = ?";

            if($stmt = mysqli_prepare($dbconn, $query)){

                echo "<br> prepare check dup <br>";
                mysqli_stmt_bind_param($stmt,"is",$rid, $uid);
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
            
        function check_friendlist($check_rid,$check_uid){
            global $dbconn;

            //echo "<br> checking for friendlist<br>";
            $query = "SELECT rid, uid FROM User_FriendList WHERE rid = ? and uid = ?";

            //$stmt = mysqli_stmt_init($dbconn);

            if($stmt = mysqli_prepare($dbconn, $query)){
                //echo "check friend list <br>";

                mysqli_stmt_bind_param($stmt,"is",$rid, $uid);
                echo "bind friend list <br>";
                $uid = $check_uid;
                $rid = $check_rid;

                if(mysqli_stmt_execute($stmt)){
                    //echo "excute friend list <br>";
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


        //set friend list
        function set_friendlist($check_rid,$check_uid){
            global $dbconn;

            //echo "<br> setting friend list <br>";
            $friendlist = "";

            $query = "INSERT INTO User_FriendList (rid, uid, friend_list) VALUES ('".$check_rid."','".$check_uid."', '".$friendlist."')";

            //$stmt = mysqli_stmt_init($dbconn);

            if($stmt = mysqli_prepare($dbconn, $query)){
                //echo "prepare set friend list <br>";
                
                // binding parameter doesnt work dont know why
                // just use the direct inject above
                // mysqli_stmt_bind_param($query,"iss", $rid, $uid);

                // echo "bind set friend list <br>";

                // $uid = $check_uid;
                // $rid = $check_rid;
                // $add_friendlist = $friendlist;

                if(mysqli_stmt_execute($stmt)){

                    //echo "excute setting friend list <br>";
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

        //get friend list, return user's friend list in array by rid and uid.
        public function get_friendlist($check_rid,$check_uid){
            global $dbconn;

            //$query = "SELECT rid, uid FROM User_FriendList WHERE uid = '".$check_rid."' and rid ='".$check_uid."'";

            $query = "SELECT rid, uid, friend_list FROM User_FriendList WHERE uid = '".$check_rid."' and rid ='".$check_uid."'";

            if($stmt = mysqli_prepare($dbconn, $query)){

                //mysqli_stmt_bind_param($stmt,"is",$q_rid, $q_uid);

                // $q_uid = $check_uid;
                // $q_rid = $check_rid;
                

                if(mysqli_stmt_execute($stmt) == true) {
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $get_friendlist = "";
                        mysqli_stmt_bind_result($stmt, $uid, $rid, $get_friendlist);

                        if (mysqli_stmt_fetch($stmt)) {
                            $friend_list=$this->FLstring_toArray($get_friendlist);
                            return $friend_list;
                        }
                    } else {
                        //excute set friend list failed
                        return $friend_list;
                    }
                }
                mysqli_stmt_close($stmt);

            }

        }

        //friendlist is an array, convert it into string;
        // $friendlist: array
        function friendlist_tostring($friendlist){
            if(empty($friendlist)){
                return "";
            }else{
                $FL_string = implode(",", $friendlist);
                return $FL_string;
            }

        }

        //flstring is a string, convert it into a friendlist array
        // $flstring: string
        function FLstring_toArray($flstring){

            $friendlist = array();
            if(empty($flstring)){
                return $friendlist;
            }else{
                $friendlist = explode(",", $flstring);
            }
        }







        //account modify
        //$new_pass: string(password)
        function change_pd($new_pass, $uid){
            global $dbconn;
            $this->password = $new_pass;

            $sql = "UPDATE Streamie_User SET pass = '".$this->password."' WHERE uid='".$uid."'"; 

            if(mysqli_query($dbconn, $sql)){ 
                echo "Record was updated successfully."; 
            } else { 
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($dbconn); 
            }  
            mysqli_close($dbconn);
        }


        //add friends; $friend_id: string
        function add_friend($friend_id, $friend_list){
            global $dbconn;

            $this->friends = $this->get_friendlist($this->rid, $this->uid);

            if(in_array($friend_id, $friend_list)){
                return false;

            }else{
                array_push($this->friends, $friend_id);

                $FL_string = $this->friendlist_tostring($this->friends);

                $query = "UPDATE User_Friendlist SET friend='".$FL_string."' WHERE uid = '".$this->uid."'";

                $stmt = mysqli_prepare($dbconn, $query);

                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                mysqli_close($dbconn);

                return true;
            }

            return false;

        }



        //remove friend; $friend_id: string
        //to do
        function remove_friend($friend_id, $friend_list){
            global $dbconn;

            $this->friends = $this->get_friendlist($this->rid, $this->uid);

            if(!in_array($friend_id, $friend_list)){
                return false;

            }else{

                //todo

                $key = array_search($friend_id, $friend_list);

                array_splice($this->friends, $key, 1);

                $FL_string = $this->friendlist_tostring($this->friends);

                $query = "UPDATE User_Friendlist SET friend='".$FL_string."' WHERE rid= '".$this->rid."'";

                $stmt = mysqli_prepare($dbconn, $query);

                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                mysqli_close($dbconn);

                return true;
            }

            return false;

        }


        // old code by jack
        /////////////////////////////////////////////////////////////////////////////////


        // //friends 
        // public function add_fri($friid){

        //     if(in_array($friid, $this->friends)){
        //         return 1;
        //     }

        //     $this->friends[] = $friid;

        //     $dbconn = mysql_connect($dbServername, $dbUsername, $dbPassword, $dbName);


        //     if(!$dbconn){
        //         die(" db Connection failed: " . mysqli_connect_error());
            
        //         echo ' failed <br> ';
        //     }else{
        //         echo ' connected successfully <br> ';
        //     }

        //     $query = "UPDATE User_Friendlist SET friend=$this->friends WHERE rid='".$this->rid."'";

        //     $stmt = mysqli_prepare($dbconn, $query);

        //     mysqli_stmt_execute($stmt);

        //     mysqli_stmt_close($stmt);

        //     mysqli_close($dbconn);


        // }

        // public function remove_fri($fri){
        //     if(!in_array($friid, $this->friends)){
        //         return 1;
        //     }

        //     $key = array_search($fri, $array);
        //     array_splice($this->friends, $key, 1);

        //     $dbconn = mysql_connect($dbServername, $dbUsername, $dbPassword, $dbName);

        //     if(!$dbconn){
        //         die(" db Connection failed: " . mysqli_connect_error());
            
        //         echo ' failed <br> ';
        //     }else{
        //         echo ' connected successfully <br> ';
        //     }

        //     $query = "UPDATE User_Friendlist SET friend=$this->friends WHERE rid=$this->rid";

        //     $stmt = mysqli_prepare($dbconn, $query);

        //     mysqli_stmt_execute($stmt);

        //     mysqli_stmt_close($stmt);

        //     mysqli_close($dbconn);

        // }





        // //constructor for users, each user is given a friend id when the account is created
        // public function register($regtitle, $reggender, $regname, $reguid, $regpassword) {

        //     // $temp = rand(1000000, 9999999);
            
        //     // //to do: check with the database for possible name dups 

        //     // while(!check_dup_uid($temp, $reguid)){
        //     //     $temp = rand(1000000, 9999999);
        //     // }

        //     // //rid
        //     // $this->rid = strval($temp);
        //     // $this->title = $regtitle;
        //     // $this->gender = $reggender;

        //     // $this->name = $regname;
        //     // //uid
        //     // $this->uid = $reguid;
        //     // $this->password = $regpassword;

        //     // $dbconn = mysql_connect($dbServername, $dbUsername, $dbPassword, $dbName);

        //     // if(!$dbconn){
        //     //     die(" db Connection failed: " . mysqli_connect_error());
        
        //     //     echo ' failed <br> ';
        //     // }else{
        //     //     echo ' connected successfully <br> ';
        //     // }

        //     $query = "INSERT INTO Streamie_User (rid, uid, fullname, gender, title, pass) VALUES ('$this->rid','$this->name','$this->gender', '$this->title', '$this->password')";
        
        //     $stmt = mysqli_prepare($dbconn, $query);

        //     $query = "INSERT INTO User_FriendList (rid, uid, friend_list) VALUES ('$this->rid', '$this->uid', '$this->friends')";

        //     mysqli_stmt_execute($stmt);

        //     echo "New records created successfully";

        //     mysqli_stmt_close($stmt);

        //     mysqli_close($dbconn);


        // }

        // public function login($uid, $password){
        //     # to do: receive infomration from the database

        //     $query = "SELECT uid, pass FROM User_FriendList WHERE uid = ?";

        //     //bind username
        //     if($stmt = mysqli_prepare($dbconn, $sql)){
        //         mysqli_stmt_bind_param($stmt, "s", $bind_uid);
        //         $bind_uid = $uid;


        //         //excute the stmt
        //         if(mysqli_stmt_execute($stmt)){
        //             //store the result
        //             mysqli_stmt_store_result($stmt);

        //             //check if exsist this user id, if yes check pw
        //             if(mysqli_stmt_num_rows($stmt) == 1){
                        
        //                 //bind user result variable

        //                 mysqli_stmt_bind_result($stmt, $uid, $username, $pw);

        //                 if(mysqli_stmt_fetch($stmt)){
        //                     session_start();
        //                     $_SESSION["loggined"] = true;
        //                     $_SESSION["uid"] = $uid;

        //                 }else{
        //                     $password_error "the password is invalide";
        //                 }

        //             }
        //         }else{
        //             $uid_error = "the uid does not exit"
        //         }

        //     mysqli_stmt_close($stmt);
        //     }
        //  mysqli_close($dbconn);

        // }


        
        }




?>