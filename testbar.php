<?php
// Set the content type
header('Content-Type: image/png');

// Create the image
$width = 500;
$height = 300;
$image = imagecreatetruecolor($width, $height);

// Define colors
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

// Fill the background with white
imagefill($image, 0, 0, $white);

// Data for the graph
$categories = ['A', 'B', 'C', 'D'];
$values = [50, 100, 75, 125];

// Calculate the width of each bar
$barWidth = 40;
$gap = 20;
$leftMargin = 50;
$bottomMargin = 30;

// Loop through the data and create the bars
for ($i = 0; $i < count($values); $i++) {
    // Calculate the position of the bar
    $x1 = $leftMargin + ($i * ($barWidth + $gap));
    $y1 = $height - $bottomMargin - $values[$i];
    $x2 = $x1 + $barWidth;
    $y2 = $height - $bottomMargin;
    
    // Draw the bar
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $blue);
    
    // Add the value label on top of the bar
    $labelX = $x1 + ($barWidth / 2) - 5;
    $labelY = $y1 - 15;
    imagestring($image, 3, $labelX, $labelY, $values[$i], $black);
    
    // Add the category label below the bar
    $categoryX = $x1 + ($barWidth / 2) - 5;
    $categoryY = $height - $bottomMargin + 5;
    imagestring($image, 3, $categoryX, $categoryY, $categories[$i], $black);
}

// Output the image
imagepng($image);

// Free up memory
imagedestroy($image);
?>
