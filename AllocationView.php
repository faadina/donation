<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

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
        <a href="DonationView.php" class="btn btn-success">Donation</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>TARGET (RM)</th>
                    <th>CURRENT (RM)</th>
                    <th colspan='3' style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $startDate = date('d/m/y', strtotime($row["allocationStartDate"]));
                        $endDate = date('d/m/y', strtotime($row["allocationEndDate"]));
                        echo "<tr>";
                        echo "<td>" . $row["allocationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . $startDate . "</td>";
                        echo "<td>" . $endDate . "</td>";
                        echo "<td>" . $row["allocationStatus"] . "</td>";
                        echo "<td>" . $row["targetAmount"] . "</td>";
                        echo "<td>" . $row["currentAmount"] . "</td>";
                        echo "<td><a href='AllocationRead.php?allocationID=" . $row["allocationID"] . "' class='btn btn-info btn-mini-column'>View</a></td>";
                        echo "<td><a href='AllocationUpdate.php?allocationID=" . $row["allocationID"] . "' class='btn btn-primary'>Update</a></td> ";
                        echo "<td><a href='AllocationDelete.php?allocationID=" . $row["allocationID"] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this allocation?\");'>Delete</a></td>";
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
