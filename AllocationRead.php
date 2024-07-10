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
</head>
<body>
    <div class="container">
        <h2 class="my-4">Allocation Records</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Allocation ID</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Details</th>
                    <th>Target Amount</th>
                    <th>Current Amount</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["allocationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . $row["allocationStartDate"] . "</td>";
                        echo "<td>" . $row["allocationEndDate"] . "</td>";
                        echo "<td>" . $row["allocationStatus"] . "</td>";
                        echo "<td>" . $row["allocationDetails"] . "</td>";
                        echo "<td>" . $row["targetAmount"] . "</td>";
                        echo "<td>" . $row["currentAmount"] . "</td>";
                        echo "<td>";
                        if (!empty($row["allocationImage"])) {
                            echo "<img src='" . $row["allocationImage"] . "' style='max-width: 100px; max-height: 100px;'>";
                        } else {
                            echo "No Image";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No allocation records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
