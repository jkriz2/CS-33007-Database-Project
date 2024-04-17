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

//initialize date variable
$date = "";



// Create connection
 $conn = new mysqli($servername, $username, $password, $dbname);
 // Check connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlstatement = $conn->prepare("SELECT member_since FROM users WHERE user_ID = ?"); //prepare the statement
$sqlstatement->bind_param("s",$_SESSION["id"]); //$conn->bind_param("s", $currentusername);
$sqlstatement->execute(); //execute the query
$result = $sqlstatement->get_result(); //return the results

while ($row = $result->fetch_assoc()) {
    //extract date
    $date = $row["member_since"];
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
    <h1 class="my-5">Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. To the Flash Fury Online© services page.</h1>
    <h1 class="my-5">You have been a user since : <?php echo $date?></b>.<br>You do not currently have a subscription active.</h1>
    <!--Will actually check subscription later, will check by querying the subscription table with user's id and if no rows are returned or no in date it's assumed none/expired-->

    <p><nav class="nav justify-content-center">
    <a href="welcome.php" class="nav-item nav-link active">Home</a>
    <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
    <a href="purchase.php" class="nav-item nav-link">Purchase</a>
     
</nav>
    <p>
        
    </p>
</body>
<footer style = "position: fixed; bottom: 10px; width: 100%; text-align: center;">
        <a href="reset-password.php" class="btn btn-warning">Reset Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out</a>
</footer>


</html>