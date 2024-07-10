<?php
// Include config file
require_once("dbConnect.php");

// Define variables and initialize with empty values 
$username = $password = $user_type = ""; 
$message = "";

// Processing form data when form is submitted 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start(); // Start a session
    
    // Validate username
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $user_type = trim($_POST["user_type"]);
    
    // Check the user type and validate the username accordingly
    if ($user_type == "student" && !preg_match("/^\d{10}$/", $username)) {
        $message = "Student username must be exactly 10 digits.";
    } elseif ($user_type == "admin" && !preg_match("/^A\d{4}$/", $username)) {
        $message = "Admin username must start with 'A' followed by 4 digits.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id, username, userpassword, userlevel FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($dbCon, $sql)) {
            // Bind variables to the prepared statement as parameters 
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement 
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $userlevel);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username; 
                            $_SESSION["userlevel"] = $userlevel;
                            
                            // Redirect user to welcome page
                            header("location: Home.php");
                        } else {
                            // Display an error message if password is not valid
                            $message = "Wrong Username & Password.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $message = "Wrong Username & Password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($dbCon);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign In</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> 
<style type="text/css">
    body { 
        font: 14px sans-serif; 
        background-color: whitesmoke; /* Dark Cyan Theme Background */
        color: #FFFFFF; /* White text for contrast */
    }
    .wrapper { 
        color:black;
        width: 360px; 
        padding: 20px; 
        margin: auto;
        margin-top: 100px;
        background-color: whitesmoke; /* Dark Cyan Box Background */
        border-radius: 10px;
        box-shadow: 0px 0px 10px 0px #000000;
    }
    .form-control {
        background-color: grey; /* Dark input fields */
        border: none;
        color: #FFFFFF;
    }
    .form-control:focus {
        background-color: black;
        color: #FFFFFF;
    }
    .btn-primary {
        background-color: #00CED1;
        border: none;
    }
    .btn-primary:hover {
        background-color: #20B2AA;
    }
    .btn-secondary {
        background-color: #696969;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #808080;
    }
    .message {
        color: #FF4500; /* OrangeRed message color for errors */
    }
    a {
        color: #00CED1;
    }
    a:hover {
        color: #20B2AA;
    }
</style>
</head>
<body>
<div class="wrapper">
    <h2>Login Page</h2>
    <p>Please fill this form to access the system.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="message"><?php if ($message != "") { echo $message; } ?></div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary" value="Reset">
        </div>
    </form>
    <br>
    <p>Don't have an account? <a href="MainSignUp.php">Register Here</a>.</p>
    <a style="text-align: center;" href="DonorHomePage.php" class="btn btn-secondary">Cancel</a>
</div>
</body>
</html>
