<?php
function getUnreadAdminPaidPaymentCount(mysqli $conn, int $accountId, bool $markViewed = false): int
{
    if ($accountId < 1) {
        return 0;
    }

    $createTableSql = "CREATE TABLE IF NOT EXISTS admin_payment_notification_reads (
        account_id VARCHAR(64) NOT NULL PRIMARY KEY,
        last_seen_payment_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (!mysqli_query($conn, $createTableSql)) {
        return 0;
    }

    $maxResult = mysqli_query($conn, "SELECT COALESCE(MAX(id), 0) AS latest_id FROM payment_tbl WHERE payment_status = 'Paid'");
    if (!$maxResult) {
        return 0;
    }
    $maxRow = mysqli_fetch_assoc($maxResult);
    $latestPaidPaymentId = (int) ($maxRow['latest_id'] ?? 0);

    $insertStatement = mysqli_prepare(
        $conn,
        'INSERT IGNORE INTO admin_payment_notification_reads (account_id, last_seen_payment_id) VALUES (?, ?)'
    );
    if (!$insertStatement) {
        return 0;
    }
    $accountIdString = (string) $accountId;
    // On the first install, surface the most recent successful payment so the
    // Admin can confirm the notification flow without replaying old history.
    $initialSeenPaymentId = max(0, $latestPaidPaymentId - 1);
    mysqli_stmt_bind_param($insertStatement, 'si', $accountIdString, $initialSeenPaymentId);
    $inserted = mysqli_stmt_execute($insertStatement);
    mysqli_stmt_close($insertStatement);
    if (!$inserted) {
        return 0;
    }

    if ($markViewed) {
        $updateStatement = mysqli_prepare(
            $conn,
            'UPDATE admin_payment_notification_reads SET last_seen_payment_id = GREATEST(last_seen_payment_id, ?) WHERE account_id = ?'
        );
        if (!$updateStatement) {
            return 0;
        }
        mysqli_stmt_bind_param($updateStatement, 'is', $latestPaidPaymentId, $accountIdString);
        $updated = mysqli_stmt_execute($updateStatement);
        mysqli_stmt_close($updateStatement);
        if (!$updated) {
            return 0;
        }
    }

    $countStatement = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM payment_tbl p
         INNER JOIN admin_payment_notification_reads n ON n.account_id = ?
         WHERE p.payment_status = 'Paid' AND p.id > n.last_seen_payment_id"
    );
    if (!$countStatement) {
        return 0;
    }
    mysqli_stmt_bind_param($countStatement, 's', $accountIdString);
    $count = 0;
    if (mysqli_stmt_execute($countStatement)) {
        $countResult = mysqli_stmt_get_result($countStatement);
        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $count = (int) ($countRow['total'] ?? 0);
        }
    }
    mysqli_stmt_close($countStatement);

    return $count;
}
?>
