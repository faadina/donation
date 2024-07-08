<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <!-- Bootstrap core CSS-->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <style>
    /* Custom CSS for centering sidebar links */
    .navbar-sidenav .nav-link {
        text-align: center;
    }
    /* Custom CSS for spacing cards */
    .card {
        margin-top: 50px; /* Adjust as per your design */
        background-color:paleturquoise; /* Add background color */
        border: 1px solid #ddd; /* Add border for clarity */
        border-radius: 5px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow */
    }
    /* Custom CSS for profile sidebar */
    .sidebar-profile .nav-link {
        padding: 10px 20px; /* Adjust padding as needed */
        text-align: left; /* Adjust alignment */
    }
    .sidebar-profile .nav-item {
        margin-bottom: 10px; /* Adjust spacing between items */
    }
    /* Custom CSS for positioning profile sidebar */
    .sidebar-profile {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        width: 250px;
        background-color: #f8f9fa; /* Adjust background color */
        padding-top: 60px; /* Adjust top padding */
        overflow-x: hidden;
        border-right: 1px solid #dee2e6; /* Add a border for separation */
        margin-top: 100px;
    }
    .content-wrapper {
        margin-left: 250px; /* Adjust to match sidebar width */
        padding: 20px; /* Adjust padding as needed */
    }
    /* Adjust top navigation to align with content */
    .navbar-nav.ml-auto.justify-content-center {
        margin-left: auto;
        margin-right: 0px; /* Adjust to match sidebar width */
    }
    /* Custom CSS for profile image */
    .profile-image {
        text-align: center;
        margin-bottom: 20px;
    }
    .profile-image img {
        width: 80px; /* Adjust image width */
        height: 80px; /* Adjust image height */
        border-radius: 50%; /* Make it circular */
    }

    /* Hover effect for cards */
    .card {
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: scale(1.02); /* Increase size on hover */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow on hover */
    }
</style>

</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <!-- Profile Sidebar -->
    <div class="sidebar-profile">
        <div class="profile-image">
            <img src="image/profilePicStaff.png" alt="Profile Image">
        </div>
        <ul class="navbar-nav">
            <!-- Welcome Message -->
            <li class="nav-item">
                <div class="nav-link">
                    <span>Welcome, User!</span>
                </div>
            </li>
            <!-- User Profile -->
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="User Profile">
                <a class="nav-link" href="#">
                    <i class="fa fa-fw fa-user"></i>
                    <span class="nav-link-text">My Profile</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
            <a class="navbar-brand" href="index.php">Madrasah Tarbiyyah</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <!-- Main Sidebar -->
                <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                    <!-- Dashboard -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                        <a class="nav-link" href="index.php">
                            <i class="fa fa-fw fa-dashboard"></i>
                            <span class="nav-link-text">Dashboard</span>
                        </a>
                    </li>
                    <!-- Staff-specific sidebar items -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Manage Donors">
                        <a class="nav-link" href="#">
                            <i class="fa fa-fw fa-users"></i>
                            <span class="nav-link-text">Manage Donors</span>
                        </a>
                    </li>
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Manage Staff">
                        <a class="nav-link" href="#">
                            <i class="fa fa-fw fa-user"></i>
                            <span class="nav-link-text">Manage Staff</span>
                        </a>
                    </li>
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Manage Allocations">
                        <a class="nav-link" href="#">
                            <i class="fa fa-fw fa-tasks"></i>
                            <span class="nav-link-text">Manage Allocations</span>
                        </a>
                    </li>
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Manage Donations">
                        <a class="nav-link" href="#">
                            <i class="fa fa-fw fa-money"></i>
                            <span class="nav-link-text">Manage Donations</span>
                        </a>
                    </li>
                </ul>

                <!-- Sidebar Toggle -->
                <ul class="navbar-nav sidenav-toggler">
                    <li class="nav-item">
                        <a class="nav-link text-center" id="sidenavToggler">
                            <i class="fa fa-fw fa-angle-left"></i>
                        </a>
                    </li>
                </ul>

                <!-- Top Navigation -->
                <ul class="navbar-nav ml-auto justify-content-center">
                    <!-- Messages Dropdown -->
                    <li class="nav-item dropdown ml-auto"> <!-- Adjusted ml-lg-2 for right margin -->
    <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-fw fa-envelope"></i>
        <span class="d-lg-none">Messages
            <span class="badge badge-pill badge-primary">12 New</span>
        </span>
        <span class="indicator text-primary d-none d-lg-block">
            <i class="fa fa-fw fa-circle"></i>
        </span>
    </a>
    <!-- Messages Dropdown Menu -->
    <div class="dropdown-menu" aria-labelledby="messagesDropdown">
        <!-- Messages content -->
    </div>
