<?php
include '../database/database_connection.php';

if (isset($_POST['update_status'])) {

    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];

    $sql = "UPDATE ticket_management_tbl
            SET status='$status'
            WHERE ticket_id='$ticket_id'";

    if (mysqli_query($conn, $sql)) {

        echo "<script>
                alert('Ticket Status Updated Successfully!');
                window.location='../admin/admin_ticket_management.php?ticket_id=$ticket_id';
              </script>";

        exit();

    } else {

        echo "Error: " . mysqli_error($conn);
    }
}
?>