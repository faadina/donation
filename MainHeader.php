<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="donor/bootstrap.min.css">
    <title><?php echo $title; ?></title>
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
            z-index: 999;
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

        .manager-info {
            display: flex;
            align-items: center;
            margin-right: 20px;
            position: relative; /* Add this line */
        }

        .manager-username {
            color: #e4b909;
            margin-left: 10px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
            z-index: 99;
            padding: 0;
            top: 100%; /* Position it right under the manager-info element */
            left: 0; /* Align it to the left of the manager-info element */
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

</head>

<body>
    <div class="header">
        <img src="images/madrasahLogo1.png" class="logo" alt="Logo">
        <div class="nav__content">
            <nav>
                <ul>
                    <li><a href="MainHome.php" class="<?php echo ($current_page == 'MainHome.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="MainAbout.php" class="<?php echo ($current_page == 'MainAbout.php') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="MainContact.php" class="<?php echo ($current_page == 'MainContact.php') ? 'active' : ''; ?>">Contact</a></li>
                    <li style="background-color:#1e1d1d; border:#fff; border-radius:8px; box-shadow: inset 0px 0px 3px rgba(0, 0, 0,5); border:none;"><a href="MainLogin.php" class="<?php echo ($current_page == 'MainLogin.php') ? 'active' : ''; ?>">Login</a></li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>
