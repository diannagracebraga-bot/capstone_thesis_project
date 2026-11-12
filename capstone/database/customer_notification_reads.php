<?php
function ensureCustomerNotificationReadsTable(mysqli $conn): bool
{
    static $tableReady = false;
    if ($tableReady) {
        return true;
    }

    $sql = "CREATE TABLE IF NOT EXISTS customer_notification_reads (
        user_id INT NOT NULL,
        notice_key VARCHAR(160) NOT NULL,
        read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, notice_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $tableReady = mysqli_query($conn, $sql) !== false;
    return $tableReady;
}

function isValidCustomerNoticeKey($noticeKey): bool
{
    return is_string($noticeKey)
        && strlen($noticeKey) <= 160
        && preg_match('/\A(?:payment|ticket|due|announcement):[A-Za-z0-9:_-]+\z/', $noticeKey) === 1;
}

function markCustomerNoticeRead(mysqli $conn, int $userId, string $noticeKey): bool
{
    if ($userId < 1 || !isValidCustomerNoticeKey($noticeKey) || !ensureCustomerNotificationReadsTable($conn)) {
        return false;
    }

    $statement = mysqli_prepare(
        $conn,
        'INSERT IGNORE INTO customer_notification_reads (user_id, notice_key) VALUES (?, ?)'
    );
    if (!$statement) {
        return false;
    }
    mysqli_stmt_bind_param($statement, 'is', $userId, $noticeKey);
    $saved = mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    return $saved;
}

function getCustomerReadNoticeKeys(mysqli $conn, int $userId): array
{
    if ($userId < 1 || !ensureCustomerNotificationReadsTable($conn)) {
        return [];
    }

    $statement = mysqli_prepare($conn, 'SELECT notice_key FROM customer_notification_reads WHERE user_id = ?');
    if (!$statement) {
        return [];
    }
    mysqli_stmt_bind_param($statement, 'i', $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $keys = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $keys[$row['notice_key']] = true;
        }
    }
    mysqli_stmt_close($statement);
    return $keys;
}
?>
