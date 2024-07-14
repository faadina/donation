<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch the user details from the database
$sql = "SELECT managerID, managerName, managerPhoneNo, managerEmail FROM manager WHERE managerID = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Set parameters
    $param_username = $username;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if the user exists, if yes then fetch the details
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $phone, $email);
            mysqli_stmt_fetch($stmt);
        } else {
            // User doesn't exist
            echo "User doesn't exist.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Manager Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            background-color: #f4f4f9;
            color: #333;
            font-family: 'Inter', sans-serif;
        }

        .detailIndex {
            color: white;
            text-align: center;
            border-radius: 10px;
            margin: 2% auto;
            width: 90%;
            height: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .detailIndex img {
            max-width: 100%;
            height: 100%;
            display: block;
            margin: 0 auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            filter: brightness(1.2) contrast(1.2);
        }

        .summary {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .summary-box {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            margin: 15px;
            padding: 20px;
            text-align: center;
            width: 40%;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .summary-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .summary-box img {
            margin-bottom: 15px;
            height: 50px;
        }

        .summary-box div {
            text-align: center;
        }

        .summary-box h3 {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 10px;
        }

        .btn {
            background-color: #88b188;
            border: none;
            border-radius: 5px;
            color: black;
            display: block;
            padding: 10px;
            text-decoration: none;
            transition: background-color 0.3s, box-shadow 0.3s;
            width: 100%;
            box-sizing: border-box;
            font-weight: 900;
        }

        .btn:hover {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            color: #88b188;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        #donationChart {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include('managerHeader.php'); ?>

    <div class="detailIndex">
        <img src="images/headerManager.png" alt="Manager Header Image">
    </div>


    <div class="summary">
        <div class="summary-box">
            <div>
                <h3 style="font-weight:800;">MONTHLY DONATION STATUS</h3>
                <div id="donationChartContainer">
                    <canvas id="donationChart"></canvas>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        <?php
        include 'dbConnect.php';

        // Fetch monthly donations
        $sql = "SELECT 
                    DATE_FORMAT(donationDate, '%M') AS month,
                    SUM(donationAmount) AS donationAmount
                FROM 
                    donation
                WHERE 
                    YEAR(donationDate) = 2024
                GROUP BY 
                    month
                ORDER BY 
                    STR_TO_DATE(CONCAT('0001 ', month, ' 01'), '%Y %M %d')";
        $result = $conn->query($sql);

        $months = [];
        $amounts = [];

        while ($row = $result->fetch_assoc()) {
            $months[] = $row['month'];
            $amounts[] = $row['donationAmount'];
        }
        ?>

        const ctx = document.getElementById('donationChart').getContext('2d');
        const donationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Donation Amount (RM)',
                    data: <?php echo json_encode($amounts); ?>,
                    backgroundColor: '#264d26',
                    borderColor: 'grey',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>