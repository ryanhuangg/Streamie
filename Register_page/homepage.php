<?php
    require_once "db_connect_config.php";
    session_save_path("sess");
    session_start();
    $uid = $_SESSION['uid'];
    $rid = $_SESSION['rid'];
    $fullname = $_SESSION['fullname'];
    $pw = $_SESSION['pw'];
    $gender = $_SESSION['gender'];
    $title = $_SESSION['title'];
    
    $_SESSION['uid'] = $uid;
    $_SESSION['rid'] = $rid;
    $_SESSION['fullname'] = $fullname;

    // if(!isset($_SESSION["loggined"]) ** $_SESSION["loggined"] == true){
    //     header("location: homepage.php");
    //     exit;
    // }
?>

<DOCTYPE html>
<html>
<head>
<title>Streamie Home</title>
</head>
<body>
<h1>Placeholder homepage for Streamie</h1>


<h2> <?php echo "Reference ID: ". $_SESSION['rid'];?> </h2>
<h2> <?php echo "Name: ". $_SESSION['fullname'];?> </h2>

    <div>
    <a href="UserProfile.html">User Profile</a>
    <a href="logout.php">Sign Out</a>
    </div>
</body>
</html>