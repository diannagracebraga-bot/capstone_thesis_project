<?php
include '../database/database_connection.php';

if (!isset($_GET['record_id']) || !isset($_GET['role'])) {
    header("Location: ../admin/admin_user_management.php");
    exit();
}

$record_id = $_GET['record_id'];
$role = $_GET['role'];

if ($role == 'Admin' || $role == 'Super Admin') {

    $query = "DELETE FROM admin_superadmin_accounts_tbl 
              WHERE account_id='$record_id'";

    if (mysqli_query($conn, $query)) {

        echo "<script>
            alert('Account deleted successfully');
            window.location.href='../admin/admin_user_management.php';
        </script>";

    } else {

        echo "Error deleting account: " . mysqli_error($conn);

    }

} else {

    header("Location: ../admin/admin_user_management.php");
    exit();

}
?>