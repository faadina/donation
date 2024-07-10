<?php
$plain_password = "pass123"; 
$hashed_password = md5($plain_password);
echo "The MD5 hash of the password is: " . $hashed_password;
?>

