<?php
include '../database/database_connection.php';

if (!isset($_GET['record_id']) || !isset($_GET['role'])) {
    header("Location: ../../admin/admin_user_management.php");
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

} elseif ($role == 'Customer') {

    // Get the user_id first
    $get_user = "SELECT user_id
                 FROM customer_tbl
                 WHERE customer_id='$record_id'";

    $result = mysqli_query($conn, $get_user);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "Customer not found.";
        exit();
    }

    $customer = mysqli_fetch_assoc($result);
    $user_id = $customer['user_id'];

    // Delete from user_accounts_tbl first
    $delete_user = "DELETE FROM user_accounts_tbl
                    WHERE user_id='$user_id'";

    if (!mysqli_query($conn, $delete_user)) {
        echo "Error deleting user account: " . mysqli_error($conn);
        exit();
    }

    // Delete from customer_tbl
    $delete_customer = "DELETE FROM customer_tbl
                        WHERE customer_id='$record_id'";

    if (!mysqli_query($conn, $delete_customer)) {
        echo "Error deleting customer: " . mysqli_error($conn);
        exit();
    }

    echo "<script>
        alert('Account deleted successfully');
        window.location.href='../admin/admin_user_management.php';
    </script>";

} else {

    header("Location: ../admin/admin_user_management.php");
    exit();
}
?>