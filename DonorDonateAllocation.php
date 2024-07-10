<?php
include 'dbConnect.php';
$title = "Donation Page";
include 'DonorHeader.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch allocations
$sql = "SELECT * FROM Allocation";
$result = $conn->query($sql);
?>


<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="card">';
            echo '<img src="' . $row["allocationImage"] . '" alt="' . $row["allocationName"] . '">';
            echo '<div class="card-content">';
            echo '<h2>' . $row["allocationName"] . '</h2>';
            echo '<p>' . substr($row["allocationDetails"], 0, 100) . '... <a href="#">Read more...</a></p>';
            echo '</div>';
            echo '<div class="card-footer">';
            echo '<div class="raised">Raised: MYR ' . number_format($row["currentAmount"], 2) . '</div>';
            echo '<div class="goal">Goal: MYR ' . ($row["targetAmount"] > 0 ? number_format($row["targetAmount"], 2) : 'Infinite') . '</div>';
            echo '<a href="DonorDetailPayment.php" class="donate-button">Donate Now</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>No allocations found</p>";
    }
    $conn->close();
    ?>
</div>

</body>
</html>
