<?php
$title = "Donation Page";
include 'DonorHeader.php';
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $amount = htmlspecialchars($_POST['amount']);
    $message = htmlspecialchars($_POST['message']);

    // Here you would typically process the donation, e.g., save it to a database, process payment, etc.

    header("Location: thank_you.php?name=" . urlencode($name));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <nav class="nav">
        <a href="index.php">Home</a>
        <a href="donate.php">Donate</a>
    </nav>
    <div class="container">
        <div class="payment-details">
            <h2>Payment Details</h2>
            <form action="process_donation.php" method="POST">
                <label for="amount">Amount</label>
                <input type="text" id="amount" name="amount" placeholder="Enter Amount" required>
                
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" required>
                
                <input type="submit" value="Pay RM 0.00">
            </form>
        </div>
</body>
</html>