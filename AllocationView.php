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

// Pagination variables
$results_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch allocation records with pagination
$sql = "SELECT * FROM Allocation ORDER BY allocationID LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Update allocationStatus if currentAmount >= targetAmount
$update_sql = "UPDATE Allocation SET allocationStatus = 'Inactivate' WHERE currentAmount >= targetAmount";
$conn->query($update_sql);

// Fetch total number of allocation records
$total_sql = "SELECT COUNT(*) AS total FROM Allocation";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_rows / $results_per_page);
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .page-title {
            margin-bottom: 20px;
            text-align: center;
            color: #454B1B;
            font-weight: 800;
        }

        .btn-custom {
            background-color: #454B1B;
            color: #fff;
            border: none;
        }

        .btn-custom:hover {
            background-color: #3A3F15;
        }

        .table {
            margin-top: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .search-input {
            width: 300px;
        }

        .search-button {
            background-color: #454B1B;
            border: none;
        }

        .search-button:hover {
            background-color: #3A3F15;
        }

        .delete-allocation {
            color: #fff;
            background-color: #DC3545;
            border: none;
        }

        .delete-allocation:hover {
            background-color: #C82333;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="page-title">Allocation Records</h2>
        <div class="d-flex justify-content-end mb-3">
            <a href="AllocationCreate.php" class="btn btn-success btn-custom">+ New Allocation</a>
        </div>
        
        <!-- Search field -->
        <div class="input-group mb-4">
            <input type="text" id="allocationID" class="form-control search-input" placeholder="Search for allocation ID..." onkeyup="searchAllocation()">
            <button class="btn btn-primary search-button" type="button" onclick="searchAllocation()">
                <i class="bi bi-search"></i>
            </button>
        </div>
        
        <div>Total Allocation Records: <b><?php echo $total_rows; ?></b></div> <!-- Display total number of allocation records -->
        
        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>TARGET (RM)</th>
                    <th>CURRENT (RM)</th>
                    <th colspan='3'>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
            <?php
if ($result->num_rows > 0) {
    $count = $start_from + 1; // Initialize a counter based on current page
    while ($row = $result->fetch_assoc()) {
        $startDate = date('d/m/Y', strtotime($row["allocationStartDate"]));
        $endDate = date('d/m/Y', strtotime($row["allocationEndDate"]));
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        // Adjust URL for allocationID
        echo "<td><a href='DonationBasedAllocation.php?allocationID=" . $row["allocationID"] . "&donationStatus=Accepted'>" . $row["allocationID"] . "</a></td>";
        echo "<td>" . $row["allocationName"] . "</td>";
        echo "<td>" . $startDate . "</td>";
        echo "<td>" . $endDate . "</td>";
        echo "<td>" . $row["allocationStatus"] . "</td>";
        echo "<td>" . number_format($row["targetAmount"], 2) . "</td>";
        echo "<td>" . number_format($row["currentAmount"], 2) . "</td>";
        echo "<td><a href='AllocationRead.php?allocationID=" . $row["allocationID"] . "' class='btn btn-info btn-sm'><i class='bi bi-eye'></i></a></td>";
        echo "<td><a href='AllocationUpdate.php?allocationID=" . $row["allocationID"] . "' class='btn btn-primary btn-sm'><i class='bi bi-pencil'></i></a></td>";
        echo "<td><button class='btn btn-danger btn-sm delete-allocation' data-id='" . $row["allocationID"] . "'><i class='bi bi-trash'></i></button></td>";
        echo "</tr>";
        $count++;
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>No allocation records found</td></tr>";
}
?>

            </tbody>
        </table>
        
        <!-- Pagination links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?page=" . $i . "'>" . $i . "</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>

    <script>
        function searchAllocation() {
            var input = document.getElementById("allocationID").value.trim().toLowerCase();
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var allocationIDCell = rows[i].getElementsByTagName("td")[1];

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
            var deleteButtons = document.querySelectorAll('.delete-allocation');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    var allocationID = this.getAttribute('data-id');

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
$stmt->close();
$conn->close();
?>
