<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect them to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include config file
require_once '/users/kent/student/jkrizan/config/config.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// If previous login attempt was unsuccessful
if (isset($_GET["msg"]) && $_GET["msg"] == 'failed') {
    $login_err = "Invalid username or password.";
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT user_ID, username, password FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);

                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Check if user is banned, if banned then prevent log in.
                            $sqlstatement = $mysqli->prepare("SELECT * FROM bans WHERE `user_ID` = ?;"); //prepare the statement
                            $sqlstatement->bind_param("i", $id);
                            $sqlstatement->execute(); //execute the query
                            $sqlstatement->store_result(); //return the results

                            $sqlstatement->bind_result($user_id, $incident_id);

                            if ($sqlstatement->num_rows == 1) {
                                $login_err = "Account currently banned.<br>Please contact support for more details.";

                                // Unset all of the session variables
                                $_SESSION = array();

                                // Destroy the session.
                                session_destroy();
                            } else {
                                $sql = "SELECT * FROM unlocks WHERE user_ID = $id LIMIT 1";
                                $result = $mysqli->query($sql);
                                $info = $result->fetch_assoc();
                                $sub_ID = $info['sub_ID']; //binding it to $sub_ID as php didn't like me sticking $info['sub_ID'] into the sql query

                                // Check if the user has a subscription
                                if ($result !== false && $result->num_rows > 0) {
                                    // Fetch the user's subscription info
                                    $sql = "SELECT * FROM subscription WHERE sub_ID = $sub_ID LIMIT 1";
                                    $result = $mysqli->query($sql);

                                    // User has a subscription
                                    $subscription = $result->fetch_assoc();

                                    // Delete subscription if expired
                                    if (time() > $subscription['sub_expire']) {
                                        $sql = "DELETE FROM subscription WHERE sub_id = $sub_ID";
                                        $mysqli->query($sql);
                                    }
                                }

                                // Redirect user to welcome page
                                header("location: welcome.php");
                            }
                        } else {
                            // Password is not valid, display a generic error message
                            header("location: login.php?msg=failed");
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    header("location: login.php?msg=failed");
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
            $sqlstatement->close();
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
    <title>Login - Flash Fury Online</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            /* position: fixed; */
            padding: 20px;
            margin: auto;
            min-width: 300px;
            width: 35%;
            max-width: 400px;
            /* border: 2px solid green; */
            margin-top: 2px;
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

        .form-control {
            min-width: 200px;
        }

        .new-player {
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
    </style>
</head>

<body>
    <video autoplay muted loop id="myVideo">
        <source src="./images/live2.mp4" type="video/mp4">
    </video>

    <img src="./images/Logoforproject.png" style="min-width: 220px; width: 15%;  height: auto;   display: block; margin-left: auto; margin-right: auto;">
    <div class="wrapper">

        <h2>Log in</h2>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p class="new-player">New player? <a href="register.php">Sign up here</a>.</p>
        </form>
    </div>
</body>

</html>