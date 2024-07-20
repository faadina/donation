<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Function to generate sequential IDs for reports (R001, R002, ...)
function generateReportID() {
    global $conn;

    $sql = "SELECT MAX(reportID) AS maxID FROM Report";
    $result = $conn->query($sql);

    $currentID = "R001"; // Default starting ID if no records are found

    if ($result && $row = $result->fetch_assoc()) {
        $maxID = $row['maxID'];
        if ($maxID) {
            // Extract numeric part of ID, increment, and format back to R001, R002, ...
            preg_match('/(\d+)$/', $maxID, $matches);
            $number = intval($matches[0]) + 1;
            $currentID = 'R' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }

    return $currentID;
}

// Fetch allocation IDs from the Allocation table for dropdown
$allocationOptions = "";
$sql = "SELECT allocationID, allocationName FROM Allocation";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $allocationOptions .= "<option value='{$row['allocationID']}'>{$row['allocationName']} ({$row['allocationID']})</option>";
    }
    // Add an option for selecting all allocations
    $allocationOptions .= "<option value='All'>All</option>";
}

// Fetch distinct donation months from the Donation table for dropdown
$reportMonthOptions = "";
$sql = "SELECT DISTINCT DATE_FORMAT(donationDate, '%Y-%m') AS reportMonth FROM Donation ORDER BY reportMonth";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Convert YYYY-MM to Month-Year
        $dateObj = DateTime::createFromFormat('Y-m', $row['reportMonth']);
        $formattedMonth = $dateObj->format('F-Y');
        $reportMonthOptions .= "<option value='{$row['reportMonth']}'>{$formattedMonth}</option>";
    }
}

// Initialize variables to hold form data
$reportID = generateReportID();
$reportType = "";
$reportName = "";
$reportMonth = "";
$allocationID = ""; // Initialize allocation ID to be empty

// Initialize variable to hold donation IDs
$donationIDs = "";

// Process form submission for report creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST["reportType"];
    $reportName = $_POST["reportName"];
    $reportMonth = $_POST["reportMonth"];
    
    // Handle allocation ID based on report type
    if ($reportType === 'Monthly Donation Report') {
        // Fetch donation IDs for the selected report month
        $sql = "SELECT donationID FROM Donation WHERE DATE_FORMAT(donationDate, '%Y-%m') = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $reportMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $donationIDs .= $row['donationID'] . "<br>";
        }
        $stmt->close();

        // Set allocationID to empty for Monthly Donation Report
        $allocationID = '';
    } else {
        $allocationID = $_POST["allocationID"]; // Directly assign allocation ID for other report types
    }

    // Retrieve managerID from session
    $managerID = $_SESSION['username'];

    // Prepare and execute the INSERT statement
    $sql = "INSERT INTO Report (reportID, reportType, reportName, reportDate, managerID, allocationID, reportMonth)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $reportDate = date('Y-m-d'); // Automatically set to current date
        $stmt->bind_param("sssssss", $reportID, $reportType, $reportName, $reportDate, $managerID, $allocationID, $reportMonth);
        if ($stmt->execute()) {
            // Report creation success
            $success = true;
            $message = "Report created successfully!";
        } else {
            $message = "Error creating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

$conn->close(); // Close database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script>
        function toggleReportOptions() {
            const reportType = document.getElementById('reportType').value;
            const reportMonthField = document.getElementById('reportMonthField');
            const allocationIDField = document.getElementById('allocationIDField');
            const donationIDsField = document.getElementById('donationIDsField');

            if (reportType === 'Monthly Donation Report') {
                reportMonthField.style.display = 'block';
                allocationIDField.style.display = 'none';
                donationIDsField.style.display = 'block';
            } else if (reportType === 'Donation Allocation Report') {
                reportMonthField.style.display = 'none';
                allocationIDField.style.display = 'block';
                donationIDsField.style.display = 'none';
            } else {
                reportMonthField.style.display = 'none';
                allocationIDField.style.display = 'none';
                donationIDsField.style.display = 'none';
            }
        }

        function validateForm() {
            const reportName = document.getElementById('reportName').value;
            const reportType = document.getElementById('reportType').value;
            const reportMonth = document.getElementById('reportMonth') ? document.getElementById('reportMonth').value : '';
            const allocationID = document.getElementById('allocationID') ? document.getElementById('allocationID').value : '';

            if (reportType === 'Monthly Donation Report' && !reportMonth) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a report month for the Monthly Donation Report!',
                });
                return false;
            }

            if (reportType === 'Donation Allocation Report' && !allocationID) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select an allocation ID for the Donation Allocation Report!',
                });
                return false;
            }

            if (reportName.trim() === '' || (reportName.includes("reportMonth") && !reportName.includes("Donation Report")) || (reportName.includes("allocationName") && !reportName.includes("Report"))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Report Name',
                    text: 'Please enter a valid report name!',
                });
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleReportOptions();
        });
    </script>
</head>
<body>

<?php include('ManagerHeader.php'); ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Generate New Report</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">

                        <div class="mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-control" id="reportType" name="reportType" required onchange="toggleReportOptions()">
                                <option value="Donation Allocation Report" <?php echo ($reportType == 'Donation Allocation Report') ? 'selected' : ''; ?>>Donation Allocation Report</option>
                                <option value="Monthly Donation Report" <?php echo ($reportType == 'Monthly Donation Report') ? 'selected' : ''; ?>>Monthly Donation Report</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reportName" class="form-label">Report Name</label>
                            <input type="text" class="form-control" id="reportName" name="reportName" value="<?php echo htmlspecialchars($reportName); ?>" required>
                        </div>

                        <div class="mb-3" id="reportMonthField" style="display:none;">
                            <label for="reportMonth" class="form-label">Report Month</label>
                            <select class="form-control" id="reportMonth" name="reportMonth">
                                <?php echo $reportMonthOptions; ?>
                            </select>
                        </div>

                        <div class="mb-3" id="allocationIDField">
                            <label for="allocationID" class="form-label">Allocation ID</label>
                            <select class="form-control" id="allocationID" name="allocationID">
                                <?php echo $allocationOptions; ?>
                            </select>
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="ManagerReport.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Report Page</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
