<?php
$current_page = basename($_SERVER['PHP_SELF']);
$donorID = isset($_SESSION["id"]) ? $_SESSION["id"] : 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Donor</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        body {
            margin: 0; 
            font-family: 'Inter', sans-serif;      
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            font-weight: 600;
            padding: 10px 20px;
            position: -webkit-sticky; 
            position: sticky;
            top: 0;
            z-index: 2;
            width: AUTO;
            margin: 0 auto; 
        }

        .logo {
            height: auto;
            width: 5rem;
            margin-right: 10px;
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
            color:lightgrey;
            font-weight: bold;
        }

        ul li a:hover,
        ul li a:focus {
            color: #b9e3c7;
            text-decoration: none;
        }

        ul li a.active {
            font-weight: 980;
            color:white;
        }

        .donor-info {
            display: flex;
            align-items: center;
            margin-right: 20px;
            position: relative; /* Add this line */
        }

        .donor-username {
            color: #e4b909;
            margin-left: 10px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            z-index: 99;
            padding: 0;
            top: 100%; 
            left: 0; 
            width: 150px;
        }

        .dropdown-menu a {
            color: white;
            padding: 8px 10px;
            text-decoration: none;
            display: block;
            white-space: nowrap;
        }

        .dropdown-menu a:hover {
            background: #37383a;
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
        <img src="images/madrasahLogo1.png" class="logo" alt="Logo">
        <div class="nav__content">
            <nav>
                <ul>
                    <li><a href="DonorDonateAllocation.php" class="<?php echo ($current_page == 'DonorDonateAllocation.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="DonorDonateHistory.php" class="<?php echo ($current_page == 'DonorDonateHistory.php') ? 'active' : ''; ?>">History</a></li>
                
                    <li class="dropdown donor-info">
                        <a style="background-color:#1e1d1d; border:#fff; border-radius:8px; box-shadow: inset 0px 0px 3px rgba(0, 0, 0,5); border:none;" href="#" class="dropdown-toggle <?php echo (strpos($current_page, 'Profile') === 0) ? 'active' : ''; ?>">
                            <img src="images/userIcon1.png" alt="User Icon" height="18" width="18"> Donor
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownProfile">
                            <a class="dropdown-item" href="DonorProfile.php">• User: <?php echo $donorID; ?></a>
                            <a class="dropdown-item" href="Logout.php">• Log Out</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>