<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
//require_once '/users/kent/student/jkrizan/config/config.php';
include '/users/kent/student/jkrizan/config/config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
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
$sqlstatement->bind_param("s", $_SESSION["id"]); //$conn->bind_param("s", $currentusername);
$sqlstatement->execute(); //execute the query
$result = $sqlstatement->get_result(); //return the results

while ($row = $result->fetch_assoc()) {
    //extract date
    $date = $row["member_since"];
}

/* leftovers
$sqlstatement = $conn->prepare("SELECT * FROM bans WHERE `user_ID` = ?;"); //prepare the statement
$sqlstatement->bind_param("i", $id);
$sqlstatement->execute(); //execute the query
$sqlstatement->store_result(); //return the results

$sqlstatement->bind_result($user_id, $incident_id);
*/
// Get the user ID dynamically (e.g., from session)
$id = $_SESSION["id"];

$sql = "SELECT * FROM unlocks WHERE user_ID = $id LIMIT 1";
$sub = $conn->query($sql);



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome - Flash Fury Online</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
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
        .wrapper {
            /* position: fixed; */
            padding: 20px;
            margin: auto;
            min-width: 300px;
            /* width: 35%; */
            max-width: 600px;
            /* border: 2px solid green; */
            margin-top: 5px;
            background: rgba( 255, 255, 255, 0.25 );
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            backdrop-filter: blur( 4px );
            -webkit-backdrop-filter: blur( 4px );
            border-radius: 10px;
            border: 1px solid rgba( 255, 255, 255, 0.18 );
            transition: background 0.3s ease 0.3s;
            color: black;


        }
        .wrapper:hover{
            background: rgba( 255, 255, 255, 0.6 );
        }

        .footer{
            padding: 20px;
            margin: auto;
            /* min-width: 300px; */
            /* width: 35%; */
            /* max-width: 600px; */
            /* border: 2px solid green; */
            /* margin-top: 5px; */
            background: rgba( 255, 255, 255, 0.25 );
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            backdrop-filter: blur( 4px );
            -webkit-backdrop-filter: blur( 4px );
            border-radius: 10px;
            border: 1px solid rgba( 255, 255, 255, 0.18 );
            transition: background 0.3s ease 0.3s;
            color: black;
        }

    </style>
</head>

<body>
    <video autoplay muted loop id="myVideo">
        <source src="./images/live2.mp4" type="video/mp4">
    </video>
    <img src="./images/Logoforproject.png" style="min-width: 250px; width: 10%;  height: auto;">
    <div class="wrapper">
    <h1 class="my-5">Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, to the Flash Fury Online© services page.</h1>
    <h1 class="my-5">You have been a user since : <?php echo (date("Y-m-d", $date)); ?></b>.</h1>
    <!--Will actually check subscription later, will check by querying the subscription table with user's id and if no rows are returned or no in date it's assumed none/expired-->


    <?php
        if ($sub !== false && $sub->num_rows > 0) {
            echo "You currently have a subscription active.";
        }else{
            echo "You do not currently have a subscription active.";
        }
    ?>

    <p>
    <!-- <nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>

    </nav> -->
    <?php include "navigation.php"?>
    </div>

</body>
<!-- <footer class = "footer" style="position: fixed; bottom: -1px; width: 100%; text-align: center;">
    <a href="reset-password.php" class="btn btn-warning">Reset Password</a>
    <a href="logout.php" class="btn btn-danger ml-3">Sign Out</a>
</footer> -->
<?php include "footer.php"?>


</html>