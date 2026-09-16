<?php
include '../database/database_connection.php';

$name = $_POST['name'];
$email = $_POST['email'];
$contact =$_POST['contact'];
$description =$_POST['description'];
$date = date("Y-m-d H:i:s");
$status = "Pending";

$sql = "INSERT INTO inquiries_tbl
(full_name, email_address, date_received, contact_number, description, status)
VALUES
('$name', '$email', '$date','$contact', '$description', '$status')";

if(mysqli_query($conn, $sql)){
    echo "<script>  
            alert('Inquiry submitted successfully!');
            window.location='../index.php';
          </script>";
}else{
    echo "Failed to Submit inquiry " . mysqli_error($conn);
}
?>