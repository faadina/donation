<?php
// Include config file
require_once "dbConnect.php";

// Initialize variables
$username = $password = "";
$message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start or resume a session
    session_start();

    // Validate inputs
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
    } else {
        // Define an array to hold the SQL statements for each user type
        $sqlStatements = [
            "donor" => "SELECT donorID, donorPassword FROM Donor WHERE donorID = ?",
            "manager" => "SELECT managerID, managerPassword FROM Manager WHERE managerID = ?",
            "staff" => "SELECT staffID, staffPassword FROM Staff WHERE staffID = ?"
        ];

        // Loop through each SQL statement and check for a match
        foreach ($sqlStatements as $userType => $sql) {
            // Attempt to prepare the SQL statement
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameter
                $param_username = $username;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $stored_password);

                    // Check if username exists and verify password
                    if (mysqli_stmt_fetch($stmt)) {
                        // Compare plain text password
                        if ($password === $stored_password) {
                            // Password is correct, so start a new session if not already started
                            if (!isset($_SESSION["loggedin"])) {
                                session_start();
                            }

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect user to the appropriate page based on user type
                            switch ($userType) {
                                case "donor":
                                    header("Location: DonorDonateAllocation.php");
                                    exit;
                                case "manager":
                                    header("Location: ManagerDashboard.php");
                                    exit;
                                case "staff":
                                    header("Location: StaffDashboard.php");
                                    exit;
                            }
                        } else {
                            // Display an error message if password is not valid
                            $message = "Wrong password.";
                        }
                        break; // Exit the loop once a match is found
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

        if (empty($message)) {
            // If no match was found, set a default error message
            $message = "Username not found or password incorrect.";
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
