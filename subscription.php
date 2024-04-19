<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
//require_once '/users/kent/student/jkrizan/config/config.php';
include '/users/kent/student/jkrizan/config/databaselogin.php';
 
// Check if the user is logged in, if not then redirect them to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 25px sans-serif; text-align: center; }
        h1{ font: 20px sans-serif; text-align: center;}
    </style>
</head>
<body>
        <img src="./images/Logoforproject.png" style = "width: 10%;  height: auto;">
        <p><nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>
     
</nav>

</body>

<footer>


</footer>

</html>