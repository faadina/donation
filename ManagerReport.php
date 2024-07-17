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
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $username;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $name, $phone, $email);
            mysqli_stmt_fetch($stmt);
        } else {
            echo "User doesn't exist.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    mysqli_stmt_close($stmt);
}

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

        if ($result === false) {
            echo "Error fetching reports.";
            exit;
        }

        $totalReports = mysqli_num_rows($result);
    } else {
        echo "Error preparing the statement.";
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
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager Dashboard</title>
    <style>
        body {
            background-color: whitesmoke;
            color: #FFFFFF;
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
            color: white;
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
            width: 80%;
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

        .btn_report {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn_view {
            text-decoration: none;
            color: #1f244a;
            background-color: whitesmoke;
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

        .total-reports {
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }

        .filter-buttons {
            display: flex;
            justify-content: center;
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include('managerHeader.php'); ?>

    <div class="detailIndex">
        <h2>REPORT</h2>
    </div>
    <div class="total-reports">
        <?php
        echo "<p>Total Reports: " . $totalReports . "</p>";
        ?>
    </div>
    <div class="filter-buttons">
        <a href="?reportType=all">All Reports</a>
        <a href="?reportType=donation">Donation Allocation Reports</a>
        <a href="?reportType=monthly">Monthly Donation Reports</a>
    </div>
    <div class="generate-report">
        <a href="ManagerGenerateReport.php" class="btn_report">Generate Report</a>
    </div>
    <div class="summary">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="summary">';
                echo '<div class="card-content">';
                echo '<div class="summary-box" style="background-color:#2a3f45">';
                echo '<img src="images/reportIcon.png" alt="Report Icon">';
                echo '<div>';
                echo '<h3>' . htmlspecialchars($row["reportName"]) . '</h3>';
                echo '<a href="ViewReport.php?reportID=' . urlencode($row["reportID"]) . '" class="btn_view">View Report</a>';
                echo '<button class="btn btn-danger" onclick="showDeleteModal(' . htmlspecialchars(json_encode($row["reportID"])) . ')">Delete</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>'; 
                echo '</div>'; 
            }
        } else {
            echo "<p>No Report found</p>";
        }
        ?>
    </div>

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

    <script>
        function showDeleteModal(reportID) {
            $('#confirmDeleteButton').data('reportid', reportID);
            $('#deleteModal').modal('show');
        }

        $('#confirmDeleteButton').click(function() {
            var reportID = $(this).data('reportid');

            $.ajax({
                url: 'deleteReport.php',
                type: 'POST',
                data: { id: reportID },
                success: function(response) {
                    if (response === 'success') {
                        $('#deleteModal').modal('hide');
                        location.reload();
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
