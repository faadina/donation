<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Pagination variables
$results_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $results_per_page;

// Get the filter status if set
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch donation records with sorting, pagination, and filtering
$sql = "SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.allocationID, a.allocationName, d.donationReceipt
        FROM Donation d
        LEFT JOIN Allocation a ON d.allocationID = a.allocationID
        WHERE (d.donationStatus = ? OR ? = '')
        ORDER BY d.donationID DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssii', $status_filter, $status_filter, $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Fetch counts for filtering buttons
$countSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN donationStatus = 'Accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN donationStatus = 'pending' THEN 1 ELSE 0 END) as pending
             FROM Donation";
$countResult = $conn->query($countSql);
$counts = $countResult->fetch_assoc();

// Fetch allocation names for the dropdown
$allocationsSql = "SELECT allocationID, allocationName FROM Allocation";
$allocationsResult = $conn->query($allocationsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
        }

        .btn-mini-column {
            width: 85px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mb-3 form {
            display: inline-block;
        }

        .mb-3 select {
            width: 350px;
        }

        /* Table border styles */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Column width adjustments */
        .col-id {
            width: 150px; /* Adjust width for Donation ID */
        }

        .col-allocation {
            width: 200px; /* Adjust width for Allocation Name */
        }

        .col-amount {
            width: 200px; /* Adjust width for Amount */
        }

        .col-date {
            width: 150px; /* Adjust width for Date */
        }

        .col-status {
            width: 100px; /* Adjust width for Status */
        }

        .col-receipt {
            width: 150px; /* Adjust width for Receipt */
        }

        .col-actions {
            width: 200px; /* Adjust width for Actions */
        }

        .col-update {
            width: 100px; /* Adjust width for Update */
        }

        .smaller-button {
            padding: 5px 10px;
            font-size: 0.9rem;
            background-color: #2b4162;
            background-image: linear-gradient(315deg, #2b4162 0%, #12100e 74%);
            color: white;
            border: none;
        }
        .smaller-buttonupdate {
            padding: 3px 8px;
            font-size: 0.9rem;
            background-color: #2b4162;
            background-image: linear-gradient(315deg, #2b4162 0%, #12100e 74%);
            color: white;
            border: none;
            width:50px;
            margin-top:7px;
        }
        .page-title {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #454B1B;
            font-weight: 800;
        }

        .btn-accept {
            background-color: green;
            background-image: linear-gradient(315deg, green 0%, darkgreen 74%);
        }

        .btn-reject {
            background-color: red;
            background-image: linear-gradient(315deg, red 0%, darkred 74%);
        }

        .btn-generate {
            font-size: 12px;
            padding: 5px 5px;
        }

        .btn-back {
            margin-bottom: 20px;
        }

        .center-btn {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>

    <div class="container">
        <h2 class="page-title">Donation Records</h2>

        <!-- Buttons for filtering and dropdown for allocation -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <div>
                <a href="?status=&page=1" class="btn btn-primary">☰ All (<?php echo $counts['total']; ?>)</a>
                <a href="?status=Accepted&page=1" class="btn btn-success mx-2">☰ Accepted (<?php echo $counts['accepted']; ?>)</a>
                <a href="?status=pending&page=1" class="btn btn-warning mx-2">☰ Pending (<?php echo $counts['pending']; ?>)</a>
            </div>
            <div class="d-flex">
                <div class="d-flex">
                    <input type="text" class="form-control me-2" id="donationIDInput" placeholder="Search Donation ID">
                    <button class="btn btn-primary" onclick="searchByDonationID()"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>

        <!-- Table for displaying donation records -->
        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th class="col-id">No</th>
                    <th class="col-id">Donation ID</th>
                    <th class="col-allocation">Allocation Name</th>
                    <th class="col-amount">Donation Amount (RM)</th>
                    <th class="col-date">Date</th>
                    <th class="col-status">Status</th>
                    <th class="col-receipt">Bank Receipt</th>
                    <th colspan='2' class="col-actions" style="text-align:center;">Action</th>
                    <th class="col-update" style="text-align:center;">Update</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $count = ($page - 1) * $results_per_page + 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $row["donationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . number_format($row["donationAmount"], 2) . "</td>";
                        echo "<td>" . date('d/m/y', strtotime($row["donationDate"])) . "</td>";

                        // Display status with color coding
                        $statusColor = '';
                        switch ($row["donationStatus"]) {
                            case 'pending':
                                $statusColor = 'orange';
                                break;
                            case 'Accepted':
                                $statusColor = 'green';
                                break;
                            case 'Rejected':
                                $statusColor = 'red';
                                break;
                            default:
                                $statusColor = '';
                                break;
                        }
                        echo "<td style='color: " . $statusColor . "; font-weight: bold;'>" . $row["donationStatus"] . "</td>";

                        // Display receipt with link to view PDF
                        echo "<td style='text-align: center;'>";
                        if (!empty($row["donationReceipt"])) {
                            echo "<a href='" . htmlspecialchars($row["donationReceipt"]) . "' 
                            target='_blank' class='btn btn-primary btn-mini-column smaller-button'>⌞view⌝</a>";
                        } else {
                            echo "No Receipt";
                        }
                        echo "</td>";

                        // Actions based on donation status
                        echo "<td colspan='2' style='text-align:center;'>";
                        switch ($row["donationStatus"]) {
                            case 'pending':
                                echo "<a href='DonationAccept.php?donationID=" . $row["donationID"] . "'  
                                class='btn btn-success btn-mini-column smaller-button btn-accept'>✓ Accept</a>";
                                break;
                            case 'Accepted':
                                echo "<a href='DonationGenerateReceipt.php?donationID=" . $row["donationID"] . "' 
                                class='btn btn-secondary btn-mini-column smaller-button btn-generate'><i class='bi bi-file-earmark-text'></i> Generate Receipt</a>";
                                break;
                            default:
                                echo "";
                                break;
                        }
                        echo "</td>";

                        // Update button
                        echo "<td style='text-align:center;'>";
                        echo "<a href='DonationUpdate.php?donationID=" . $row["donationID"] . "' 
                        class='btn btn-warning btn-mini-column smaller-buttonupdate'><i class='bi bi-pencil'></i></a>";
                        echo "</td>";

                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='10'>No donation records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                $total_pages = ceil($counts['total'] / $results_per_page);
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?page=" . $i . "&status=" . htmlspecialchars($status_filter) . "'>" . $i . "</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        function searchByDonationID() {
            var donationID = document.getElementById('donationIDInput').value.trim();
            if (donationID !== '') {
                window.location.href = '?donationID=' + encodeURIComponent(donationID) + '&status=' + encodeURIComponent('<?php echo $status_filter; ?>') + '&page=1';
            } else {
                alert('Please enter a Donation ID');
            }
        }
    </script>
</body>
</html>
