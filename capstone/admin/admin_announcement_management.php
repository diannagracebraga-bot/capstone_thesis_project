<?php
session_start();
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'publish') {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($title === '' || $message === '') {
            $formError = 'Ilagay ang title at announcement bago i-publish.';
        } else {
            $statement = mysqli_prepare($conn, 'INSERT INTO announcements_tbl (title, message) VALUES (?, ?)');
            if ($statement) {
                mysqli_stmt_bind_param($statement, 'ss', $title, $message);
                if (mysqli_stmt_execute($statement)) {
                    header('Location: admin_announcement_management.php?published=1');
                    exit();
                }
                $formError = 'Hindi na-publish ang announcement. Subukan ulit.';
                mysqli_stmt_close($statement);
            } else {
                $formError = 'Hindi naihanda ang announcement form. Subukan ulit.';
            }
        }
    } elseif ($action === 'delete' && isset($_POST['announcement_id'])) {
        $announcementId = (int) $_POST['announcement_id'];
        $statement = mysqli_prepare($conn, 'DELETE FROM announcements_tbl WHERE announcement_id = ?');
        if ($statement) {
            mysqli_stmt_bind_param($statement, 'i', $announcementId);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
        }
        header('Location: admin_announcement_management.php?deleted=1');
        exit();
    }
}

$announcements = mysqli_query($conn, 'SELECT announcement_id, title, message, created_at FROM announcements_tbl WHERE is_active = 1 ORDER BY created_at DESC, announcement_id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Announcements | MITZTIANPC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_announcement_management.css?v=1">
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<main class="announcement-admin-page">
    <section class="announcement-admin-card">
        <div class="announcement-admin-heading">
            <span class="announcement-admin-icon"><i class="bi bi-megaphone-fill" aria-hidden="true"></i></span>
            <div><h2>Announcements</h2><p>Mag-publish ng anunsyo na makikita ng mga customer.</p></div>
        </div>

        <?php if (!empty($_GET['published'])): ?><div class="alert alert-success">Na-publish na ang announcement.</div><?php endif; ?>
        <?php if (!empty($_GET['deleted'])): ?><div class="alert alert-info">Inalis na ang announcement.</div><?php endif; ?>
        <?php if ($formError !== ''): ?><div class="alert alert-danger"><?php echo htmlspecialchars($formError); ?></div><?php endif; ?>

        <form method="post" class="announcement-form">
            <input type="hidden" name="action" value="publish">
            <label for="announcement-title">Title</label>
            <input id="announcement-title" name="title" maxlength="180" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            <label for="announcement-message">Announcement</label>
            <textarea id="announcement-message" name="message" rows="4" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
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
                    <form method="post" onsubmit="return confirm('Alisin ang announcement na ito?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="announcement_id" value="<?php echo (int) $announcement['announcement_id']; ?>">
                        <button type="submit" class="delete-announcement">Delete</button>
                    </form>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-announcements">Wala pang naka-publish na announcement.</p>
        <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
