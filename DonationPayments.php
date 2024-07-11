<?php
include 'dbConnect.php';
$title = "Donation Page";
include 'DonorHeader.php'; // Include your header here

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donations
$sql = "SELECT * FROM donation";
$result = $conn->query($sql);
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
            background-color: black;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
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
                        <th>Donation Method</th>
                        <th>Donation Status</th>
                        <th>Donation Receipt</th>
                        <th>Allocation ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['donationID']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationAmount']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationMethod']); ?></td>
                            <td><?php echo htmlspecialchars($row['donationStatus']); ?></td>
                            <td>
                                <?php if (!empty($row['donationReceipt'])): ?>
                                    <?php $receiptPath = 'uploads/' . urlencode($row['donationReceipt']); ?>
                                    <button class="view-receipt-btn" data-receipt-url="<?php echo htmlspecialchars($receiptPath); ?>">View Receipt</button>
                                    <!-- Debug information -->
                                    <div style="display:none;"><?php echo htmlspecialchars($receiptPath); ?></div>
                                <?php else: ?>
                                    No Receipt
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['allocationID']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    $files = scandir('uploads/');
    echo '<pre>';
    print_r($files);
    echo '</pre>';
    ?>
    <script>
        // JavaScript for handling receipt viewing with SweetAlert
        const receiptButtons = document.querySelectorAll('.view-receipt-btn');
        receiptButtons.forEach(button => {
            button.addEventListener('click', function() {
                const receiptUrl = this.getAttribute('data-receipt-url');
                const fileExtension = receiptUrl.split('.').pop().toLowerCase();

                let receiptHtml = '';

                if (fileExtension === 'pdf') {
                    receiptHtml = `
                        <div style="text-align: center;">
                            <embed src="${receiptUrl}" width="100%" height="600px" type="application/pdf">
                        </div>
                    `;
                } else {
                    receiptHtml = `
                        <div style="text-align: center;">
                            <img src="${receiptUrl}" class="receipt-image" alt="Receipt" onerror="this.onerror=null;this.src='error.png';">
                        </div>
                    `;
                }

                // Show SweetAlert with embedded receipt image or PDF
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
                            text: "Download",
                            value: "download", // Custom value to distinguish download action
                        },
                    },
                }).then((value) => {
                    if (value === "download") {
                        // Show loading state with three dots
                        swal({
                            title: "Downloading...",
                            text: "Please wait",
                            buttons: false, // Disable any buttons during loading state
                            closeOnClickOutside: false, // Prevent closing by clicking outside
                            closeOnEsc: false, // Prevent closing by pressing ESC key
                            timer: 3000, // Adjust timeout as needed (in milliseconds)
                        }).then(() => {
                            // After loading state, open the receipt URL in a new tab
                            window.open(receiptUrl, '_blank');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
