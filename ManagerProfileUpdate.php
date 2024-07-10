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
$sql = "SELECT managerID, managerName, managerPhoneNo,managerEmail FROM manager WHERE managerID = ?";
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
            mysqli_stmt_bind_result($stmt, $id, $name,  $phone, $email);
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
    if (empty(trim($_POST['name']))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST['name']);
    }


    // Validate phone number
    $phone = trim($_POST['phone']);
    if (empty($phone)) {
        $phone_err = "Please enter your phone number.";
    } elseif (!preg_match('/^\d{3}-\d{7}$/', $phone)) {
        $phone_err = "Please enter a valid phone number in the format XXX-XXXXXXX.";
    } else {
        $phone = trim($_POST['phone']);
    }

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST['email']);
    }

    // Check input errors before updating the database
    if (empty($name_err) && empty($birthdate_err)  && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Update the user details in the database
        $sql = "UPDATE manager SET managerName = ?,  managerPhoneNo = ?,  managerEmail = ? WHERE managerID = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $username);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Set session variable for success message
                $_SESSION['profile_update_success'] = true;
                // Redirect to profile page after successful update
                header("location: ManagerProfile.php");
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
    <?php include('ManagerHeader.php'); ?>

    <div class="content_section">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <br><br>
        <h1>Manager Profile Information</h1>
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
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control">
                <span class="error"><?php echo $phone_err; ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="button-container">
                <a href="ManagerProfile.php" class="edit-button">↤ Back</a>
                <button type="submit" class="edit-button">Update ⟳</button>
            </div>
        </form>
    </div>
</body>

</html>
