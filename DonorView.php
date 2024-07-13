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

// Fetch donor records from the database
$sql = "SELECT * FROM Donor";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 100px;
            max-height: 100px;
        }
    
        .input-group-append {
            display: flex;
            align-items: stretch; /* Ensuring alignment */
            margin-left: 0px; /* Pushing to the right */
            margin-right: 1000px; 
        }
        .input-group .form-control {
            width: 200px; /* Adjust width of input field */
        }
    </style>
</head>
<body>
    <?php include('staffHeader.php'); ?>
    <div class="container">
        <h2 class="my-4">Donor Records</h2>
        <a href="StaffDashboard.php" class="btn btn-primary mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
            </svg>
        </a>
        <div class="input-group mb-4">
            <input type="text" id="donorID" class="form-control" placeholder="Search for donor ID..." onkeyup="searchDonor()">
            <div class="input-group-append">
                <button class="btn btn-primary search-button" type="button" onclick="searchDonor()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </button>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr><th>No</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th colspan='3' style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
              if ($result->num_rows > 0) {
                $count = 1; // Initialize a counter
                while ($row = $result->fetch_assoc()) {
                    $dob = date('d/m/y', strtotime($row["donorDOB"]));
                    echo "<tr>";
                    echo "<td>" . $count . "</td>"; // Display the row number
                    echo "<td>" . $row["donorID"] . "</td>";
                    echo "<td>" . $row["donorName"] . "</td>";
                    echo "<td>" . $row["donorPhoneNo"] . "</td>";
                    echo "<td>" . $dob . "</td>";
                    echo "<td>" . $row["donorAddress"] . "</td>";
                    echo "<td>" . $row["donorEmail"] . "</td>";
                    echo "<td><a href='DonorUpdate.php?donorID=" . $row["donorID"] . "' class='btn btn-primary btn-mini-column'><i class='bi bi-pencil'></i></a></td>";
                    echo "</tr>";
                    $count++; // Increment the counter
                }
            } else {
                echo "<tr><td colspan='7'>No donor records found</td></tr>";
            }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchDonor() {
            var input = document.getElementById("donorID").value.trim().toLowerCase();
            var table = document.getElementsByTagName("table")[0];
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                var donorIDCell = rows[i].getElementsByTagName("td")[0]; // Assuming donorID is in the first column
                
                if (donorIDCell) {
                    var textValue = donorIDCell.textContent || donorIDCell.innerText;
                    
                    if (textValue.trim().toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }       
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
