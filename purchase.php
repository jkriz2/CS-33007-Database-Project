<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize the session
session_start();
// Include config file
//require_once '/users/kent/student/jkrizan/config/config.php';
include '/users/kent/student/jkrizan/config/databaselogin.php';



// Variables
$cardname = $expdate = $ccv = $zipcode = $cardnumber = "";
$cardname_err = $expdate_err = $ccv_err = $zipcode_err = $cardnumber_err = "";

 
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

if($_SERVER["REQUEST_METHOD"] == "POST"){

// Validate cardname
if(empty(trim($_POST["cardname"]))){
    $cardname_err = "Please enter a name.";     
} elseif(strlen(trim($_POST["cardname"])) < 1){
    $cardname_err = "Please enter a name.";
} else{
    $cardname = trim($_POST["cardname"]);
}

if(empty(trim($_POST["cardnumber"]))){
    $cardnumber_err = "Please enter a credit card number.";     
} elseif(strlen(trim($_POST["cardnumber"])) < 8){
    $cardnumber_err = "Please enter a credit card number.";
} else{
    $cardnumber = trim($_POST["cardnumber"]);
}

if(empty(trim($_POST["ccv"]))){
    $ccv_err = "Please enter a ccv.";     
} elseif(strlen(trim($_POST["ccv"])) < 3){
    $ccv_err = "Please enter a ccv.";
} else{
    $ccv = trim($_POST["ccv"]);
}

if(empty(trim($_POST["zipcode"]))){
    $zipcode_err = "Please enter a zip code.";     
} elseif(strlen(trim($_POST["zipcode"])) < 5){
    $zipcode_err = "Please enter a zip code.";
} else{
    $zipcode = trim($_POST["zipcode"]);
}

if(empty(trim($_POST["expdate"]))){
    $expdate_err = "Please enter the card's expiration date.";     
} elseif(strlen(trim($_POST["expdate"])) < 1){
    $expdate_err = "Please enter the card's expiration date.";
} else{
    $expdate = trim($_POST["expdate"]);
}

$conn->close();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{text-align: center; }
        .navigation{font: 25px sans-serif; }
        h1{ font: 20px sans-serif;}
        .forminput{margin: auto; width: 40%; font: 20px;overflow: hidden; max-width: 400px; min-width: 300px;}
        .form-control{width: 70%; box-shadow: 0px 0px 6px #9768e3;}
        #tier{width: 150px; height: 30px; border:2px solid #9768e3; border-radius: 5px; transition: background-color 0.3s ease 0.3s;box-shadow: 0px 0px 18px black;}
        #tier:hover{background-color: #dacdfa;}

        .form-group{ display: flex; flex-direction: column; justify-content: center; align-items: center;}

    </style>
</head>
<body>

    <div class="navigation">
        <img src="./images/Logoforproject.png" style = "width: 10%;  height: auto;">
        <p>
        <nav class="nav justify-content-center">
        <a href="welcome.php" class="nav-item nav-link active">Home</a>
        <a href="subscription.php" class="nav-item nav-link">Subscriptions</a>
        <a href="purchase.php" class="nav-item nav-link">Purchase</a>
        </p>
        </nav>
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
                    <label>Name on card</label>
                    <input type="text" name="cardname" class="form-control <?php echo (!empty($cardname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cardname; ?>">
                    <span class="invalid-feedback"><?php echo $cardname_err; ?></span>
            </div>
            <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" name="cardnumber" class="form-control <?php echo (!empty($cardnumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cardnumber; ?>">
                    <span class="invalid-feedback"><?php echo $cardnumber_err; ?></span>
            </div>   
            <div class="form-group">
                    <label>CCV</label>
                    <input type="text" name="ccv" class="form-control <?php echo (!empty($ccv_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ccv; ?>">
                    <span class="invalid-feedback"><?php echo $ccv_err; ?></span>
            </div>   
            <div class="form-group">
                    <label>Zip Code</label>
                    <input type="text" name="zipcode" class="form-control <?php echo (!empty($zipcode_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $zipcode; ?>">
                    <span class="invalid-feedback"><?php echo $zipcode_err; ?></span>
            </div>   
            <div class="form-group">
                    <label>Exp. Date</label>
                    <input type="month" name="expdate" class="form-control <?php echo (!empty($expdate_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $expdate; ?>">
                    <span class="invalid-feedback"><?php echo $expdate_err; ?></span>
            </div>
            <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <!--<input type="reset" class="btn btn-secondary ml-2" value="Reset">-->
            </div>
        </form>
    </div>

     


</body>

<footer>


</footer>

</html>