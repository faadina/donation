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
    if ($user_type == "donor" ) {
        $message = "Please enter the username correctly";
    } elseif ($user_type == "staff" ) {
        $message = "Please enter the username correctly";
    } elseif ($user_type == "manager" ) {
        $message = "Please enter the username correctly";
    } else {
        // Prepare a select statement
        $sql = "";
        switch ($user_type) {
            case "staff":
                $sql = "SELECT staffID, staffPassword, 'role' FROM Staff WHERE staffID = ?";
                break;
            case "donor":
                $sql = "SELECT donorID, donorPassword, 'role' FROM Donor WHERE donorID = ?";
                break;
            case "manager":
                $sql = "SELECT managerID, managerPassword, 'role' FROM Manager WHERE managerID = ?";
                break;
        }
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
                            header("location: DonorHomePage.php");
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
<title>Login Page</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<style type="text/css">
    body { 
        font: 14px sans-serif; 
        background-color: whitesmoke;
        color: #FFFFFF;
    }
    .wrapper { 
        color:black;
        width: 360px; 
        padding: 20px; 
        margin: auto;
        margin-top: 100px;
        background-color: whitesmoke;
        border-radius: 10px;
        box-shadow: 0px 0px 10px 0px #000000;
    }
    .form-control:focus {
        background-color: grey;
        color: #FFFFFF;
    }
    .btn-primary {
        background-color: black;
        border: none;
    }
    .btn-primary:hover {
        background-color: #808080;
    }
    .btn-secondary {
        background-color: black;
        border: none;
    }
    .btn-secondary:hover {
        background-color: #808080;
    }
    .message {
        color: #FF4500;
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
    <p>Don't have an account? <button type="button" class="btn btn-link" data-toggle="modal" data-target="#joinUsModal">Register Here</button>.</p>
    <a style="text-align: center;" href="DonorHomePage.php" class="btn btn-secondary">Cancel</a>
</div>

<!-- Modal -->
<div class="modal fade" id="joinUsModal" tabindex="-1" role="dialog" aria-labelledby="joinUsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="joinUsModalLabel">Join Us</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Click the button below to sign up.</p>
        <a href="MainSignUp.php" class="btn btn-primary">Sign Up Now</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
