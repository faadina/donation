<?php
session_start();
include 'dbConnect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die('User ID not found in session.');
}

$donorID = $_SESSION['username'];
$recordsPerPage = 10;

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch donor name based on donor ID
$donorNameStmt = $conn->prepare("SELECT donorName FROM Donor WHERE donorID = ?");
$donorNameStmt->bind_param('s', $donorID);
$donorNameStmt->execute();
$donorNameResult = $donorNameStmt->get_result();
$donorNameRow = $donorNameResult->fetch_assoc();
$donorName = $donorNameRow['donorName'];

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Get the search query if set
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the base SQL query
$sql = "
    SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, d.donationReceipt, a.allocationName 
    FROM Donation d 
    JOIN Allocation a ON d.allocationID = a.allocationID 
    WHERE d.donorID = ?
";

// Add search condition if a search query is provided
if ($search) {
    $sql .= " AND a.allocationName LIKE ?";
}

// Get the total number of donations
$totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM ($sql) as totalDonations");
if ($search) {
    $searchParam = "%$search%";
    $totalStmt->bind_param('ss', $donorID, $searchParam);
} else {
    $totalStmt->bind_param('s', $donorID);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalDonations = $totalRow['total'];
$totalPages = ceil($totalDonations / $recordsPerPage);

// Prepare SQL statement to fetch donation history with limit and offset
$sql .= " ORDER BY d.donationID DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bind_param('ssii', $donorID, $searchParam, $recordsPerPage, $offset);
} else {
    $stmt->bind_param('sii', $donorID, $recordsPerPage, $offset);
}
$stmt->execute();

// Get result set from the executed SQL query
$result = $stmt->get_result();

// Initialize counter for accepted donations
$acceptedCount = 0;
$countDonation = $result->num_rows; // Total number of donations on the current page

// Iterate through donation records
while ($row = $result->fetch_assoc()) {
    // Check if donation status is 'Accepted'
    if ($row['donationStatus'] === 'Accepted') {
        $acceptedCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>

    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Include Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .titleTable {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            color: rgb(164, 231, 192);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .donation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .donation-table th,
        .donation-table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
            vertical-align: middle; /* Ensures vertical centering */
        }

        .donation-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .donation-table tr:hover {
            background-color: #f9f9f9;
        }

        .view-receipt-btn {
            background-color: #333;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-receipt-btn:hover {
            background-color: #555;
        }

        .receipt-image {
            max-width: 100%;
            height: auto;
        }

        .receipt-iframe {
            width: 100%;
            height: 500px;
            border: none;
        }

        .headertable {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: left;
        }

        .header-info {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            color: white;
            border-radius: 8px;
            padding: 2px 9px;
        }

        .accepted {
            color: green;
            font-weight: 700;
        }

        .pending {
            color: inherit;
            /* or any other styling you want for pending */
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #333;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin: 0 2px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #333;
            color: white;
            border: 1px solid #333;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .donation-table td.center {
        display: flex;
        justify-content: center; /* Centers horizontally */
        align-items: center; /* Centers vertically */
    }
    </style>
</head>

<body>
    <?php include 'DonorHeader.php'; ?> <!-- Include your header file -->

    <div class="wrapper">
        <div class="titleTable">
            <h2>Donation History</h2>
        </div>
        <form method="GET" action="" class="d-flex align-items-center">
            <input type="text" name="search" class="form-control me-2" placeholder="Search Allocation Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <div class="headertable">
            <div>
                <h4>ID: <?php echo htmlspecialchars($donorID); ?> <br><br>Name: <?php echo htmlspecialchars($donorName); ?></h4>
            </div>
            
            <div class="header-info">
                <p>Donations: <b><?php echo $totalDonations; ?></b> | Accepted: <b><?php echo $acceptedCount; ?></b></p>
            </div>
        </div>

        <div class="donation-container">
            <table class="donation-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Allocation Name</th>
                        <th>Donation Amount (RM)</th>
                        <th>Donation Date</th>
                        <th>Donation Status</th>
                        <th>Donation Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result->data_seek(0); // Reset result pointer to the beginning

                    // Display donation records
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['donationID']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['allocationName']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['donationAmount']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['donationDate']) . '</td>';
                        echo '<td class="' . ($row['donationStatus'] === 'Accepted' ? 'accepted' : 'pending') . '">' . htmlspecialchars($row['donationStatus']) . '</td>';
                        echo '<td class="center"><button class="view-receipt-btn" onclick="viewReceipt(\'' . htmlspecialchars($row['donationReceipt']) . '\')">⌞view⌝</button></td>';
                        echo '</tr>';
                    }

                    // Close statement and connection
                    $stmt->close();
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">« Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next »</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function viewReceipt(receiptURL) {
            Swal.fire({
                title: 'Donation Receipt',
                html: '<iframe class="receipt-iframe" src="' + receiptURL + '"></iframe>',
                showCloseButton: true
            });
        }
    </script>
</body>
</html>
