
<?php
require_once __DIR__ . '/../admin/require_super_admin.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../admin/admin_user_management.php");
    exit();
}

if (!isset($_POST['record_id']) || !isset($_POST['role'])) {
    header("Location: ../admin/admin_user_management.php");
    exit();
}

$record_id = intval($_POST['record_id']);
$role = $_POST['role'];

if ($record_id <= 0 || ($role != 'Admin' && $role != 'Super Admin')) {
    header("Location: ../admin/admin_user_management.php");
    exit();
}

$role = mysqli_real_escape_string($conn, $role);

$query = "SELECT account_status
          FROM admin_superadmin_accounts_tbl
          WHERE account_id = '$record_id'
          AND role = '$role'";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Account not found.");
}

$account = mysqli_fetch_assoc($result);

if ($account['account_status'] == 'Active') {
    $new_status = 'Deactivated';
} else {
    $new_status = 'Active';
}

$update = "UPDATE admin_superadmin_accounts_tbl
           SET account_status = '$new_status'
           WHERE account_id = '$record_id'
           AND role = '$role'";

if (mysqli_query($conn, $update)) {

    echo "<script>
        alert('Account status changed to $new_status.');
        window.location.href = '../admin/admin_user_management.php';
    </script>";

    exit();

} else {

    echo "Error updating account status.";

}
?>
