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

// Initialize an empty array to store report data
$reportData = array();

// Determine which type of report to fetch
$reportType = isset($_GET['reportType']) ? $_GET['reportType'] : 'all';

// Fetch report data from the database based on the selected report type
if ($reportType == 'donation') {
    $sql = "SELECT reportID, reportName FROM report WHERE managerID = ? AND reportType = 'Donation Allocation Report'";
} elseif ($reportType == 'monthly') {
    $sql = "SELECT reportID, reportName FROM report WHERE managerID = ? AND reportType = 'Monthly Donation Report'";
} else {
    $sql = "SELECT reportID, reportName FROM report WHERE managerID = ?";
}

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_managerID);
    $param_managerID = $username;

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reportData[] = $row;
            }
        } else {
            echo "Error fetching reports.";
            exit;
        }
    } else {
        echo "Error executing statement.";
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <style>
        body {
            background-color: whitesmoke;
            color: #FFFFFF;
            font-family: Arial, sans-serif;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: black;
        }

        .btn_report,
        .btn_view {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn_report:hover,
        .btn_view:hover {
            background-color: #ffd000;
        }

        .generate-report {
            text-align: right;
            margin-right: 20px;
            margin-top: 10px;
        }

        .filter-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-buttons a {
            text-decoration: none;
            color: white;
            background-color: #1f244a;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-buttons a:hover {
            background-color: #ffc107;
            color: #1f244a;
        }
    </style>
    <!-- Ensure jQuery is included before any scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Ensure Bootstrap CSS is included -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Ensure Popper.js and Bootstrap JS are included -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include('managerHeader.php'); ?>

    <h2 style="text-align: center;">REPORTS</h2>

    <div class="generate-report">
        <a href="ManagerGenerateReport.php" class="btn_report">Generate Report</a>
    </div>

    <div class="filter-buttons">
        <a href="?reportType=all">All Reports</a>
        <a href="?reportType=donation">Donation Allocation Reports</a>
        <a href="?reportType=monthly">Monthly Donation Reports</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Report Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData as $report) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['reportID']); ?></td>
                    <td><?php echo htmlspecialchars($report['reportName']); ?></td>
                    <td>
                        <a href="ViewReport.php?reportID=<?php echo urlencode($report['reportID']); ?>" class="btn_view">View Report</a>
                        <button class="btn btn-danger" onclick="showDeleteModal('<?php echo htmlspecialchars(json_encode($report['reportID'])); ?>')">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this report?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



    <script>
        function showDeleteModal(reportID) {
            $('#confirmDeleteButton').data('reportid', reportID);
            $('#deleteModal').modal('show');
        }

        $('#confirmDeleteButton').click(function() {
            var reportID = $(this).data('reportid');

            // AJAX call to deleteReport.php
            $.ajax({
                url: 'deleteReport.php',
                type: 'POST',
                data: { id: reportID },
                success: function(response) {
                    if (response === 'success') {
                        $('#deleteModal').modal('hide');
                        location.reload(); // Reload the page to update the report list
                    } else {
                        alert('Error deleting report.');
                    }
                },
                error: function() {
                    alert('Error deleting report.');
                }
            });
        });
    </script>
</body>

</html>
