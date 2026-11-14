
<?php
include '../database/database_connection.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$description = trim($_POST['description'] ?? '');
$date = date("Y-m-d H:i:s");
$status = "Pending";

// Require the applicant to acknowledge the privacy notice before submitting.
if (($_POST['privacy_notice_acknowledged'] ?? '') !== '1') {
    echo "<script>
            alert('Please read and acknowledge the Privacy Notice before submitting.');
            window.history.back();
          </script>";
    exit;
}


    // Validate Gmail address
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strtolower(substr($email, -10)) !== '@gmail.com'
    ) {
        echo "<script>
                alert('Please enter a valid Gmail address.');
                window.history.back();
              </script>";
        exit;
    }

    // Validate if Gmail domain exists
    if (!checkdnsrr('gmail.com', 'MX')) {
        echo "<script>
                alert('The Gmail domain does not exist. Please try again later.');
                window.history.back();
              </script>";
        exit;
    }

    $email = mysqli_real_escape_string($conn, $email);


// Validate contact number
if (!preg_match('/^[0-9]{11}$/', $contact)) {
    echo "<script>
            alert('Contact number must contain exactly 11 digits.');
            window.history.back();
          </script>";
    exit;
}

$sql = "INSERT INTO inquiries_tbl
(full_name, email_address, date_received, contact_number, description, status)
VALUES
('$name', '$email', '$date', '$contact', '$description', '$status')";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Inquiry submitted successfully!');
            window.location='../index.php';
          </script>";
} else {
    echo "Failed to Submit inquiry " . mysqli_error($conn);
}
?>
