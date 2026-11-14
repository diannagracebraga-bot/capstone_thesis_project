<?php
include '../admin/check_super_admin_access.php';
include '../database/database_connection.php';
require_once '../database/announcements_helper.php';
if (!isset($_SESSION['account_id'])) {
    header('Location: ../index.php');
    exit();
}
$accountId = mysqli_real_escape_string($conn, (string) $_SESSION['account_id']);
$roleResult = mysqli_query($conn, "SELECT role FROM admin_superadmin_accounts_tbl WHERE account_id = '$accountId' LIMIT 1");
$roleRow = $roleResult ? mysqli_fetch_assoc($roleResult) : null;
if (!$roleRow || strcasecmp(trim($roleRow['role']), 'Super Admin') !== 0) {
    http_response_code(403);
    exit('Access denied. Super Admin access is required.');
}
if (!ensureAnnouncementsTable($conn)) {
    die('Unable to prepare the announcements list.');
}
$formError = '';
$allowedAttachmentTypes = [
    'png' => ['image/png'],
    'jpg' => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'pdf' => ['application/pdf'],
    'mp4' => ['video/mp4'],
    'webm' => ['video/webm'],
    'mov' => ['video/quicktime'],
    'avi' => ['video/x-msvideo', 'video/avi'],
    'mkv' => ['video/x-matroska'],
    'ogv' => ['video/ogg'],
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'publish') {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $upload = $_FILES['attachment'] ?? null;
        $attachmentPath = null;
        if ($title === '' || $message === '') {
            $formError = 'Enter a title and announcement before publishing.';
        } else {
            if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($upload['error'] !== UPLOAD_ERR_OK) {
                    $formError = $upload['error'] === UPLOAD_ERR_INI_SIZE || $upload['error'] === UPLOAD_ERR_FORM_SIZE
                        ? 'The file exceeds the upload limit. Choose a file up to 50 MB.'
                        : 'The attachment could not be uploaded. Please try again.';
                } elseif ($upload['size'] > 50 * 1024 * 1024 || !is_uploaded_file($upload['tmp_name'])) {
                    $formError = 'Choose a valid file up to 50 MB.';
                } else {
                    $extension = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo ? finfo_file($finfo, $upload['tmp_name']) : '';
                    if ($finfo) finfo_close($finfo);
                    if (!isset($allowedAttachmentTypes[$extension]) || !in_array($mimeType, $allowedAttachmentTypes[$extension], true)) {
                        $formError = 'Only PNG, JPG, JPEG, PDF, or video files (MP4, WEBM, MOV, AVI, MKV, OGV) are allowed.';
                    } else {
                        $uploadDirectory = __DIR__ . '/../uploads/announcements/';
                        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                            $formError = 'The upload folder could not be prepared. Please try again.';
                        } else {
                            $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
                            if (move_uploaded_file($upload['tmp_name'], $uploadDirectory . $storedName)) {
                                $attachmentPath = 'uploads/announcements/' . $storedName;
                            } else {
                                $formError = 'The attachment could not be saved. Please try again.';
                            }
                        }
                    }
                }
            }
            if ($formError === '') {
                $statement = mysqli_prepare($conn, 'INSERT INTO announcements_tbl (title, message, attachment_path) VALUES (?, ?, ?)');
                if ($statement) {
                    mysqli_stmt_bind_param($statement, 'sss', $title, $message, $attachmentPath);
                    if (mysqli_stmt_execute($statement)) {
                        echo "<script>alert('Announcement published successfully.'); window.location.href='admin_announcement_management.php';</script>";
                        exit();
                    }
                    $formError = 'The announcement could not be published. Please try again.';
                    mysqli_stmt_close($statement);
                } else {
                    $formError = 'The announcement form could not be prepared. Please try again.';
                }
                if ($formError !== '' && $attachmentPath !== null) {
                    $failedUpload = __DIR__ . '/../' . $attachmentPath;
                    if (is_file($failedUpload)) unlink($failedUpload);
                }
            }
        }
    } elseif ($action === 'delete' && isset($_POST['announcement_id'])) {
        $announcementId = (int) $_POST['announcement_id'];
        $lookup = mysqli_prepare($conn, 'SELECT attachment_path FROM announcements_tbl WHERE announcement_id = ?');
        $attachmentPath = null;
        if ($lookup) {
            mysqli_stmt_bind_param($lookup, 'i', $announcementId);
            mysqli_stmt_execute($lookup);
            $lookupResult = mysqli_stmt_get_result($lookup);
            $lookupRow = $lookupResult ? mysqli_fetch_assoc($lookupResult) : null;
            $attachmentPath = $lookupRow['attachment_path'] ?? null;
            mysqli_stmt_close($lookup);
        }
        $statement = mysqli_prepare($conn, 'DELETE FROM announcements_tbl WHERE announcement_id = ?');
        $deleted = false;
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'i', $announcementId);
            $deleted = mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
        }
        if ($deleted && $attachmentPath && strpos($attachmentPath, 'uploads/announcements/') === 0) {
            $attachmentFile = __DIR__ . '/../' . $attachmentPath;
            if (is_file($attachmentFile)) unlink($attachmentFile);
        }
        if ($deleted) {
            echo "<script>alert('Announcement deleted successfully.'); window.location.href='admin_announcement_management.php';</script>";
        } else {
            echo "<script>alert('The announcement could not be deleted. Please try again.'); window.location.href='admin_announcement_management.php';</script>";
        }
        exit();
    }
}
$announcements = mysqli_query($conn, 'SELECT announcement_id, title, message, attachment_path, created_at FROM announcements_tbl WHERE is_active = 1 ORDER BY created_at DESC, announcement_id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Announcements | MITZTIANPC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_announcement_management.css?v=2">
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<main class="announcement-admin-page">
    <section class="announcement-admin-card">
        <div class="announcement-admin-heading">
            <span class="announcement-admin-icon"><i class="bi bi-megaphone-fill" aria-hidden="true"></i></span>
            <div><h2>Announcements</h2><p>Publish announcements for customers to see.</p></div>
        </div>
        <?php if ($formError !== ''): ?><div class="alert alert-danger"><?php echo htmlspecialchars($formError); ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="announcement-form">
            <input type="hidden" name="action" value="publish">
            <label for="announcement-title">Title</label>
            <input id="announcement-title" name="title" maxlength="180" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            <label for="announcement-message">Announcement</label>
            <textarea id="announcement-message" name="message" rows="4" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            <label for="announcement-attachment">Attachment (optional)</label>
            <input id="announcement-attachment" name="attachment" type="file" accept=".png,.jpg,.jpeg,.pdf,.mp4,.webm,.mov,.avi,.mkv,.ogv,image/png,image/jpeg,application/pdf,video/*">
            <small class="attachment-help">PNG, JPG, JPEG, PDF, o video (MP4, WEBM, MOV, AVI, MKV, OGV). Hanggang 50 MB.</small>
            <button type="submit"><i class="bi bi-send-fill" aria-hidden="true"></i> Publish Announcement</button>
        </form>
        <h3>Published announcements</h3>
        <div class="announcement-list">
        <?php if ($announcements && mysqli_num_rows($announcements) > 0): ?>
            <?php while ($announcement = mysqli_fetch_assoc($announcements)): ?>
                <article class="announcement-entry">
                    <div><h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                    <small><?php echo htmlspecialchars(date('F d, Y · g:i A', strtotime($announcement['created_at']))); ?></small>
                    <p><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p></div>
                    <?php if (!empty($announcement['attachment_path'])): ?>
                        <?php $attachmentUrl = '../' . $announcement['attachment_path']; $attachmentExt = strtolower(pathinfo($announcement['attachment_path'], PATHINFO_EXTENSION)); ?>
                        <div class="announcement-attachment">
                        <?php if (in_array($attachmentExt, ['png', 'jpg', 'jpeg'], true)): ?>
                            <a href="<?php echo htmlspecialchars($attachmentUrl); ?>" target="_blank" rel="noopener"><img src="<?php echo htmlspecialchars($attachmentUrl); ?>" alt="Announcement attachment"></a>
                        <?php elseif (in_array($attachmentExt, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'ogv'], true)): ?>
                            <video controls preload="metadata"><source src="<?php echo htmlspecialchars($attachmentUrl); ?>"></video>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($attachmentUrl); ?>" target="_blank" rel="noopener">View or download PDF attachment</a>
                        <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="announcement_id" value="<?php echo (int) $announcement['announcement_id']; ?>">
                        <button type="submit" class="delete-announcement">Delete</button>
                    </form>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-announcements">No announcements have been published yet.</p>
        <?php endif; ?>
        </div>
    </section>
</main>
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
</body>
</html>
