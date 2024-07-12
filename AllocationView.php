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

// Fetch allocation records from the database
$sql = "SELECT * FROM Allocation";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocation Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
        <h2 class="my-4">Allocation Records</h2>
        <a href="AllocationCreate.php" class="btn btn-success">Create New Allocation</a>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>TARGET (RM)</th>
                    <th>CURRENT (RM)</th>
                    <th colspan='3' style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $startDate = date('d/m/Y', strtotime($row["allocationStartDate"]));
                        $endDate = date('d/m/Y', strtotime($row["allocationEndDate"]));
                        echo "<tr>";
                        echo "<td>" . $row["allocationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . $startDate . "</td>";
                        echo "<td>" . $endDate . "</td>";
                        echo "<td>" . $row["allocationStatus"] . "</td>";
                        echo "<td>" . number_format($row["targetAmount"], 2) . "</td>";
                        echo "<td>" . number_format($row["currentAmount"], 2) . "</td>";
                        echo "<td>";
                        echo "<a href='AllocationRead.php?allocationID=" . $row["allocationID"] . "' class='btn btn-info btn-sm'>";
                        echo "<i class='bi bi-eye'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='AllocationUpdate.php?allocationID=" . $row["allocationID"] . "' class='btn btn-primary btn-sm'>";
                        echo "<i class='bi bi-pencil'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='AllocationDelete.php?allocationID=" . $row["allocationID"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this allocation?\");'>";
                        echo "<i class='bi bi-trash'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No allocation records found</td></tr>";
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
