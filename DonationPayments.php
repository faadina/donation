<?php
session_start();
include 'dbConnect.php';

$title = "Donation Page";
include 'DonorHeader.php'; // Assuming this includes your header

// Check if user ID is set in session
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];
$allocationID = $_GET['allocationID'] ?? '';

if (empty($allocationID)) {
    die('No allocation selected.');
}

// Fetch allocation details for display
$stmt = $conn->prepare("SELECT * FROM Allocation WHERE allocationID = ?");
$stmt->bind_param('s', $allocationID);
$stmt->execute();
$result = $stmt->get_result();
$allocation = $result->fetch_assoc();
$stmt->close();

// Fetch allocation status from Allocation table
$allocationStatus = $allocation['allocationStatus'] ?? '';

// Check if donation status is 'Inactive'
$isInactive = ($allocationStatus === 'Inactive');

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 10%;
            padding: 0;
        }
        .main-content {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .allocation-details {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: row;
        }
        .allocation-image {
            max-width: 40%;
            margin-right: 20px;
        }
        .allocation-image img {
            width: 100%;
            border-radius: 5px;
        }
        .allocation-info {
            flex-grow: 1;
        }
        .allocation-info h2 {
            margin-top: 0;
        }
        .allocation-info p {
            margin: 5px 0;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .btn-closed {
            background-color: #dc3545;
            color: white;
        }
        .btn-closed:hover {
            background-color: #c82333;
        }
        .progress-container {
            flex-basis: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        .progress-bar-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar {
            height: 20px;
            background-color: #28a745;
            width: <?php echo ($allocation['currentAmount'] / $allocation['targetAmount']) * 100; ?>%;
        }
        .raised-goal {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .raised, .goal {
            margin: 0;
        }
    </style>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
</head>
<body>
    <div class="main-content">
        <?php if ($allocation): ?>
        <div class="allocation-details">
            <div class="allocation-image">
                <img src="<?php echo htmlspecialchars($allocation['allocationImage']); ?>" alt="Allocation Image">
            </div>
            <div class="allocation-info">
                <h2><b><?php echo htmlspecialchars($allocation['allocationName']); ?></b></h2>
                <p><strong>Details:</strong> <?php echo htmlspecialchars($allocation['allocationDetails']); ?></p>
                <div class="raised-goal">
                    <div class="raised">Raised: RM <?php echo htmlspecialchars($allocation['currentAmount']); ?></div>
                    <div class="goal">Goal: RM <?php echo htmlspecialchars($allocation['targetAmount']); ?></div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar"></div>
                </div>
                <form id="donationForm" enctype="multipart/form-data">
                    <input type="hidden" name="donorID" value="<?php echo htmlspecialchars($donorID); ?>">
                    <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocationID); ?>">
                    <div class="form-group">
                        <label for="donationAmount">Donation Amount (RM):</label>
                        <input type="number" id="donationAmount" name="donationAmount" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="donationReceipt">Donation Receipt (PDF only):</label>
                        <input type="file" id="donationReceipt" name="donationReceipt" accept="application/pdf" required>
                    </div>
                    <?php if ($isInactive): ?>
                        <button type="button" class="btn btn-closed" disabled>CLOSED</button>
                        <button type="button" class="btn btn-back" onclick="history.back()">BACK</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success">DONATE NOW</button> 
                        <button type="button" class="btn btn-back" onclick="history.back()">BACK</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php else: ?>
        <p>No allocation found or selected.</p>
        <?php endif; ?>
    </div>

    <!-- Include SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var donationForm = document.getElementById('donationForm');

            donationForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var fileInput = document.getElementById('donationReceipt');
                var file = fileInput.files[0];

                if (!file || file.type !== 'application/pdf') {
                    swal({
                        title: "Invalid File Type",
                        text: "Please upload a PDF file.",
                        icon: "error",
                        button: "OK",
                    });
                    return;
                }

                var formData = new FormData(donationForm);

                fetch('ProceedPayment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    var status = data.status;
                    var message = data.message;
                    var donationID = data.donationID;

                    swal({
                        title: status === 'success' ? "Thank You for Your Donation!" : "Error",
                        text: message + (donationID ? "\nYour Donation ID is: " + donationID : ""),
                        icon: status,
                        buttons: {
                            confirm: {
                                text: "Return to History",
                                value: true,
                                visible: true,
                                className: "btn btn-primary",
                                closeModal: true
                            }
                        }
                    }).then((willGoToHistory) => {
                        if (willGoToHistory) {
                            window.location.href = "DonorDonateHistory.php";
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    swal("Error", "An error occurred while processing your donation.", "error");
                });
            });
        });
    </script>
</body>
</html>
