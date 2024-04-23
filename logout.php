<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
// Initialize the session
session_start();

// Logging user out
$_SESSION["loggedin"] = false;
$_SESSION["id"] = NULL;
$_SESSION["username"] = NULL;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" http-equiv="refresh" content="5;./index.php">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 25px sans-serif;
            text-align: center;
        }

        h1 {
            font: 20px sans-serif;
            text-align: center;
        }
    </style>
</head>

<body>
    <img src="./images/Logoforproject.png" style="width: 10%;  height: auto;">
    <h1 class="my-5">Thanks for playing. See you soon!.</h1>

    <!-- <nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>

    </nav> -->

</body>
<!-- <footer style="position: fixed; bottom: 10px; width: 100%; text-align: center;">
    <a href="reset-password.php" class="btn btn-warning">Reset Password</a>
    <a href="logout.php" class="btn btn-danger ml-3">Sign Out</a>
</footer> -->


</html>