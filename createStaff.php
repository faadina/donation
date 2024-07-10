<?php
require_once("dbConnect.php"); 

// Initialize variables
$staffID = $staffName = $staffPhone = $staffEmail = $staffPassword = "";
$staffID_err = $staffName_err = $staffPhone_err = $staffEmail_err = $staffPassword_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate staff ID
    if (empty(trim($_POST["staffID"]))) {
        $staffID_err = "Please enter a staff ID.";
    } else {
        $staffID = trim($_POST["staffID"]);
    }

    // Validate staff name
    if (empty(trim($_POST["staffName"]))) {
        $staffName_err = "Please enter the staff's name.";
    } elseif (!preg_match("/^[a-zA-Z@. ]*$/", $_POST["staffName"])) {
        $staffName_err = "Name can only contain letters, spaces, '.', and '@'.";
    } else {
        $staffName = trim($_POST["staffName"]);
    }

    // Validate staff phone number
    if (!empty(trim($_POST["staffPhone"]))) {
        if (!preg_match("/^[0-9-]*$/", $_POST["staffPhone"])) {
            $staffPhone_err = "Phone number can only contain digits and hyphens (-).";
        } else {
            $staffPhone = trim($_POST["staffPhone"]);
        }
    }

    // Validate staff email
    if (empty(trim($_POST["staffEmail"]))) {
        $staffEmail_err = "Please enter the staff's email.";
    } elseif (!filter_var(trim($_POST["staffEmail"]), FILTER_VALIDATE_EMAIL)) {
        $staffEmail_err = "Invalid email format.";
    } else {
        $staffEmail = trim($_POST["staffEmail"]);
    }

    // Validate staff password
    if (empty(trim($_POST["staffPassword"]))) {
        $staffPassword_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["staffPassword"])) < 6) {
        $staffPassword_err = "Password must have at least 6 characters.";
    } elseif (strlen(trim($_POST["staffPassword"])) > 50) {
        $staffPassword_err = "Password exceeds the maximum allowed length.";
    } else {
        $staffPassword = trim($_POST["staffPassword"]);
    }

    // Check input errors before inserting in database
    if (empty($staffID_err) && empty($staffName_err) && empty($staffPhone_err) && empty($staffEmail_err) && empty($staffPassword_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO staff (staffID, staffName, staffPhoneNo, staffEmail, staffPassword) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "sssss", $staffID, $staffName, $staffPhone, $staffEmail, $staffPassword);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Registration successful, redirect to StaffDetails.php
                header("location: StaffDetails.php?staffID=" . urlencode($staffID));
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
<title>Create Staff</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style type="text/css">
    body { 
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
    <?php
    include('ManagerHeader.php');
    ?>
    <div class="wrapper">
        <h2>Create Staff</h2>
        <p>Please fill this form to create a staff member.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Staff ID</label>
                <input type="text" name="staffID" class="form-control <?php echo (!empty($staffID_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $staffID; ?>">
                <span class="invalid-feedback"><?php echo $staffID_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="staffPassword" class="form-control <?php echo (!empty($staffPassword_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $staffPassword; ?>">
                <span class="invalid-feedback"><?php echo $staffPassword_err; ?></span>
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="staffName" class="form-control <?php echo (!empty($staffName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $staffName; ?>">
                <span class="invalid-feedback"><?php echo $staffName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="staffPhone" class="form-control <?php echo (!empty($staffPhone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $staffPhone; ?>">
                <span class="invalid-feedback"><?php echo $staffPhone_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="staffEmail" class="form-control <?php echo (!empty($staffEmail_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $staffEmail; ?>">
                <span class="invalid-feedback"><?php echo $staffEmail_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form>
    </div>
</body>
</html>
