<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isset($_SESSION['account_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/database_connection.php';
$accountId = (int) $_SESSION['account_id'];
$roleStatement = mysqli_prepare(
    $conn,
    'SELECT role FROM admin_superadmin_accounts_tbl WHERE account_id = ? LIMIT 1'
);
if (!$roleStatement) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to check account access']);
    exit;
}
mysqli_stmt_bind_param($roleStatement, 'i', $accountId);
mysqli_stmt_execute($roleStatement);
$roleResult = mysqli_stmt_get_result($roleStatement);
$roleRow = $roleResult ? mysqli_fetch_assoc($roleResult) : null;
mysqli_stmt_close($roleStatement);
$role = strtolower(trim($roleRow['role'] ?? ''));

if (!in_array($role, ['admin', 'super admin'], true)) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/admin_payment_notifications.php';
$unreadCount = getUnreadAdminPaidPaymentCount($conn, $accountId);
$lastSeenPaymentId = 0;
$readStatement = mysqli_prepare(
    $conn,
    'SELECT last_seen_payment_id FROM admin_payment_notification_reads WHERE account_id = ? LIMIT 1'
);
if ($readStatement) {
    $accountIdString = (string) $accountId;
    mysqli_stmt_bind_param($readStatement, 's', $accountIdString);
    mysqli_stmt_execute($readStatement);
    $readResult = mysqli_stmt_get_result($readStatement);
    $readRow = $readResult ? mysqli_fetch_assoc($readResult) : null;
    $lastSeenPaymentId = (int) ($readRow['last_seen_payment_id'] ?? 0);
    mysqli_stmt_close($readStatement);
}

$recentPayments = [];
$paymentResult = mysqli_query(
    $conn,
    "SELECT id, f_name, m_name, l_name, amount, created_at
     FROM payment_tbl
     WHERE payment_status = 'Paid'
     ORDER BY created_at DESC, id DESC
     LIMIT 5"
);
if ($paymentResult) {
    while ($payment = mysqli_fetch_assoc($paymentResult)) {
        $payerName = trim(implode(' ', array_filter([
            $payment['f_name'] ?? '',
            $payment['m_name'] ?? '',
            $payment['l_name'] ?? '',
        ], static function ($part) {
            return trim((string) $part) !== '';
        })));
        $paidAt = !empty($payment['created_at']) ? strtotime($payment['created_at']) : false;
        $recentPayments[] = [
            'name' => $payerName !== '' ? $payerName : 'Customer',
            'amount' => (float) ($payment['amount'] ?? 0),
            'created_at' => $paidAt ? date('M j, Y · g:i A', $paidAt) : 'Paid',
            'is_new' => (int) $payment['id'] > $lastSeenPaymentId,
        ];
    }
}

echo json_encode([
    'unread_paid_payments' => $unreadCount,
    'recent_payments' => $recentPayments,
], JSON_INVALID_UTF8_SUBSTITUTE);
?>
