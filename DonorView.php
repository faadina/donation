<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch donor records from the database
$sql = "SELECT * FROM Donor";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="my-4">Donor Records</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th colspan='3' style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dob = date('d/m/y', strtotime($row["donorDOB"]));
                        echo "<tr>";
                        echo "<td>" . $row["donorID"] . "</td>";
                        echo "<td>" . $row["donorName"] . "</td>";
                        echo "<td>" . $row["donorPhoneNo"] . "</td>";
                        echo "<td>" . $dob . "</td>";
                        echo "<td>" . $row["donorAddress"] . "</td>";
                        echo "<td>" . $row["donorEmail"] . "</td>";
                        echo "<td><a href='DonorUpdate.php?donorID=" . $row["donorID"] . "' class='btn btn-primary btn-mini-column'><i class='bi bi-pencil'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No donor records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
