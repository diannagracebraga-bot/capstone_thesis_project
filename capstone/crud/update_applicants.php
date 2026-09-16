<?php
include '../database/database_connection.php';

if (isset($_POST['update_status'])) {

    $applicant_id = $_POST['applicant_id'];
    $status = $_POST['status'];

    $sql = "UPDATE internet_application_tbl
            SET status='$status'
            WHERE applicant_id='$applicant_id'";

    if (mysqli_query($conn, $sql)) {

        echo "<script>
                alert('Status Updated Successfully!');
                window.location='../admin/admin_applicants.php';
              </script>";
        exit();

    } else {

        echo "Error: " . mysqli_error($conn);
    }
}
?>