<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Fetch donation records from the database
$sql = "SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.allocationID, a.allocationName
        FROM Donation d
        LEFT JOIN Allocation a ON d.allocationID = a.allocationID";
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
        .mb-3 {
            margin-bottom: 15px; /* Adjust margin bottom as needed */
        }
        .mb-3 form {
            display: inline-block; /* Display the form inline */
        }
    </style>
</head>
    <?php include('staffHeader.php'); ?>
    
    <div class="container">
    
        <h2 class="my-4">Donation Records</h2>
        
        <!-- Buttons for filtering -->
        <div class="mb-3">
            <button class="btn btn-success mr-2" onclick="showAccepted()">List Accepted</button>
            <button class="btn btn-danger mr-2" onclick="showRejected()">List Rejected</button>
            <button class="btn btn-warning mr-2" onclick="showPending()">List Pending</button>
            <button class="btn btn-primary" onclick="showAll()">View All</button>
        </div>
        <div class="mb-3" style="text-align: right;">
            <form id="searchForm" onsubmit="return searchDonation()">
                <div class="input-group">
                    <input type="text" class="form-control" id="donationID" name="donationID" placeholder="Enter Donation ID">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
<body>
        

        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Donation ID</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Allocation Type</th>
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
                        echo "<td>" . $row["donationStatus"] . "</td>";
                        
                        // Displaying receipt with a link to view PDF
                        echo "<td><a href='ReceiptView.php?donationID=" . $row["donationID"] . "' target='_blank' class='btn btn-primary btn-mini-column'>View</a></td>";
                        
                        echo "<td>" . $row["allocationName"] . "</td>";

                        // Check the donation status and display corresponding actions
                        if ($row["donationStatus"] == "pending") {
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
                    echo "<tr><td colspan='10'>No donation records found</td></tr>";
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
                var statusCell = rows[i].getElementsByTagName("td")[3];
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
                var statusCell = rows[i].getElementsByTagName("td")[3];
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
                var statusCell = rows[i].getElementsByTagName("td")[3];
                if (statusCell && statusCell.innerText.trim() !== "pending") {
                    rows[i].style.display = "none";
                } else {
                    rows[i].style.display = "";
                }
            }
        }

        // JavaScript function to show all donations
        function showAll() {
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                rows[i].style.display = "";
            }
        }

        // JavaScript function to search donations by ID
        function searchDonation() {
            var input = document.getElementById("donationID").value.trim().toLowerCase();
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            
            for (var i = 0; i < rows.length; i++) {
                var donationIDCell = rows[i].getElementsByTagName("td")[0]; // Assuming donationID is in the first column
                
                if (donationIDCell) {
                    var textValue = donationIDCell.textContent || donationIDCell.innerText;
                    
                    if (textValue.trim().toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }       
            }
            
            return false; // Prevent form submission
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
