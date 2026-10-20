<?php
require_once __DIR__ . '/../database/database_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update'])) {
    header('Location: ../admin/admin_customer.php');
    exit;
}

$customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
$requiredFields = [
    'f_name', 'm_name', 'l_name', 'contact_number', 'age', 'sex',
    'barangay', 'house_name', 'internet_plan', 'connection_status',
    'due_date', 'installation_date',
];

if (!$customerId) {
    header('Location: ../admin/admin_customer.php?error=invalid_customer');
    exit;
}

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        header('Location: ../admin/admin_customer.php?error=missing_fields');
        exit;
    }
}

$fName = trim($_POST['f_name']);
$mName = trim($_POST['m_name']);
$lName = trim($_POST['l_name']);
$contactNumber = trim($_POST['contact_number']);
$age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
$sex = $_POST['sex'];
$barangay = $_POST['barangay'];
$houseName = trim($_POST['house_name']);
$internetPlan = filter_var($_POST['internet_plan'], FILTER_VALIDATE_INT);
$connectionStatus = $_POST['connection_status'];
$dueDate = $_POST['due_date'];
$installationDate = $_POST['installation_date'];

$allowedSexes = ['Male', 'Female'];
$allowedBarangays = ['Bagtas', 'Punta I'];
$allowedStatuses = ['Connected', 'Disconnected', 'Frozen', 'Inactive'];
$validDate = static function ($value) {
    if ($value === '') {
        return true;
    }
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
};

if (
    $fName === '' || $lName === '' || $contactNumber === '' || $age === false || $age < 0 ||
    !$internetPlan || !in_array($sex, $allowedSexes, true) ||
    !in_array($barangay, $allowedBarangays, true) ||
    !in_array($connectionStatus, $allowedStatuses, true) ||
    !$validDate($dueDate) || !$validDate($installationDate)
) {
    header('Location: ../admin/admin_customer.php?error=invalid_fields');
    exit;
}

try {
    mysqli_begin_transaction($conn);

    $planCheck = mysqli_prepare($conn, 'SELECT plan_id FROM internet_plan_tbl WHERE plan_id = ?');
    mysqli_stmt_bind_param($planCheck, 'i', $internetPlan);
    mysqli_stmt_execute($planCheck);
    $planResult = mysqli_stmt_get_result($planCheck);
    if (!$planResult || mysqli_num_rows($planResult) !== 1) {
        throw new RuntimeException('Invalid internet plan.');
    }
    mysqli_stmt_close($planCheck);

    $update = mysqli_prepare(
        $conn,
        'UPDATE customer_tbl SET f_name = ?, m_name = ?, l_name = ?, contact_number = ?, age = ?, sex = ?, barangay = ?, house_name = ?, internet_plan = ?, connection_status = ?, due_date = IF(? = \'\', due_date, ?), installation_date = IF(? = \'\', installation_date, ?) WHERE customer_id = ?'
    );
    mysqli_stmt_bind_param(
        $update,
        'ssssisssisssssi',
        $fName,
        $mName,
        $lName,
        $contactNumber,
        $age,
        $sex,
        $barangay,
        $houseName,
        $internetPlan,
        $connectionStatus,
        $dueDate,
        $dueDate,
        $installationDate,
        $installationDate,
        $customerId
    );
    if (!mysqli_stmt_execute($update)) {
        throw new RuntimeException(mysqli_stmt_error($update));
    }
    if (mysqli_stmt_affected_rows($update) === 0) {
        $exists = mysqli_prepare($conn, 'SELECT customer_id FROM customer_tbl WHERE customer_id = ?');
        mysqli_stmt_bind_param($exists, 'i', $customerId);
        mysqli_stmt_execute($exists);
        $existsResult = mysqli_stmt_get_result($exists);
        $customerExists = $existsResult && mysqli_num_rows($existsResult) === 1;
        mysqli_stmt_close($exists);
        if (!$customerExists) {
            throw new RuntimeException('Customer record not found.');
        }
    }

    mysqli_stmt_close($update);
    mysqli_commit($conn);
    header('Location: ../admin/admin_customer.php?updated=1');
    exit;
} catch (Throwable $error) {
    mysqli_rollback($conn);
    error_log('Customer update failed: ' . $error->getMessage());
    header('Location: ../admin/admin_customer.php?error=save_failed');
    exit;
}
?>
