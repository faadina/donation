<?php
include 'dbConnect.php'; 
$title = "Home Page";
include 'MainHeader.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Allocation";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        :root {
            --colour1: #1cf0b1;
            --colour2: #07d799;
            --speed: 8s;
        }

        body {
            font-family: 'Trebuchet MS', sans-serif;
            margin: 0;
            padding: 0;
        }

        .main {
            position: relative;
            background-image: url("images/madrasah3.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 60vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            overflow: hidden;
            padding-top: 60px;
            transition: background-image 1s ease; 
        }

        .main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .main_content {
            position: relative;
            z-index: 2;
            width: 80%;
        }

        .main h2 {
            font-size: 3rem;
            font-weight: bold;
            line-height: 1.2;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.9);
        }

        .main h2 span {
            font-size: 0.4em;
            opacity: 1;
            font-weight: 350;
            display: block;
            margin-top: 10px;
            font-style: italic;
        }

        .main .btn {
            margin-top: 10px;
            filter: drop-shadow(2px 2px 10px rgba(0, 0, 1, 2));
        }

        .main .btn a {
            text-decoration: none;
            display: inline-block;
            padding: 10px 30px;
            font-size: 1.1rem;
            font-weight: bold;
            color: #444C38;
            background-color: #D0F0C0;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .main .btn a:hover {
            background-color: #444C38;
            color: #D0F0C0;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .card-content {
            padding: 10px;
            /* Adjusted padding */
            flex: 1;
        }

        .card-content h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.2rem;
            /* Adjusted font size */
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-footer {
            padding: 10px;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .raised,
        .goal {
            margin: 0;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }


        .progress-bar-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin: 1px 0;
            filter: drop-shadow(0px 0px 2px #d0f0c0);
            border: 1px solid black;
        }

        .progress-bar {
            height: 15px;
            width: 0;
            animation: loadProgress 0s ease-in-out forwards, slide var(--speed) linear infinite;
            background-color: var(--colour2);
            background-image: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 10px,
                    var(--colour1) 10px,
                    var(--colour1) 40px);
        }



        @keyframes loadProgress {
            0% {
                width: 0;
            }

            100% {
                width: var(--progress);
            }
        }

        @keyframes slide {
            from {
                background-position-x: 0;
            }

            to {
                background-position-x: 113px;
            }
        }

        .donate-button,
        .closed-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
        }

        .donate-button:hover {
            background-color: #1cf0b1;
            color:black;
        }

        .closed-button {
            background-color: #dc3545;
        }

        .closed-button:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="main_content">
            <h2>
                <div style="transform:scaleX(0.8);">Madrasah Tarbiyyah Islamiyyah Darul Hijrah</div> Donation System
                <hr>
                <span>Donate to Educate: Support Education for Every Child in Need</span>
            </h2>
            <div class="btn">
                <a href="MainLogin.php">JOIN US NOW</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $progress = ($row["currentAmount"] / $row["targetAmount"]) * 100 . '%';
                echo '<div class="card">';
                echo '<img src="' . htmlspecialchars($row["allocationImage"]) . '" alt="' . htmlspecialchars($row["allocationName"]) . '">';
                echo '<div class="card-content">';
                echo '<h2 style="font-weight: 800;">' . htmlspecialchars($row["allocationName"]) . '</h2>';
                echo '</div>';
                echo '<div class="card-footer">';
                echo '<div class="raised">RM ' . number_format($row["currentAmount"], 2) . ' <span style="flex: 1;"></span> of RM ' . number_format($row["targetAmount"], 2) . '</div>';
                echo '<div class="progress-bar-container">';
                echo '<div class="progress-bar" style="--progress:' . $progress . ';"></div>';
                echo '</div>';

                // Check if allocation is closed or inactive
                if ($row["allocationStatus"] == 'Inactive' || $row["currentAmount"] >= $row["targetAmount"]) {
                    echo '<button class="closed-button" disabled>CLOSED</button>';
                    $sql_update = "UPDATE Allocation SET allocationStatus = 'Inactive' WHERE allocationID = '" . $row["allocationID"] . "'";
                } else {
                    echo '<a href="MainLogin.php?allocationID=' . htmlspecialchars($row["allocationID"]) . '" class="donate-button">Donate Now</a>';
                    $sql_update = "UPDATE Allocation SET allocationStatus = 'Active' WHERE allocationID = '" . $row["allocationID"] . "'";
                }

                echo '</div>'; // card-footer
                echo '</div>'; // card
            }
        } else {
            echo "<p>No allocations found</p>";
        }
        $conn->close();
        ?>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Array of image URLs to cycle through
            var images = [
                'images/madrasah1.jpg',
                'images/madrasah2.jpg',
                'images/madrasah3.jpg'
            ];
            
            var currentIndex = 0;
            var mainDiv = document.querySelector('.main');
            
            // Function to change background image
            function changeBackground() {
                mainDiv.style.backgroundImage = 'url("' + images[currentIndex] + '")';
                currentIndex = (currentIndex + 1) % images.length; // Cycle through images
            }
            
            // Call changeBackground every 8 seconds (adjust timing as needed)
            setInterval(changeBackground, 4000); // Change image every 8 seconds
        });
    </script>

</body>

</html>J