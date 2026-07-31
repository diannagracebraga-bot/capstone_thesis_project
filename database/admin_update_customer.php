<?php
include 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update'])) {
    header('Location: ../admin/admin_customer.php');
    exit();
}

$customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
$age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
$planId = filter_input(INPUT_POST, 'internet_plan', FILTER_VALIDATE_INT);

if (!$customerId || $age === false || !$planId) {
    header('Location: ../admin/admin_customer.php?update_error=1');
    exit();
}

$email = trim($_POST['email'] ?? '');
$fName = trim($_POST['f_name'] ?? '');
$mName = trim($_POST['m_name'] ?? '');
$lName = trim($_POST['l_name'] ?? '');
$contactNumber = trim($_POST['contact_number'] ?? '');
$sex = trim($_POST['sex'] ?? '');
$civilStatus = trim($_POST['civil_status'] ?? '');
$birthDate = trim($_POST['birth_date'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$subdivision = trim($_POST['subdivision'] ?? '');
$street = trim($_POST['street'] ?? '');
$houseNumber = trim($_POST['house_name'] ?? '');
$status = trim($_POST['connection_status'] ?? '');

$sql = 'UPDATE customer_tbl SET email_address = ?, first_name = ?, middle_name = ?, last_name = ?, contact_number = ?, age = ?, sex = ?, civil_status = ?, birth_date = ?, barangay = ?, subdivision = ?, street = ?, house_number = ?, internet_plan_id = ?, status = ? WHERE customer_id = ?';
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, 'sssssisssssssisi', $email, $fName, $mName, $lName, $contactNumber, $age, $sex, $civilStatus, $birthDate, $barangay, $subdivision, $street, $houseNumber, $planId, $status, $customerId);

if (!mysqli_stmt_execute($statement)) {
    header('Location: ../admin/admin_customer.php?update_error=1');
    exit();
}

$password = $_POST['password'] ?? '';
if ($password !== '') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $passwordStatement = mysqli_prepare($conn, 'UPDATE customer_tbl SET password = ? WHERE customer_id = ?');
    mysqli_stmt_bind_param($passwordStatement, 'si', $hashedPassword, $customerId);
    mysqli_stmt_execute($passwordStatement);
}

header('Location: ../admin/admin_customer.php?updated=1');
exit();