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
$sql = "SELECT managerID, managerName, managerPhoneNo, managerEmail FROM manager WHERE managerID = ?";
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


        .generate-report {
        margin-left: 1120px;           
        margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php
    include('managerHeader.php');
    ?>

    <div class="detailIndex">
    <h2>REPORT</h2>
    </div>
    <div class="generate-report">
        <a href="ManagerGenerateReport.php" class="btn">Generate Report</a>
    </div>
    <div class="summary">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="summary">';
            echo '<div class="card-content">';
            echo '<div class="summary-box" style="background-color:#2a3f45">';
            echo '<img src="images/reportIcon.png" alt="Report Icon">';
            echo '<div>';
            echo '<h3>DONATION ALLOCATION REPORT</h3>';
            echo '<a href="ManagerReportGenerated.php" class="btn">View Report</a>';
            echo '</div>';
            echo '</div>';

            echo '<h2>' . htmlspecialchars($row["allocationName"]) . '</h2>';
            echo '<p>' . htmlspecialchars(substr($row["allocationDetails"], 0, 100)) . '</p>';
            echo '<a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '">Read More</a>';
            echo '<p id="' . $row["allocationID"] . '-details" style="display: none;">' . htmlspecialchars($row["allocationDetails"]) . '</p>';
            echo '</div>';
            echo '<div class="card-footer">';
            echo '<div class="raised">Raised: MYR ' . number_format($row["currentAmount"], 2) . '</div>';
            echo '<div class="goal">Goal: MYR ' . ($row["targetAmount"] > 0 ? number_format($row["targetAmount"], 2) : 'Infinite') . '</div>';
            echo '<div class="progress-bar-container">';
            echo '<div class="progress-bar" style="width:' . $progress . '%;"></div>';
            echo '</div>';

            // Check if allocation is closed or inactive
            if ($row["allocationStatus"] == 'Inactive' || $row["currentAmount"] >= $row["targetAmount"]) {
                echo '<button class="closed-button" disabled>CLOSED</button>';
                $sql_update = "UPDATE Allocation SET allocationStatus = 'Inactive' WHERE allocationID = '" . $row["allocationID"] . "'";

            } else {
                echo '<a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '" class="donate-button">Donate Now</a>';
                $sql_update = "UPDATE Allocation SET allocationStatus = 'Active' WHERE allocationID = '" . $row["allocationID"] . "'";
            }

            echo '</div>'; // card-footer
            echo '</div>'; // card
        }
    } else {
        echo "<p>No allocations found</p>";
    }
    $conn->close();
    ?>
</div>
    <div class="summary">
        <div class="summary-box" style="background-color:#2a3f45">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>DONATION ALLOCATION REPORT</h3>
                <a href="ManagerReportGenerated.php" class="btn">View Report</a>
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