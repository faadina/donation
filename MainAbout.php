<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$title = "About Us";
include 'MainHeader.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
        }

        .detailIndex {
            color: white;
            text-align: center;
            border-radius: 10px;
            margin: 2% auto;
            width: 90%;
            height: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .detailIndex img {
            max-width: 100%;
            height: 100%;
            display: block;
            margin: 0 auto;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            filter: brightness(1.2) contrast(1.2);
        }

        .background {
            color: white;
            text-align: center;
            margin: 2% auto;
            width: 70%;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4) inset;
            position: relative;
            overflow: hidden;
            padding: 10px 10px;
        }

        .background h2 {
            font-weight: 800;
            color: rgb(240, 191, 44);
            filter: drop-shadow(0px 0px 8px black);
        }

        .background p {
            font-size: 18px;
            line-height: 1.6;
            text-align: justify;
        }

        .product .product_container {
            display: flex;
            justify-content: center;
            margin: 20px 5%;
        }

        .product .product_container .item {
            width: 25%;
            margin: 10px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            transition: box-shadow 0.3s ease;
            text-align: center;
        }

        .product .product_container .item:hover {
            box-shadow: 0px 3px 15px 0px rgb(243, 170, 11);
        }

        .product .product_container .item .item_img img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
            filter: brightness(0.4) hue-rotate(130deg);
        }

        .product .product_container .item .item_content h3 {
            font-size: 24px;
            color: rgb(243, 170, 11);
            font-weight: 900;
            margin-bottom: 10px;
        }

        .product .product_container .item .item_content p {
            font-size: 16px;
            line-height: 1.5;
        }

        .donation-section {
            color: white;
            text-align: center;
            margin: 2% auto;
            width: 70%;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4) inset;
            position: relative;
            overflow: hidden;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
        }

        .donation-section h2 {
            font-weight: 800;
            color: rgb(240, 191, 44);
            filter: drop-shadow(0px 0px 8px black);
        }

        .donation-section ul {
            list-style: none;
            padding: 0;
        }

        .donation-section ul li {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .donation-section ul li img {
            width: 50px;
            height: auto;
            margin-right: 15px;
        }

        .donation-section ul li p {
            font-size: 18px;
            line-height: 1.6;
            text-align: justify;
            margin: 0;
        }

        .update-section {
            padding: 50px 0;
            text-align: center;
        }

        .update-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: white;
            font-weight: 670;
        }

        .update-images {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .update-images img {
            max-width: 100%;
            max-height: 200px;
            /* Adjust height as needed */
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .update-images img:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <div class="detailIndex">
        <img src="images/aboutHeader.png" alt="Manager Header Image">
    </div>

    <div class="background">
        <h2>Background</h2>
        <p>Kompleks Anak Yatim Tahfiz Darul Hijrah is an independent educational institution founded by Al Fadhil Ustaz Wan Muhammad Mizan bin Wan Abdul Latif in 2012. It aims to enhance education based on the Quran and Sunnah, aspiring to produce more Islamic scholars, especially among orphans and the underprivileged. The institution focuses on supporting children in memorizing, understanding the Quran, and comprehensively grasping Hadith.</p>
    </div>

    <div class="product product_about">
        <h2 style="text-align: center; color: rgb(243, 170, 11); font-weight: 900; font-size: 40px;">Every Contribution Matters</h2>
        <h3 style="text-align: center; color: white; font-weight: 500; font-size: 20px; ">
            Donations help us provide essential services and support to the children at Kompleks Anak Yatim Tahfiz Darul Hijrah.<br> By contributing to our cause, you help us in:</h3>

        <div class="product_container">
            <div class="item">
                <div class="item_img">
                    <img src="images/house.png" alt="Shelter and Housing">
                </div>
                <div class="item_content">
                    <h3>Shelter and Housing</h3>
                    <p>Ensure comfortable living conditions with adequate facilities.</p>
                </div>
            </div>

            <div class="item">
                <div class="item_img">
                    <img src="images/iftar.png" alt="Nutritious Meals">
                </div>
                <div class="item_content">
                    <h3>Nutritious Meals</h3>
                    <p>Ensure children receive nutritious meals daily, supporting their physical growth and development.</p>
                </div>
            </div>

            <div class="item">
                <div class="item_img">
                    <img src="images/education.png" alt="Education">
                </div>
                <div class="item_content">
                    <h3>Education</h3>
                    <p>Providing educational materials and resources to enhance learning opportunities based on Islamic teachings.</p>
                </div>
            </div>

            <div class="item">
                <div class="item_img">
                    <img src="images/participation.png" alt="Education">
                </div>
                <div class="item_content">
                    <h3>Programs</h3>
                    <p>Supporting various programs and activities that promote physical, emotional, and spiritual development.</p>
                </div>
            </div>
        </div>

    </div>

    <section class="update-section" style="box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4) inset;">
        <div class="container">
            <h2>See How Your Donations Are Making a Difference</h2>
            <div class="video-container">
                <iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fwatch%2F%3Fv%3D1269043860302506&show_text=0&width=560" width="530" height="300" style="border:none;overflow:hidden; border-radius:8px;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            </div>
            <div class="update-images">
                <img src="images/madrasah4.jpg" alt="Update Image 1">
                <img src="images/madrasah5.jpg" alt="Update Image 2">
                <img src="images/madrasah6.jpg" alt="Update Image 3">
                <img src="images/madrasah7.jpg" alt="Update Image 4">
            </div>
        </div><br>
    </section>
</body>

</html>