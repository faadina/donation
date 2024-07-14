<?php
require_once("dbConnect.php");

// Initialize variables and errors
$donationID = $managerID = $reportDate = $reportName = $reportType = $reportID = "";
$donationID_err = $managerID_err = $reportDate_err = $reportName_err = $reportType_err = $reportID_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate donationID
    if (empty(trim($_POST["donationID"]))) {
        $donationID_err = "Please enter a donation ID.";
    } else {
        $donationID = trim($_POST["donationID"]);
    }

    // Validate managerID
    if (empty(trim($_POST["managerID"]))) {
        $managerID_err = "Please enter a manager ID.";
    } else {
        $managerID = trim($_POST["managerID"]);
    }

    // Validate reportDate
    if (empty(trim($_POST["reportDate"]))) {
        $reportDate_err = "Please enter a report date.";
    } else {
        $reportDate = trim($_POST["reportDate"]);
    }

    // Validate reportName
    if (empty(trim($_POST["reportName"]))) {
        $reportName_err = "Please enter a report name.";
    } else {
        $reportName = trim($_POST["reportName"]);
    }

    // Validate reportType
    if (empty(trim($_POST["reportType"]))) {
        $reportType_err = "Please select a report type.";
    } else {
        $reportType = trim($_POST["reportType"]);
    }

    // Validate reportID
    if (empty(trim($_POST["reportID"]))) {
        $reportID_err = "Please enter a report ID.";
    } else {
        $reportID = trim($_POST["reportID"]);
    }

    // Check input errors before inserting in database
    if (empty($donationID_err) && empty($managerID_err) && empty($reportDate_err) && empty($reportName_err) && empty($reportType_err) && empty($reportID_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO report (donationID, managerID, reportDate, reportName, reportType, reportID) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_donationID, $param_managerID, $param_reportDate, $param_reportName, $param_reportType, $param_reportID);

            // Set parameters
            $param_donationID = $donationID;
            $param_managerID = $managerID;
            $param_reportDate = $reportDate;
            $param_reportName = $reportName;
            $param_reportType = $reportType;
            $param_reportID = $reportID;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to a success page (or display success message)
                echo "Report created successfully.";
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
            background-color: whitesmoke;
            color: #333;
        }
        .wrapper {
            width: 50%;
            padding: 20px;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
        }
        .form-container {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #333;
            border: none;
        }
        .btn-primary:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="form-container">
            <h2 style="text-align:center;">Create Report</h2>
            <p style="text-align:center;">Please fill in this form to create a report.</p>
            <form action="create_report.php" method="post">
                <div class="form-group">
                    <label>Donation ID</label>
                    <input type="text" name="donationID" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Manager ID</label>
                    <input type="text" name="managerID" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Report Date</label>
                    <input type="date" name="reportDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Report Name</label>
                    <input type="text" name="reportName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Report Type</label>
                    <select name="reportType" class="form-control" required>
                        <option value="Monthly Donation Report">Monthly Donation Report</option>
                        <option value="Donation Allocation Report">Donation Allocation Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Report ID</label>
                    <input type="text" name="reportID" class="form-control" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Create Report</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
