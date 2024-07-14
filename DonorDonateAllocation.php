<?php
session_start();
include 'dbConnect.php'; // Use the existing $conn variable for the connection
$title = "Donation Page";
include 'DonorHeader.php';

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
            font-size: 1.25rem; /* Adjusted font size */
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
            padding: 8px 16px; /* Adjusted padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            font-size: 0.9rem; /* Adjusted font size */
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
    </style>
</head>
<body>

<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $progress = ($row["currentAmount"] / $row["targetAmount"]) * 100;
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($row["allocationImage"]) . '" alt="' . htmlspecialchars($row["allocationName"]) . '">';
            echo '<div class="card-content">';
            echo '<h2>' . htmlspecialchars($row["allocationName"]) . '</h2>';
            echo '<p>' . htmlspecialchars(substr($row["allocationDetails"], 0, 100)) . '...</p>';
            echo '<a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '">Read More</a>';
            echo '<p id="' . $row["allocationID"] . '-details" style="display: none;">' . htmlspecialchars($row["allocationDetails"]) . '</p>';
            echo '</div>';
            echo '<div class="card-footer">';
            echo '<div class="raised">Raised: MYR ' . number_format($row["currentAmount"], 2) . '</div>';
            echo '<div class="goal">Goal: MYR ' . ($row["targetAmount"] > 0 ? number_format($row["targetAmount"], 2) : 'Infinite') . '</div>';
            echo '<div class="progress-bar-container">';
            echo '<div class="progress-bar" style="width:' . $progress . '%;"></div>';
            echo '</div>';

            // Check if allocation is closed or inactive
            if ($row["allocationStatus"] == 'Inactive' || $row["currentAmount"] >= $row["targetAmount"]) {
                echo '<button class="closed-button" disabled>CLOSED</button>';
                $sql_update = "UPDATE Allocation SET allocationStatus = 'Inactive' WHERE allocationID = '" . $row["allocationID"] . "'";

            } else {
                echo '<a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '" class="donate-button">Donate Now</a>';
                $sql_update = "UPDATE Allocation SET allocationStatus = 'Active' WHERE allocationID = '" . $row["allocationID"] . "'";
            }

            echo '</div>'; // card-footer
            echo '</div>'; // card
        }
    } else {
        echo "<p>No allocations found</p>";
    }
    $conn->close();
    ?>
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
