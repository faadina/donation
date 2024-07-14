<?php
// Include the database connection
include 'dbConnect.php';

// Check if donationID is set in the URL query string
if (isset($_GET['donationID'])) {
    // Sanitize input to prevent SQL injection
    $donationID = $_GET['donationID'];
    
    $query = "SELECT d.donationID, d.donationAmount, d.donationDate, d.donationStatus, 
    a.allocationID, a.allocationName, 
    o.donorID, o.donorName, o.donorPhoneNo
FROM Donation d
INNER JOIN Allocation a ON d.AllocationID = a.allocationID
INNER JOIN Donor o ON d.donorID = o.donorID
WHERE d.donationID = ?";

    
    // Prepare and bind parameter
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $donationID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if query was successful and at least one row is returned
    if ($result && $result->num_rows > 0) {
        // Fetch the row (there should be only one since donationID is unique)
        $row = $result->fetch_assoc();

        // Close statement
        $stmt->close();

        // HTML content for the receipt
        $htmlContent = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Donation Receipt</title>
            <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
            <div class='page-content container'>
                <div class='page-header text-blue-d2'>
                    <h1 class='page-title text-secondary-d1'>
                        Donation Receipt
                        <small class='page-info'>
                            <i class='fa fa-angle-double-right text-80'></i>
                            ID: #{$row['donationID']}
                        </small>
                    </h1>
  
                    <div class='page-tools'>
                        <div class='action-buttons'>
                            <button class='btn bg-white btn-light mx-1px text-95' onclick='window.print()'>
                                <i class='mr-1 fa fa-print text-primary-m1 text-120 w-2'></i>
                                Print
                            </button>
                        </div>
                    </div>
                </div>
  
                <div class='container px-0'>
                    <div class='row mt-4'>
                        <div class='col-12 col-lg-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <div class='text-center text-150'>
                                        <img src='images/madrasahLogo1.png' alt='Madrasah Logo' class='img-fluid' style='max-width: 80px;'>
                                        <span class='text-default-d3'>MADRASAH TARBIYYAH ISLAMIYYAH DARUL HIJRAH</span>
                                    </div>
                                </div>
                            </div>
                            <!-- .row -->
  
                            <hr class='row brc-default-l1 mx-n1 mb-4' />
  
                            <div class='row'>
                                <div class='col-sm-6'>
                                    <div>
                                        <span class='text-sm text-grey-m2 align-middle'>To:</span>
                                        <span class='text-600 text-110 text-blue align-middle'>{$row['donorName']}</span>
                                    </div>
                                    <div class='text-grey-m2'>
                                        <div class='my-1'>
                                            <!-- Address details if available -->
                                        </div>
                                        <div class='my-1'>
                                            <i class='fa fa-phone fa-flip-horizontal text-secondary'></i>
                                            <b class='text-600'>{$row['donorPhoneNo']}</b>
                                        </div>
                                    </div>
                                </div>
  
                                <!-- /.col -->
  
                                <div class='text-95 col-sm-6 align-self-start d-sm-flex justify-content-end'>
                                    <hr class='d-sm-none' />
                                    <div class='text-grey-m2'>
                                        <div class='mt-1 mb-2 text-secondary-m1 text-600 text-125'>
                                            Donation Details
                                        </div>
  
                                        <div class='my-2'><i class='fa fa-circle text-blue-m2 text-xs mr-1'></i> <span class='text-600 text-90'>Donation ID:</span> {$row['donationID']}</div>
  
                                        <div class='my-2'><i class='fa fa-circle text-blue-m2 text-xs mr-1'></i> <span class='text-600 text-90'>Amount:</span> RM {$row['donationAmount']}</div>
  
                                        <div class='my-2'><i class='fa fa-circle text-blue-m2 text-xs mr-1'></i> <span class='text-600 text-90'>Date:</span> {$row['donationDate']}</div>
  
                                        <div class='my-2'><i class='fa fa-circle text-blue-m2 text-xs mr-1'></i> <span class='text-600 text-90'>Status:</span> {$row['donationStatus']}</div>
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
  
                            <div class='mt-4'>
                                <div class='row text-600 text-white bgc-default-tp1 py-25'>
                                    <div class='d-none d-sm-block col-1'>#</div>
                                    <div class='col-9 col-sm-5'>Description</div>
                                    <div class='d-none d-sm-block col-4 col-sm-2'>Qty</div>
                                    <div class='d-none d-sm-block col-sm-2'>Unit Price</div>
                                    <div class='col-2'>Amount</div>
                                </div>
  
                                <div class='text-95 text-secondary-d3'>
                                    <div class='row mb-2 mb-sm-0 py-25'>
                                        <div class='d-none d-sm-block col-1'>1</div>
                                        <div class='col-9 col-sm-5'>{$row['allocationName']}</div>
                                        <div class='d-none d-sm-block col-2'>1</div>
                                        <div class='d-none d-sm-block col-2 text-95'>RM {$row['donationAmount']}</div>
                                        <div class='col-2 text-secondary-d2'>RM {$row['donationAmount']}</div>
                                    </div>
                                </div>
  
                                <div class='row border-b-2 brc-default-l2'></div>
  
                                <div class='row mt-3'>
                                    <div class='col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0'>
                                        <!-- Additional notes or payment information -->
                                    </div>
  
                                    <div class='col-12 col-sm-5 text-grey text-90 order-first order-sm-last'>
                                        <div class='row my-2'>
                                            <div class='col-7 text-right'>
                                                Total Amount
                                            </div>
                                            <div class='col-5'>
                                                <span class='text-150 text-success-d3 opacity-2'>RM {$row['donationAmount']}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />
  
                                <div>
                                    <span class='text-secondary-d1 text-105'>Thank you for your donation.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        // Output the HTML content
        echo $htmlContent;
  
    } else {
        echo "Error: Donation ID not found or unable to fetch data.";
    }

    // Close database connection
    $conn->close();
} else {
    echo "Error: Donation ID parameter is missing.";
}
?>
