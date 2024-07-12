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


// Initialize an empty array to store staff data
$staffData = array();

// Attempt to fetch staff data from the database
$sql = "SELECT staffID, staffName, staffPhoneNo, staffEmail, staffPassword FROM staff";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if ($result) {
    $count = mysqli_num_rows($result); // Count total number of staff
    $i = 1; // Counter for enumeration

    while ($row = mysqli_fetch_assoc($result)) {
        $row['No'] = $i++;
        $staffData[] = $row;
    }
} else {
    echo "Error retrieving staff data: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Staff Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            background-color: whitesmoke;
            color: #FFFFFF;
        }

        .wrapper {
            color: black;
            width: 80%;
            padding: 20px;
            margin: 0 auto;
            margin-top: 50px;
            background-color: whitesmoke;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
        }

        table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: black;
        }

        .btn-primary {
            background-color: black;
            border: none;
        }

        .btn-primary:hover {
            background-color: #808080;
        }

        .btn {
            margin: 0 auto;
        }

        .modal-content {
            color: black;
        }

        .modal-body {
            color: black;
        }

        .modal-header {
            color: black;
        }

        .modal-title {
            color: black;
            /* Font color for modal title */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <?php include('ManagerHeader.php'); ?>
    <div class="wrapper">
        <h2>Staff Information</h2>
        <div class="row mb-3">
            <div class="col-md-6">
                <p>Total Staff: <?php echo $count; ?></p>
            </div>
            <div class="col-md-6 text-right">
                <a style="background:green;" href="createStaff.php" class="btn btn-primary" ><i class="bi bi-person-plus"></i>&nbsp;Staff</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th colspan='3' style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffData as $staff) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['No']); ?></td>
                        <td><?php echo htmlspecialchars($staff['staffID']); ?></td>
                        <td><?php echo htmlspecialchars($staff['staffName']); ?></td>
                        <td><?php echo htmlspecialchars($staff['staffPhoneNo']); ?></td>
                        <td><?php echo htmlspecialchars($staff['staffEmail']); ?></td>
                        <td><?php echo htmlspecialchars(substr($staff['staffPassword'], 0, 3)) . str_repeat('*', strlen($staff['staffPassword']) - 3); ?></td>
                        <td style="text-align:center;"><button style="background:none; border:none; color:green;" class="btn btn-info btn-sm" onclick="showReadModal('<?php echo htmlspecialchars($staff['staffID']); ?>', '<?php echo htmlspecialchars($staff['staffName']); ?>', '<?php echo htmlspecialchars($staff['staffPhoneNo']); ?>', '<?php echo htmlspecialchars($staff['staffEmail']); ?>', '<?php echo htmlspecialchars($staff['staffPassword']); ?>')"><i class="bi bi-eye"></i></button></td>
                        <td style="text-align:center;"><a style="background:none; border:none; color:orange;"  href="updateStaff.php?id=<?php echo $staff['staffID']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a></td>
                        <td style="text-align:center;"><button  style="background:none; border:none; color:red;"  class="btn btn-danger btn-sm" onclick="showDeleteModal('<?php echo htmlspecialchars($staff['staffID']); ?>', '<?php echo htmlspecialchars($staff['staffName']); ?>', '<?php echo htmlspecialchars($staff['staffPhoneNo']); ?>', '<?php echo htmlspecialchars($staff['staffEmail']); ?>')"><i class="bi bi-trash"></i></button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <!-- Read Modal -->
    <div class="modal fade" id="readModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Staff Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Staff ID:</strong> <span id="readStaffID"></span></p>
                    <p><strong>Name:</strong> <span id="readStaffName"></span></p>
                    <p><strong>Phone Number:</strong> <span id="readStaffPhoneNo"></span></p>
                    <p><strong>Email:</strong> <span id="readStaffEmail"></span></p>
                    <p><strong>Password:</strong> <span id="readStaffPassword"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete Staff</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the following staff?</p>
                    <p><strong>Staff ID:</strong> <span id="staffID"></span></p>
                    <p><strong>Name:</strong> <span id="staffName"></span></p>
                    <p><strong>Phone Number:</strong> <span id="staffPhoneNo"></span></p>
                    <p><strong>Email:</strong> <span id="staffEmail"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function showReadModal(staffID, staffName, staffPhoneNo, staffEmail, staffPassword) {
            document.getElementById('readStaffID').innerText = staffID;
            document.getElementById('readStaffName').innerText = staffName;
            document.getElementById('readStaffPhoneNo').innerText = staffPhoneNo;
            document.getElementById('readStaffEmail').innerText = staffEmail;
            document.getElementById('readStaffPassword').innerText = staffPassword;
            $('#readModal').modal('show');
        }

        function showDeleteModal(staffID, staffName, staffPhoneNo, staffEmail) {
            document.getElementById('staffID').innerText = staffID;
            document.getElementById('staffName').innerText = staffName;
            document.getElementById('staffPhoneNo').innerText = staffPhoneNo;
            document.getElementById('staffEmail').innerText = staffEmail;
            document.getElementById('confirmDeleteButton').setAttribute('data-staffid', staffID);
            $('#deleteModal').modal('show');
        }

        $('#confirmDeleteButton').click(function() {
            var staffID = $(this).data('staffid');

            $.ajax({
                url: 'deleteStaff.php',
                type: 'POST',
                data: {
                    id: staffID
                },
                success: function(response) {
                    if (response === 'success') {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error deleting staff.');
                    }
                },
                error: function() {
                    alert('Error deleting staff.');
                }
            });
        });
    </script>
</body>

</html>