<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
//require_once '/users/kent/student/jkrizan/config/config.php';
include '/users/kent/student/jkrizan/config/config.php';



// Variables
$cardname = $expdate = $ccv = $zipcode = $cardnumber = "";
$cardname_err = $expdate_err = $ccv_err = $zipcode_err = $cardnumber_err = "";
$bill_id = $sub_ID = "";


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

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Function to check if the user has an active subscription
    function hasActiveSubscription($mysqli, $user_id){
        $sql = "SELECT sub_ID FROM unlocks WHERE user_ID = ? LIMIT 1";
        if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();
            return $num_rows > 0;
        }
        return false;
    }

    // Validate cardname
    if (empty(trim($_POST["cardname"]))) {
        $cardname_err = "Please enter a name.";
    } elseif (strlen(trim($_POST["cardname"])) < 1) {
        $cardname_err = "Please enter a name.";
    } elseif (strlen(trim($_POST["cardname"])) > 30) {
        $cardname_err = "Please enter a name.";
    } else {
        $cardname = trim($_POST["cardname"]);
    }

    if (empty(trim($_POST["cardnumber"]))) {
        $cardnumber_err = "Please enter a credit card number.";
    } elseif (strlen(trim($_POST["cardnumber"])) < 8) {
        $cardnumber_err = "Please enter a credit card number.";
    } elseif (strlen(trim($_POST["cardnumber"])) > 19) {
        $cardnumber_err = "Please enter a credit card number.";
    } elseif (!ctype_digit(trim($_POST["cardnumber"]))) {
        $cardnumber_err = "Please enter a credit card number.";
    } else {
        $cardnumber = trim($_POST["cardnumber"]);
    }

    if (empty(trim($_POST["ccv"]))) {
        $ccv_err = "Please enter a CCV.";
    } elseif (strlen(trim($_POST["ccv"])) < 3) {
        $ccv_err = "Please enter a CCV.";
    } elseif (!ctype_digit(trim($_POST["ccv"]))) {
        $ccv_err = "Please enter a CCV.";
    } else {
        $ccv = trim($_POST["ccv"]);
    }

    if (empty(trim($_POST["zipcode"]))) {
        $zipcode_err = "Please enter a ZIP code.";
    } elseif (strlen(trim($_POST["zipcode"])) < 5) {
        $zipcode_err = "Please enter a ZIP code.";
    } elseif (!ctype_digit(trim($_POST["zipcode"]))) {
        $zipcode_err = "Please enter a ZIP code.";
    } else {
        $zipcode = trim($_POST["zipcode"]);
    }

    if (empty(trim($_POST["expdate"]))) {
        $expdate_err = "Please enter the card's expiration date.";
    } elseif (strlen(trim($_POST["expdate"])) < 1) {
        $expdate_err = "Please enter the card's expiration date.";
    } else {
        $expdate = trim($_POST["expdate"]);
    }

    $tier = trim($_POST["tier"]);


    /* debug statements
    echo $cardname . "<br>";
    echo $cardnumber . "<br>";
    echo $ccv . "<br>";
    echo $zipcode . "<br>";
    echo $expdate . "<br>";
    echo $tier . "<br>";

    echo $cardname_err . "<br>";
    echo $cardnumber_err . "<br>";
    echo $ccv_err . "<br>";
    echo $zipcode_err . "<br>";
    echo $expdate_err . "<br>";
    */

    #we need to add all sorts of code over here and make sure we are inserting in properly
    // Assuming your existing code for database connection, session validation, and form validation...

