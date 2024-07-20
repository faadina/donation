<?php
session_start();
include 'dbConnect.php'; // Use the existing $conn variable for the connection
$title = "Donation Page";
include 'DonorHeader.php';
require_once("birthdayNotificationCheck.php");

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Ensure the connection is properly established using $conn from dbConnect.php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve all allocations from the database
$sql = "SELECT * FROM Allocation";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 40px;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
            flex: 1;
        }
        .card-content h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.25rem;
            color: #333;
        }
        .card-content p {
            margin: 0 0 10px;
            color: #555;
            line-height: 1.4;
            font-size: 0.9rem;
        }
        .card-content a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .card-footer {
            padding: 15px;
            background-color: #fafafa;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 1px solid #e0e0e0;
        }
        .raised, .goal {
            margin: 0;
            font-size: 0.9rem;
            color: #777;
        }
        .progress-bar-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
            position: relative;
        }
        .progress-bar {
            height: 15px;
            background-color: #40E0D0;
            width: 0;
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 1rem 1rem;
            animation: progress-bar-stripes 1s linear infinite;
        }
        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }
            100% {
                background-position: 0 0;
            }
        }
        .donate-button, .closed-button {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }
        .donate-button:hover {
            background-color: #218838;
        }
        .closed-button {
            background-color: #dc3545;
        }
        .closed-button:hover {
            background-color: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php if (isset($_SESSION["birthday_notification"]) && $_SESSION["birthday_notification"] === true): ?>
    <div id="birthdayModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Happy Birthday!</h2>
            <p>We have a special message for you.</p>
            <iframe src="birthdayEmail.html" frameborder="0" style="width:100%; height:400px;"></iframe>
        </div>
    </div>
    <script>
        var modal = document.getElementById("birthdayModal");
        var span = document.getElementsByClassName("close")[0];

        // Check session storage to see if the popup has been shown
        if (!sessionStorage.getItem('birthdayPopupShown')) {
            modal.style.display = "block";
            sessionStorage.setItem('birthdayPopupShown', 'true'); // Set the flag in session storage
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <?php unset($_SESSION["birthday_notification"]); ?>
<?php endif; ?>
<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php $progress = ($row["currentAmount"] / $row["targetAmount"]) * 100; ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($row["allocationImage"]); ?>" alt="<?php echo htmlspecialchars($row["allocationName"]); ?>">
                <div class="card-content">
                    <h2><?php echo htmlspecialchars($row["allocationName"]); ?></h2>
                    <p><?php echo htmlspecialchars(substr($row["allocationDetails"], 0, 100)); ?>...</p>
                    <a href="DonationPayments.php?allocationID=<?php echo htmlspecialchars($row["allocationID"]); ?>">Read More</a>
                    <p id="<?php echo $row["allocationID"]; ?>-details" style="display: none;"><?php echo htmlspecialchars($row["allocationDetails"]); ?></p>
                </div>
                <div class="card-footer">
                    <div class="raised">Raised: MYR <?php echo number_format($row["currentAmount"], 2); ?></div>
                    <div class="goal">Goal: MYR <?php echo $row["targetAmount"] > 0 ? number_format($row["targetAmount"], 2) : 'Infinite'; ?></div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                    </div>

                    <?php if ($row["allocationStatus"] == 'Inactive' || $row["currentAmount"] >= $row["targetAmount"]): ?>
                        <button class="closed-button" disabled>CLOSED</button>
                        <?php $sql_update = "UPDATE Allocation SET allocationStatus = 'Inactive' WHERE allocationID = '" . $row["allocationID"] . "'"; ?>
                    <?php else: ?>
                        <a href="DonationPayments.php?allocationID=<?php echo htmlspecialchars($row["allocationID"]); ?>" class="donate-button">Donate Now</a>
                        <?php $sql_update = "UPDATE Allocation SET allocationStatus = 'Active' WHERE allocationID = '" . $row["allocationID"] . "'"; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No allocations found</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
</div>

<script>
function toggleDetails(cardId) {
    var details = document.getElementById(cardId + '-details');
    var readMoreBtn = document.getElementById(cardId + '-readmore');

    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'block';
        readMoreBtn.innerText = 'Read Less';
    } else {
        details.style.display = 'none';
        readMoreBtn.innerText = 'Read More';
    }
}
</script>

</body>
</html>
