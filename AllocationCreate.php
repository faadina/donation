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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Allocation</title>
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
                        <h2 class="card-title">Create Allocation</h2>
                    </div>
                    <div class="card-body">
                        <!-- Validation messages container -->
                        <div id="validationMessages" class="mb-3"></div>
                        <form action="AllocationCreate.php" method="post" enctype="multipart/form-data" id="createForm">
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
                                <label for="allocationDetails" class="form-label">Allocation Details</label>
                                <textarea class="form-control" id="allocationDetails" name="allocationDetails" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="targetAmount" class="form-label">Target Amount</label>
                                <input type="number" step="0.01" class="form-control" id="targetAmount" name="targetAmount" required>
                            </div>
                            <div class="mb-3">
                                <label for="allocationImage" class="form-label">Allocation Image</label>
                                <input type="file" class="form-control" id="allocationImage" name="allocationImage" accept=".jpg, .jpeg, .png, .gif">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="validateForm()">
                                <i class="bi bi-check"></i> Create
                            </button>
                            <a href="AllocationView.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Allocation Records</a>
                            <input type="submit" class="d-none" name="confirm_create" id="confirm_create">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        let allocationName = document.getElementById('allocationName').value.trim();
        let allocationStartDate = document.getElementById('allocationStartDate').value;
        let allocationEndDate = document.getElementById('allocationEndDate').value;
        let allocationDetails = document.getElementById('allocationDetails').value.trim();
        let targetAmount = document.getElementById('targetAmount').value.trim();
        let allocationImage = document.getElementById('allocationImage').value;

        // Remove previous validation messages
        clearValidationMessages();

        let validationMessages = [];

        // Basic required field checks
        if (allocationName === "") {
            validationMessages.push({ field: 'allocationName', message: 'Allocation name is required.' });
        }
        if (allocationStartDate === "") {
            validationMessages.push({ field: 'allocationStartDate', message: 'Allocation start date is required.' });
        }
        if (allocationEndDate === "") {
            validationMessages.push({ field: 'allocationEndDate', message: 'Allocation end date is required.' });
        }
        if (allocationDetails === "") {
            validationMessages.push({ field: 'allocationDetails', message: 'Allocation details are required.' });
        }
        if (targetAmount === "") {
            validationMessages.push({ field: 'targetAmount', message: 'Target amount is required.' });
        }
        // Validate date range
        if (new Date(allocationStartDate) >= new Date(allocationEndDate)) {
            validationMessages.push({ field: 'allocationEndDate', message: 'End date must be after start date.' });
        }

        // Validate file upload (optional)
        if (allocationImage !== "" && !['jpeg', 'jpg', 'png', 'gif'].includes(getFileExtension(allocationImage))) {
            validationMessages.push({ field: 'allocationImage', message: 'Only JPG, PNG, and GIF files are allowed.' });
        }

        // Display all validation messages below their respective fields
        validationMessages.forEach(message => {
            let inputElement = document.getElementById(message.field);
            let feedbackElement = document.createElement('div');
            feedbackElement.classList.add('invalid-feedback');
            feedbackElement.textContent = message.message;
            inputElement.classList.add('is-invalid');
            inputElement.parentNode.appendChild(feedbackElement);
        });

        // If all validations pass, confirm with user
        if (validationMessages.length === 0) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to create a new allocation.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, create it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirm_create').click(); // Click the hidden submit button
                }
            });
        }
    }

    function clearValidationMessages() {
        let invalidFeedback = document.querySelectorAll('.invalid-feedback');
        invalidFeedback.forEach(element => {
            element.parentNode.removeChild(element);
        });

        let inputFields = document.querySelectorAll('.is-invalid');
        inputFields.forEach(element => {
            element.classList.remove('is-invalid');
        });
    }

    function getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }
</script>

</body>
</html>
