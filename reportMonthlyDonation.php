<?php
include 'dbConnect.php';
$title = "Donation Page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager Dashboard</title>
    <style>
        body { 
            background-color: whitesmoke; /* Dark Cyan Theme Background */
            color: #FFFFFF; /* White text for contrast */
        }
        .donation-table {
            align-items: left;
            width: 40%;
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
        .detailIndex {
            margin: 2% auto;
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .detailIndex h1 {
            font-size: 50px;
            color: #1a1649;
            margin-bottom: 3px;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex h2 {
            font-size: 35px;
            color: #1a1649;
            margin-bottom: 1%;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex p {
            color: #1a5172;
            line-height: 22px;
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px transparent #ccc;
            color: BLACK;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .summary-box {
            display: flex;
            align-items: center;
            margin: 15px;
            padding: 20px;
            border: 1px transparent #ccc;
            background-color: #4d4855;
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            text-align: left;
            width: 30%;
        }

        .summary-box img {
            margin-right: 15px;
            height: 80px;
        }

        .summary-box div {
            text-align: center;
        }

        .summary-box p {
            font-size: 35px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .summary-box h3 {
            font-size: 15px;
        }

        .summary-box:hover {
            transform: translateY(-10px);
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
            filter: drop-shadow(1px 1px 2px rgba(244, 242, 239, 0.8));
        }

        .btn {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #donationChart {
            width: 40%;
            margin: 20px;
        }
        .generate-report {
        margin-left: 1120px;           
        margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include('managerHeader.php'); ?>

    <div class="detailIndex">
        <h2>MONTHLY DONATION REPORT</h2>
    </div>
    <div class="generate-report">
                    <button class="btn" onclick="generateReport()">Generate Report</button>
                </div>
    <div class="content-wrapper">
        <div class="summary">
            <?php if (count($months) > 0): ?>
                <table class="donation-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Donation Amount (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($months as $index => $month): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($month); ?></td>
                                <td><?php echo htmlspecialchars($amounts[$index]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <p>No donations found for the year 2024.</p>
            <?php endif; ?>
        </div>
        
        <div id="donationChartContainer">
            <canvas id="donationChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('donationChart').getContext('2d');
        const donationChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Donation Amount (RM)',
                    data: <?php echo json_encode($amounts); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
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

        function generateReport() {
            const reportWindow = window.open('', '', 'width=800,height=600');
            reportWindow.document.write('<html><head><title>Monthly Donation Report</title></head><body>');
            reportWindow.document.write('<h2>Monthly Donation Report for 2024</h2>');
            reportWindow.document.write('<table border="1"><tr><th>Month</th><th>Donation Amount (RM)</th></tr>');
            <?php foreach ($months as $index => $month): ?>
                reportWindow.document.write('<tr><td><?php echo htmlspecialchars($month); ?></td><td><?php echo htmlspecialchars($amounts[$index]); ?></td></tr>');
            <?php endforeach; ?>
            reportWindow.document.write('</table></body></html>');
            reportWindow.document.close();
            reportWindow.print();
        }
    </script>
</body>
</html>
