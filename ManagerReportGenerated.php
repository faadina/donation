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

$title = "Manager Page";

// Get reportID from URL parameters
$reportID = $_GET['reportID'] ?? '';

// Debugging: Display the reportID value
echo "reportID: " . htmlspecialchars($reportID) . "<br>";

// Fetch reportName and reportType based on reportID
$sql = "SELECT reportName, reportType FROM report WHERE reportID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Error preparing statement: ' . $conn->error);
}
$stmt->bind_param('s', $reportID); // Assuming reportID is a string, adjust if it's a different type
$stmt->execute();
$stmt->bind_result($reportName, $reportType);
$stmt->fetch();
$stmt->close();

// Debugging: Display the fetched reportName and reportType
echo "reportName: " . htmlspecialchars($reportName) . "<br>";
echo "reportType: " . htmlspecialchars($reportType) . "<br>";

// Depending on reportType, fetch and display data accordingly
if ($reportType === "Donation Allocation Report") {
    // Extract allocationName from reportName
    $allocationName = str_replace(" Allocation Report", "", $reportName);

    // Fetch allocation details based on allocationName
    $sql = "SELECT * FROM Allocation WHERE allocationName = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }
    $stmt->bind_param('s', $allocationName);
    $stmt->execute();
    $allocationReportResult = $stmt->get_result();
    if (!$allocationReportResult) {
        die('Error getting result: ' . $conn->error);
    }

    // Debugging: Check if any rows were returned
    echo "Allocation rows found: " . $allocationReportResult->num_rows . "<br>";

} elseif ($reportType === "Monthly Donation Report") {
    // Fetch monthly donation details based on month-year format of reportName
    $sql = "SELECT DATE_FORMAT(donationDate, '%Y-%m') AS DonationMonth, donationDate, donationAmount
            FROM donation
            WHERE DATE_FORMAT(donationDate, '%Y-%m') = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }
    $stmt->bind_param('s', $reportName);
    $stmt->execute();
    $monthlyReportResult = $stmt->get_result();
    if (!$monthlyReportResult) {
        die('Error getting result: ' . $conn->error);
    }

    // Debugging: Check if any rows were returned
    echo "Monthly donation rows found: " . $monthlyReportResult->num_rows . "<br>";
}

// Check if data is fetched
if ($reportType === "Donation Allocation Report" && isset($allocationReportResult) && $allocationReportResult->num_rows > 0) {
    echo "Rows found: " . $allocationReportResult->num_rows . "<br>";
} elseif ($reportType === "Monthly Donation Report" && isset($monthlyReportResult) && $monthlyReportResult->num_rows > 0) {
    echo "Rows found: " . $monthlyReportResult->num_rows . "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager Dashboard</title>
    <style>
        body {
            background-color: whitesmoke;
            color: #000000;
            font-family: Arial, sans-serif;
        }

        .donation-table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .donation-table th,
        .donation-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .donation-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            color: #000000;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include('managerHeader.php'); ?>

    <div class="summary">
        <?php if ($reportType === "Donation Allocation Report"): ?>
            <!-- Display allocation details -->
            <h3><?php echo htmlspecialchars($reportType); ?>: <?php echo htmlspecialchars($reportName); ?></h3>
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Allocation ID</th>
                        <th>Allocation Name</th>
                        <th>Allocation Start Date</th>
                        <th>Allocation End Date</th>
                        <th>Allocation Status</th>
                        <th>Target Amount</th>
                        <th>Current Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $allocationReportResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['allocationID']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationName']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationStartDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationEndDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationStatus']); ?></td>
                            <td><?php echo htmlspecialchars($row['targetAmount']); ?></td>
                            <td><?php echo htmlspecialchars($row['currentAmount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($reportType === "Monthly Donation Report"): ?>
            <!-- Display monthly donation details -->
            <h3><?php echo htmlspecialchars($reportType); ?>: <?php echo htmlspecialchars($reportName); ?></h3>
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Donation Month</th>
                        <th>Donation Date</th>
                        <th>Donation Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $monthlyReportResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['DonationMonth']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationAmount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Display a message if no valid reportType -->
            <h3>No valid reportType found: <?php echo htmlspecialchars($reportType); ?></h3>
        <?php endif; ?>
    </div>

</body>
</html>
