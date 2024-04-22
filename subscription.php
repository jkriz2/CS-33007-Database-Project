<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
// require_once '/users/kent/student/"name"/config/"name of config file"';
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

// Get the user ID dynamically (e.g., from session)
$id = $_SESSION["id"];

// Validate user ID
if (!$id || !is_numeric($id)) {
    die("Invalid user ID");
}

// Check if the user has a subscription
$sql = "SELECT * FROM subscription WHERE sub_ID = $id LIMIT 1";
$result = $conn->query($sql);

// Check if the user has a subscription
if ($result !== false && $result->num_rows > 0) {
    // User has a subscription
    $subscription = $result->fetch_assoc();
    $subexpires = date("m-d-y", $subscription['sub_expire']);
    $subscription_info = "<h2>Subscription Information</h2>";
    $subscription_info .= "<p>Subscription Plan: {$subscription['tier']}</p>";
    $subscription_info .= "<p>Expires on: {$subexpires}</p>";
    $subscription_info .= "<form method='post'>";
    $subscription_info .= "<input type='hidden' name='user_id' value='$id'>";
    $subscription_info .= "<button type='submit' name='toggle_renewal' ";
    $subscription_info .= $subscription['auto_renew'] ?  : "";
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
        $subscription_info = "<h2>No Subscription Found</h2><p>Would you like to subscribe?</p>";
    }
}

// Process auto-renewal toggle
if (isset($_POST['toggle_renewal'])) {
    $user_id = $_POST['user_id'];
    $sql_update = "UPDATE subscription SET auto_renew = NOT auto_renew WHERE sub_ID = $user_id";
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
    <?php echo $subscription_info; ?>
</body>

<footer>


</footer>

</html>
