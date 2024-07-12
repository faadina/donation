<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload
function uploadImage($file)
{
    $targetDir = "uploads/"; // Directory where uploaded images will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
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

// Function to generate sequential allocationID like A001, A002, A003, ...
function generateUniqueID() {
    // Implement logic to get the latest allocationID from database and generate the next one
    global $conn;

    $sql = "SELECT MAX(allocationID) AS maxID FROM Allocation";
    $result = $conn->query($sql);

    $currentID = "A001"; // Default starting ID if no records are found

    if ($result && $row = $result->fetch_assoc()) {
        $maxID = $row['maxID'];
        if ($maxID) {
            // Extract numeric part of ID, increment, and format back to A001, A002, ...
            preg_match('/(\d+)$/', $maxID, $matches);
            $number = intval($matches[0]) + 1;
            $currentID = 'A' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }

    return $currentID;
}

// Initialize variables to hold form data
$allocationID = generateUniqueID(); // Generate allocationID
$allocationName = "";
$allocationStartDate = "";
$allocationEndDate = "";
$allocationStatus = "";
$allocationDetails = "";
$targetAmount = "";
$allocationImage = "";

// Process form submission for creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allocationName = $_POST["allocationName"];
    $allocationStartDate = $_POST["allocationStartDate"];
    $allocationEndDate = $_POST["allocationEndDate"];
    $allocationStatus = $_POST["allocationStatus"];
    $allocationDetails = $_POST["allocationDetails"];
    $targetAmount = $_POST["targetAmount"];

    // Check if an image file was uploaded
    $allocationImage = "";
    if (!empty($_FILES["allocationImage"]["name"])) {
        $allocationImage = uploadImage($_FILES["allocationImage"]);
    }

    // Prepare and execute the INSERT statement
    $sql = "INSERT INTO Allocation (allocationID, allocationName, allocationStartDate, allocationEndDate, allocationStatus, allocationDetails, targetAmount, currentAmount, allocationImage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $defaultCurrentAmount = 0; // Assuming currentAmount can default to 0 if no donations are made yet
        $stmt->bind_param("ssssssdss", $allocationID, $allocationName, $allocationStartDate, $allocationEndDate, $allocationStatus, $allocationDetails, $targetAmount, $defaultCurrentAmount, $allocationImage);
        if ($stmt->execute()) {
            // Redirect to AllocationView.php after successful creation
            header("Location: AllocationView.php");
            exit();
        } else {
            echo "Error creating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close(); // Close database connection
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 1rem;
        }

        .form-check-inline {
            margin-right: 1rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

<?php
include('StaffHeader.php'); // Assuming you have a header include file for your staff section
?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Create New Allocation</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                        enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="allocationName" class="form-label">Allocation Name</label>
                            <input type="text" class="form-control" id="allocationName" name="allocationName"
                                value="<?php echo htmlspecialchars($allocationName); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="allocationStartDate"
                                        name="allocationStartDate"
                                        value="<?php echo htmlspecialchars($allocationStartDate); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allocationEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="allocationEndDate"
                                        name="allocationEndDate"
                                        value="<?php echo htmlspecialchars($allocationEndDate); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusActive"
                                    name="allocationStatus" value="Active" checked required>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="statusInactive"
                                    name="allocationStatus" value="Inactive" required>
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="allocationDetails" name="allocationDetails" rows="4"
                                required><?php echo htmlspecialchars($allocationDetails); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="targetAmount" class="form-label">Target Amount (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" class="form-control" id="targetAmount"
                                    name="targetAmount"
                                    value="<?php echo htmlspecialchars($targetAmount); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="allocationImage" class="form-label">Upload Image</label>
                            <input type="file" class="form-control" id="allocationImage" name="allocationImage">
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="AllocationView.php" class="btn btn-secondary"><i
                                    class="bi bi-arrow-left"></i> Back to Allocation Records</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript and dependencies (optional if not needed for your form interactions) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