// Check if form is submitted and there are no errors
    if($_SERVER["REQUEST_METHOD"] == "POST" && empty($cardname_err) && empty($cardnumber_err) && empty($ccv_err) && empty($zipcode_err) && empty($expdate_err)){

        // Prepare and execute the purchase insertion query
        $sql_purchase = "INSERT INTO purchase (tier, price, name_on_card, exp_date, cvv, zip, credit_card_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if($stmt = $mysqli->prepare($sql_purchase)){
            $price = 29.99; // Assuming the price is fixed for all tiers
            $stmt->bind_param("sdssiii", $tier, $price, $cardname, $expdate, $ccv, $zipcode, $cardnumber);
            $stmt->execute();
            echo "yep";

            // Retrieve the last inserted ID (bill_ID)
            $bill_id = $mysqli->insert_id;
            
            // Close the statement
            $stmt->close();
        }

        // Insert subscription information only if the user doesn't have an active subscription
        if(!empty($bill_id) && !hasActiveSubscription($mysqli, $_SESSION["id"])){
            $sub_begin = time(); // Assuming the subscription begins immediately
            $sub_expire = strtotime('+30 days', $sub_begin); // Subscription expires after 30 days
            $auto_renew = 0; // Assuming auto-renewal is initially disabled

            $sql_subscription = "INSERT INTO subscription (tier, sub_begin, sub_expire, auto_renew) VALUES (?, ?, ?, ?)";
            if($stmt = $mysqli->prepare($sql_subscription)){
                $stmt->bind_param("siii", $tier, $sub_begin, $sub_expire, $auto_renew);
                $stmt->execute();

                // Retrieve the last inserted ID (sub_ID)
                $sub_id = $mysqli->insert_id;

                // Close the statement
                $stmt->close();

                // Insert into the subscribes table
                if(!empty($sub_id)){
                    $sql_subscribes = "INSERT INTO subscribes (bill_ID, sub_ID) VALUES (?, ?)";
                    if($stmt = $mysqli->prepare($sql_subscribes)){
                        $stmt->bind_param("ii", $bill_id, $sub_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

                // Insert into the unlocks table
                if(!empty($sub_id)){
                    $sql_unlocks = "INSERT INTO unlocks (user_ID, sub_ID) VALUES (?, ?)";
                    if($stmt = $mysqli->prepare($sql_unlocks)){
                        $user_id = $_SESSION["id"];
                        $stmt->bind_param("ii", $user_id, $sub_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }
    }




    
    

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase - Flash Fury Online</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            text-align: center;
        }

        .navigation {
            font: 25px sans-serif;
        }

        h1 {
            font: 20px sans-serif;
        }

        .forminput {
            margin: auto;
            width: 40%;
            font: 20px;
            overflow: hidden;
            max-width: 400px;
            min-width: 300px;
        }

        .form-control {
            width: 70%;
            box-shadow: 0px 0px 6px #9768e3;
        }

        #tier {
            width: 150px;
            height: 30px;
            border: 2px solid #9768e3;
            border-radius: 5px;
            transition: background-color 0.3s ease 0.3s;
            box-shadow: 0px 0px 18px black;
        }

        #tier:hover {
            background-color: #dacdfa;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="navigation">
        <img src="./images/Logoforproject.png" style="width: 10%;  height: auto;">
        <!-- <p>
        <nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>
        </p>
        </nav> -->
        <?php include "navigation.php" ?>
    </div>
    <div class="forminput"><br />
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <select name="tier" id="tier">
                <option value="Basic">Basic</option>
                <option value="Standard">Standard</option>
                <option value="Premium">Premium</option>
            </select>

            <p>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="cardname" maxlength="30" placeholder="Jane Doe" class="form-control <?php echo (!empty($cardname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cardname; ?>">
                <span class="invalid-feedback"><?php echo $cardname_err; ?></span>
            </div>
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" name="cardnumber" maxlength="19" placeholder="1111222233334444" class="form-control <?php echo (!empty($cardnumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cardnumber; ?>">
                <span class="invalid-feedback"><?php echo $cardnumber_err; ?></span>
            </div>
            <div class="form-group">
                <label>CCV</label>
                <input type="text" name="ccv" maxlength="3" placeholder="123" class="form-control <?php echo (!empty($ccv_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ccv; ?>">
                <span class="invalid-feedback"><?php echo $ccv_err; ?></span>
            </div>
            <div class="form-group">
                <label>Zip Code</label>
                <input type="text" name="zipcode" maxlength="5" placeholder="12345" class="form-control <?php echo (!empty($zipcode_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $zipcode; ?>">
                <span class="invalid-feedback"><?php echo $zipcode_err; ?></span>
            </div>
            <div class="form-group">
                <label>Exp. Date</label>
                <input type="month" name="expdate" maxlength="5" placeholder="MM/YY" class="form-control <?php echo (!empty($expdate_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $expdate; ?>">
                <span class="invalid-feedback"><?php echo $expdate_err; ?></span>
            </div>
            <div class="form-group">
                <script>
                    function clicked(submit) {
                        if (!confirm('Are you sure?')) {
                            submit.preventDefault();
                        }
                    }
                </script>

                <input type="submit" class="btn btn-primary" value="Submit" onclick="clicked(event)">
            </div>
        </form>
    </div>




</body>

<footer>


</footer>

</html>
