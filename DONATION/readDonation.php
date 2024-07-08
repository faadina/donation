<?php
include 'db.php';


$sql = "SELECT * FROM Donation";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Amount</th><th>Date</th><th>Method</th><th>Status</th><th>Donor ID</th><th>Staff ID</th><th>Allocation ID</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["donationID"]."</td><td>".$row["donationAmount"]."</td><td>".$row["donationDate"]."</td><td>".$row["donationMethod"]."</td><td>".$row["donationStatus"]."</td><td>".$row["donorID"]."</td><td>".$row["staffID"]."</td><td>".$row["allocationID"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
?>
