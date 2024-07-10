<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Fetch donation records from the database
$sql = "SELECT * FROM Donation";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
        }
        .btn-mini-column {
            width: 85px;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="my-4">Donation Records</h2>

        <!-- Buttons for filtering -->
        <div class="mb-3">
            <button class="btn btn-success mr-2" onclick="showAccepted()">List Accepted</button>
            <button class="btn btn-danger mr-2" onclick="showRejected()">List Rejected</button>
            <button class="btn btn-warning" onclick="showPending()">List Pending</button>
        </div>

        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Donor ID</th>
                    <th>Staff ID</th>
                    <th>Allocation ID</th>
                    <th colspan='2' style="text-align:center;">Action</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["donationID"] . "</td>";
                        echo "<td>" . $row["donationAmount"] . "</td>";
                        echo "<td>" . date('d/m/y', strtotime($row["donationDate"])) . "</td>";
                        echo "<td>" . $row["donationMethod"] . "</td>";
                        echo "<td>" . $row["donationStatus"] . "</td>";
                        echo "<td><a href='#' class='btn btn-primary btn-mini-column' onclick='viewReceipt(\"" . $row["donationReceipt"] . "\")'>View</a></td>";
                        echo "<td>" . $row["donorID"] . "</td>";
                        echo "<td>" . $row["staffID"] . "</td>";
                        echo "<td>" . $row["allocationID"] . "</td>";

                        // Check the donation status and display corresponding actions
                        if ($row["donationStatus"] == "Pending") {
                            echo "<td><a href='DonationAccept.php?donationID=" . $row["donationID"] . "' class='btn btn-success btn-mini-column'>Accept</a></td>";
                            echo "<td><a href='DonationReject.php?donationID=" . $row["donationID"] . "' class='btn btn-danger btn-mini-column'>Reject</a></td>";
                        } elseif ($row["donationStatus"] == "Accepted") {
                            echo "<td colspan='2' style='text-align:center;'><a href='generate_receipt.php?donationID=" . $row["donationID"] . "' class='btn btn-secondary btn-mini-column'><i class='bi bi-file-earmark-text'></i> Generate Receipt</a></td>";
                        } elseif ($row["donationStatus"] == "Rejected") {
                            echo "<td colspan='2' style='text-align:center;'><button class='btn btn-secondary btn-mini-column' disabled>Rejected</button></td>";
                        }
                        
                        

                        // Icon-based edit button
                        echo "<td><a href='DonationUpdate.php?donationID=" . $row["donationID"] . "' class='btn btn-info btn-mini-column'><i class='bi bi-pencil-square'></i></a></td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>No donation records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // JavaScript function to show only accepted donations
        function showAccepted() {
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                var statusCell = rows[i].getElementsByTagName("td")[4];
                if (statusCell && statusCell.innerText.trim() !== "Accepted") {
                    rows[i].style.display = "none";
                } else {
                    rows[i].style.display = "";
                }
            }
        }

        // JavaScript function to show only rejected donations
        function showRejected() {
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                var statusCell = rows[i].getElementsByTagName("td")[4];
                if (statusCell && statusCell.innerText.trim() !== "Rejected") {
                    rows[i].style.display = "none";
                } else {
                    rows[i].style.display = "";
                }
            }
        }

        // JavaScript function to show only pending donations
        function showPending() {
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                var statusCell = rows[i].getElementsByTagName("td")[4];
                if (statusCell && statusCell.innerText.trim() !== "Pending") {
                    rows[i].style.display = "none";
                } else {
                    rows[i].style.display = "";
                }
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
