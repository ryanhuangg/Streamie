<?php
// Initialize the session varaible from homepage
session_start();

if($_SESSION["loggined"] == true){
 
    // Unset all session variables
    $_SESSION = array();
    
    // Destory all session stored.
    session_destroy();
}

session_destroy();

// Redirect to back login (login.html)
header("location: login.html");
exit;
?>