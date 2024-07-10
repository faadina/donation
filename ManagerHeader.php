<?php
// Start or resume session
session_start();

// Check if managerID is set in session
$managerID = isset($_SESSION["id"]) ? $_SESSION["id"] : 'Unknown'; // Replace 'Unknown' with a default message or handle empty case accordingly

// Check if $current_page is set, assuming it's defined somewhere in your script
$current_page = isset($current_page) ? $current_page : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(120deg, #d4fc79 0%, #96e6a1 100%);
            font-weight: 600;
            padding: 10px 20px;
            position: -webkit-sticky; /* Safari */
            position: sticky;
            top: 0;
            z-index:2;
        }

        .logo {
            height: auto;
            width: 8rem;
            margin-right: 10px;
            filter: drop-shadow(2px 2px 3px rgb(252, 252, 252));
        }

        nav {
            display: flex;
            align-items: center;
        }

        ul {
            display: flex;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 10px;
        }

        ul li a {
            padding: 5px 10px;
            border: 1px solid transparent;
            text-decoration: none;
            color: #fdfdfb;
            font-weight: bold;
            border-bottom: 2px solid transparent;
        }

        ul li a:hover,
        ul li a:focus {
            color: #e4b909;
        }

        ul li a.active {
            color: #e4b909;
            border-bottom: 2px solid #e4b909;
        }

        .manager-info {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .manager-username {
            color: #e4b909;
            margin-left: 10px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            z-index: 99;
            padding: 0;
            margin-top: 10%;
            margin-left: 2%;
            width: 13%;
        }

        .dropdown-menu a {
            color: #000;
            padding: 5px 10px;
            text-decoration: none;
            display: block;
            white-space: nowrap;
        }

        .dropdown-menu a:hover {
            background: #104854;
            color: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function(e) {
                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        });
    </script>
</head>

<body>
    <div class="header">
        <img src="images/madrasahLogo.png" class="logo" alt="Logo">
        <div class="nav__content">
            <nav>
                <ul>
                    <li><a href="ManagerDashboard.php" class="<?php echo ($current_page == 'ManagerDashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="StaffDetails.php" class="<?php echo ($current_page == 'StaffDetails.php') ? 'active' : ''; ?>">Staff</a></li>
                    <li><a href="ManagerReport.php" class="<?php echo ($current_page == 'ManagerReport.php') ? 'active' : ''; ?>">Report</a></li>
                    
                    <li class="dropdown manager-info">
                        <a style="background-color:white; border-radius:4px; color:#104854;" href="#" class="dropdown-toggle <?php echo (strpos($current_page, 'Report') === 0) ? 'active' : ''; ?>">
                            <img src="images/userIcon1.png" alt="User Icon" height="20" width="20"> Manager: <?php echo $managerID; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownProfile">
                            <a class="dropdown-item" href="ManagerProfile.php">🗝 Profile</a>
                            <a class="dropdown-item" href="Logout.php">🗝 Log Out</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>
