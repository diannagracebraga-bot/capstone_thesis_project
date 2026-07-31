<?php
include 'database_connection.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../admin/admin_payment.php?error=1');
    exit();
}

$statement = mysqli_prepare($conn, 'DELETE FROM payment_tbl WHERE id = ?');
mysqli_stmt_bind_param($statement, 'i', $id);

if (mysqli_stmt_execute($statement)) {
    header('Location: ../admin/admin_payment.php?deleted=1');
} else {
    header('Location: ../admin/admin_payment.php?error=1');
}
exit();
