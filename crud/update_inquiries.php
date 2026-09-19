<?php
include '../database/database_connection.php';

if (isset($_POST['update_status'])) {

    $inquiries_id = $_POST['inquiries_id'];
    $status = $_POST['status'];

    $sql = "UPDATE inquiries_tbl
            SET status='$status'
            WHERE inquiries_id='$inquiries_id'";

    if (mysqli_query($conn, $sql)) {

        echo "<script>
                alert('Status Updated Successfully!');
                window.location='../admin/admin_inquiries.php?inquiries_id=$inquiries_id';
              </script>";

        exit();

    } else {

        echo "Error: " . mysqli_error($conn);
    }
}
?>