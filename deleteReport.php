<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['reportID'])) {
    $reportID = $_GET['reportID'];
    
    // Prepare a DELETE statement
    $sql = "DELETE FROM report WHERE reportID = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_reportID);
        $param_reportID = $reportID;

        if (mysqli_stmt_execute($stmt)) {
            // Deletion successful, redirect back to manager dashboard or wherever appropriate
            header("location: ManagerReport.php");
            exit;
        } else {
            echo "Error deleting report.";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} else {
    // If reportID is not provided in the URL
    echo "Report ID not specified.";
}
?>
