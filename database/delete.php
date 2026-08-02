<?php
include '../database/database_connection.php';
if (!isset($_GET['id'])) {
    header("Location: ../admin/admin_payment.php");
    exit();
}
$id = $_GET['id'];
$query = "DELETE FROM payment_tbl WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    echo "<script>
        alert('Record deleted successfully');
        window.location.href='../admin/admin_payment.php';
    </script>";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
if (!isset($_GET['id'])) {
    header("Location: ../admin/admin_user_management.php");
    exit();
}
$id = $_GET['id'];
$query = "DELETE FROM login_tbl WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    echo "<script>
        alert('Record deleted successfully');
        window.location.href='../admin/admin_user_management.php';
    </script>";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>