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

    if(!isset($_SESSION["loggined"]) ** $_SESSION["loggined"] == true){
        header("location: homepage.php");
        exit;
    }

?>

<link rel="stylesheet" href="home.css">
<DOCTYPE html>
    <html>

    <head>

        <title>Streamie Home</title>

    </head>

    <body>
        <div class="header">
            <h1>Streamie Home Page</h1>
        </div>
        <div class="nav">
            
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
            <a href="UserProfile.php" class="btn btn-warning">User Profile</a>
        </div>
        <div class="status">     
            <div class="id">Your User ID: <?php echo $uid; ?> </div>
            <div class="regid">Your Registered ID: <?php echo $rid; ?> </div>
            <div class="name">Name: <?php echo $fullname; ?> </div>
        </div>

        <div class="vl"></div>

        <div class="goplay">
            <a href="sync_master.html" class="play">Host a Stream</a>
            <a href="sync_viewer.html" class="play2">Join a Stream</a>
            <a href="all_sync_players.html" class="play1">All Sync</a>
        </div>

    </body>

    </html>