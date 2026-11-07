<?php
include '../database/database_connection.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$description = trim($_POST['description'] ?? '');
$date = date("Y-m-d H:i:s");
$status = "Pending";

// Validate email address
if (
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)$/i', $email)
) {
    echo "<script>
            alert('Please enter a valid email address, such as customer@gmail.com.');
            window.history.back();
          </script>";
    exit;
}

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