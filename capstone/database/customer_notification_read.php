<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isset($_SESSION['user_id']) || strtolower(trim((string) ($_SESSION['role'] ?? ''))) !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false]);
    exit;
}

require_once __DIR__ . '/database_connection.php';
require_once __DIR__ . '/customer_notification_reads.php';

$noticeKeys = $_POST['notice_keys'] ?? [];
if (!is_array($noticeKeys)) {
    $noticeKeys = [];
}

$savedAll = true;
foreach (array_slice($noticeKeys, 0, 100) as $noticeKey) {
    if (isValidCustomerNoticeKey($noticeKey) && !markCustomerNoticeRead($conn, (int) $_SESSION['user_id'], $noticeKey)) {
        $savedAll = false;
    }
}

echo json_encode(['success' => $savedAll], JSON_INVALID_UTF8_SUBSTITUTE);
?>
