<?php
include 'dbConnect.php';
$title = "Donation Page";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donations
$sql = "SELECT * FROM allocation";
$result = $conn->query($sql);
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
            width: 80%;
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
            text-shadow: 2px 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex h2 {
            font-size: 35px;
            color: #1a1649;
            margin-bottom: 1%;
            text-shadow: 2px 3px 1px rgba(130, 9, 9, 0.1);
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

        .generate-report {
        margin-left: 1120px;           
        margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php
    include('managerHeader.php');
    ?>

    <div class="detailIndex">
        <h2>DONATION ALLOCATION REPORT</h2>
    </div>
    <div class="generate-report">
            <button class="btn" onclick="ManagerGenerateReport.php">Generate Report</button>
        </div>
    <div class="summary">
        
        <table class="donation-table">
            <thead>
                <tr>
                    <th>Allocation ID</th>
                    <th>Allocation Name</th>
                    <th>Allocation Start Date</th>
                    <th>Allocation End Date</th>
                    <th>Allocation Status</th>
                    <th>Allocation Details</th>
                    <th>Target Amount</th>
                    <th>Current Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['allocationID']); ?></td>
                        <td><?php echo htmlspecialchars($row['allocationName']); ?></td>
                        <td><?php echo htmlspecialchars($row['allocationStartDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['allocationEndDate']); ?></td>
                        <td><?php echo htmlspecialchars($row['allocationStatus']); ?></td>                         
                        <td><?php echo htmlspecialchars($row['allocationDetails']); ?></td>
                        <td><?php echo htmlspecialchars($row['targetAmount']); ?></td>
                        <td><?php echo htmlspecialchars($row['currentAmount']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

    <script>
        function generateReport() {
            const reportWindow = window.open('', '', 'width=800,height=600');
            reportWindow.document.write('<html><head><title>Donation Allocation Report</title></head><body>');
            reportWindow.document.write('<h2>Donation Allocation Report</h2>');
            reportWindow.document.write('<table border="1"><tr><th>Allocation ID</th><th>Allocation Name</th><th>Allocation Start Date</th><th>Allocation End Date</th><th>Allocation Status</th><th>Allocation Details</th><th>Target Amount</th><th>Current Amount</th></tr>');
            <?php
            $result->data_seek(0); // Reset the result pointer to the beginning
            while ($row = $result->fetch_assoc()): ?>
                reportWindow.document.write('<tr><td><?php echo htmlspecialchars($row['allocationID']); ?></td><td><?php echo htmlspecialchars($row['allocationName']); ?></td><td><?php echo htmlspecialchars($row['allocationStartDate']); ?></td><td><?php echo htmlspecialchars($row['allocationEndDate']); ?></td><td><?php echo htmlspecialchars($row['allocationStatus']); ?></td><td><?php echo htmlspecialchars($row['allocationDetails']); ?></td><td><?php echo htmlspecialchars($row['targetAmount']); ?></td><td><?php echo htmlspecialchars($row['currentAmount']); ?></td></tr>');
            <?php endwhile; ?>
            reportWindow.document.write('</table></body></html>');
            reportWindow.document.close();
            reportWindow.print();
        }
    </script>
</body>
</html>
