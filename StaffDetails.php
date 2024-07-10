<?php
require_once("dbConnect.php"); // Ensure your database connection file is included

// Initialize an empty array to store staff data
$staffData = array();

// Attempt to fetch staff data from the database
$sql = "SELECT staffID, staffName, staffPhoneNo, staffEmail, staffPassword FROM staff";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if ($result) {
    // Fetch each row of data and store it in $staffData array
    while ($row = mysqli_fetch_assoc($result)) {
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
    <style type="text/css">
         @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body { 
            background-color: whitesmoke; /* Dark Cyan Theme Background */
            color: #FFFFFF; /* White text for contrast */
        }
        .wrapper { 
            color: black;
            width: auto; /* Adjust width as necessary */
            padding: 20px; 
            margin: auto;
            margin-top: 50px;
            background-color: whitesmoke; 
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000000;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
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
    </style>
</head>
<body>
    <?php include('ManagerHeader.php'); ?>
    <div class="wrapper">
        <h2>Staff Information</h2>
        <a href="createStaff.php" class="btn btn-primary mb-3">Create Staff Account</a> 

        <table>
            <thead>
                <tr>
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
                    <td><?php echo htmlspecialchars($staff['staffID']); ?></td>
                    <td><?php echo htmlspecialchars($staff['staffName']); ?></td>
                    <td><?php echo htmlspecialchars($staff['staffPhoneNo']); ?></td>
                    <td><?php echo htmlspecialchars($staff['staffEmail']); ?></td>
                    <td><?php echo htmlspecialchars($staff['staffPassword']); ?></td>
                    <td><a href="viewStaff.php?id=<?php echo $staff['staffID']; ?>" class="btn btn-info btn-sm">View</a></td>
                    <td><a href="updateStaff.php?id=<?php echo $staff['staffID']; ?>" class="btn btn-warning btn-sm">Update</a></td>
                    <td><a href="deleteStaff.php?id=<?php echo $staff['staffID']; ?>" class="btn btn-danger btn-sm">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
