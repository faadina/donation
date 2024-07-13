<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$title = "About Us";
include 'MainHeader.php';
?>
<html>

<head>
    <style>
        /*--------------------------------------------HOW WE HELP----------------------------------------*/
        .product {
            float: left;
            background-image: url("images/poor.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding-bottom: 50px;
            height: 100vh;
            font-family: 'Trebuchet MS', sans-serif;
            color: white;
        }

        .product h2 {
            font-size: 45px;
            text-align: center;
            padding: 20px;
        }

        .product .product_container {
            display: flex;
            margin-left: 5%;
            margin-right: 5%;
        }

        .product .product_container .item {
            width: 33%;
            margin: 10px;
            height: 10%;
            background-color: black;
            opacity: 80%;
            border-radius: 50%;
        }

        .product .product_container .item:hover {
            box-shadow: 0px 3px 65px 0px rgb(255, 189, 165);
        }

        .product .product_container .item .item_img {
            width: 60%;
            height: 20%;
            overflow: hidden;
            margin-left: 23%;
        }

        .product .product_container .item .item_img img {
            width: 100%;
            height: 10%;
        }

        .product .product_container .item .item_content {
            text-align: center;
        }

        .product .product_container .item .item_content h3 {
            font-size: 25px;
            line-height: 2px;
            color: rgb(79, 255, 88);
            font-weight: bolder;
        }

        .product .product_container .item .item_content p {
            font-size: 20px;
            line-height: 25px;
            width: 80%;
            text-align: center;
            padding-left: 20%;
        }

        /*------------------------------------------------MAIN_ABOUT----------------------------------------*/
        .main_about {
            font-family: 'Trebuchet MS', sans-serif;
            background-image: url("images/madrasah1.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 80vh;
            margin-top: -1.5%;
        }

        .main_about .main_content_about h2 {
            color: white;
            font-size: 390%;
            font-family: 'Trebuchet MS', sans-serif;
            font-weight: bolder;
            text-align: center;
            padding: 20%;
        }

        .product_about {
            float: left;
            background-image: url("images/about_back.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding-bottom: 50px;
            height: 100vh;
            font-family: 'Trebuchet MS', sans-serif;
            color: white;
        }

        /*--------------------------------------------DONATE----------------------------------------*/
        .donate {
            font-family: 'Trebuchet MS', sans-serif;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            margin-top: -1.5%;
            color: white;
            text-align: center;
            padding-top: 10%;
        }

        .donate .donate_container {
            background-color: black;
            opacity: 80%;
            text-align: center;
            margin: 0% 20% 0% 20%;
            padding-top: 10%;
            padding-bottom: 10%;
        }

        .donate .donate_container h1 {
            color: rgb(255, 188, 79);
            line-height: 100px;
        }

        .donate .donate_container p {
            font-size: large;
            width: 80%;
            text-align: center;
            padding-left: 20%;
        }

        .donate .donate_container .second {
            padding-bottom: 10%;
        }

        .donate .side_btn a {
            text-decoration: none;
            font-size: 120%;
            padding: 10px 20px;
            font-weight: bold;
            color: white;
            font-family: 'Trebuchet MS', sans-serif;
            border: 2px double whitesmoke;
            transition: 0.5s;
        }

        .donate .side_btn a:hover {
            background-color: white;
            color: black;
            font-weight: bolder;
            border: none;
            border: 4px solid green;
            transition: 0.5s;
        }
    </style>

</head>

</html>

<body>
    <div class="main_about">
        <div class="main_content_about">
            <h2>About Madrasah</h2>
            <p>Kompleks Anak Yatim Tahfiz Darul Hijrah is an independent educational institution established by Al Fadhil Ustaz Wan Muhammad Mizan bin Wan Abdul Latif in 2012. It was initiated to elevate an educational system based on the Quran and Sunnah, aiming to realize the aspiration of producing more Islamic scholars, particularly among orphans and the poor. This institution focuses on preventing dropouts and enabling these children to master the skills of memorizing, understanding the knowledge of the Quran, and comprehensively grasping Hadith.</p>
        </div>
    </div>
    <div class="product product_about">
        <h2>HOW WE HELP</h2>
        <div class="product_container">

            <div class="item">
                <div class="item_img">
                    <img src="images/pro1.svg">
                </div>
                <div class="item_content">
                    <h3>Pure Food & Water</h3><br>
                    <p>We supply needy childen with basic necessities like pure food and water</p>
                    <br><br><br>
                </div>
            </div>

            <div class="item">
                <div class="item_img">
                    <img src="images/pro2.svg">
                </div>
                <div class="item_content">
                    <h3>Health and Medicine</h3><br>
                    <p>Health being the foremost priority, we aim at giving children every medical support</p>
                    <br><br>
                </div>
            </div>

            <div class="item">
                <div class="item_img">
                    <img src="images/pro3.svg">
                </div>
                <div class="item_content">
                    <h3>Education</h3><br>
                    <p>We provide education facilities to children all over the world</p>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
    <hr>