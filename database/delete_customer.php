<?php
include 'database_connection.php';

$customerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$customerId) {
    header('Location: ../admin/admin_customer.php?delete_error=1');
    exit();
}

mysqli_begin_transaction($conn);

try {
    $getUser = mysqli_prepare($conn, 'SELECT user_id FROM customer_tbl WHERE customer_id = ? FOR UPDATE');
    mysqli_stmt_bind_param($getUser, 'i', $customerId);
    mysqli_stmt_execute($getUser);
    $customer = mysqli_fetch_assoc(mysqli_stmt_get_result($getUser));

    if (!$customer) {
        throw new Exception('Customer not found.');
    }

    $deleteCustomer = mysqli_prepare($conn, 'DELETE FROM customer_tbl WHERE customer_id = ?');
    mysqli_stmt_bind_param($deleteCustomer, 'i', $customerId);
    if (!mysqli_stmt_execute($deleteCustomer)) {
        throw new Exception('Unable to delete customer.');
    }

    $userId = (int) $customer['user_id'];
    if ($userId > 0) {
        $deleteUser = mysqli_prepare($conn, 'DELETE FROM user_accounts_tbl WHERE id = ?');
        mysqli_stmt_bind_param($deleteUser, 'i', $userId);
        if (!mysqli_stmt_execute($deleteUser)) {
            throw new Exception('Unable to delete user account.');
        }
    }

    mysqli_commit($conn);
    header('Location: ../admin/admin_customer.php?deleted=1');
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    header('Location: ../admin/admin_customer.php?delete_error=1');
}
exit();
