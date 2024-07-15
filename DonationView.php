<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Pagination variables
$limit = 10; // Number of records per page

// Calculate total pages
$countSql = "SELECT COUNT(*) as total FROM Donation";
$countResult = $conn->query($countSql);
$total_rows = $countResult->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Current page calculation
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($current_page - 1) * $limit;

// Fetch donation records with pagination and sorting
$sql = "SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.allocationID, a.allocationName, d.donationReceipt
        FROM Donation d
        LEFT JOIN Allocation a ON d.allocationID = a.allocationID
        ORDER BY d.donationDate DESC
        LIMIT $start, $limit";
$result = $conn->query($sql);

// Fetch allocation names for the dropdown
$allocationsSql = "SELECT allocationID, allocationName FROM Allocation";
$allocationsResult = $conn->query($allocationsSql);

// Fetch counts for filtering buttons
$countSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN donationStatus = 'Accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN donationStatus = 'Rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN donationStatus = 'pending' THEN 1 ELSE 0 END) as pending
             FROM Donation";
$countResult = $conn->query($countSql);
$counts = $countResult->fetch_assoc();

// Fetch total count of donation records
$total_rows = $counts['total'];
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
            margin-bottom: 15px;
        }

        .mb-3 form {
            display: inline-block;
        }

        .mb-3 select {
            width: 350px;
        }

        /* Table border styles */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .smaller-button {
            padding: 5px 10px;
            font-size: 0.9rem;
            background-color: #2b4162;
            background-image: linear-gradient(315deg, #2b4162 0%, #12100e 74%);
            color: white;
            border: none;
        }

        .page-title {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #454B1B;
            font-weight: 800;
        }

        /* Button styles */
        .btn-accept {
            background-color: green;
            background-image: linear-gradient(315deg, green 0%, darkgreen 74%);
        }

        .btn-reject {
            background-color: red;
            background-image: linear-gradient(315deg, red 0%, darkred 74%);
        }

        .btn-generate {
            font-size: 12px;
            padding: 5px 5px;
        }

        /* Adjustments for the back button */
        .btn-back {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include('staffHeader.php'); ?>

    <div class="container">
        <h2 class="page-title">Donation Records</h2>
        <!-- Display donation records categorized by allocation names -->
       

        <!-- Buttons for filtering and dropdown for allocation -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <div>
                <button class="btn btn-primary" onclick="showAll()">☰ All (<?php echo $counts['total']; ?>)</button>
                <button class="btn btn-success mx-2" onclick="showAccepted()">☰ Accepted (<?php echo $counts['accepted']; ?>)</button>
                <button class="btn btn-danger mx-2" onclick="showRejected()">☰ Rejected (<?php echo $counts['rejected']; ?>)</button>
                <button class="btn btn-warning mx-2" onclick="showPending()">☰ Pending (<?php echo $counts['pending']; ?>)</button>
            </div>
            <div class="d-flex">
                <select class="form-select me-2" id="allocationSelect" onchange="filterByAllocation()">
                    <option value="">Allocation Name</option>
                    <?php
                    $allocationsResult->data_seek(0); // Reset the result pointer
                    while ($row = $allocationsResult->fetch_assoc()) {
                        echo "<option value='" . $row["allocationName"] . "'>" . $row["allocationName"] . "</option>";
                    }
                    ?>
                </select>
                <div class="d-flex">
                    <input type="text" class="form-control me-2" id="donationIDInput" placeholder="Search Donation ID">
                    <button class="btn btn-primary" onclick="searchByDonationID()"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>

        <!-- Table for displaying donation records -->
        <table id="donationTable" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Donation ID</th>
                    <th>Allocation Name</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th colspan='2' style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $count = $start + 1;  // Initialize a counter
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>"; // Display the row number
                        echo "<td>" . $row["donationID"] . "</td>";
                        echo "<td>" . $row["allocationName"] . "</td>";
                        echo "<td>" . $row["donationAmount"] . "</td>";
                        echo "<td>" . date('d/m/y', strtotime($row["donationDate"])) . "</td>";

                        // Check the donation status and display corresponding styles
                        $statusColor = '';
                        switch ($row["donationStatus"]) {
                            case 'pending':
                                $statusColor = 'orange';
                                break;
                            case 'Accepted':
                                $statusColor = 'green';
                                break;
                            case 'Rejected':
                                $statusColor = 'red';
                                break;
                            default:
                                $statusColor = '';
                                break;
                        }
                        echo "<td style='color: " . $statusColor . "; font-weight: bold;'>" . $row["donationStatus"] . "</td>";

                        // Displaying receipt with a link to view PDF
                        echo "<td style='text-align: center;'>"; // Center-align the content in the table cell
                        if (!empty($row["donationReceipt"])) {
                            echo "<a href='" . htmlspecialchars($row["donationReceipt"]) . "' 
                            target='_blank' class='btn btn-primary btn-mini-column smaller-button'>⌞view⌝</a>";
                        } else {
                            echo "No Receipt";
                        }
                        echo "</td>";

                        // Display actions based on donation status
                        echo "<td colspan='2' style='text-align:center;'>";
                        switch ($row["donationStatus"]) {
                            case 'pending':
                                echo "<a href='DonationAccept.php?donationID=" . $row["donationID"] . "'  
                                class='btn btn-success btn-mini-column smaller-button btn-accept'>✓ Accept</a>";  //add alert this donationID is Accepted
                                echo "<a href='DonationReject.php?donationID=" . $row["donationID"] . "' 
                                class='btn btn-danger btn-mini-column smaller-button btn-reject'>✗ Reject</a>";   //add alert this donationID is Rejected
                                break;
                            case 'Accepted':
                                echo "<a href='DonationGenerateReceipt.php?donationID=" . $row["donationID"] . "' 
                                class='btn btn-secondary btn-mini-column smaller-button btn-generate'><i class='bi bi-file-earmark-text'></i> Generate Receipt</a>";
                                break;
                            case 'Rejected':
                                echo "<button class='btn btn-secondary btn-mini-column smaller-button' disabled>Rejected</button>";
                                break;
                            default:
                                break;
                        }
                        echo "</td>";

                        echo "</tr>";
                        $count++; // Increment the counter
                    }
                } else {
                    echo "<tr><td colspan='8'>No donation records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                    <li class="page-item <?php if ($page == $current_page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script>
        // JavaScript functions for filtering donations
        function showAccepted() {
            filterByStatus('Accepted');
        }

        function showRejected() {
            filterByStatus('Rejected');
        }

        function showPending() {
            filterByStatus('pending');
        }

        function showAll() {
            filterByStatus('');
        }

        function filterByStatus(status) {
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");
            for (var i = 1; i < rows.length; i++) {
                var statusCell = rows[i].getElementsByTagName("td")[5];
                if (statusCell && status !== '' && statusCell.innerText.trim() !== status) {
                    rows[i].style.display = "none";
                } else {
                    rows[i].style.display = "";
                }
            }
        }

        // JavaScript function to filter donations by allocation name
        function filterByAllocation() {
            var selectedAllocation = document.getElementById("allocationSelect").value.trim().toLowerCase();
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var allocationCell = rows[i].getElementsByTagName("td")[2];

                if (allocationCell) {
                    var textValue = allocationCell.textContent || allocationCell.innerText;

                    if (selectedAllocation === "" || textValue.trim().toLowerCase() === selectedAllocation) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }

        // JavaScript function to filter donations by donation ID
        function searchByDonationID() {
            var donationIDInput = document.getElementById("donationIDInput").value.trim().toLowerCase();
            var table = document.getElementById("donationTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var donationIDCell = rows[i].getElementsByTagName("td")[1];

                if (donationIDCell) {
                    var textValue = donationIDCell.textContent || donationIDCell.innerText;

                    if (donationIDInput === "" || textValue.trim().toLowerCase().includes(donationIDInput)) {
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
