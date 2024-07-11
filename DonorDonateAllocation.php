<?php
session_start();
include 'dbConnect.php'; // Use the existing $con variable for the connection
$title = "Donation Page";
include 'DonorHeader.php';

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Ensure the connection is properly established using $con from dbConnect.php
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
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 10%;
            padding: 0;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 300px;
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-content {
            padding: 20px;
        }
        .card-content h2 {
            margin-top: 0;
        }
        .card-footer {
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .raised, .goal {
            margin: 0;
        }
        .donate-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .donate-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($row["allocationImage"]) . '" alt="' . htmlspecialchars($row["allocationName"]) . '">';
            echo '<div class="card-content">';
            echo '<h2>' . htmlspecialchars($row["allocationName"]) . '</h2>';
            echo '<p>' . htmlspecialchars(substr($row["allocationDetails"], 0, 100)) . '... <a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '">Read more...</a></p>';
            echo '</div>';
            echo '<div class="card-footer">';
            echo '<div class="raised">Raised: MYR ' . number_format($row["currentAmount"], 2) . '</div>';
            echo '<div class="goal">Goal: MYR ' . ($row["targetAmount"] > 0 ? number_format($row["targetAmount"], 2) : 'Infinite') . '</div>';
            echo '<a href="DonationPayments.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '" class="donate-button">Donate Now</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>No allocations found</p>";
    }
    $conn->close();
    ?>
</div>

</body>
</html>
