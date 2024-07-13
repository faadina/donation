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
        /* Your existing CSS styles */
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
        }

        .donation-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .receipt-image {
            max-width: 100%;
            height: auto;
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

        .donation-table th, .donation-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .donation-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .view-receipt-btn {
            background-color: #333;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .view-receipt-btn:hover {
            background-color: #555;
        }

        .receipt-iframe {
            width: 100%;
            height: 800px; /* Increased height for better visibility */
            border: none;
        }
    </style>
</head>
<body>
    <?php include 'DonorHeader.php'; ?> <!-- Include your header file -->

    <div class="wrapper">
        <h2>Donation History</h2>
        <h3>Donor ID: <?php echo htmlspecialchars($donorID); ?></h3>

        <div class="donation-container">
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Allocation Name</th>
                        <th>Donation Amount</th>
                        <th>Donation Date</th>
                        <th>Donation Status</th>
                        <th>Donation Receipt</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['donationID']); ?></td>
                            <td><?php echo htmlspecialchars($row['allocationName']); ?></td>
                            <td>MYR <?php echo number_format($row['donationAmount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['donationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationStatus']); ?></td>
                            <td>
                                <?php if (!empty($row['donationReceipt'])): ?>
                                    <button class="view-receipt-btn" data-receipt-url="<?php echo htmlspecialchars($row['donationReceipt']); ?>">View Receipt</button>
                                <?php else: ?>
                                    No Receipt
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['donationStatus'] === 'Accepted'): ?>
                                    <div class='page-tools'>
                                        <div class='action-buttons'>
                                            <a href='DonationGenerateReceipt.php?donationID=<?php echo urlencode($row['donationID']); ?>' class='btn btn-light mx-1px text-95'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer text-primary-m1 text-120" viewBox="0 0 16 16">
                                                    <path d="M11.5 5h-3v-.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V5zm-1 4h1v3.5a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 8 12.5V9zm-1-4H5v-.5A1.5 1.5 0 0 1 6.5 3h3A1.5 1.5 0 0 1 11 4.5V5zM3 5h1V3H3v2zm0 9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9H3v5zM2 8h12V7H2v1z"/>
                                                </svg>
                                                Receipt
                                            </a>
                                        </div>
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

                    if (fileExtension === 'pdf') {
                        // Show SweetAlert with embedded PDF viewer and print option
                        Swal.fire({
                            title: "View Receipt",
                            html: `<iframe src="${receiptUrl}" class="receipt-iframe" frameborder="0"></iframe>`,
                            showCancelButton: true,
                            confirmButtonText: "Print",
                            cancelButtonText: "Close",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open the receipt URL in a new tab for printing
                                window.open(receiptUrl, '_blank');
                            }
                        });
                    } else {
                        // Show SweetAlert with image preview and print option
                        Swal.fire({
                            title: "View Receipt",
                            html: `<img src="${receiptUrl}" class="receipt-image" alt="Receipt">`,
                            showCancelButton: true,
                            confirmButtonText: "Print",
                            cancelButtonText: "Close",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open the receipt URL in a new tab for printing
                                window.open(receiptUrl, '_blank');
                            }
                        });
                    }
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
