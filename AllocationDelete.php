<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Process deletion request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocationID'])) {
    $allocationID = $_POST['allocationID'];

    // SQL statement for deletion
    $sql = "DELETE FROM Allocation WHERE allocationID = '$allocationID'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Delete Allocation</h2>
        <form action="AllocationDelete.php" method="post">
            <div class="mb-3">
                <label for="allocationID" class="form-label">Allocation ID</label>
                <input type="text" class="form-control" id="allocationID" name="allocationID" required>
            </div>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</body>
</html>
