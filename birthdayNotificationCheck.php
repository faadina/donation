<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("dbConnect.php");

$donorID = isset($_SESSION["id"]) ? $_SESSION["id"] : 'Unknown';
$birthday_notification = false;

if ($donorID != 'Unknown') {
    $currentDate = date('Y-m-d');
    $sql = "SELECT donorName, donorDOB FROM donor WHERE donorID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $donorID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $donorName, $donorDOB);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $donorBirthMonthDay = date('m-d', strtotime($donorDOB));
    $todayMonthDay = date('m-d');

    if ($donorBirthMonthDay == $todayMonthDay) {
        $_SESSION["birthday_notification"] = true;
    } else {
        $_SESSION["birthday_notification"] = false;
    }
}
?>
