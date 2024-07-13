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

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .wrapper {
            display: flex;
            width: 720px;
            background-color: whitesmoke;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
            overflow: hidden;
        }

        .image-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: whitesmoke;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-container {
            flex: 1;
            padding: 20px;
        }

        .form-control:focus {
            background-color: whitesmoke;
            color: #FFFFFF;
        }

        .btn-login {
            background-color: #444C38;
            border: none;
            border-radius: 8px;
            padding: 5px 20px;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
        }

        .btn-login:hover {
            background-color: #B2BEB5;
            color: #444C38;

        }

        .btn-reset {
            background-color: #D0F0C0;
            border: none;
            border-radius: 8px;
            padding: 5px 20px;
        }

        .btn-reset:hover {
            background-color: #808080;
            color: #D0F0C0;
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

        .input-group {
            position: relative;
            margin: 10px 0;
        }

        .input-group input {
            width: 100%;
            height: 50px;
            border-radius: 8px;
            font-size: 18px;
            padding: 0 10px;
            border: 1px solid #1047548e;
            background: transparent;
            color: #104854;
            outline: none;
            margin-bottom: 10px;
        }

        .input-group label {
            position: absolute;
            top: 40%;
            left: 20px;
            transform: translateY(-50%);
            color: #104854;
            font-size: 15px;
            pointer-events: none;
            transition: 0.3s;
        }

        input:focus {
            border: 2px solid #4D5D53;
        }

        input:focus~label,
        input:valid~label {
            top: 0;
            left: 20px;
            font-size: 16px;
            padding: 0 2px;
            background: whitesmoke;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.inline {
            display: flex;
            justify-content: space-between;
        }

        .form-group.inline .btn {
            flex: 1;
            margin: 0 0.5rem;
        }
    </style>
</head>

<body>
    <?php
    include('MainHeader.php');
    ?>
    <div class="container">
        <div class="wrapper">
            <div class="image-container">
                <img src="images/mtidhlogo.jpg" alt="Madarasah Logo">
            </div>
            <div class="form-container">
                <h2 style="text-align:center; color:#444C38; font-weight:700;">Welcome Back!</h2>
                <p style="text-align:center;">Please fill this form to login.</p>

                <?php if (!empty($message)) : ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-group">
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>

                    <div class="form-group inline">
                        <input type="reset" class="btn-reset" value="Reset">
                        <input type="submit" class="btn-login" value="Login">
                    </div>
                </form>
                <br>
                <p style="text-align:center;">Don't have an account? <a href="MainSignUp.php" style="text-decoration: none; color: #6B8E23; font-weight:700;">Register Here</a>.</p>

            </div>
        </div>
    </div>
</body>

</html>