<?php
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $reportID = $_POST['id'];

    // SQL statement for deletion
    $sql = "DELETE FROM report WHERE reportID = ?";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $reportID);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        // Close statement
        $stmt->close();
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}

// Close database connection
$conn->close();
?>
