<?php

include '../database/database_connection.php';

if (!isset($_GET['id'])) {
    header("Location: ../admin/admin_customer.php");
    exit();
}

$customer_id = $_GET['id'];

$query = "DELETE FROM customer_tbl 
          WHERE customer_id='$customer_id'";

if (mysqli_query($conn, $query)) {

    echo "<script>
            alert('Customer deleted successfully!');
            window.location.href='../admin/admin_customer.php';
          </script>";

} else {

    echo "Error deleting customer: " . mysqli_error($conn);
}

?>