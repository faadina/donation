<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch the user details from the database
$sql = "SELECT guest_id, guest_name, guest_gender, guest_birthOfDate, guest_address, guest_contactNo, guest_email FROM guest WHERE guest_id = ?";
if ($stmt = mysqli_prepare($dbCon, $sql)) {
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
            mysqli_stmt_bind_result($stmt, $id, $name, $gender, $birthdate, $address, $phone, $email);
            mysqli_stmt_fetch($stmt);

            // Format the birthdate to dd/mm/yyyy
            $birthdate = date('d/m/Y', strtotime($birthdate));
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

// Close connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest | Profile </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/gProfileStyle.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .update-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3faf9;
            color: #104854;
            border: 2px solid #104854;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 0.8em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .update-button img {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }
        .buttons-container {
            display: flex;
            justify-content: flex-end;
            margin-left:10%;
            width: 40%;
            filter: drop-shadow(2px 2px 8px rgba(0, 0, 0, 0.5));
            margin-top:6px;
        }
        .update-button:hover {
            transform: scale(1.07);
        }
    </style>
</head>
<body>
<?php include('navGuest.php'); ?>

<div class="content_section">
    <h1>PROFILE INFORMATION</h1><br>
    <div class="content_box">
        <div class="user-icon-container">
            <img src="image/userIcon.png" class="shadow-img">
        </div>
        <table>
            <tr>
                <th>NAME</th>
                <td>: <?php echo htmlspecialchars($name); ?></td>
            </tr>
            <tr>
                <th>BIRTH OF DATE</th>
                <td>: <?php echo htmlspecialchars($birthdate); ?></td>
            </tr>
            <tr>
                <th>GENDER</th>
                <td>: <?php echo htmlspecialchars($gender); ?></td>
            </tr>
            <tr>
                <th>ADDRESS</th>
                <td>: <?php echo htmlspecialchars($address); ?></td>
            </tr>
            <tr>
                <th>PHONE NUMBER</th>
                <td>: <?php echo htmlspecialchars($phone); ?></td>
            </tr>
            <tr>
                <th>EMAIL</th>
                <td>: <?php echo htmlspecialchars($email); ?></td>
            </tr>
        </table>
    </div>
    <div class="buttons-container">
        <a href="GProfileUpdate.php" class="update-button">
            <img src="image/editIcon.png" alt="Edit Icon">UPDATE PROFILE
        </a>
    </div>
</div>
</body>

</html>
