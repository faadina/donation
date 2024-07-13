<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

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

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } elseif (!preg_match("/^[a-zA-Z@. ]*$/", $_POST["name"])) {
        $name_err = "Name can only contain letters, spaces, '.', and '@'.";
    } elseif (preg_match("/\d/", $_POST["name"])) {
        $name_err = "Name cannot contain numbers.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate birthdate
    if (empty(trim($_POST["birthdate"]))) {
        $birthdate_err = "Please enter your birthdate.";
    } else {
        $birthdate = trim($_POST["birthdate"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } elseif (!preg_match("/^[0-9-]*$/", $_POST["phone"])) {
        $phone_err = "Phone number can only contain digits and hyphens (-).";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);

        // Check if email already exists in staff table
        $sql_donor = "SELECT donorID FROM donor WHERE donorEmail = ?";
        $stmt_donor = mysqli_prepare($conn, $sql_donor);
        if ($stmt_donor) {
            mysqli_stmt_bind_param($stmt_donor, "s", $email);
            mysqli_stmt_execute($stmt_donor);
            mysqli_stmt_store_result($stmt_donor);
            if (mysqli_stmt_num_rows($stmt_donor) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_donor);
        } else {
            echo "Something went wrong with the donor table query.";
        }

        // Check if email already exists in donor table
        $sql_staff = "SELECT staffID FROM staff WHERE staffEmail = ?";
        $stmt_staff = mysqli_prepare($conn, $sql_staff);
        if ($stmt_staff) {
            mysqli_stmt_bind_param($stmt_staff, "s", $email);
            mysqli_stmt_execute($stmt_staff);
            mysqli_stmt_store_result($stmt_staff);
            if (mysqli_stmt_num_rows($stmt_staff) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_staff);
        } else {
            echo "Something went wrong with the staff table query.";
        }

   

        // Check if email already exists in manager table
        $sql_manager = "SELECT managerID FROM manager WHERE managerEmail = ?";
        $stmt_manager = mysqli_prepare($conn, $sql_manager);
        if ($stmt_manager) {
            mysqli_stmt_bind_param($stmt_manager, "s", $email);
            mysqli_stmt_execute($stmt_manager);
            mysqli_stmt_store_result($stmt_manager);
            if (mysqli_stmt_num_rows($stmt_manager) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_manager);
        } else {
            echo "Something went wrong with the manager table query.";
        }
    }


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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to update the donor details?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit the form if confirmed
            }
        });
    });
});
</script>
</body>
</html>
