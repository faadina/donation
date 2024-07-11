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

// Initialize variables to hold current values
$allocationID = "";
$allocationName = "";
$allocationStartDate = "";
$allocationEndDate = "";
$allocationStatus = "";
$allocationDetails = "";
$targetAmount = "";
$currentAmount = "";
$allocationImage = "";

// Check if allocationID is provided via GET parameter
if (isset($_GET['allocationID'])) {
    $allocationID = $_GET['allocationID'];

    // Fetch existing allocation details from the database
    $sql = "SELECT * FROM Allocation WHERE allocationID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $allocationID); // Adjust "s" if allocationID is of different type
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Populate variables with current values
        $row = $result->fetch_assoc();
        $allocationName = $row["allocationName"];
        $allocationStartDate = $row["allocationStartDate"];
        $allocationEndDate = $row["allocationEndDate"];
        $allocationStatus = $row["allocationStatus"];
        $allocationDetails = $row["allocationDetails"];
        $targetAmount = $row["targetAmount"];
        $allocationImage = $row["allocationImage"];
    } else {
        echo "Allocation not found.";
    }
}

// Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allocationID = $_POST["allocationID"];
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

    // Prepare SQL statement for update
    $sql = "UPDATE Allocation SET
            allocationName = ?,
            allocationStartDate = ?,
            allocationEndDate = ?,
            allocationStatus = ?,
            allocationDetails = ?,
            targetAmount = ?";

    // Append allocationImage update if an image was uploaded
    $params = array($allocationName, $allocationStartDate, $allocationEndDate, $allocationStatus, $allocationDetails, $targetAmount);

    if (!empty($allocationImage)) {
        $sql .= ", allocationImage = ?";
        $params[] = $allocationImage;
    }

    $sql .= " WHERE allocationID = ?";
    $params[] = $allocationID;

    // Execute SQL statement
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssss", ...$params);
        if ($stmt->execute()) {
            // Redirect to AllocationView.php after successful update
            header("Location: AllocationView.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $stmt->close(); // Close statement
    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Update Allocation</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="allocationID" value="<?php echo htmlspecialchars($allocationID); ?>">
            <div class="mb-3">
                <label for="allocationName" class="form-label">Allocation Name</label>
                <input type="text" class="form-control" id="allocationName" name="allocationName" value="<?php echo htmlspecialchars($allocationName); ?>" required>
            </div>
            <div class="mb-3">
                <label for="allocationStartDate" class="form-label">Allocation Start Date</label>
                <input type="date" class="form-control" id="allocationStartDate" name="allocationStartDate" value="<?php echo htmlspecialchars($allocationStartDate); ?>" required>
            </div>
            <div class="mb-3">
                <label for="allocationEndDate" class="form-label">Allocation End Date</label>
                <input type="date" class="form-control" id="allocationEndDate" name="allocationEndDate" value="<?php echo htmlspecialchars($allocationEndDate); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Allocation Status</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="statusActive" name="allocationStatus" value="Active" <?php if ($allocationStatus === "Active") echo "checked"; ?> required>
                    <label class="form-check-label" for="statusActive">Active</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="statusInactive" name="allocationStatus" value="Inactive" <?php if ($allocationStatus === "Inactive") echo "checked"; ?> required>
                    <label class="form-check-label" for="statusInactive">Inactive</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="allocationDetails" class="form-label">Allocation Details</label>
                <textarea class="form-control" id="allocationDetails" name="allocationDetails" required><?php echo htmlspecialchars($allocationDetails); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="targetAmount" class="form-label">Target Amount</label>
                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" value="<?php echo htmlspecialchars($targetAmount); ?>" required>
            </div>
            <div class="mb-3">
                <label for="allocationImage" class="form-label">Current Allocation Image</label><br>
                <?php if (!empty($allocationImage)) : ?>
                    <img src="<?php echo htmlspecialchars($allocationImage); ?>" class="img-fluid rounded" alt="Current Allocation Image">
                <?php else : ?>
                    No Image
                <?php endif; ?>
                <input type="file" class="form-control mt-3" id="allocationImage" name="allocationImage">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
