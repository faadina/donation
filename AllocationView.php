<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

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
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
        }
        /* Adjust search button */
        .search-button {
            min-width: 40px; /* Ensure minimum width */
            flex: 0 0 auto; /* Fixing width */
            
        }
        .input-group-append {
            display: flex;
            align-items: stretch; /* Ensuring alignment */
            width:1000px;
        }
        .input-group .form-control {
            width: 10px; /* Adjust width of input field */
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="my-4">Allocation Records</h2>
        <a href="StaffDashboard.php" class="btn btn-primary mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
            </svg>
        </a>
        <a href="AllocationCreate.php" class="btn btn-success mb-3">Create New Allocation</a>
        
        <!-- Search field -->
        <div class="input-group mb-4">
            <input type="text" id="allocationID" class="form-control" placeholder="Search for allocation ID..." onkeyup="searchAllocation()">
            <div class="input-group-append">
                <button class="btn btn-primary search-button" type="button" onclick="searchAllocation()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>TARGET (RM)</th>
                    <th>CURRENT (RM)</th>
                    <th>DONATION</th>
                    <th colspan='3' style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $startDate = date('d/m/Y', strtotime($row["allocationStartDate"]));
                        $endDate = date('d/m/Y', strtotime($row["allocationEndDate"]));
                        echo "<tr>";
                        echo "<td>" . $row["allocationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . $startDate . "</td>";
                        echo "<td>" . $endDate . "</td>";
                        echo "<td>" . $row["allocationStatus"] . "</td>";
                        echo "<td>" . number_format($row["targetAmount"], 2) . "</td>";
                        echo "<td>" . number_format($row["currentAmount"], 2) . "</td>";
                        echo "<td>";
                        echo "<a href='DonationView.php?allocationID=" . $row["allocationID"] . "' class='btn btn-info btn-sm'>";
                        echo "View";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='AllocationRead.php?allocationID=" . $row["allocationID"] . "' class='btn btn-info btn-sm'>";
                        echo "<i class='bi bi-eye'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='AllocationUpdate.php?allocationID=" . $row["allocationID"] . "' class='btn btn-primary btn-sm'>";
                        echo "<i class='bi bi-pencil'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "<td>";
                        echo "<a href='AllocationDelete.php?allocationID=" . $row["allocationID"] . "' class='btn btn-danger btn-sm delete-allocation' data-id='" . $row["allocationID"] . "'>";
                        echo "<i class='bi bi-trash'></i>";
                        echo "</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No allocation records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchAllocation() {
            var input = document.getElementById("allocationID").value.trim().toLowerCase();
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                var allocationIDCell = rows[i].getElementsByTagName("td")[0]; // Assuming allocationID is in the first column
                
                if (allocationIDCell) {
                    var textValue = allocationIDCell.textContent || allocationIDCell.innerText;
                    
                    if (textValue.trim().toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }       
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
        // Select all elements with class 'delete-allocation'
        var deleteButtons = document.querySelectorAll('.delete-allocation');

        // Loop through each delete button
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Get the allocation ID from the data-id attribute
                var allocationID = this.getAttribute('data-id');

                // Use SweetAlert to confirm deletion
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to delete script
                        window.location.href = 'AllocationDelete.php?allocationID=' + allocationID;
                    }
                });
            });
        });
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>
