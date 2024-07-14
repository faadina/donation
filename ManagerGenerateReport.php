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


// Function to generate sequential reportID like R001, R002, R003, ...
function generateUniqueID() {
    // Implement logic to get the latest reportID from database and generate the next one
    global $conn;

    $sql = "SELECT MAX(reportID) AS maxID FROM Report";
    $result = $conn->query($sql);

    $currentID = "R001"; // Default starting ID if no records are found

    if ($result && $row = $result->fetch_assoc()) {
        $maxID = $row['maxID'];
        if ($maxID) {
            // Extract numeric part of ID, increment, and format back to A001, A002, ...
            preg_match('/(\d+)$/', $maxID, $matches);
            $number = intval($matches[0]) + 1;
            $currentID = 'R' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }

    return $currentID;
}

// Initialize variables to hold form data
$reportID = generateUniqueID(); // Generate reportID
$reportType = "";
$reportName = "";
$reportDate = "";

// Retrieve managerID from session
$managerID = $_SESSION['username'];


// Retrieve donationID from database or session (update this according to your actual data retrieval method)
$donationID = "D001"; // Example default value, replace with actual retrieval method

// Process form submission for creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST["reportType"];    
    $reportName = $_POST["reportName"];
    $reportDate = $_POST["reportDate"];

    // Prepare and execute the INSERT statement
    $sql = "INSERT INTO Report (reportID, reportType, reportName, reportDate, managerID, donationID)
            VALUES (?, ?, ?, ?,?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssss", $reportID, $reportType, $reportName, $reportDate, $managerID, $donationID);
        if ($stmt->execute()) {
            // Set success flag for SweetAlert
            $success = true;
        } else {
            echo "Error creating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php
include('ManagerHeader.php'); // Assuming you have a header include file for your staff section
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Generate New Report</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-control" id="reportType" name="reportType" required>
                                <option value="Donation Allocation Report" <?php echo ($reportType == 'Donation Allocation Report') ? 'selected' : ''; ?>>Donation Allocation Report</option>
                                <option value="Monthly Donation Report" <?php echo ($reportType == 'Monthly Donation Report') ? 'selected' : ''; ?>>Monthly Donation Report</option>
                            </select>
                        </div>
                    
                        <div class="mb-3">
                            <label for="reportName" class="form-label">Report Name</label>
                            <input type="text" class="form-control" id="reportName" name="reportName"
                                value="<?php echo htmlspecialchars($reportName); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reportDate" class="form-label">Report Date</label>
                                    <input type="date" class="form-control" id="reportDate"
                                        name="reportDate"
                                        value="<?php echo htmlspecialchars($reportDate); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="ManagerReport.php" class="btn btn-secondary"><i
                                    class="bi bi-arrow-left"></i> Back to Report Page</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if (isset($success) && $success) : ?>
    Swal.fire({
        title: 'Success!',
        text: 'Report generated successfully.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'ManagerReport.php';
    });
    <?php endif; ?>
</script>

</body>

</html>
