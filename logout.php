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
    <meta charset="UTF-8" http-equiv="refresh" content="3;./index.php">
    <title>Logout - Flash Fury Online</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            height:100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font: 25px sans-serif;
            text-align: center;
        }

        h1 {
            font: 20px sans-serif;
            text-align: center;
        }
        #myVideo {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Cover the container without losing aspect ratio */
            object-position: center;
            /* Center the video within the element */
            z-index: -1;
        }
        .my-5{
            color:white;
        }
    </style>
</head>

<body>
    <video autoplay muted loop id="myVideo">
        <source src="./images/live2.mp4" type="video/mp4">
    </video>
    <img src="./images/Logoforproject.png" style="width: 255px;  height: auto;">
    <h1 class="my-5">Thanks for playing. See you soon!</h1>

</body>

</html>