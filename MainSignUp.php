<?php
session_start();

// Include dbConnect.php file
require_once("dbConnect.php");

// Define variables and initialize with empty values
$username = $password = $confirm_password = $userlevel = "";
$username_err = $password_err = $confirm_password_err = $userlevel_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate user level
    if (empty(trim($_POST["userlevel"]))) {
        $userlevel_err = "Please select a user level.";
    } else {
        $userlevel = trim($_POST["userlevel"]);
        if (!in_array($userlevel, ['1', '2'])) {
            $userlevel_err = "Invalid user level.";
        }
    }

    // Validate username based on user level
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif ($userlevel == '1' && !preg_match("/^\d{10}$/", trim($_POST["username"]))) {
        $username_err = "Student username must be exactly 10 digits.";
    } elseif ($userlevel == '2' && !preg_match("/^A\d{4}$/", trim($_POST["username"]))) {
        $username_err = "Admin username must start with 'A' followed by 4 digits.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = mysqli_prepare($dbCon, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($userlevel_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, userpassword, userlevel) VALUES (?, ?, ?)";
        
        if ($stmt = mysqli_prepare($dbCon, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_userlevel);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Create a password hash
            $param_userlevel = $userlevel; // Set user level
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Successfully created an account');</script>";
                // Redirect to login page
                echo "<script>location.href='login.php';</script>";
            } else {
                echo "Something went wrong. Please try again later.";
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
<title>Sign Up</title>
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
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>User Level</label>
            <select name="userlevel" class="form-control">
                <option value="1" <?php echo (isset($_POST['role']) && $_POST['role'] == '1') ? 'selected' : ''; ?>>Donor</option>
                <option value="2" <?php echo (isset($_POST['role']) && $_POST['role'] == '2') ? 'selected' : ''; ?>>Staff</option>
                <option value="3" <?php echo (isset($_POST['role']) && $_POST['role'] == '3') ? 'selected' : ''; ?>>Manager</option>
            </select>
            <span class="message"><?php echo $userlevel_err; ?></span>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="message"><?php echo $username_err; ?></span>
        </div>    
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            <span class="message"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
            <span class="message"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary" value="Reset">
        </div>

    </form>
    <br>
    <p>Already have an account? <a href="MainLogin.php">Login here</a>.</p>
    <a style="text-align: center;" href="DonorHomePage.php" class="btn btn-secondary">Cancel</a>

</div>
</body>
</html>
