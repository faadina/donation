<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload (if needed)
function uploadFile($file, $targetDir) {
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check file size (example limit set to 500KB)
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats (example: JPG, PNG, JPEG, PDF)
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf');
    if (!in_array($fileType, $allowedExtensions)) {
        echo "Sorry, only JPG, JPEG, PNG & PDF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        return "";
    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
            return "";
        }
    }
}

// Initialize variables to hold current values
$donorID = "";
$donorName = "";
$donorPhoneNo = "";
$donorDOB = "";
$donorAddress = "";
$donorEmail = "";
$donorPassword = "";

// Check if donorID is provided via GET parameter
if (isset($_GET['donorID'])) {
    $donorID = $_GET['donorID'];

    // Fetch existing donor details from the database
    $sql = "SELECT * FROM Donor WHERE donorID = '$donorID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Populate variables with current values
        $row = $result->fetch_assoc();
        $donorName = $row["donorName"];
        $donorPhoneNo = $row["donorPhoneNo"];
        $donorDOB = $row["donorDOB"];
        $donorAddress = $row["donorAddress"];
        $donorEmail = $row["donorEmail"];
        $donorPassword = $row["donorPassword"];
    } else {
        echo "Donor not found.";
    }
}

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donorID = $_POST["donorID"];
    $donorName = $_POST["donorName"];
    $donorPhoneNo = $_POST["donorPhoneNo"];
    $donorDOB = $_POST["donorDOB"];
    $donorAddress = $_POST["donorAddress"];
    $donorEmail = $_POST["donorEmail"];
    $donorPassword = $_POST["donorPassword"];

    // Prepare SQL statement for update
    $sql = "UPDATE Donor SET
            donorName = '$donorName',
            donorPhoneNo = '$donorPhoneNo',
            donorDOB = '$donorDOB',
            donorAddress = '$donorAddress',
            donorEmail = '$donorEmail',
            donorPassword = '$donorPassword'
            WHERE donorID = '$donorID'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Redirect to DonorView.php after successful update
        header("Location: DonorView.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Donor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('StaffHeader.php'); ?> <!-- Assuming staffHeader.php contains your header information -->

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Update Donor</h2>
                    </div>
                    <div class="card-body">
                        <form action="DonorUpdate.php" method="post">
                            <input type="hidden" name="donorID" value="<?php echo $donorID; ?>">
                            <div class="mb-3">
                                <label for="donorName" class="form-label">Donor Name</label>
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
                                <input type="text" class="form-control" id="donorAddress" name="donorAddress" value="<?php echo $donorAddress; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="donorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="donorEmail" name="donorEmail" value="<?php echo $donorEmail; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="donorPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="donorPassword" name="donorPassword" value="<?php echo $donorPassword; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="DonorView.php" class="btn btn-secondary">Back to Donor Records</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
