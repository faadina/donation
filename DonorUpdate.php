<?php
include 'dbConnect.php'; // Include your database connection script

// Initialize variables to hold donor information
$donorID = $donorName = $donorPhoneNo = $donorDOB = $donorAddress = $donorEmail = '';

// Check if donorID is provided in the URL
if (isset($_GET['donorID'])) {
    $donorID = $_GET['donorID'];

    // Fetch donor information from the database based on donorID
    $sql = "SELECT * FROM Donor WHERE donorID = '$donorID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $donorName = $row['donorName'];
        $donorPhoneNo = $row['donorPhoneNo'];
        $donorDOB = $row['donorDOB'];
        $donorAddress = $row['donorAddress'];
        $donorEmail = $row['donorEmail'];
    } else {
        echo "No donor found with ID: " . $donorID;
        exit; // Stop further execution if donor is not found
    }
} else {
    echo "No donorID parameter provided.";
    exit; // Stop further execution if donorID is not provided
}

// Handle form submission for updating donor information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_update'])) {
    // Retrieve updated values from the form submission
    $donorName = $_POST['donorName'];
    $donorPhoneNo = $_POST['donorPhoneNo'];
    $donorDOB = $_POST['donorDOB'];
    $donorAddress = $_POST['donorAddress'];
    $donorEmail = $_POST['donorEmail'];

    // Update the donor information in the database
    $sqlUpdate = "UPDATE Donor SET donorName='$donorName', donorPhoneNo='$donorPhoneNo', donorDOB='$donorDOB', donorAddress='$donorAddress', donorEmail='$donorEmail' WHERE donorID='$donorID'";

    if ($conn->query($sqlUpdate) === TRUE) {
        // Redirect to DonorView.php after successful update
        header("Location: DonorView.php");
        exit;
    } else {
        echo "Error updating donor information: " . $conn->error;
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Donor Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Donor Information</h2>
                    </div>
                    <div class="card-body">
                        <form id="updateForm" method="post">
                            <div class="mb-3">
                                <label for="donorName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="donorName" name="donorName" value="<?php echo $donorName; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donorPhoneNo" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="donorPhoneNo" name="donorPhoneNo" value="<?php echo $donorPhoneNo; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="donorDOB" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="donorDOB" name="donorDOB" value="<?php echo $donorDOB; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="donorAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="donorAddress" name="donorAddress" rows="3"><?php echo $donorAddress; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="donorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="donorEmail" name="donorEmail" value="<?php echo $donorEmail; ?>" required>
                            </div>
                            <!-- Update Donor button with SweetAlert confirmation -->
                            <button type="button" class="btn btn-primary" onclick="confirmUpdate()">
                                <i class="bi bi-check"></i> Update Donor
                            </button>
                            <a href="DonorView.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Donor Records</a>
                            <input type="submit" class="d-none" name="confirm_update" id="confirm_update">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmUpdate() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to update this donor's information.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirm_update').click(); // Click the hidden submit button
                }
            });
        }
    </script>
</body>
</html>

