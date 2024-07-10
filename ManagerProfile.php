<?php
session_start();

/*// Check if the user is logged in, if not then redirect to login page*/
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];


// Fetch the user details from the database
$sql = "SELECT donorID, donorName, donorDOB, donorPhoneNo,  donorAddress,donorEmail FROM donor WHERE donorID = ?";
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
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: white;
            font-family: "Inter", sans-serif;
        }

        .content_section {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .content_section h1 {
            color: white;
            background: black;
            width: 50%;
            height: 40%;
            font-weight: 700;
            margin-top: 3%;
            margin-bottom: 10px;
            text-align: center;
            box-shadow: black;
            border-radius: 10px;
            text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.5);
        }

        .content_box {
            width: 50%;
            height: auto;
            border-radius: 26px;
            background: linear-gradient(0deg, white 0%, #EFE8E8 100%);
            box-shadow: 7px 4px 4px rgba(0, 0, 0, 0.25);
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            margin-bottom: 3px;
            color: black;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .content_box table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            text-align: center;
        }

        .content_box th {
            font-weight: 700;
            font-size: 1.5em; /* Increased font size */
            padding: 10px; /* Increased padding */
        }

        .shadow-img {
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));
            width: 150px; /* Increased size of the user icon */
            height: auto;
            margin-bottom: 20px; /* Add margin below the user icon */
        }

        .update-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3faf9;
            color: #104854;
            border: 2px solid black;
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
            margin-right: 1px;
            width: 40%;
            filter: black;
            margin-top: 20px; /* Increased margin-top */
        }

        .update-button:hover {
            transform: scale(1.07);
        }
    </style>
</head>
<body>
    <?php include('donorHeader.php'); ?>
    <div class="content_section">
        <h1></h1>
        <h1>MANAGER PROFILE INFORMATION</h1>
        <div class="content_box">
            <img src="images/userIcon.png" class="shadow-img">
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
                <img src="images/editIcon.png" alt="Edit Icon">UPDATE PROFILE
            </a>
            <br>
        </div>
    </div>
</body>
</html>
