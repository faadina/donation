<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

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
            mysqli_stmt_bind_result($stmt, $id, $name, $phone, $email);
            mysqli_stmt_fetch($stmt);

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Manager Dashboard</title>
    <style>
        body { 
            background-color: whitesmoke; /* Dark Cyan Theme Background */
            color: #FFFFFF; /* White text for contrast */
        }

        .detailIndex {
            margin: 2% auto;
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .detailIndex h1 {
            font-size: 50px;
            color: #1a1649;
            margin-bottom: 3px;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex h2 {
            font-size: 35px;
            color: #1a1649;
            margin-bottom: 1%;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex p {
            color: #1a5172;
            line-height: 22px;
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px transparent #ccc;
            color: white;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .summary-box {
            display: flex;
            align-items: center;
            margin: 15px;
            padding: 20px;
            border: 1px transparent #ccc;
            background-color: #4d4855;
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            text-align: left;
            width: 30%;
        }

        .summary-box img {
            margin-right: 15px;
            height: 80px;
        }

        .summary-box div {
            text-align: center;
        }

        .summary-box p {
            font-size: 35px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .summary-box h3 {
            font-size: 15px;
        }

        .summary-box:hover {
            transform: translateY(-10px);
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
            filter: drop-shadow(1px 1px 2px rgba(244, 242, 239, 0.8));
        }

        .btn {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php
    include('managerHeader.php');
    ?>

    <div class="detailIndex">
        <h1>MADRASAH TARBIYYAH ISLAMIYYAH <br>DARUL HIJRAH</h1>
        <h2>DONATION SYSTEM</h2>
        <p>Manager Dashboard</p>
    </div>

    <div class="summary">
        <div class="summary-box" style="background-color:#2a3f45">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>DONATION ALLOCATION REPORT</h3>
                <a href="reportDonationAllocation.php" class="btn">View Report</a>
            </div>
        </div>
        <div class="summary-box" style="background-color:#2a3f45">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>MONTHLY DONATION REPORT</h3>
                <a href="reportMonthlyDonation.php" class="btn">View Report</a>
            </div>
        </div>
    </div>
</body>

</html>