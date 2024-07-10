<?php
include 'db.php'; // Include your database connection details

// SQL query to select all staff records
$sql = "SELECT * FROM Staff";

// Execute query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Staff ID: " . $row["staffID"]. "<br>";
        echo "Name: " . $row["staffName"]. "<br>";
        echo "Phone Number: " . $row["staffPhoneNo"]. "<br>";
        echo "Email: " . $row["staffEmail"]. "<br>";
        echo "Role: " . $row["role"]. "<br>";
        echo "<hr>";
    }
} else {
    echo "0 results";
}

$conn->close(); // Close the database connection
?>
