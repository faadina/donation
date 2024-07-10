<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #104854;
            color: #fff;
            font-weight: 600;
            padding: 10px 20px;
        }

        .logo {
            height: auto;
            width: 4.5rem;
            margin-right: 10px; /* Adjusted margin */
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

        .guest-info {
            display: flex;
            align-items: center;
            margin-right: 20px; /* Adjusted margin */
        }

        .guest-username {
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
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownToggle.addEventListener('click', function (e) {
                e.preventDefault();
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', function (e) {
                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        });
    </script>
</head>

<body>
    <div class="header">
        <img src="image/logoVacayVista.png" class="logo" alt="Logo">
        <div class="nav__content">
            <nav>
                <ul>
                <li><a href="ManagerDashboard.php" class="<?php echo ($current_page == 'ManagerDashboard.php')? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="ManagerReport.php" class="<?php echo ($current_page == 'ManagerReport.php')? 'active' : ''; ?>">Report</a></li>
                    <li class="dropdown guest-info">
                        <a href="#" class="dropdown-toggle <?php echo (strpos($current_page, 'Report') === 0) ? 'active' : ''; ?>">
                            <img src="image/userIcon1.png" alt="User Icon" height="20" width="20"> Manager: <?php echo htmlspecialchars($username); ?>
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
