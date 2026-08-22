<?php

include '../database/database_connection.php';

if (isset($_POST['update'])) {

    $business_name = $_POST['business_name'];
    $business_email = $_POST['business_email'];
    $business_contact = $_POST['business_contact'];
    $business_social_media = $_POST['business_social_media'];
    $business_address = $_POST['business_address'];
    $business_description = $_POST['business_description'];

    $update = "UPDATE content_management_about_tbl SET
                business_name='$business_name',
                business_email='$business_email',
                business_contact='$business_contact',
                business_social_media='$business_social_media',
                business_address='$business_address',
                business_description='$business_description'
               WHERE about_id = 1";

    if (mysqli_query($conn, $update)) {

        echo "<script>
                alert('Content updated successfully!');
                window.location='../admin/admin_content_management.php';
              </script>";

        exit();

    } else {

        echo "<script>
                alert('Update failed: " . mysqli_error($conn) . "');
                window.location='../admin/admin_content_management.php';
              </script>";

        exit();
    }
}
?>