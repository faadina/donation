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

    // SQL query to update data in Staff table
    $sql = "UPDATE Staff 
            SET staffName='$staffName', staffPhoneNo='$staffPhoneNo', staffEmail='$staffEmail', 
                staffPassword='$staffPassword', role=$role
            WHERE staffID='$staffID'";

    if ($conn->query($sql) === TRUE) {
        echo "Staff member updated successfully";
    } else {
        echo "Error updating staff member: " . $conn->error;
    }
}
?>
