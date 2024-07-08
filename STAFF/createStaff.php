<?php
include 'db.php'; // Ensure this file includes your database connection details

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming form data is submitted and validated
    $staffID = $_POST['staffID'];
    $staffName = $_POST['staffName'];
    $staffPhoneNo = $_POST['staffPhoneNo'];
    $staffEmail = $_POST['staffEmail'];
    $staffPassword = $_POST['staffPassword'];
    $role = $_POST['role'];

    // SQL query to insert data into Staff table
    $sql = "INSERT INTO Staff (staffID, staffName, staffPhoneNo, staffEmail, staffPassword, role)
            VALUES ('$staffID', '$staffName', '$staffPhoneNo', '$staffEmail', '$staffPassword', $role)";

    if ($conn->query($sql) === TRUE) {
        echo "New staff member created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>


