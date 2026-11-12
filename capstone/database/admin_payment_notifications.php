<?php
function getAdminNotificationState(mysqli $conn, int $accountId, string $module, bool $markViewed = false): array
{
    $emptyState = ['unread_count' => 0, 'last_seen_id' => 0];
    if ($accountId < 1) {
        return $emptyState;
    }

    $modules = [
        'applicants' => [
            'table' => 'internet_application_tbl',
            'id' => 'applicant_id',
            'active_condition' => "status = 'Pending'",
            'initial_condition' => "status = 'Pending'",
            'show_latest_when_empty' => false,
        ],
        'payments' => [
            'table' => 'payment_tbl',
            'id' => 'id',
            'active_condition' => "payment_status IN ('Pending', 'Paid')",
            'initial_condition' => "payment_status = 'Pending'",
            'show_latest_when_empty' => true,
        ],
        'inquiries' => [
            'table' => 'inquiries_tbl',
            'id' => 'inquiries_id',
            'active_condition' => "status = 'Pending'",
            'initial_condition' => "status = 'Pending'",
            'show_latest_when_empty' => false,
        ],
        'tickets' => [
            'table' => 'ticket_management_tbl',
            'id' => 'ticket_id',
            'active_condition' => "status = 'Pending'",
            'initial_condition' => "status = 'Pending'",
            'show_latest_when_empty' => false,
        ],
    ];
    if (!isset($modules[$module])) {
        return $emptyState;
    }
    $config = $modules[$module];

    static $readsTableReady = false;
    if (!$readsTableReady) {
        $createTableSql = "CREATE TABLE IF NOT EXISTS admin_notification_reads (
            account_id VARCHAR(64) NOT NULL,
            module_key VARCHAR(32) NOT NULL,
            last_seen_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (account_id, module_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        if (!mysqli_query($conn, $createTableSql)) {
            return $emptyState;
        }
        $readsTableReady = true;
    }

    $maxResult = mysqli_query(
        $conn,
        "SELECT COALESCE(MAX({$config['id']}), 0) AS max_id FROM {$config['table']}"
    );
    if (!$maxResult) {
        return $emptyState;
    }
    $maxRow = mysqli_fetch_assoc($maxResult);
    $maxId = (int) ($maxRow['max_id'] ?? 0);

    $initialResult = mysqli_query(
        $conn,
        "SELECT MIN({$config['id']}) AS first_id FROM {$config['table']} WHERE {$config['initial_condition']}"
    );
    $initialRow = $initialResult ? mysqli_fetch_assoc($initialResult) : null;
    if (!empty($initialRow['first_id'])) {
        $initialLastSeenId = max(0, (int) $initialRow['first_id'] - 1);
    } elseif ($config['show_latest_when_empty'] && $maxId > 0) {
        $initialLastSeenId = $maxId - 1;
    } else {
        $initialLastSeenId = $maxId;
    }

    $accountIdString = (string) $accountId;
    $insertStatement = mysqli_prepare(
        $conn,
        'INSERT IGNORE INTO admin_notification_reads (account_id, module_key, last_seen_id) VALUES (?, ?, ?)'
    );
    if (!$insertStatement) {
        return $emptyState;
    }
    mysqli_stmt_bind_param($insertStatement, 'ssi', $accountIdString, $module, $initialLastSeenId);
    $inserted = mysqli_stmt_execute($insertStatement);
    mysqli_stmt_close($insertStatement);
    if (!$inserted) {
        return $emptyState;
    }

    if ($markViewed) {
        $updateStatement = mysqli_prepare(
            $conn,
            'UPDATE admin_notification_reads SET last_seen_id = GREATEST(last_seen_id, ?) WHERE account_id = ? AND module_key = ?'
        );
        if (!$updateStatement) {
            return $emptyState;
        }
        mysqli_stmt_bind_param($updateStatement, 'iss', $maxId, $accountIdString, $module);
        $updated = mysqli_stmt_execute($updateStatement);
        mysqli_stmt_close($updateStatement);
        if (!$updated) {
            return $emptyState;
        }
    }

    $readStatement = mysqli_prepare(
        $conn,
        'SELECT last_seen_id FROM admin_notification_reads WHERE account_id = ? AND module_key = ? LIMIT 1'
    );
    if (!$readStatement) {
        return $emptyState;
    }
    mysqli_stmt_bind_param($readStatement, 'ss', $accountIdString, $module);
    mysqli_stmt_execute($readStatement);
    $readResult = mysqli_stmt_get_result($readStatement);
    $readRow = $readResult ? mysqli_fetch_assoc($readResult) : null;
    $lastSeenId = (int) ($readRow['last_seen_id'] ?? 0);
    mysqli_stmt_close($readStatement);

    $countStatement = mysqli_prepare(
        $conn,
        "SELECT COUNT(*) AS total
         FROM {$config['table']} records
         INNER JOIN admin_notification_reads read_state
            ON read_state.account_id = ? AND read_state.module_key = ?
         WHERE {$config['active_condition']} AND records.{$config['id']} > read_state.last_seen_id"
    );
    if (!$countStatement) {
        return ['unread_count' => 0, 'last_seen_id' => $lastSeenId];
    }
    mysqli_stmt_bind_param($countStatement, 'ss', $accountIdString, $module);
    $unreadCount = 0;
    if (mysqli_stmt_execute($countStatement)) {
        $countResult = mysqli_stmt_get_result($countStatement);
        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $unreadCount = (int) ($countRow['total'] ?? 0);
        }
    }
    mysqli_stmt_close($countStatement);

    return ['unread_count' => $unreadCount, 'last_seen_id' => $lastSeenId];
}
?>
