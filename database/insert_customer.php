<?php
include '../database/database_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
    header('Location: ../admin/admin_user_management.php?page=add_customer');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$fName = trim($_POST['f_name'] ?? '');
$mName = trim($_POST['m_name'] ?? '');
$lName = trim($_POST['l_name'] ?? '');
$contact = trim($_POST['contact_number'] ?? '');
$birthDate = trim($_POST['birth_date'] ?? '');
$age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
$sex = trim($_POST['sex'] ?? '');
$civilStatus = trim($_POST['civil_status'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$subdivision = trim($_POST['subdivision'] ?? '');
$street = trim($_POST['street'] ?? '');
$houseNumber = trim($_POST['house_number'] ?? '');
$planId = filter_input(INPUT_POST, 'internet_plan', FILTER_VALIDATE_INT);
$status = trim($_POST['connection_status'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $fName === '' || $lName === '' || $age === false || !$planId) {
    header('Location: ../admin/admin_user_management.php?page=add_customer&error=1'); exit();
}

$check = mysqli_prepare($conn, 'SELECT id FROM user_accounts_tbl WHERE email = ? LIMIT 1');
mysqli_stmt_bind_param($check, 's', $email);
mysqli_stmt_execute($check);
if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
    header('Location: ../admin/admin_user_management.php?page=add_customer&exists=1'); exit();
}

mysqli_begin_transaction($conn);
try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'customer';
    $insertUser = mysqli_prepare($conn, 'INSERT INTO user_accounts_tbl (email, password, role) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($insertUser, 'sss', $email, $hash, $role);
    if (!mysqli_stmt_execute($insertUser)) { throw new Exception('Unable to create the user account.'); }
    $userId = mysqli_insert_id($conn);

    $lastCustomer = mysqli_query($conn, 'SELECT COALESCE(MAX(customer_id), 0) + 1 AS next_id FROM customer_tbl');
    $nextId = (int) mysqli_fetch_assoc($lastCustomer)['next_id'];
    $accountNumber = 'MPC-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);

    $sql = 'INSERT INTO customer_tbl (user_id, email_address, first_name, middle_name, last_name, birth_date, age, sex, contact_number, civil_status, barangay, house_number, street, subdivision, internet_plan_id, password, role, status, account_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $insertCustomer = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($insertCustomer, 'isssssisssssssissss', $userId, $email, $fName, $mName, $lName, $birthDate, $age, $sex, $contact, $civilStatus, $barangay, $houseNumber, $street, $subdivision, $planId, $hash, $role, $status, $accountNumber);
    if (!mysqli_stmt_execute($insertCustomer)) { throw new Exception('Unable to create the customer record.'); }
    mysqli_commit($conn);
    header('Location: ../admin/admin_customer.php?created=1');
} catch (Throwable $exception) {
    mysqli_rollback($conn);
    header('Location: ../admin/admin_user_management.php?page=add_customer&error=1');
}
exit();