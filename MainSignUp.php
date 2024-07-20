<?php
require_once("dbConnect.php");

// Initialize variables
$username = $password = $name = $birthdate = $address = $phone = $email = "";
$username_err = $password_err = $name_err = $birthdate_err = $address_err = $phone_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } elseif (!preg_match("/^[a-zA-Z@. ]*$/", $_POST["name"])) {
        $name_err = "Name can only contain letters, spaces, '.', and '@'.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate birthdate
    if (empty(trim($_POST["birthdate"]))) {
        $birthdate_err = "Please enter your birthdate.";
    } else {
        $birthdate = trim($_POST["birthdate"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } elseif (!preg_match("/^[0-9-]*$/", $_POST["phone"])) {
        $phone_err = "Phone number can only contain digits and hyphens (-).";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);

        // Check if email already exists in donor table
        $sql_email = "SELECT donorID FROM donor WHERE donorEmail = ?";
        $stmt_email = mysqli_prepare($conn, $sql_email);
        mysqli_stmt_bind_param($stmt_email, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt_email);
        mysqli_stmt_store_result($stmt_email);

        if (mysqli_stmt_num_rows($stmt_email) == 1) {
            $email_err = "This email is already taken.";
        }
        mysqli_stmt_close($stmt_email);
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);

        // Check if username already exists in donor table
        $sql_username = "SELECT donorID FROM donor WHERE donorID = ?";
        $stmt_username = mysqli_prepare($conn, $sql_username);
        mysqli_stmt_bind_param($stmt_username, "s", $param_username);
        $param_username = $username;
        mysqli_stmt_execute($stmt_username);
        mysqli_stmt_store_result($stmt_username);

        if (mysqli_stmt_num_rows($stmt_username) == 1) {
            $username_err = "This username is already taken.";
        }
        mysqli_stmt_close($stmt_username);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($name_err) && empty($birthdate_err) && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Prepare an insert statement without 'role' column
        $sql = "INSERT INTO donor (donorID, donorName, donorPassword, donorDOB, donorAddress, donorPhoneNo, donorEmail) VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $param_username, $param_name, $param_password, $param_birthdate, $param_address, $param_phone, $param_email);

            // Set parameters
            $param_username = $username;
            $param_name = $name;
            // Directly assign password from $_POST without hashing (for demonstration purposes)
            $param_password = $_POST["password"];
            $param_birthdate = $birthdate;
            $param_address = $address;
            $param_phone = $phone;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: MainLogin.php?registration_success=1");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
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
            background-color: whitesmoke;
            color: #FFFFFF;
        }

        .wrapper {
            width: 50%;
            background-color: whitesmoke;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
            overflow: hidden;
            display: flex;
            margin: auto;
            padding: 4px;
            margin-top: 2%;
        }

        .form-container {
            flex: 1;
            padding: 10px;
        }

        .form-control {
            height: 40px;
        }

        .btn-register {
            background-color: #444C38;
            border: none;
            border-radius: 8px;
            padding: 5px 20px;
            font-weight: 600;
            color: white;
            display: block;
            margin: 20px auto 0;
        }

        .btn-register:hover {
            background-color: #B2BEB5;
            color: #444C38;
        }

        .invalid-feedback {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <?php include('MainHeader.php'); ?>
    <div class="wrapper">
    <?php if (isset($_GET['registration_success']) && $_GET['registration_success'] == '1') {
    echo '<div class="alert alert-success">Registration successful. Please login.</div>';
    
}?>
        <div class="form-container">
            <h2 style="text-align:center; color:#444C38; font-weight:700;">Sign Up</h2>
            <p style="text-align:center;">Please fill this form to create an account as donor.</p>

            <!-- Display all validation errors here -->
            <?php if (!empty($username_err) || !empty($password_err) || !empty($name_err) || !empty($birthdate_err) || !empty($address_err) || !empty($phone_err) || !empty($email_err)) : ?>
                <div class="alert alert-danger">
                    <ul class="error-list">
                        <?php if (!empty($username_err)) : ?>
                            <li><?php echo $username_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($password_err)) : ?>
                            <li><?php echo $password_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($name_err)) : ?>
                            <li><?php echo $name_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($birthdate_err)) : ?>
                            <li><?php echo $birthdate_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($address_err)) : ?>
                            <li><?php echo $address_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($phone_err)) : ?>
                            <li><?php echo $phone_err; ?></li>
                        <?php endif; ?>
                        <?php if (!empty($email_err)) : ?>
                            <li><?php echo $email_err; ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control"  value="<?php echo $password; ?>">
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control"  value="<?php echo $name; ?>">
                </div>
                <div class="form-group">
                    <label for="birthdate">Birthdate| We will wish you happy birthday!</label>
                    <input type="date" name="birthdate" id="birthdate" class="form-control" placeholder="Birthdate" value="<?php echo $birthdate; ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control"  value="<?php echo $address; ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control"  value="<?php echo $phone; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"  value="<?php echo $email; ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-register">Register</button>
                </div>
            </form>
            <p style="text-align:center;">Already have an account? <a href="MainLogin.php" style="text-decoration: none; color: #6B8E23; font-weight:700;">Login Here</a>.</p>
        </div>
    </div>

</body>

</html>
