<?php
function ensureAnnouncementsTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS announcements_tbl (
        announcement_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(180) NOT NULL,
        message TEXT NOT NULL,
        attachment_path VARCHAR(255) DEFAULT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (announcement_id),
        KEY idx_announcements_active_created (is_active, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!mysqli_query($conn, $sql)) {
        return false;
    }

    // CREATE TABLE IF NOT EXISTS does not add columns to an existing table.
    $columnResult = mysqli_query($conn, "SHOW COLUMNS FROM announcements_tbl LIKE 'attachment_path'");
    if (!$columnResult) {
        return false;
    }
    $hasAttachmentColumn = mysqli_num_rows($columnResult) > 0;
    mysqli_free_result($columnResult);

    if (!$hasAttachmentColumn && !mysqli_query($conn, 'ALTER TABLE announcements_tbl ADD attachment_path VARCHAR(255) DEFAULT NULL AFTER message')) {
        return false;
    }

    return true;
}
?>
