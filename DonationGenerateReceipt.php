<?php
// Include the database connection
include 'dbConnect.php';

// Check if donationID is set in the URL query string
if (isset($_GET['donationID'])) {
    // Sanitize input to prevent SQL injection
    $donationID = $_GET['donationID'];

    // Query to fetch data for the specified donationID from Donation, Allocation, and Donor tables
    $query = "SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, 
                     a.allocationID, a.allocationName, 
                     o.donorID, o.donorName
              FROM Donation d
              INNER JOIN Allocation a ON d.AllocationID = a.allocationID
              INNER JOIN Donor o ON d.donorID = o.donorID
              WHERE d.donationID = ?";
    
    // Prepare and bind parameter
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $donationID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if query was successful and at least one row is returned
    if ($result && $result->num_rows > 0) {
        // Fetch the row (there should be only one since donationID is unique)
        $row = $result->fetch_assoc();

        // Display receipt information
        echo "<!DOCTYPE html>
              <html lang='en'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Donation Receipt</title>
                  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
              </head>
              <body>
                  <div class='container'>
                      <h2 class='my-4'>Donation Receipt</h2>
                      <div class='card'>
                          <div class='card-body'>
                              <h5 class='card-title'>Donation Details</h5>
                              <p class='card-text'><strong>Donation ID:</strong> {$row['donationID']}</p>
                              <p class='card-text'><strong>Amount:</strong> RM {$row['donationAmount']}</p>
                              <p class='card-text'><strong>Date:</strong> {$row['donationDate']}</p>
                              <p class='card-text'><strong>Status:</strong> {$row['donationStatus']}</p>
                              <p class='card-text'><strong>Allocation ID:</strong> {$row['allocationID']}</p>
                              <p class='card-text'><strong>Allocation Name:</strong> {$row['allocationName']}</p>
                              <p class='card-text'><strong>Donor ID:</strong> {$row['donorID']}</p>
                              <p class='card-text'><strong>Donor Name:</strong> {$row['donorName']}</p>
                          </div>
                      </div>
                      <div class='mt-4'>
                          <button class='btn btn-primary' onclick='window.print()'>Print Receipt</button>
                          <a href='DonationView.php' class='btn btn-secondary'>Back to Donation Records</a>
                      </div>
                  </div>
              </body>
              </html>";
    } else {
        echo "Error: Donation ID not found or unable to fetch data.";
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Error: Donation ID parameter is missing.";
}
?>
