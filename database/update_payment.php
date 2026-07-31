<?php
include 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/admin_payment.php');
    exit();
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$plan = trim($_POST['plan'] ?? '');
$fName = trim($_POST['f_name'] ?? '');
$mName = trim($_POST['m_name'] ?? '');
$lName = trim($_POST['l_name'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? '');
$dueDate = trim($_POST['due_date'] ?? '');
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$remarks = trim($_POST['remarks'] ?? '');

$plans = ['50 mbps', '100 mbps', '150 mbps', '200 mbps', '250 mbps'];
$methods = ['Cash', 'Gcash'];
$remarkOptions = ['Paid', 'Not Paid'];

if (!$id || !in_array($plan, $plans, true) || $fName === '' || $lName === '' ||
    !in_array($paymentMethod, $methods, true) || !in_array($remarks, $remarkOptions, true) ||
    !$dueDate || $amount === false || $amount < 0) {
    header('Location: ../admin/admin_payment.php?error=1');
    exit();
}

$statement = mysqli_prepare($conn, 'UPDATE payment_tbl SET plan = ?, f_name = ?, m_name = ?, l_name = ?, payment_method = ?, due_date = ?, amount = ?, remarks = ? WHERE id = ?');
mysqli_stmt_bind_param($statement, 'ssssssdsi', $plan, $fName, $mName, $lName, $paymentMethod, $dueDate, $amount, $remarks, $id);

if (mysqli_stmt_execute($statement)) {
    header('Location: ../admin/admin_payment.php?updated=1');
} else {
    header('Location: ../admin/admin_payment.php?error=1');
}
exit();
