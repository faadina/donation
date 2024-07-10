<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

require_once("dbConnect.php");

// Check if staff ID is provided in the URL
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $staffID = mysqli_real_escape_string($conn, $_GET['id']);

    // Prepare SQL statement to fetch staff details
    $sql = "SELECT staffID, staffName, staffPhoneNo, staffEmail, staffPassword FROM staff WHERE staffID = '$staffID'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Check if staff member exists
        if (mysqli_num_rows($result) == 1) {
            $staff = mysqli_fetch_assoc($result);
        } else {
            echo "Staff member not found.";
            exit;
        }
    } else {
        echo "Error retrieving staff data: " . mysqli_error($conn);
        exit;
    }
} else {
    echo "Staff ID not specified.";
    exit;
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <?php include('ManagerHeader.php'); ?>
    <div class="container">
        <h2>Staff Details</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($staff['staffName']); ?></h5>
                <p class="card-text"><strong>Staff ID:</strong> <?php echo htmlspecialchars($staff['staffID']); ?></p>
                <p class="card-text"><strong>Phone Number:</strong> <?php echo htmlspecialchars($staff['staffPhoneNo']); ?></p>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($staff['staffEmail']); ?></p>
                <p class="card-text"><strong>Password:</strong> <?php echo htmlspecialchars($staff['staffPassword']); ?></p>
                <a href="StaffDetails.php?id=<?php echo $staff['staffID']; ?>">Back</a>
            </div>
        </div>
    </div>
</body>
</html>
