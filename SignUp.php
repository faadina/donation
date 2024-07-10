<?php
require_once("dbConnect.php"); // Include your database connection file

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
    if ($stmt_donor) {
        mysqli_stmt_bind_param($stmt_donor, "s", $username);
        mysqli_stmt_execute($stmt_donor);
        mysqli_stmt_store_result($stmt_donor);
        if (mysqli_stmt_num_rows($stmt_donor) > 0) {
            $username_err = "This username is already taken.";
        }
        mysqli_stmt_close($stmt_donor);
    } else {
        echo "Something went wrong with the donor table query.";
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } elseif (strlen(trim($_POST["password"])) > 50) {
        $password_err = "Password exceeds the maximum allowed length.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($name_err) && empty($birthdate_err) && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO donor (donorID, donorPassword, donorName, donorDOB, donorAddress, donorPhoneNo, donorEmail) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // Set parameters
            $param_password = md5($password); 
            $param_name = $name;
            $param_birthdate = $birthdate;
            $param_address = $address;
            $param_phone = $phone;
            $param_email = $email;

            // Bind parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $username, $param_password, $param_name, $param_birthdate, $param_address, $param_phone, $param_email);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Registration successful, redirect to login page
                echo "<script>alert('Registration successful. Please login.');</script>";
                echo "<script>location.href='MainLogin.php';</script>";
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

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
        background-color: whitesmoke; /* Dark Cyan Theme Background */
        color: #FFFFFF; /* White text for contrast */
    }
    .wrapper { 
        color: black;
        width: 360px; 
        padding: 20px; 
        margin: auto;
        margin-top: 100px;
        background-color: whitesmoke; /* Dark Cyan Box Background */
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
        color: #808080;
        font-size: 12px;
        margin-top: 10px;
    }
</style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" class="form-control <?php echo (!empty($birthdate_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $birthdate; ?>">
                <span class="invalid-feedback"><?php echo $birthdate_err; ?></span>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo $address; ?></textarea>
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="MainLogin.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