</li>

                    <!-- Alerts Dropdown -->
                    <li class="nav-item dropdown ml-lg-2"> <!-- Adjusted ml-lg-2 for right margin -->
    <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-fw fa-bell"></i>
        <span class="d-lg-none">Alerts
            <span class="badge badge-pill badge-warning">6 New</span>
        </span>
        <span class="indicator text-warning d-none d-lg-block">
            <i class="fa fa-fw fa-circle"></i>
        </span>
    </a>
    <!-- Alerts Dropdown Menu -->
    <div class="dropdown-menu" aria-labelledby="alertsDropdown">
        <!-- Alerts content -->
    </div>
</li>

                    <li class="nav-item mr-lg-0 mt-3 mt-lg-2"> <!-- Add mt-1 and mt-lg-0 for margin top -->
                    <form class="form-inline my-2 my-lg-0 ml-auto"> <!-- Adjusted ml-auto for right alignment -->
    <div class="input-group">
        <input class="form-control" type="text" placeholder="Search for...">
        <span class="input-group-append">
            <button class="btn btn-primary" type="button" id="searchButton">
                <i class="fa fa-search"></i>
            </button>
        </span>
    </div>
</form>
                    </li>
                    <!-- Logout Button -->
                    <li class="nav-item ml-lg-2"> <!-- Adjusted ml-lg-2 for right margin -->
    <a class="nav-link" id="logoutButton">
        <i class="fa fa-fw fa-sign-out"></i>Logout
    </a>
</li>

                </ul>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
            <!-- Breadcrumbs -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">My Dashboard</li>
            </ol>

            <div class="row">
    <!-- Donor Details -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fa fa-fw fa-user"></i> Donor
                </h2>
                <ul class="list-group">
                    <!-- PHP code for displaying donor details -->
                    <?php
                    include 'db.php';
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM donor";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo $row["donor_name"];
                            echo '<span class="badge badge-primary badge-pill">';
                            echo $row["donation_amount"];
                            echo '</span></li>';
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                    ?>
                    <!-- End PHP code -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Staff Details -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fa fa-fw fa-users"></i> Staff
                </h2>
                <ul class="list-group">
                    <!-- PHP code for displaying staff details -->
                    <?php
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM staff";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                            echo $row["staff_name"];
                            echo '<span class="badge badge-primary badge-pill">';
                            echo $row["role"];
                            echo '</span></li>';
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                    ?>
                    <!-- End PHP code -->
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Allocation Details -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fa fa-fw fa-tasks"></i> Allocation
                </h2>
                <ul class="list-group">
                    <!-- PHP code for displaying allocation details -->
                    <?php
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM Allocation";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<li class="list-group-item">';
                            echo htmlspecialchars($row["allocationType"]) . ' - Target: $' . htmlspecialchars($row["targetAmount"]);
                            echo '</li>';
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                    ?>
                    <!-- End PHP code -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Donation Details -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fa fa-fw fa-money"></i> Donation
                </h2>
                <ul class="list-group">
                    <!-- PHP code for displaying donation details -->
                    <?php
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM Donation";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<li class="list-group-item">Donation ID: ';
                            echo htmlspecialchars($row["donationID"]) . ' - Amount: $' . htmlspecialchars($row["donationAmount"]);
                            echo '</li>';
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                    ?>
                    <!-- End PHP code -->
                </ul>
            </div>
        </div>
    </div>
</div>

            
        <!-- /.container-fluid -->

        <!-- Footer -->
        <footer class="sticky-footer">
            <div class="container">
                <div class="text-center">
                    <small>Copyright © Your Website 2024</small>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top Button -->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>
    </div>
    <!-- /#wrapper -->

    <!-- SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom Script -->
    <script>
        // Handle logout button click
        document.getElementById('logoutButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Ready to Leave?',
                text: 'Select "Logout" below if you are ready to end your current session.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to logout page or perform logout action
                    window.location.href = 'login.html';
                }
            });
        });
    </script>
</body>
</html>
