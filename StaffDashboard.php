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

$resultAllocation = $conn->query($sqlAllocationCount);
$resultDonation = $conn->query($sqlDonationCount);
$resultDonor = $conn->query($sqlDonorCount);
$resultAcceptedDonationCount = $conn->query($sqlAcceptedDonationCount);

// Initialize variables to hold counts
$allocationCount = 0;
$donationCount = 0;
$donorCount = 0;
$acceptedDonationCount = 0;

// Fetch counts if queries are successful
if ($resultAllocation && $resultDonation && $resultDonor && $resultAcceptedDonationCount) {
    $rowAllocation = $resultAllocation->fetch_assoc();
    $allocationCount = $rowAllocation['allocationCount'];

    $rowDonation = $resultDonation->fetch_assoc();
    $donationCount = $rowDonation['donationCount'];

    $rowDonor = $resultDonor->fetch_assoc();
    $donorCount = $rowDonor['donorCount'];

    $rowAcceptedDonationCount = $resultAcceptedDonationCount->fetch_assoc();
    $acceptedDonationCount = $rowAcceptedDonationCount['acceptedDonationCount'];
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
    <title>Manager Dashboard</title>
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
        margin: 2% auto;
        max-width: 100%;
        padding: 10px;
        position: relative;
        z-index: 1;
    }

    .detailIndex h1 {
        font-size: 40px;
        color: #1a1649;
        text-shadow: 2px 3px 1px rgba(130, 9, 9, 0.1);
        text-align: center;
        font-weight: 700;
    }

    .detailIndex h1,
    .detailIndex h2 {
        margin: 2px;
    }

    .detailIndex h2 {
        font-size: 30px;
        color: #1a1649;
        text-shadow: 2px 3px 1px rgba(130, 9, 9, 0.1);
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
        color: white;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .summary-box {
        margin: 20px 10px; /* Adjusted margin */
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
            width: 100%; /* Full width on smaller screens */
        }
    }
</style>

</head>

<body>
    <?php
    include('staffHeader.php');
    ?>

    <div class="detailIndex">
        <h1>MADRASAH TARBIYYAH ISLAMIYYAH <br>DARUL HIJRAH</h1>
        <h2>DONATION SYSTEM</h2>
        <p>Staff Dashboard</p>
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
        
        <div class="summary-box">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>DONATIONS</h3>
                <p><?php echo $acceptedDonationCount; ?></p>
                <a href="DonationView.php" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, if needed -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
