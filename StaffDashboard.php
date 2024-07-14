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

// Fetch counts from respective tables
$sqlAllocationCount = "SELECT COUNT(*) AS allocationCount FROM Allocation";
$sqlDonationCount = "SELECT COUNT(*) AS donationCount FROM Donation";
$sqlDonorCount = "SELECT COUNT(*) AS donorCount FROM Donor";
$sqlAcceptedDonationCount = "SELECT COUNT(*) AS acceptedDonationCount FROM Donation WHERE donationStatus = 'Accepted'";
$sqlTotalAcceptedDonationAmount = "
    SELECT SUM(d.donationAmount) AS totalAcceptedAmount
    FROM Donation d
    INNER JOIN Allocation a ON d.allocationID = a.allocationID
    WHERE d.donationStatus = 'Accepted'
";

$resultAllocation = $conn->query($sqlAllocationCount);
$resultDonation = $conn->query($sqlDonationCount);
$resultDonor = $conn->query($sqlDonorCount);
$resultAcceptedDonationCount = $conn->query($sqlAcceptedDonationCount);
$resultTotalAcceptedAmount = $conn->query($sqlTotalAcceptedDonationAmount);

// Initialize variables to hold counts and amount
$allocationCount = 0;
$donationCount = 0;
$donorCount = 0;
$acceptedDonationCount = 0;
$totalAcceptedAmount = 0;

// Fetch counts if queries are successful
if ($resultAllocation && $resultDonation && $resultDonor && $resultAcceptedDonationCount && $resultTotalAcceptedAmount) {
    $rowAllocation = $resultAllocation->fetch_assoc();
    $allocationCount = $rowAllocation['allocationCount'];

    $rowDonation = $resultDonation->fetch_assoc();
    $donationCount = $rowDonation['donationCount'];

    $rowDonor = $resultDonor->fetch_assoc();
    $donorCount = $rowDonor['donorCount'];

    $rowAcceptedDonationCount = $resultAcceptedDonationCount->fetch_assoc();
    $acceptedDonationCount = $rowAcceptedDonationCount['acceptedDonationCount'];

    $rowTotalAcceptedAmount = $resultTotalAcceptedAmount->fetch_assoc();
    $totalAcceptedAmount = $rowTotalAcceptedAmount['totalAcceptedAmount'];
} else {
    echo "Error fetching counts: " . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f9fcff;
            background-image: linear-gradient(147deg, #f9fcff 0%, #dee4ea 74%);
            font-family: "Inter", sans-serif;
        }

        .detailIndex {
            color: white;
            text-align: center;
            border-radius: 10px;
            margin: 2% auto;
            width: 90%;
            height: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .detailIndex img {
            max-width: 100%;
            height: 100%;
            display: block;
            margin: 0 auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            filter: brightness(1.2) contrast(1.2);
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            color: white;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .summary-box {
            margin: 20px 10px;
            padding: 15px;
            background-color: #4d4855;
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            text-align: center;
            width: 250px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .summary-box img {
            margin-bottom: 10px;
            height: 60px;
        }

        .summary-box p {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
        }

        .summary-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: white;
        }

        .summary-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .btn {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 6px 12px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #ffa000;
        }

        @media (max-width: 768px) {
            .summary-box {
                width: 100%;
            }
        }

        .summary-donation {
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            border-radius: 10px;
            padding: 15px;
            text-align: left;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
        }

        .summary-icon {
            height: 50px;
            margin-right: 20px;
        }

        .summary-content {
            flex: 1;
            padding-left: 20px;
            /* Add padding for better alignment */
        }

        .summary-donation h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: white;
        }

        .summary-donation p {
            font-size: 18px;
            color: white;
            margin-bottom: 10px;
        }

        .summary-donation a.btn {
            background-color: white;
            color: #007bff;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .summary-donation a.btn:hover {
            background-color: #0056b3;
            color: white;
        }

        .summary-count {
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            width: 80%;
            max-width: 500px;
            color: white;
        }
        .summary-icon {
            height: 50px;
            margin-right: 20px;
        }

        .summary-content {
            padding-left: 20px;
            text-align: left;
            flex: 1;
        }

        .summary-content p {
            font-size: 18px;
            color: white;
            margin-bottom: 10px;
        }

        .summary-content a.btn {
            background-color: white;
            color: #007bff;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .summary-content a.btn:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>

</head>

<body>
    <?php
    include('staffHeader.php');
    ?>

    <div class="detailIndex">
        <img src="images/staffHeader.png" alt="Manager Header Image">
    </div>

    <div class="summary-count">
        <img src="images/reportIcon.png" alt="Report Icon" class="summary-icon">
        <div class="summary-content"><br>
            <p>Approved Donations: <?php echo $acceptedDonationCount; ?> | RM<?php echo number_format($totalAcceptedAmount, 2); ?> Collected </p>
        </div>
        <a href="DonationView.php" class="btn btn-primary">View</a>
    </div>

    <div class="summary">
        <div class="summary-box">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>ALLOCATIONS</h3>
                <p><?php echo $allocationCount; ?></p>
                <a href="AllocationView.php" class="btn btn-primary">View</a>
            </div>
        </div>
        <div class="summary-box">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>DONORS</h3>
                <p><?php echo $donorCount; ?></p>
                <a href="DonorView.php" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, if needed -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>