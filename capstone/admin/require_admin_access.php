<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['account_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once __DIR__ . '/../database/database_connection.php';
$accessAccountId = mysqli_real_escape_string($conn, (string) $_SESSION['account_id']);
$accessResult = mysqli_query(
    $conn,
    "SELECT role FROM admin_superadmin_accounts_tbl WHERE account_id = '$accessAccountId' LIMIT 1"
);
$accessAccount = $accessResult ? mysqli_fetch_assoc($accessResult) : null;
$authenticatedAdminRole = trim($accessAccount['role'] ?? '');

if (!in_array(strtolower($authenticatedAdminRole), ['admin', 'super admin'], true)) {
    http_response_code(403);
    exit('Access denied. Admin access is required.');
}
