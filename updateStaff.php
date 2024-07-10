<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

require_once("dbConnect.php");

// Initialize variables
$staffID = $staffName = $staffPhoneNo = $staffEmail = $staffPassword = "";
$updateStatus = "";

// Check if staff ID is provided in the URL for updating
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $staffID = $_GET['id'];

    // Fetch current staff details from database
    $sql = "SELECT * FROM staff WHERE staffID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $staffID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo "Error fetching staff details: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($result) == 0) {
        echo "Staff not found.";
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    // Populate form fields with current staff details
    $staffName = $row['staffName'];
    $staffPhoneNo = $row['staffPhoneNo'];
    $staffEmail = $row['staffEmail'];
    $staffPassword = $row['staffPassword'];

    mysqli_stmt_close($stmt);
}

// Check if form is submitted for updating staff details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffID = $_POST['staffID'];
    $staffName = $_POST['staffName'];
    $staffPhoneNo = $_POST['staffPhoneNo'];
    $staffEmail = $_POST['staffEmail'];
    $staffPassword = $_POST['staffPassword']; // You might want to hash this for security

    // Update staff details in the database
    $sql = "UPDATE staff SET staffName = ?, staffPhoneNo = ?, staffEmail = ?, staffPassword = ? WHERE staffID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $staffName, $staffPhoneNo, $staffEmail, $staffPassword, $staffID);

    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect to StaffDetails.php after successful update
        header("location: StaffDetails.php");
        exit;
    } else {
        echo "Error updating staff: " . mysqli_error($conn);
    }


    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Staff Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            background-color: whitesmoke;
            color: #FFFFFF;
        }

        .wrapper {
            color: black;
            width: 80%;
            padding: 20px;
            margin: 0 auto;
            margin-top: 50px;
            background-color: whitesmoke;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: black;
            border: none;
        }

        .btn-primary:hover {
            background-color: #808080;
        }

        .btn {
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <?php include('ManagerHeader.php'); ?>
    <div class="wrapper">
        <h2>Update Staff Information</h2>
        <?php if (!empty($updateStatus)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $updateStatus; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="staffID" value="<?php echo htmlspecialchars($staffID); ?>">
            <div class="form-group">
                <label for="staffName">Name:</label>
                <input type="text" class="form-control" id="staffName" name="staffName" value="<?php echo htmlspecialchars($staffName); ?>" required>
            </div>
            <div class="form-group">
                <label for="staffPhoneNo">Phone Number:</label>
                <input type="text" class="form-control" id="staffPhoneNo" name="staffPhoneNo" value="<?php echo htmlspecialchars($staffPhoneNo); ?>" required>
            </div>
            <div class="form-group">
                <label for="staffEmail">Email:</label>
                <input type="email" class="form-control" id="staffEmail" name="staffEmail" value="<?php echo htmlspecialchars($staffEmail); ?>" required>
            </div>
            <div class="form-group">
                <label for="staffPassword">Password:</label>
                <input type="password" class="form-control" id="staffPassword" name="staffPassword" value="<?php echo htmlspecialchars($staffPassword); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Staff</button>
        </form>
    </div>

    <!-- Include Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
