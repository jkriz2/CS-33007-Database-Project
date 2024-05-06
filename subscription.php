<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
// require_once '/users/kent/student/"name"/config/"name of config file"';
include '/users/kent/student/jkrizan/config/config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID dynamically (e.g., from session)
$id = $_SESSION["id"];

// Validate user ID
if (!$id || !is_numeric($id)) {
    die("Invalid user ID");
}

// Adjustment to use the relational table to look up a user
$sql = "SELECT * FROM unlocks WHERE user_ID = $id LIMIT 1";
$result = $conn->query($sql);
$info = $result->fetch_assoc();
$sub_ID = $info['sub_ID']; //binding it to $sub_ID as php didn't like me sticking $info['sub_ID'] into the sql query

// Check if the user has a subscription
if ($result !== false && $result->num_rows > 0) {
    // Fetch the user's subscription info
    $sql = "SELECT * FROM subscription WHERE sub_ID = $sub_ID LIMIT 1";
    $result = $conn->query($sql);

    // User has a subscription
    $subscription = $result->fetch_assoc();
    $subexpires = date("m-d-y", $subscription['sub_expire']);
    $subscription_info = "<h2>Subscription Information</h2>";
    $subscription_info .= "<p>Subscription Plan: {$subscription['tier']}</p>";
    $subscription_info .= "<p>Expires on: {$subexpires}</p>";
    $subscription_info .= "<form method='post'>";
    $subscription_info .= "<input type='hidden' name='user_id' value='$id'>";
    $subscription_info .= "<button type='submit' name='toggle_renewal' is class='btn btn-outline-success butt' style = 'color:black'";
    $subscription_info .= $subscription['auto_renew'] ?: "";
    $subscription_info .= ">Toggle Auto-Renewal</button>";
    $subscription_info .= $subscription['auto_renew'] ? "<span style='margin-left: 5px;'>&#10003;</span>" : "";
    $subscription_info .= "</form>";
} else {
    // User does not have a subscription or there was an error in the query
    if ($result === false) {
        // Handle query error
        $subscription_info = "Error executing query: " . $conn->error;
    } else {
        // No subscription found
        $subscription_info = "<h2 style='margin-top:15px;'>No Subscription Found</h2><p>Would you like to subscribe?</p>";
    }
}


// Process auto-renewal toggle
if (isset($_POST['toggle_renewal'])) {
    $user_id = $_POST['user_id'];
    $sql_update = "UPDATE subscription SET auto_renew = NOT auto_renew WHERE sub_ID = $sub_ID";
    $result_update = $conn->query($sql_update);

    // Assuming the update was successful, you can redirect to the same page to refresh the subscription information
    header("Location: subscription.php");
    exit();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Subscriptions - Flash Fury Online</title>
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
        .butt:hover{
            color:white;
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
            height: 300px;
            background: rgba( 255, 255, 255, 0.25 );
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            backdrop-filter: blur( 4px );
            -webkit-backdrop-filter: blur( 4px );
            border-radius: 10px;
            border: 1px solid rgba( 255, 255, 255, 0.18 );
            transition: background ease 0.5s;
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
    <img src="./images/Logoforproject.png" style="width: 225px;  height: auto; margin-top: 100px;">
    <p>
    <div class="wrapper">


        <!-- <nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>
    </nav> -->
        <?php include "navigation.php" ?>
        <?php echo $subscription_info; ?>
    </div>
</body>
<?php include "footer.php" ?>
</html>