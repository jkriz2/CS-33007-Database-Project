<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Include config file
require_once '/users/kent/student/jkrizan/config/config.php';



// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$time = 0;
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT user_ID FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }


    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (strlen(trim($_POST["email"])) < 6) {
        $email_err = "Emails must have at least 6 characters.";
    } elseif (strlen(trim($_POST["email"])) > 320) {
        $email_err = "Emails must have less than 320 characters.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } elseif (strlen(trim($_POST["password"])) > 64) {
        $password_err = "Password must be 64 characters or less.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, member_since, email) VALUES (?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssis", $param_username, $param_password, $time, $param_email);

            // Set parameters
            $time = time();
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up - Flash Fury Online</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            margin: auto;
            width: 40%;
            padding: 20px;
            max-width: 400px;
        }

        .form-group {
            min-width: 200px;
            display: flex;
            flex-direction: column;
        }

        .btn {
            justify-content: center;
            align-items: center;
        }

        .sub {
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }

        .got-account {
            font-style: italic;
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
            /* padding: 20px; */
            /* margin: auto; */
            /* min-width: 300px; */
            /* width: 35%; */
            /* max-width: 600px; */
            /* border: 2px solid green; */
            /* margin-top: 5px; */
            /* height: 300px; */

            margin: auto;
            min-width: 300px;
            width: 40%;
            padding: 20px;
            max-width: 400px;
            background: rgba( 255, 255, 255, 0.25 );
            box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
            backdrop-filter: blur( 4px );
            -webkit-backdrop-filter: blur( 4px );
            border-radius: 10px;
            border: 1px solid rgba( 255, 255, 255, 0.18 );
            transition: background ease 0.5s;
            color: black;
            overflow: hidden;


        }
        .wrapper:hover{
            background: rgba( 255, 255, 255, 0.6 );
        }
    </style>
</head>

<body>
    <video autoplay muted loop id="myVideo">
        <source src="./images/live2.mp4" type="video/mp4">
    </video>
    <img src="./images/Logoforproject.png" style="width: 15%; min-width: 200px;  height: auto;   display: block; margin-left: auto; margin-right: auto;">
    <div class="wrapper">

        <h2>Sign Up</h2>
        <p>Fill in your details and start playing today</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" maxlength="20" placeholder="Flash_101" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" maxlength="320" placeholder="youremail@domain.com" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" maxlength="64" placeholder="******" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" maxlength="64" placeholder="******" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group sub">
                <input type="submit" class="btn btn-success" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p class="got-account">Got an account? <a href="login.php">Log in here</a>.</p>
        </form>
    </div>
</body>

</html>