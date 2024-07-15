<?php
session_start();
include 'dbConnect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement to fetch donation history
$stmt = $conn->prepare("
    SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.donationReceipt, a.allocationName 
    FROM Donation d 
    JOIN Allocation a ON d.allocationID = a.allocationID 
    WHERE d.donorID = ?
");
$stmt->bind_param('s', $donorID);
$stmt->execute();

// Get result set from the executed SQL query
$result = $stmt->get_result();

// Initialize counter for accepted donations
$acceptedCount = 0;
$countDonation = $result->num_rows; // Total number of donations

// Iterate through donation records
while ($row = $result->fetch_assoc()) {
    // Check if donation status is 'Accepted'
    if ($row['donationStatus'] === 'Accepted') {
        $acceptedCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>

    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Include Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .titleTable {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            color: rgb(164, 231, 192);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .donation-table {
            width: 100%;
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
            padding: 12px;
            text-align: left;
        }

        .donation-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .donation-table tr:hover {
            background-color: #f9f9f9;
        }

        .view-receipt-btn {
            background-color: #333;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-receipt-btn:hover {
            background-color: #555;
        }

        .receipt-image {
            max-width: 100%;
            height: auto;
        }

        .receipt-iframe {
            width: 100%;
            height: 500px;
            border: none;
        }

        .headertable {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .accepted {
            color: green;
            font-weight:700;
        }

        .pending {
            color: inherit;
            /* or any other styling you want for pending */
        }
    </style>
</head>

<body>
    <?php include 'DonorHeader.php'; ?> <!-- Include your header file -->

    <div class="wrapper">
        <div class="titleTable">
            <h2>Donation History</h2>
        </div>
        <div class="headertable">
            <h3>Donor ID: <?php echo htmlspecialchars($donorID); ?></h3>
            <div style="background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%); color:white; border-radius:8px; padding:2px 9px;">
                <p>Donations: <b><?php echo $countDonation; ?></b> | Accepted: <b><?php echo $acceptedCount; ?></b></p>
            </div>
        </div>

        <div class="donation-container">
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Allocation Name</th>
                        <th>Donation Amount </th>
                        <th>Donation Date</th>
                        <th>Donation Status</th>
                        <th>Bank Receipt</th>
                        <th>Donation Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset result pointer to start from the beginning
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) :
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['donationID']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationName']); ?></td>
                            <td>RM <?php echo number_format($row['donationAmount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['donationDate']); ?></td>
                            <td style="text-align: center;" class="<?php echo strtolower($row['donationStatus']); ?>"><?php echo htmlspecialchars($row['donationStatus']); ?></td>

                            <td style="text-align: center;">
                                <?php if (!empty($row['donationReceipt'])) : ?>
                                    <button class="view-receipt-btn" data-receipt-url="<?php echo htmlspecialchars($row['donationReceipt']); ?>">⌞ Receipt ⌝</button>
                                <?php else : ?>
                                    No Receipt
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($row['donationStatus'] === 'Accepted') : ?>
                                    <a href="DonationGenerateReceipt.php?donationID=<?php echo urlencode($row['donationID']); ?>">
                                        <img src="images/download (1).png" alt="Generate Receipt" style="width: auto; height: 24px; cursor: pointer; background-image: linear-gradient(315deg, #2b4162 0%, #12100e 74%); color:white; padding: 6px; border-radius: 4px;">
                                    </a>
                                <?php elseif ($row['donationStatus'] === 'pending') : ?>
                                    <div style="color: grey;">
                                        <p>No action</p>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // JavaScript for handling receipt viewing with SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            const receiptButtons = document.querySelectorAll('.view-receipt-btn');
            receiptButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const receiptUrl = this.getAttribute('data-receipt-url');
                    const fileExtension = receiptUrl.split('.').pop().toLowerCase();

                    let titleText = 'View Receipt';
                    let contentHtml = '';

                    if (fileExtension === 'pdf') {
                        contentHtml = '<iframe src="' + receiptUrl + '" class="receipt-iframe" frameborder="0"></iframe>';
                    } else {
                        contentHtml = '<img src="' + receiptUrl + '" class="receipt-image" alt="Receipt">';
                    }

                    Swal.fire({
                        title: titleText,
                        html: contentHtml,
                        showCancelButton: true,
                        confirmButtonText: "Print",
                        cancelButtonText: "Close",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(receiptUrl, '_blank');
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>

<?php
// Close prepared statement and database connection
$stmt->close();
$conn->close();
?>