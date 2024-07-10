<?php
$title = "Allocation Details";
include 'DonorHeader.php';
include 'dbConnect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch allocation details
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Using prepared statement to ensure no SQL injection
    $stmt = $conn->prepare("SELECT * FROM Allocation WHERE allocationID = ?");
    $stmt->bind_param('s', $allocationID); // Using 's' for string type
    $stmt->execute();
    $result = $stmt->get_result();
    $allocation = $result->fetch_assoc();
    $stmt->close();

    if (!$allocation) {
        echo "No allocation found with ID $allocationID.";
        exit;
    }

} else {
    echo "No allocation selected.";
    exit;
}
?>

<div class="main-content d-flex justify-content-center">
    <div class="allocation-details d-flex justify-content-center">
        <div class="allocation-image">
            <img src="<?php echo htmlspecialchars($allocation['allocationImage']); ?>" alt="Allocation Image">
        </div>
        <div class="allocation-info">
            <h2><b><?php echo htmlspecialchars($allocation['allocationName']); ?></b></h2>
            <p><strong>Details:</strong> <?php echo htmlspecialchars($allocation['allocationDetails']); ?></p>
            <p><strong>Raised : RM </strong> <?php echo htmlspecialchars($allocation['currentAmount']); ?></p>
            <p><strong>Goal: RM </strong> <?php echo htmlspecialchars($allocation['targetAmount']); ?></p>
            <form id="donationForm" method="POST" action="processDonation.php" enctype="multipart/form-data">
                <label for="donationAmount">Donation Amount (MYR):</label>
                <input type="number" id="donationAmount" name="donationAmount" class="form-control" required min="1" step="0.01">
                
                <label for="donorReceipt">Upload Receipt:</label>
                <input type="file" id="donorReceipt" name="donorReceipt" accept="image/*" class="form-control" required>
                
                <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocation['allocationID']); ?>">
                <button type="button" class="btn btn-success" onclick="validateAndSubmit()">Donate Now</button>
            </form>
        </div>
    </div>
</div>

<script>
    function validateAndSubmit() {
        var donationAmount = document.getElementById('donationAmount').value;
        var allocationID = "<?php echo $allocationID; ?>";

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "validateDonation.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status == "success") {
                    document.getElementById('donationForm').submit();
                } else {
                    alert(response.message);
                }
            }
        };
        xhr.send("donationAmount=" + donationAmount + "&allocationID=" + allocationID);
    }
</script>

</body>
</html>

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
    .form-control {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .btn {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #218838;
    }
</style>
