<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the allocationID from the URL
$allocationID = isset($_GET['allocationID']) ? $_GET['allocationID'] : '';

// Pagination variables
$results_per_page = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch allocation name
$allocation_sql = "SELECT allocationName FROM Allocation WHERE allocationID = ?";
$allocation_stmt = $conn->prepare($allocation_sql);
$allocation_stmt->bind_param('s', $allocationID);
$allocation_stmt->execute();
$allocation_result = $allocation_stmt->get_result();
$allocation_row = $allocation_result->fetch_assoc();
$allocationName = $allocation_row['allocationName'];

// Fetch donation records based on allocationID and donationStatus='Accepted' with pagination
$sql = "SELECT * FROM Donation WHERE allocationID = ? AND donationStatus = 'Accepted' ORDER BY donationID LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sii', $allocationID, $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of donation records for the given allocationID and donationStatus='Accepted'
$total_sql = "SELECT COUNT(*) AS total FROM Donation WHERE allocationID = ? AND donationStatus = 'Accepted'";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('s', $allocationID);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_rows / $results_per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Records for <?php echo htmlspecialchars($allocationName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .page-title {
        margin-top: 20px;
        margin-bottom: 30px;
        text-align: center;
        color: #454B1B;
        font-weight: 800;
    }

    .total-count {
        margin-bottom: 20px;
        text-align: center;
        font-size: 1.1rem;
        font-weight: 600;
    }

    table {
        border-collapse: collapse;
        width: 100%; /* Ensure table uses full width of container */
    }

    th, td {
        border: 1px solid #ddd;
        padding: 4px; /* Reduced padding */
        text-align: left;
        font-size: 0.875rem; /* Smaller font size */
    }

    th {
        background-color: #f2f2f2;
    }

    .col-id {
        width: 40px; /* Further reduced width */
    }

    .col-amount {
        width: 60px; /* Further reduced width */
    }

    .col-date {
        width: 80px; /* Further reduced width */
    }
</style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="page-title">Donation for <?php echo htmlspecialchars($allocationName); ?></h2>
        <div class="total-count">Total Donations: <?php echo number_format($total_rows); ?></div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="col-id">NO</th>
                    <th class="col-id">ID</th>
                    <th class="col-amount">AMOUNT (RM)</th>
                    <th class="col-date">DATE</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                $count = $start_from + 1; // Initialize a counter based on current page
                while ($row = $result->fetch_assoc()) {
                    $donationDate = date('d/m/Y', strtotime($row["donationDate"]));
                    echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . htmlspecialchars($row["donationID"]) . "</td>";
                    echo "<td>" . number_format($row["donationAmount"], 2) . "</td>";
                    echo "<td>" . $donationDate . "</td>";
                    echo "</tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No donation records found</td></tr>";
            }
            ?>
            </tbody>
        </table>
        
        <!-- Pagination links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='DonationBasedAllocation.php?allocationID=" . urlencode($allocationID) . "&page=" . $i . "'>" . $i . "</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>
</body>
</html>

<?php
$stmt->close();
$total_stmt->close();
$allocation_stmt->close();
$conn->close();
?>
