<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: MainLogin.php");
    exit;
}

require_once("dbConnect.php");


$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="donor/style.css">
    <title>Manager Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f9fcff;
            background-image: linear-gradient(147deg, #f9fcff 0%, #dee4ea 74%);
            font-family: "Inter", sans-serif;
        }

        .detailIndex {
            margin: 2% auto;
            max-width: 100%;
            padding: 10px;
            position: relative;
            z-index: 1;
        }

        .detailIndex h1 {
            font-size: 40px;
            color: #1a1649;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex h1, .detailIndex h2{
            margin: 2px;
        }

        .detailIndex h2 {
            font-size: 30px;
            color: #1a1649;
            text-shadow: 2PX 3px 1px rgba(130, 9, 9, 0.1);
            text-align: center;
            font-weight: 700;
        }

        .detailIndex p {
            color: #1a5172;
            line-height: 22px;
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            border: 1px transparent #ccc;
            color: white;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .summary-box {
            display: flex;
            align-items: center;
            margin: 15px;
            padding: 20px;
            border: 1px transparent #ccc;
            background-color: #4d4855;
            background-image: linear-gradient(147deg, #4d4855 0%, #000000 74%);
            text-align: left;
            width: 30%;
        }

        .summary-box img {
            margin-right: 15px;
            height: 80px;
        }

        .summary-box div {
            text-align: center;
        }

        .summary-box p {
            font-size: 35px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .summary-box h3 {
            font-size: 15px;
        }

        .summary-box:hover {
            transform: translateY(-10px);
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
            filter: drop-shadow(1px 1px 2px rgba(244, 242, 239, 0.8));
        }

        .btn {
            text-decoration: none;
            color: #1f244a;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php
    include('staffHeader.php');
    ?>

    <div class="detailIndex">
        <h1>MADRASAH TARBIYYAH ISLAMIYYAH <br>DARUL HIJRAH</h1>
        <h2>DONATION SYSTEM</h2>
        <p>Manager Dashboard</p>
    </div>

    <div class="summary">
        <div class="summary-box" style="background-color:#2a3f45">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>ALLOCATION</h3>
                <a href="reportMonthlyDonation.php" class="btn">View Report</a>
            </div>
        </div>
        <div class="summary-box" style="background-color:#2a3f45">
            <img src="images/reportIcon.png" alt="Report Icon">
            <div>
                <h3>DONOR</h3>
                <a href="reportDonationAllocation.php" class="btn">View</a>
            </div>
        </div>
    </div>
</body>

</html>