<?php
include 'dbConnect.php'; // Ensure this file includes your database connection details

// Function to handle file upload
function uploadImage($file) {
    $targetDir = "uploads/"; // Directory where uploaded images will be stored
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
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
    // Validate and sanitize input data
    $allocationName = htmlspecialchars($_POST["allocationName"]);
    $allocationStartDate = $_POST["allocationStartDate"];
    $allocationEndDate = $_POST["allocationEndDate"];
    $allocationDetails = htmlspecialchars($_POST["allocationDetails"]);
    $targetAmount = floatval($_POST["targetAmount"]);

    // Validate dates (simple validation)
    if (empty($allocationStartDate) || empty($allocationEndDate) || strtotime($allocationStartDate) >= strtotime($allocationEndDate)) {
        echo "Invalid date range.";
        exit();
    }

    // Check if an image file was uploaded
    $allocationImage = "";
    if (!empty($_FILES["allocationImage"]["name"])) {
        $allocationImage = uploadImage($_FILES["allocationImage"]);
    }

    // Generate a unique allocationID
    $allocationID = generateUniqueID($conn);

    // Prepare SQL statement for insertion
    $sql = "INSERT INTO Allocation (allocationID, allocationName, allocationStartDate, allocationEndDate, allocationStatus, allocationDetails, targetAmount, currentAmount, allocationImage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Create a prepared statement
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $allocationStatus = "Active"; // Set allocation status to Active
        $currentAmount = 0; // Set initial current amount to 0
        
        // Bind parameters to the statement
        $stmt->bind_param("ssssssdds", $allocationID, $allocationName, $allocationStartDate, $allocationEndDate, $allocationStatus, $allocationDetails, $targetAmount, $currentAmount, $allocationImage);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to view page upon successful insertion
            header("Location: AllocationView.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
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
