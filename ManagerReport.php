<?php
session_start();

// Check if user is logged in
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

// Get the filter type and report ID if set
$reportType_filter = isset($_GET['reportType']) ? $_GET['reportType'] : '';
$reportID_search = isset($_GET['reportID']) ? $_GET['reportID'] : '';

// Fetch report data with sorting, pagination, and filtering
$sql = "SELECT reportID, reportName 
        FROM report
        WHERE (reportType = ? OR ? = '')
        AND managerID = ?
        AND (reportID LIKE ? OR ? = '')
        ORDER BY reportID DESC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$reportID_search_like = '%' . $reportID_search . '%';
$stmt->bind_param('sssssis', $reportType_filter, $reportType_filter, $_SESSION['username'], $reportID_search_like, $reportID_search, $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Fetch counts for filtering buttons
$countSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN reportType = 'Donation Allocation Report' THEN 1 ELSE 0 END) as donation,
                SUM(CASE WHEN reportType = 'Monthly Donation Report' THEN 1 ELSE 0 END) as monthly
             FROM report WHERE managerID = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param('s', $_SESSION['username']);
$countStmt->execute();
$countResult = $countStmt->get_result();
$counts = $countResult->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .page-title {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #454B1B;
            font-weight: 800;
        }

        .btn-filter {
            margin: 0 5px;
        }

        .btn-filter.active {
            background-color: #007bff;
            color: white;
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
            margin-bottom: 20px; /* Added margin for spacing */
        }

        .d-flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include('managerHeader.php'); ?>

    <div class="container">
        <h2 class="page-title">Report Management</h2>

        <!-- Buttons for filtering and Generate New Report button -->
        <div class="d-flex-between mb-3">
            <div>
                <a href="?reportType=&page=1" class="btn btn-primary btn-filter <?php echo $reportType_filter === '' ? 'active' : ''; ?>">All (<?php echo $counts['total']; ?>)</a>
                <a href="?reportType=Donation Allocation Report&page=1" class="btn btn-success btn-filter <?php echo $reportType_filter === 'Donation Allocation Report' ? 'active' : ''; ?>">Donation Allocation (<?php echo $counts['donation']; ?>)</a>
                <a href="?reportType=Monthly Donation Report&page=1" class="btn btn-warning btn-filter <?php echo $reportType_filter === 'Monthly Donation Report' ? 'active' : ''; ?>">Monthly Donation (<?php echo $counts['monthly']; ?>)</a>
            </div>
            <div class="d-flex">
                <input type="text" class="form-control me-2" id="reportIDInput" placeholder="Search Report ID">
                <button class="btn btn-primary" onclick="searchByReportID()"><i class="bi bi-search"></i></button>
            </div>
        </div>

        <div class="center-btn mb-3">
            <a href="ManagerGenerateReport.php" class="btn btn-success btn-generate">Generate New Report</a>
        </div>

        <!-- Table for displaying report records -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Report ID</th>
                    <th>Report Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $row_number = $start_from + 1; // Initialize row number
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row_number++) . "</td>"; // Display row number
                        echo "<td>" . htmlspecialchars($row["reportID"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["reportName"]) . "</td>";
                        echo "<td>";
                        echo "<a href='ViewReport.php?reportID=" . urlencode($row["reportID"]) . "' class='btn btn-info btn-sm'><i class='bi bi-eye'></i> View</a> ";
                        echo "<button class='btn btn-danger btn-sm' onclick='confirmDelete(\"" . htmlspecialchars($row["reportID"]) . "\")'><i class='bi bi-trash'></i> Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No report records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $total_pages = ceil($counts['total'] / $results_per_page);
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?page=" . $i . "&reportType=" . urlencode($reportType_filter) . "'>" . $i . "</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchByReportID() {
            var reportID = document.getElementById('reportIDInput').value.trim();
            if (reportID !== '') {
                window.location.href = '?reportID=' + encodeURIComponent(reportID) + '&reportType=' + encodeURIComponent('<?php echo $reportType_filter; ?>') + '&page=1';
            } else {
                alert('Please enter a Report ID');
            }
        }

        function confirmDelete(reportID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL
                    window.location.href = 'deleteReport.php?reportID=' + encodeURIComponent(reportID);
                }
            });
        }
    </script>
</body>
</html>
