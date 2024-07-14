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
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if username already exists in donor table
    $sql_donor = "SELECT donorID FROM donor WHERE donorID = ?";
    $stmt_donor = mysqli_prepare($conn, $sql_donor);
    mysqli_stmt_bind_param($stmt_donor, "s", $param_username);
    $param_username = $username;
    mysqli_stmt_execute($stmt_donor);
    mysqli_stmt_store_result($stmt_donor);

    if (mysqli_stmt_num_rows($stmt_donor) == 1) {
        $username_err = "This username is already taken.";
    }
    mysqli_stmt_close($stmt_donor);

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must have at least 8 characters.";
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
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_birthdate = $birthdate;
            $param_address = $address;
            $param_phone = $phone;
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: MainLogin.php");
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
            padding: 20px;
            margin-top: 2%;
        }

        .form-container {
            flex: 1;
            padding: 20px;
        }

        .form-control:focus {
            background-color: whitesmoke;
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
            color: #808080;
            font-size: 12px;
            margin-top: 10px;
        }

        .input-group {
            position: relative;
            margin: 10px 0;
        }

        .input-group input,
        .input-group textarea {
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

        input:focus,
        textarea:focus {
            border: 2px solid #4D5D53;
        }

        input:focus~label,
        textarea:focus~label,
        input:valid~label,
        textarea:valid~label {
            top: 0;
            left: 20px;
            font-size: 16px;
            padding: 0 2px;
            background: whitesmoke;
        }

        .form-group {
            margin-bottom: 1rem;
        }
        
        .btn-register {
            background-color: #444C38;
            border: none;
            border-radius: 8px;
            padding: 5px 100px;
            font-weight: 600;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            text-align: center;
            margin: 0 auto;
        }

        .btn-register:hover {
            background-color: #B2BEB5;
            color: #444C38;

        }
        
        .invalid-feedback {
            color: red;
            font-size: 12px;
            position: absolute;
            bottom: -20px; /* Adjusted to ensure visibility */
            left: 20px;
        }
    </style>
</head>

<body>
    <?php
    include('MainHeader.php');
    ?>
    <div class="wrapper">
        <div class="form-container">
            <h2 style="text-align:center; color:#444C38; font-weight:700;">Sign Up</h2>
            <p style="text-align:center;">Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group" style="position: relative;">
                    <input type="text" name="username" required value="<?php echo $username; ?>">
                    <label>Username</label>
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="password" name="password" required value="<?php echo $password; ?>">
                    <label>Password</label>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="text" name="name" required value="<?php echo $name; ?>">
                    <label>Name</label>
                    <span class="invalid-feedback"><?php echo $name_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="date" name="birthdate" required value="<?php echo $birthdate; ?>">
                    <label style="top: 5px; left: 15px; font-size: 12px;">Birthdate</label>
                    <span class="invalid-feedback"><?php echo $birthdate_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="text" name="address" required value="<?php echo $address; ?>">
                    <label>Address</label>
                    <span class="invalid-feedback"><?php echo $address_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="text" name="phone" required value="<?php echo $phone; ?>">
                    <label>Phone Number</label>
                    <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="email" name="email" required value="<?php echo $email; ?>">
                    <label>Email</label>
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-register" value="Submit">
                </div>
            </form>
            <p style="text-align:center;">Already have an account? <a href="MainLogin.php" style="text-decoration: none; color: #6B8E23; font-weight:700;">Login Here</a>.</p>
        </div>
    </div>
</body>

</html>
