<?php
include '../database/database_connection.php';

if (!isset($_GET['record_id']) || !isset($_GET['role'])) {
    header("Location: ../admin/admin_user_management.php");
    exit();
}

$record_id = $_GET['record_id'];
$role = $_GET['role'];

if ($role == 'Admin' || $role == 'Super Admin') {

    // Get current status
    $query = "SELECT account_status
              FROM admin_superadmin_accounts_tbl
              WHERE account_id='$record_id'
              AND role='$role'";

    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Account not found.");
    }

    $account = mysqli_fetch_assoc($result);

    $current_status = $account['account_status'];

    // Change status
    if ($current_status == 'Active') {
        $new_status = 'Deactivated';
    } else {
        $new_status = 'Active';
    }

    $update = "UPDATE admin_superadmin_accounts_tbl
               SET account_status='$new_status'
               WHERE account_id='$record_id'
               AND role='$role'";

    if (mysqli_query($conn, $update)) {

        echo "<script>
            alert('Account status changed to $new_status.');
            window.location.href='../admin/admin_user_management.php';
        </script>";

    } else {

        echo "Error updating account status: " . mysqli_error($conn);

    }

} else {

    header("Location: ../admin/admin_user_management.php");
    exit();

}
?>