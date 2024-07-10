<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload
function uploadImage($file) {
    $targetDir = "uploads/"; // Directory where uploaded images will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check !== false) {
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
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a unique allocationID
    $allocationID = generateUniqueID($conn);

    $allocationName = $_POST["allocationName"];
    $allocationStartDate = $_POST["allocationStartDate"];
    $allocationEndDate = $_POST["allocationEndDate"];
    $allocationStatus = $_POST["allocationStatus"];
    $allocationDetails = $_POST["allocationDetails"];
    $targetAmount = $_POST["targetAmount"];
    $currentAmount = $_POST["currentAmount"];
    
    // Check if an image file was uploaded
    $allocationImage = "";
    if (!empty($_FILES["allocationImage"]["name"])) {
        $allocationImage = uploadImage($_FILES["allocationImage"]);
    }

    // Prepare SQL statement for insertion
    $sql = "INSERT INTO Allocation (allocationID, allocationName, allocationStartDate, allocationEndDate, allocationStatus, allocationDetails, targetAmount, currentAmount, allocationImage)
            VALUES ('$allocationID', '$allocationName', '$allocationStartDate', '$allocationEndDate', '$allocationStatus', '$allocationDetails', '$targetAmount', '$currentAmount', '$allocationImage')";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Display the newly inserted allocationID
        header("Location: AllocationView.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }



   
    $conn->close(); // Close database connection
}

// Function to generate unique allocationID based on the highest existing ID in the database
function generateUniqueID($conn) {
    $prefix = "A"; // Prefix for allocation IDs
    $sql = "SELECT MAX(allocationID) AS maxID FROM Allocation WHERE allocationID LIKE 'A%'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxID = $row["maxID"];
        if ($maxID) {
            $lastNumber = intval(substr($maxID, 1)); // Extract numeric part after prefix
            $newNumber = $lastNumber + 1; // Increment number
            $newID = $prefix . str_pad($newNumber, 3, "0", STR_PAD_LEFT); // Generate new ID
        } else {
            // If no existing records, start with the first allocation ID
            $newID = $prefix . "001";
        }
    } else {
        // Query error handling
        $newID = $prefix . "001";
    }

    return $newID;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Create Allocation</h2>
        <form action="AllocationCreate.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="allocationName" class="form-label">Allocation Name</label>
                <input type="text" class="form-control" id="allocationName" name="allocationName" required>
            </div>
            <div class="mb-3">
                <label for="allocationStartDate" class="form-label">Allocation Start Date</label>
                <input type="date" class="form-control" id="allocationStartDate" name="allocationStartDate" required>
            </div>
            <div class="mb-3">
                <label for="allocationEndDate" class="form-label">Allocation End Date</label>
                <input type="date" class="form-control" id="allocationEndDate" name="allocationEndDate" required>
            </div>
            <div class="mb-3">
                <label for="allocationStatus" class="form-label">Allocation Status</label>
                <input type="text" class="form-control" id="allocationStatus" name="allocationStatus" required>
            </div>
            <div class="mb-3">
                <label for="allocationDetails" class="form-label">Allocation Details</label>
                <textarea class="form-control" id="allocationDetails" name="allocationDetails" required></textarea>
            </div>
            <div class="mb-3">
                <label for="targetAmount" class="form-label">Target Amount</label>
                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" required>
            </div>
            <div class="mb-3">
                <label for="currentAmount" class="form-label">Current Amount</label>
                <input type="number" step="0.01" class="form-control" id="currentAmount" name="currentAmount" required>
            </div>
            <div class="mb-3">
                <label for="allocationImage" class="form-label">Allocation Image</label>
                <input type="file" class="form-control" id="allocationImage" name="allocationImage">
            </div>
            <button type="submit" class="btn btn-primary">Create</button>

        </form>
    </div>
</body>
</html>
