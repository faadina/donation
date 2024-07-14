<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
    <title>View Report</title>
    <style>
        body {
            color: black; /* Setting text color to black */
            background-color: #f2f2f2; /* Adding a light background color */
            font-family: Arial, sans-serif; /* Setting a default font family */
            margin: 0; /* Removing default margin */
            padding: 0; /* Removing default padding */
        }

        .detailIndex {
            margin: 2% auto;
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .detailIndex h1, .detailIndex h2 {
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

        .donation-details {
            margin-top: 20px;
            padding: 10px;
            border: 1px transparent #ccc;
            color: black; /* Changing donation details text color to black */
        }

        .donation-details-box {
            margin: 15px 0;
            padding: 20px;
            background-color: #4d4855;
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            color: white; /* Setting donation details box text color to white */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .donation-details-box h3 {
            font-size: 18px; /* Adjusted font size for heading */
            font-weight: bold;
            margin-bottom: 10px;
            color: white; /* Setting donation details box heading color to white */
        }

        .donation-details-box table {
            width: 100%;
            margin-bottom: 0;
            color: white;
        }

        .donation-details-box th,
        .donation-details-box td {
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 8px;
            text-align: left;
        }

        .donation-details-box th {
            background-color: #1a1649;
        }

        .donation-details-box tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn {
            color: #1f244a;
            background-color: #ffc107;
            padding: 8px 16px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .detailIndex h1,
            .detailIndex h2 {
                font-size: 28px;
            }

            .donation-details-box {
                padding: 15px;
            }

            .btn {
                padding: 6px 12px;
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: MainLogin.php");
        exit;
    }

    // Include the database connection file
    require_once("dbConnect.php");

    // Check if reportID is provided in the URL
    if (isset($_GET['reportID'])) {
        $reportID = $_GET['reportID'];

        // Fetch report details based on reportID
        $sql = "SELECT * FROM report WHERE reportID = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $reportID);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $reportName = $row['reportName'];
                    $reportType = $row['reportType']; // Fetch reportType from the report
                    $reportMonth = $row['reportMonth']; // Fetch reportMonth from the report

                    // Fetch donation details based on allocationID
                    $allocationID = $row['allocationID'];
                    $donationDetails = "";

                    if ($reportType == "Monthly Donation Report") {
                        // Fetch donation details for the specific month and status 'Accepted'
                        $sql_donations = "SELECT donationID, donationAmount, donationDate FROM Donation WHERE DATE_FORMAT(donationDate, '%Y-%m') = ? AND donationStatus = 'Accepted'";
                        $stmt_donations = $conn->prepare($sql_donations);
                        if ($stmt_donations) {
                            $stmt_donations->bind_param("s", $reportMonth);
                            if ($stmt_donations->execute()) {
                                $result_donations = $stmt_donations->get_result();
                                if ($result_donations->num_rows > 0) {
                                    $totalDonations = $result_donations->num_rows; // Counting total donations
                                    $donationDetails .= "<div class='donation-details-box'>";
                                    $donationDetails .= "<h3>Donation Details for the month of $reportMonth</h3>";
                                    $donationDetails .= "<div class='table-responsive'>";
                                    $donationDetails .= "<table class='table table-striped'>";
                                    $donationDetails .= "<thead><tr><th>Donation ID</th><th>Donation Amount</th><th>Donation Date</th></tr></thead>";
                                    $donationDetails .= "<tbody>";
                                    while ($row_donations = $result_donations->fetch_assoc()) {
                                        $donationDetails .= "<tr>";
                                        $donationDetails .= "<td>" . $row_donations['donationID'] . "</td>";
                                        $donationDetails .= "<td>RM " . $row_donations['donationAmount'] . "</td>";
                                        $donationDetails .= "<td>" . $row_donations['donationDate'] . "</td>";
                                        $donationDetails .= "</tr>";
                                    }
                                    $donationDetails .= "</tbody></table>";
                                    $donationDetails .= "</div>";
                                    $donationDetails .= "<p class='text-right'>Total Donations: $totalDonations</p>"; // Display total donations count
                                    $donationDetails .= "</div>";
                                } else {
                                    $donationDetails = "<p class='text-center'>No donation records found for the month of $reportMonth</p>";
                                }
                            } else {
                                $donationDetails = "<p class='text-center'>Error executing donation details query: " . $stmt_donations->error . "</p>";
                            }
                            $stmt_donations->close();
                        } else {
                            $donationDetails = "<p class='text-center'>Error preparing donation details statement: " . $conn->error . "</p>";
                        }
                    } else {
                        // Fetch all donation details for the allocation
                        $sql_donations = "SELECT donationID, donationAmount, donationDate FROM Donation WHERE allocationID = ? AND donationStatus = 'Accepted'";
                        $stmt_donations = $conn->prepare($sql_donations);
                        if ($stmt_donations) {
                            $stmt_donations->bind_param("s", $allocationID);
                            if ($stmt_donations->execute()) {
                                $result_donations = $stmt_donations->get_result();
                                if ($result_donations->num_rows > 0) {
                                    $totalDonations = $result_donations->num_rows; // Counting total donations
                                    $donationDetails .= "<div class='donation-details-box'>";
                                    $donationDetails .= "<h3>Donation Details for Allocation ID: $allocationID</h3>";
                                    $donationDetails .= "<div class='table-responsive'>";
                                    $donationDetails .= "<table class='table table-striped'>";
                                    $donationDetails .= "<thead><tr><th>Donation ID</th><th>Donation Amount</th><th>Donation Date</th></tr></thead>";
                                    $donationDetails .= "<tbody>";
                                    while ($row_donations = $result_donations->fetch_assoc()) {
                                        $donationDetails .= "<tr>";
                                        $donationDetails .= "<td>" . $row_donations['donationID'] . "</td>";
                                        $donationDetails .= "<td>RM " . $row_donations['donationAmount'] . "</td>";
                                        $donationDetails .= "<td>" . $row_donations['donationDate'] . "</td>";
                                        $donationDetails .= "</tr>";
                                    }
                                    $donationDetails .= "</tbody></table>";
                                    $donationDetails .= "</div>";
                                    $donationDetails .= "<p class='text-right'>Total Donations: $totalDonations</p>"; // Display total donations count
                                    $donationDetails .= "</div>";
                                } else {
                                    $donationDetails = "<p class='text-center'>No donation records found for Allocation ID: $allocationID</p>";
                                }
                            } else {
                                $donationDetails = "<p class='text-center'>Error executing donation details query: " . $stmt_donations->error . "</p>";
                            }
                            $stmt_donations->close();
                        } else {
                            $donationDetails = "<p class='text-center'>Error preparing donation details statement: " . $conn->error . "</p>";
                        }
                    }
                } else {
                    $donationDetails = "<p class='text-center'>Report not found.</p>";
                }
            } else {
                $donationDetails = "<p class='text-center'>Error executing report query: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $donationDetails = "<p class='text-center'>Error preparing report statement: " . $conn->error . "</p>";
        }
    } else {
        $donationDetails = "<p class='text-center'>Report ID not provided.</p>";
    }

    $conn->close(); // Close database connection
    ?>

    <?php include('managerHeader.php'); ?>
    <div class="text-center mt-2">
    <div class="text-right mb--2">
        <button class="btn btn-light" onclick="printReport()">
            <i class="fa fa-print mr-1"></i> Print
        </button>
    </div>
</div>
    
    <div class='row'>
       
            <div class='col-md-8 mx-auto'>
                <div class='text-center mb-4'>
                    <h2><?php echo htmlspecialchars($reportName); ?></h2>
                </div>
                <?php echo $donationDetails; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript and additional scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Print Button Script -->
    <script>
        function printReport() {
            window.print();
        }
    </script>
</body>

</html>
