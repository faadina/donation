<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$title = "Contact";
include 'MainHeader.php';
?>
<style>
    /*--------------------------------------------CONTACT---------------------------------------*/
    body {
            background: radial-gradient(circle at 24.1% 68.8%, rgb(50, 50, 50) 0%, rgb(0, 0, 0) 99.4%);
    }
    .main-content {
        margin-top: 5%;
    }

    .contact-container {
        background-color: #fff;
        width: 70%;
        margin: auto;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) inset;
        border-radius: 15px;
        padding: 10px 20px;
    }

    .title {
        color: #4f8236;
        margin-bottom: 30px;
        font-size: 2.4em;
        font-weight: 800;
        text-align: center;
    }

    .contact-section {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
    }

    .contact-info {
        flex: 1;
        margin-right: 20px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        transition: background-color 0.3s ease-in-out;
        padding: 10px;
        border-radius: 10px;
    }

    .contact-item:hover {
        background-color: #f0f0f0;
    }

    .contact-icon {
        width: 50px;
        height: 50px;
        margin-right: 20px;
        border-radius: 50%;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .contact-icon:hover {
        transform: scale(1.1);
    }

    .contact-item p {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .contact-item a {
        color: #333;
        text-decoration: none;
        transition: color 0.2s ease-in-out;
    }

    .contact-item a:hover {
        color: #589558;
    }

    .contact-image {
        flex: 1;
        text-align: center;
    }

    .contact-image iframe {
        width: 100%;
        height: 300px;
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) inset;
        border: none;
    }

    @media (max-width: 768px) {
        .contact-section {
            flex-direction: column;
            align-items: center;
        }

        .contact-info {
            margin-right: 0;
            margin-bottom: 20px;
        }

        .contact-image {
            text-align: center;
        }
    }
</style>
<body>
    <div class="main-content d-flex justify-content-center align-items-center">
        <div class="contact-container">
            <h1 class="title text-center">Contact Us</h1>
            <div class="contact-section">
                <div class="contact-info">
                    <div class="contact-item">
                        <img src="images/phone.png" alt="WhatsApp" class="contact-icon">
                        <p><a href="tel:09-6176525" target="_blank">09-617 6525</a></p>
                    </div>
                    <div class="contact-item">
                        <img src="images/facebook.png" alt="Facebook" class="contact-icon">
                        <p><a href="https://www.facebook.com/MTIDH" target="_blank">Madrasah Tarbiyyah Islamiyyah Darul Hijrah</a></p>
                    </div>
                    <div class="contact-item">
                        <img src="images/email.png" alt="Email" class="contact-icon">
                        <p><a href="mailto:kompleksdarulhijrah@gmail.com" target="_blank">kompleksdarulhijrah@gmail.com</a></p>
                    </div>
                </div>
                <div class="contact-image">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.966431908857!2d103.17000597498311!3d5.267909894710188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31b7bfe221dee31f%3A0x1cb8cc1a27750f5b!2sMadrasah%20Tarbiyyah%20Islamiyyah%20Darul%20Hijrah%20-%20Kompleks%20Tahfiz%20%26%20Anak%20Yatim!5e0!3m2!1sen!2smy!4v1720875265078!5m2!1sen!2smy" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
