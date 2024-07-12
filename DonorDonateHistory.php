<?php
session_start();
include 'dbConnect.php';
$title = "Donation History";
include 'DonorHeader.php'; // Include your header here

// Check if user ID is set in session
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donations for the logged-in donor
$stmt = $conn->prepare("
    SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.donationReceipt, a.allocationName 
    FROM Donation d 
    JOIN Allocation a ON d.allocationID = a.allocationID 
    WHERE d.donorID = ?
");
$stmt->bind_param('s', $donorID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>

    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">

    <!-- Include SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

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

        .btn-feedback {
            background-color: darkcyan;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-feedback:hover {
            background-color: #0d7a8a;
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

        @keyframes moveDots {
            0% { content: '.'; }
            25% { content: '..'; }
            50% { content: '...'; }
            75% { content: '..'; }
            100% { content: '.'; }
        }

        .loading-dots:after {
            content: '.';
            animation: moveDots 1s infinite;
            display: inline-block;
        }

        .receipt-iframe {
            width: 100%;
            height: 800px; /* Increased height for better visibility */
            border: none;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <br><br><br><br>
        <h2>Donation History</h2>
        <div class="donation-container">
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Donation Amount</th>
                        <th>Donation Date</th>
                        <th>Donation Status</th>
                        <th>Donation Receipt</th>
                        <th>Allocation Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['donationID']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationAmount']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationStatus']); ?></td>
                            <td>
                                <?php if (!empty($row['donationReceipt'])): ?>
                                    <button class="view-receipt-btn" data-receipt-url="<?php echo htmlspecialchars($row['donationReceipt']); ?>">View Receipt</button>
                                <?php else: ?>
                                    No Receipt
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['allocationName']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // JavaScript for handling receipt viewing with SweetAlert
        const receiptButtons = document.querySelectorAll('.view-receipt-btn');
        receiptButtons.forEach(button => {
            button.addEventListener('click', function() {
                const receiptUrl = this.getAttribute('data-receipt-url');
                const fileExtension = receiptUrl.split('.').pop().toLowerCase();

                if (fileExtension === 'pdf') {
                    // Show SweetAlert with embedded PDF viewer and print option
                    swal({
                        title: "View Receipt",
                        content: {
                            element: "iframe",
                            attributes: {
                                src: receiptUrl,
                                class: "receipt-iframe"
                            },
                        },
                        buttons: {
                            cancel: "Close",
                            confirm: {
                                text: "Print",
                                value: "print", // Custom value to distinguish print action
                            },
                        },
                    }).then((value) => {
                        if (value === "print") {
                            // Open the receipt URL in a new tab for printing
                            window.open(receiptUrl, '_blank');
                        }
                    });
                } else {
                    // Construct HTML content to embed receipt image in SweetAlert message
                    const receiptHtml = `
                        <div style="text-align: center;">
                            <img src="${receiptUrl}" class="receipt-image" alt="Receipt">
                        </div>
                    `;

                    // Show SweetAlert with embedded receipt image and print option
                    swal({
                        title: "View Receipt",
                        content: {
                            element: "div",
                            attributes: {
                                innerHTML: receiptHtml
                            },
                        },
                        buttons: {
                            cancel: "Close",
                            confirm: {
                                text: "Print",
                                value: "print", // Custom value to distinguish print action
                            },
                        },
                    }).then((value) => {
                        if (value === "print") {
                            // Open the receipt URL in a new tab for printing
                            window.open(receiptUrl, '_blank');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
