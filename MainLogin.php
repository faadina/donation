<?php
// Include config file
require_once "dbConnect.php";

// Initialize variables
$username = $password = $user_type = "";
$message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start or resume a session
    session_start();

    // Validate inputs
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $user_type = isset($_POST["user_type"]) ? trim($_POST["user_type"]) : "";

    if (empty($username) || empty($password) ) {
        $message = "Please enter username, password, and select user type.";
    } else {
        // Prepare a select statement based on user type
        switch ($user_type) {
            case "staff":
                $sql = "SELECT staffID, staffPassword, '2' AS role FROM Staff WHERE staffID = ?";
                break;
            case "donor":
                $sql = "SELECT donorID, donorPassword, '1' AS role FROM Donor WHERE donorID = ?";
                break;
            case "manager":
                $sql = "SELECT managerID, managerPassword, '3' AS role FROM Manager WHERE managerID = ?";
                break;

                break;
        }

        if (!empty($sql)) {
            // Attempt to prepare the SQL statement
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameter
                $param_username = $username;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $hashed_password, $userlevel);

                    // Check if username exists and verify password
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session if not already started
                            if (!isset($_SESSION["loggedin"])) {
                                session_start();
                            }

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["userlevel"] = $userlevel;

                            // Redirect user to appropriate page based on user type
                            switch ($userlevel) {
                                case "2":
                                    header("Location: StaffDashboard.php");
                                    exit;
                                case "1":
                                    header("Location: DonorDonateAllocation.php");
                                    exit;
                                case "3":
                                    header("Location: ManagerDashboard.php");
                                    exit;
                                default:
                                    header("Location: DonorHeader.php"); // Default redirect if userlevel is not recognized
                                    exit;
                            }
                        } else {
                            // Display an error message if password is not valid
                            $message = "Wrong password.";
                        }
                    } else {
                        // Display an error message if username doesn't exist
                        $message = "Username not found.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close connection
        mysqli_close($conn);
    }
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
        color: black;
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

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>

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
    <p>Don't have an account? <a href="MainSignUp.php">Register Here</a>.</p>
    <a style="text-align: center;" href="MainHome.php" class="btn btn-secondary">Cancel</a>
</div>

</body>
</html>
