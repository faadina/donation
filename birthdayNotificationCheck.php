<?php
// Include the database connection
require_once("dbConnect.php");

// Initialize notification flag
$birthday_notification = false;

// Get the donor's ID from the session
$donorID = isset($_SESSION["id"]) ? $_SESSION["id"] : 'Unknown';

// Check if donorID is valid
if ($donorID != 'Unknown') {
    // Get current date
    $currentDate = date('Y-m-d');

    // Query to get donor's birthdate
    $sql = "SELECT donorName, donorDOB FROM donor WHERE donorID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $donorID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $donorName, $donorDOB);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Extract the month and day from the donor's birthdate
    $donorBirthMonthDay = date('m-d', strtotime($donorDOB));
    $todayMonthDay = date('m-d');

    // Check if today is the donor's birthday
    if ($donorBirthMonthDay == $todayMonthDay) {
        $birthday_notification = true;
    }
}


?>
