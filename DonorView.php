<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Constants for pagination
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch donor records from the database with pagination
$sql = "SELECT * FROM Donor LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total number of rows for pagination
$total_sql = "SELECT COUNT(*) AS total FROM Donor";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .page-title {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #454B1B; 
            font-weight: 800;
        }
        .container {
            padding-top: 20px;
        }
        .search-box {
            max-width: 400px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-box input {
            flex: 1;
        }
        .table-actions {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="page-title">Donor Records</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>Total Registered Donors: <b><?php echo $total_rows; ?></b></div> 
            <div class="search-box">
                <input type="text" id="donorID" class="form-control" placeholder="Search for donor ID..." onkeyup="searchDonor()">
                <button class="btn btn-primary" type="button" onclick="searchDonor()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </button>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Donor ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th class="table-actions" colspan='3'>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $count = $start_from + 1;
                    while ($row = $result->fetch_assoc()) {
                        $dob = date('d/m/y', strtotime($row["donorDOB"]));
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $row["donorID"] . "</td>";
                        echo "<td>" . $row["donorName"] . "</td>";
                        echo "<td>" . $row["donorPhoneNo"] . "</td>";
                        echo "<td>" . $dob . "</td>";
                        echo "<td>" . $row["donorAddress"] . "</td>";
                        echo "<td>" . $row["donorEmail"] . "</td>";
                        echo "<td class='table-actions'><a href='DonorUpdate.php?donorID=" . $row["donorID"] . "' class='btn btn-primary'><i class='bi bi-pencil'></i></a></td>";
                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No donor records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="DonorView.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script>
        function searchDonor() {
            var input = document.getElementById("donorID").value.trim().toLowerCase();
            var table = document.getElementsByTagName("table")[0];
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var donorIDCell = rows[i].getElementsByTagName("td")[1];
                
                if (donorIDCell) {
                    var textValue = donorIDCell.textContent || donorIDCell.innerText;
                    
                    if (textValue.trim().toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
