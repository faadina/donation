<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Initialize variables
$name = $birthdate  = $address = $phone = $email = "";
$name_err = $birthdate_err  = $address_err = $phone_err = $email_err = "";

// Fetch the user details from the database
$sql = "SELECT donorID, donorName, donorDOB, donorPhoneNo, donorAddress, donorEmail FROM donor WHERE donorID = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Set parameters
    $param_username = $username;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if the user exists, if yes then fetch the details
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $birthdate, $phone, $address, $email);
            mysqli_stmt_fetch($stmt);

            // Format the birthdate to Y-m-d
            $birthdate = date('Y-m-d', strtotime($birthdate));
        } else {
            // User doesn't exist
            echo "User doesn't exist.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } elseif (!preg_match("/^[a-zA-Z@. ]*$/", $_POST["name"])) {
        $name_err = "Name can only contain letters, spaces, '.', and '@'.";
    } elseif (preg_match("/\d/", $_POST["name"])) {
        $name_err = "Name cannot contain numbers.";
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
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);


        // Check if email already exists in staff table
        $sql_staff = "SELECT staffID FROM staff WHERE staffEmail = ?";
        $stmt_staff = mysqli_prepare($conn, $sql_staff);
        if ($stmt_staff) {
            mysqli_stmt_bind_param($stmt_staff, "s", $email);
            mysqli_stmt_execute($stmt_staff);
            mysqli_stmt_store_result($stmt_staff);
            if (mysqli_stmt_num_rows($stmt_staff) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_staff);
        } else {
            echo "Something went wrong with the staff table query.";
        }

        // Check if email already exists in manager table
        $sql_manager = "SELECT managerID FROM manager WHERE managerEmail = ?";
        $stmt_manager = mysqli_prepare($conn, $sql_manager);
        if ($stmt_manager) {
            mysqli_stmt_bind_param($stmt_manager, "s", $email);
            mysqli_stmt_execute($stmt_manager);
            mysqli_stmt_store_result($stmt_manager);
            if (mysqli_stmt_num_rows($stmt_manager) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_manager);
        } else {
            echo "Something went wrong with the manager table query.";
        }
    }

    // Check input errors before updating the database
    if (empty($name_err) && empty($birthdate_err)  && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Update the user details in the database
        $sql = "UPDATE donor SET donorName = ?, donorDOB = ?, donorPhoneNo = ?, donorAddress = ?, donorEmail = ? WHERE donorID = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $birthdate, $phone, $address, $email, $username);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Set session variable for success message
                $_SESSION['profile_update_success'] = true;
                // Redirect to profile page after successful update
                header("location: DonorProfile.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Donor | Update Profile</title>
    <link rel="stylesheet" href="css/gProfileUpdateStyletest.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        .content_section {
            margin-top: 100px;
            width: 60%;
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .content_section h1 {

            color: black;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }

        .content_section p {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            color: #333;
        }

        .form-control:disabled {
            background-color: #e9ecef;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .edit-button {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            background-color: black;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #333;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <?php include('DonorHeader.php'); ?>
    <div class="content_section">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <br><br>
        <h1>Update Profile Information</h1>
            <p>Please fill this form to update your details.</p>
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" value="<?php echo htmlspecialchars($id); ?>" disabled class="form-control">
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="form-control">
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="birthdate">Date of Birth</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" class="form-control">
                <span class="error"><?php echo $birthdate_err; ?></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control">
                <span class="error"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" class="form-control">
                <span class="error"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="button-container">
                <a href="DonorProfile.php" class="edit-button">↤ Back</a>
                <button type="submit" class="edit-button">Update ⟳</button>
            </div>
        </form>
    </div>
</body>

</html>
