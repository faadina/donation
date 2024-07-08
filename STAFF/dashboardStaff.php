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
    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">
    <style>
        /* Custom CSS for centering sidebar links */
        .navbar-sidenav .nav-link {
            text-align: center;
        }
        /* Custom CSS for spacing cards */
        .card {
            margin-top: 50px; /* Adjust as per your design */
        }
    </style>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <a class="navbar-brand" href="index.php">Start Bootstrap</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <!-- Sidebar -->
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <!-- Dashboard -->
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                    <a class="nav-link" href="index.php">
                        <i class="fa fa-fw fa-dashboard"></i>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>
                <!-- Other menu items -->
                <!-- Adjust as per your requirement -->
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-envelope"></i>
                        <span class="d-lg-none">Messages
                            <span class="badge badge-pill badge-primary">12 New</span>
                        </span>
                        <span class="indicator text-primary d-none d-lg-block">
                            <i class="fa fa-fw fa-circle"></i>
                        </span>
                    </a>
                    <!-- Messages Dropdown - Adjust as per your needs -->
                    <div class="dropdown-menu" aria-labelledby="messagesDropdown">
                        <!-- Messages content -->
                    </div>
                </li>
                <!-- Alerts Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-bell"></i>
                        <span class="d-lg-none">Alerts
                            <span class="badge badge-pill badge-warning">6 New</span>
                        </span>
                        <span class="indicator text-warning d-none d-lg-block">
                            <i class="fa fa-fw fa-circle"></i>
                        </span>
                    </a>
                    <!-- Alerts Dropdown - Adjust as per your needs -->
                    <div class="dropdown-menu" aria-labelledby="alertsDropdown">
                        <!-- Alerts content -->
                    </div>
                </li>
                <li class="nav-item mr-lg-3 mt-3 mt-lg-2"> <!-- Add mt-1 and mt-lg-0 for margin top -->
    <form class="form-inline my-2 my-lg-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for...">
            <span class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </form>
</li>
                <!-- Logout Button -->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-fw fa-sign-out"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Page Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumbs -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">My Dashboard</li>
            </ol>
            <!-- Content Cards -->
            <div class="row">
                <!-- Donor Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="card-title">Donor Details</h2>
                            <ul class="list-group">
                                <?php
                                // Fetching and displaying donor details
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "donationdb";

                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql_donors = "SELECT * FROM Donor";
                                $result_donors = $conn->query($sql_donors);

                                if ($result_donors->num_rows > 0) {
                                    while($row = $result_donors->fetch_assoc()) {
                                        echo '<li class="list-group-item">' . htmlspecialchars($row["donorName"]) . ' - ' . htmlspecialchars($row["donorEmail"]) . '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item">No donors found</li>';
                                }

                                $conn->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Staff Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="card-title">Staff Details</h2>
                            <ul class="list-group">
                                <?php
                                // Fetching and displaying staff details
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql_staff = "SELECT * FROM Staff";
                                $result_staff = $conn->query($sql_staff);

                                if ($result_staff->num_rows > 0) {
                                    while($row = $result_staff->fetch_assoc()) {
                                        echo '<li class="list-group-item">' . htmlspecialchars($row["staffName"]) . ' - ' . htmlspecialchars($row["staffEmail"]) . '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item">No staff found</li>';
                                }

                                $conn->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Allocation and Donation -->
            <div class="row">
                <!-- Allocation Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="card-title">Allocation Details</h2>
                            <ul class="list-group">
                                <?php
                                // Fetching and displaying allocation details
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql_allocations = "SELECT * FROM Allocation";
                                $result_allocations = $conn->query($sql_allocations);

                                if ($result_allocations->num_rows > 0) {
                                    while($row = $result_allocations->fetch_assoc()) {
                                        echo '<li class="list-group-item">' . htmlspecialchars($row["allocationType"]) . ' - Target: $' . htmlspecialchars($row["targetAmount"]) . '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item">No allocations found</li>';
                                }

                                $conn->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Donation Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="card-title">Donation Details</h2>
                            <ul class="list-group">
                                <?php
                                // Fetching and displaying donation details
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                $sql_donations = "SELECT * FROM Donation";
                                $result_donations = $conn->query($sql_donations);

                                if ($result_donations->num_rows > 0) {
                                    while($row = $result_donations->fetch_assoc()) {
                                        echo '<li class="list-group-item">Donation ID: ' . htmlspecialchars($row["donationID"]) . ' - Amount: $' . htmlspecialchars($row["donationAmount"]) . '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item">No donations found</li>';
                                }

                                $conn->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
        <!-- Footer -->
        <footer class="sticky-footer">
            <div class="container">
                <div class="text-center">
                    <small>&copy; 2024 Your Company. All Rights Reserved.</small>
                </div>
            </div>
        </footer>
        <!-- Scroll to Top Button -->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fa fa-angle-up"></i>
        </a>
        <!-- Logout Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="login.html">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-wrapper -->
    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
